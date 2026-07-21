<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketTier;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Models\Category;
use App\Mail\EventTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman formulir checkout.
     */
    public function create(Event $event)
    {
        // PROTEKSI: Jika event sudah lewat, kembalikan ke halaman detail
        if (Carbon::now()->startOfDay()->gt(Carbon::parse($event->date)->startOfDay())) {
            return redirect()->route('event.show', $event->id)
                ->with('error', 'Mohon maaf, pendaftaran untuk acara ini sudah ditutup karena acara telah selesai.');
        }

        $categories = Category::all();

        return view('checkout.create', compact('event', 'categories'));
    }

    /**
     * Memproses checkout / pembuatan pesanan tiket (support via Form POST dari Detail/Checkout).
     * Dapat dipanggil melalui route: route('checkout.store', $event)
     */
    public function store(Request $request, $event)
    {
        // 1. Ambil data Event (Mendukung ID maupun Route Model Binding)
        $eventModel = $event instanceof Event ? $event : Event::findOrFail($event);

        // 2. Proteksi Tanggal Event
        if (Carbon::now()->startOfDay()->gt(Carbon::parse($eventModel->date)->startOfDay())) {
            return redirect()->route('event.show', $eventModel->id)
                ->with('error', 'Mohon maaf, pendaftaran untuk acara ini sudah ditutup.');
        }

        // 3. Validasi Input Data Pemesan & Tiket
        $validated = $request->validate([
            'ticket_tier_id' => 'required|exists:ticket_tiers,id',
            'quantity'       => 'required|integer|min:1',
            'customer_name'  => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'voucher_code'   => 'nullable|string',
        ]);

        // Fallback otomatis menggunakan data user yang sedang login jika tidak diisi di form
        $customerName  = $validated['customer_name'] ?? auth()->user()->name ?? 'Guest';
        $customerEmail = $validated['customer_email'] ?? auth()->user()->email ?? null;
        $customerPhone = $validated['customer_phone'] ?? auth()->user()->phone ?? '-';

        if (!$customerEmail) {
            return back()->with('error', 'Email pemesan wajib diisi.');
        }

        // 4. Ambil Tier Tiket & Pastikan Milik Event Terkait
        $tier = TicketTier::where('event_id', $eventModel->id)
            ->findOrFail($validated['ticket_tier_id']);

        // 5. Cek Stok / Kuota Tiket
        if ($tier->quota < $validated['quantity']) {
            return back()->with('error', 'Mohon maaf, jumlah tiket melebihi sisa kuota yang tersedia.');
        }

        // 6. Hitung Subtotal & Potongan Voucher
        $subtotal = $tier->price * $validated['quantity'];
        $discount = 0;

        if (!empty($validated['voucher_code'])) {
            $voucher = Voucher::where('code', strtoupper($validated['voucher_code']))
                ->where('quota', '>', 0)
                ->whereDate('expires_at', '>=', now())
                ->first();

            if ($voucher) {
                $discount = $voucher->type === 'percentage'
                    ? ($voucher->discount_value / 100) * $subtotal
                    : $voucher->discount_value;

                $voucher->decrement('quota');
            }
        }

        $finalTotal = max(0, $subtotal - $discount);
        $orderId    = 'TRX-' . time() . '-' . Str::random(5);

        // =========================================================================
        // 7. BYPASS TRANSAKSI UNTUK EVENT GRATIS / DISKON 100%
        // =========================================================================
        if ($finalTotal == 0) {
            $transaction = Transaction::create([
                'event_id'       => $eventModel->id,
                'ticket_tier_id' => $tier->id,
                'order_id'       => $orderId,
                'customer_name'  => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'quantity'       => $validated['quantity'],
                'total_price'    => 0,
                'status'         => 'success', // Langsung Lunas
            ]);

            // Kurangi kuota tier tiket secara langsung
            $tier->decrement('quota', $validated['quantity']);

            // Kurangi juga stok utama di event agar sinkron
            if ($tier->event) {
                $tier->event->decrement('stock', $validated['quantity']);
            }

            // Kirim E-Ticket via Email
            try {
                Mail::to($transaction->customer_email)
                    ->send(new EventTicketMail($transaction));
            } catch (\Exception $e) {
                Log::error('Gagal mengirim email E-Ticket gratis: ' . $e->getMessage());
            }

            return redirect()->route('checkout.success', $transaction->order_id)
                ->with('success', 'Tiket gratis berhasil diklaim!');
        }

        // =========================================================================
        // 8. INTEGRASI MIDTRANS (UNTUK EVENT BERBAYAR)
        // =========================================================================
        $transaction = Transaction::create([
            'event_id'       => $eventModel->id,
            'ticket_tier_id' => $tier->id,
            'order_id'       => $orderId,
            'customer_name'  => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'quantity'       => $validated['quantity'],
            'total_price'    => $finalTotal,
            'status'         => 'Pending',
        ]);

        // Setup Konfigurasi Midtrans Snap
        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = config('app.env') === 'production';
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $finalTotal,
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email'      => $customerEmail,
                'phone'      => $customerPhone,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

            return redirect()->route('checkout.payment', $transaction->order_id)
                ->with('success', 'Pesanan berhasil dibuat, silakan selesaikan pembayaran.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses jaringan pembayaran Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * Alias method untuk menjaga kompatibilitas route lama.
     */
    public function processCheckout(Request $request)
    {
        $event = $request->input('event_id');
        return $this->store($request, $event);
    }

    /**
     * Halaman pembayaran tiket (Midtrans Snap View).
     */
    public function payment($order_id)
    {
        $categories  = Category::all();
        $transaction = Transaction::with(['event', 'ticketTier'])
            ->where('order_id', $order_id)
            ->firstOrFail();

        return view('checkout.payment', compact('transaction', 'categories'));
    }

    /**
     * Halaman bukti transaksi sukses / E-Ticket & verifikasi status Midtrans.
     */
    public function success($order_id)
    {
        $categories  = Category::all();
        $transaction = Transaction::with(['event', 'ticketTier'])
            ->where('order_id', $order_id)
            ->firstOrFail();

        // Jika transaksi gratis/berbayar sudah berstatus success di database lokal
        if (strtolower($transaction->status) === 'success') {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        // Konfigurasi Midtrans API Status Checking
        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = config('app.env') === 'production';
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        try {
            $status = \Midtrans\Transaction::status($order_id);

            if ($status) {
                $trx_status = is_array($status) ? ($status['transaction_status'] ?? '') : ($status->transaction_status ?? '');

                if (in_array($trx_status, ['settlement', 'capture'])) {
                    if (strtolower($transaction->status) === 'pending') {
                        $transaction->update(['status' => 'success']);

                        // Potong kuota tier setelah konfirmasi sukses pembayaran Midtrans
                        if ($transaction->ticketTier && $transaction->ticketTier->quota >= $transaction->quantity) {
                            $transaction->ticketTier->decrement('quota', $transaction->quantity);
                        }

                        // Kurangi juga stok utama di Event agar sinkron
                        if ($transaction->event && $transaction->event->stock >= $transaction->quantity) {
                            $transaction->event->decrement('stock', $transaction->quantity);
                        }

                        // Send E-Ticket
                        try {
                            Mail::to($transaction->customer_email)
                                ->send(new EventTicketMail($transaction));
                        } catch (\Exception $e) {
                            Log::error('Gagal mengirim email E-Ticket via Callback Check: ' . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diverifikasi.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}