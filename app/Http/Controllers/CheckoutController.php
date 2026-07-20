<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        // PROTEKSI BARU: Jika event sudah lewat, usir kembali ke halaman detail
        if (\Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($event->date)->startOfDay())) {
            return redirect()->route('event.show', $event->id)->with('error', 'Mohon maaf, pendaftaran untuk acara ini sudah ditutup karena acara telah selesai.');
        }

        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        return view('checkout.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        // PROTEKSI TAMBAHAN: Mencegah kecurangan checkout via API/Postman jika acara sudah selesai
        if (\Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($event->date)->startOfDay())) {
            return redirect()->route('event.show', $event->id)->with('error', 'Mohon maaf, pendaftaran untuk acara ini sudah ditutup karena acara telah selesai.');
        }

        // 1. Validasi Input Kredensial Pelanggan & Kode Voucher (Terbaru)
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'voucher_code'   => 'nullable|string', 
        ]);

        // 2. Cegah Check-out Jika Tiket Habis
        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX (Unik)
        $orderId = 'TRX-' . time() . '-' . Str::random(5);

        // 4. Cek Kondisi: Apakah Event Gratis atau Berbayar
        if ($event->price == 0) {
            // ==========================================
            // JALUR KHUSUS EVENT GRATIS (TANPA MIDTRANS)
            // ==========================================
            
            $transaction = Transaction::create([
                'event_id' => $event->id,
                'order_id' => $orderId,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'total_price' => 0,
                'status' => 'success', // Langsung 'success' (mengikuti format database lama)
            ]);

            // Kurangi stok tiket secara otomatis karena tidak lewat webhook Midtrans
            $event->stock = $event->stock - 1;
            $event->save();

            // Mengirimkan email E-Ticket langsung ke pelanggan
            try {
                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                    ->send(new \App\Mail\EventTicketMail($transaction));
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim email E-Ticket otomatis untuk Event Gratis: ' . $e->getMessage());
            }

            // Langsung arahkan ke halaman sukses invoice bawaan Anda
            return redirect()->route('checkout.success', $transaction->order_id)
                             ->with('success', 'Pendaftaran event gratis berhasil!');

        } else {
            // ==========================================
            // JALUR EVENT BERBAYAR (MENDUKUNG VOUCHER)
            // ==========================================
            
            // 1. Hitung harga dasar awal (Harga tiket + admin)
            $totalPrice = $event->price + 5000; 

            // 2. Jalankan Logika Voucher jika input 'voucher_code' terisi
            if ($request->filled('voucher_code')) {
                // Gunakan query penentu voucher yang aman
                $voucher = \App\Models\Voucher::where('code', strtoupper($request->voucher_code))
                    ->where('quota', '>', 0)
                    ->whereDate('expires_at', '>=', now())
                    ->first();

                if ($voucher) {
                    // Hitung nilai potongan
                    $discount = $voucher->type === 'percentage' 
                        ? ($voucher->discount_value / 100) * $event->price 
                        : $voucher->discount_value;

                    // Potong total harga transaksi (minimal Rp 0)
                    $totalPrice = max(0, $totalPrice - $discount);
                    
                    // Kurangi kuota kupon
                    $voucher->decrement('quota');
                }
            }

            // 3. LOGIKA BYPASS JIKA HARGA JADI RP 0 SETELAH DIDISKON
            if ($totalPrice == 0) {
                $transaction = Transaction::create([
                    'event_id' => $event->id,
                    'order_id' => $orderId,
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'total_price' => 0,
                    'status' => 'success', // Langsung dianggap lunas
                ]);

                $event->stock = $event->stock - 1;
                $event->save();

                try {
                    \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                        ->send(new \App\Mail\EventTicketMail($transaction));
                } catch (\Exception $e) {
                    \Log::error('Gagal mengirim email E-Ticket otomatis: ' . $e->getMessage());
                }

                return redirect()->route('checkout.success', $transaction->order_id)
                                 ->with('success', 'Pendaftaran berhasil menggunakan diskon 100%!');
            }

            // 4. KODE LANJUTAN MIDTRANS JIKA MASIH BERBAYAR (> 0)
            $transaction = Transaction::create([
                'event_id' => $event->id,
                'order_id' => $orderId,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'total_price' => $totalPrice, // Menggunakan harga terupdate yang sudah didiskon
                'status' => 'Pending', 
            ]);

            // Integrasi Snap Midtrans
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = false; 
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            // Paket Array Data Transaksi (Kirim $totalPrice yang sudah terpotong diskon)
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $totalPrice, 
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                ],
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                $transaction->update(['snap_token' => $snapToken]);

                return redirect()->route('checkout.payment', $transaction->order_id);
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal memproses pembayaran jaringan: ' . $e->getMessage());
            }
        }
    }

    public function payment($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        // Menambahkan parameter $order_id ke dalam query where
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        return view('checkout.payment', compact('transaction', 'categories'));
    }

    public function success($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();

        // Mengambil data transaksi dengan relasi data event-nya
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();

        // Jika transaksi ini adalah tiket gratis yang sudah sukses, langsung bypass pengecekan Midtrans API
        if ($transaction->event && $transaction->event->price == 0 && strtolower($transaction->status) === 'success') {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        // Konfigurasi Midtrans untuk mengecek status transaksi langsung ke API (Untuk tiket berbayar)
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        try {
            // Mengecek status pesanan secara mandiri (Bypass ke Midtrans)
            $status = \Midtrans\Transaction::status($order_id);
            
            if ($status) {
                // Mengambil nilai status transaksi secara dinamis baik berupa array maupun objek
                $trx_status = is_array($status) ? ($status['transaction_status'] ?? '') : ($status->transaction_status ?? '');
                
                // Jika API Midtrans mengonfirmasi bahwa transaksi telah berhasil (settlement / capture)
                if (in_array($trx_status, ['settlement', 'capture'])) {
                    
                    // Hanya lakukan update jika status di database lokal masih 'pending' (indikasi Webhook tidak masuk)
                    if (strtolower($transaction->status) === 'pending') {
                        $transaction->update(['status' => 'success']);
                         
                        // Jika tiket masih ada dan terhubung dengan data event, kurangi jumlahnya sebanyak 1
                        if ($transaction->event && $transaction->event->stock > 0) {
                            $transaction->event->stock = $transaction->event->stock - 1;
                            $transaction->event->save();
                             
                            // Mengirimkan email E-Ticket ke pelanggan
                            try {
                                \Illuminate\Support\Facades\Mail::to($transaction->customer_email)
                                    ->send(new \App\Mail\EventTicketMail($transaction));
                            } catch (\Exception $e) {
                                \Log::error('Gagal mengirim email E-Ticket secara manual (Bypass): ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika terjadi error dari API Midtrans (transaksi tidak valid), kembalikan ke beranda
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}