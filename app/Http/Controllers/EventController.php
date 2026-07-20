<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function __construct()
    {
        // Konfigurasi dasar Midtrans (Pastikan kredensial ini sudah diset di .env Anda)
        \Midtrans\Config::$serverKey = config('midtrans.server_key', env('MIDTRANS_SERVER_KEY'));
        \Midtrans\Config::$isProduction = config('midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * 1. Menampilkan detail event
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('event-detail', compact('event'));
    }

    /**
     * 2. Menampilkan halaman checkout (Menggunakan file checkout/create.blade.php)
     */
    public function checkout($id)
    {
        $event = Event::findOrFail($id);
        
        return view('checkout.create', compact('event'));
    }

    /**
     * 3. Memproses data formulir checkout & simpan ke database (Mendukung Event Gratis & Berbayar + Voucher + Midtrans)
     */
    public function store(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Validasi data input pembeli beserta kode voucher yang bersifat opsional
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'voucher_code'   => 'nullable|string', 
        ]);

        // Cek stok event sebagai pengaman tambahan awal
        if ($event->stock <= 0) {
            return redirect()->back()->with('error', 'Maaf, kuota tiket untuk event ini sudah habis!');
        }

        try {
            // Gunakan DB::transaction untuk mengamankan perubahan data kuota voucher & stok tiket
            $redirectData = DB::transaction(function () use ($request, $event) {
                
                // 1. Hitung harga dasar awal (Harga tiket + admin)
                $total_price = $event->price + 5000;

                // Format Baru: Menjamin keunikan ID transaksi agar tidak ditolak oleh Midtrans
                $order_id = 'TRX-' . date('YmdHis') . '-' . rand(100, 999); 

                // 2. Jalankan Logika Voucher/Kupon untuk memotong $total_price
                if ($request->filled('voucher_code') && $event->price > 0) {
                    // lockForUpdate menghindari race condition saat kuota voucher diperebutkan
                    $voucher = Voucher::where('code', strtoupper($request->voucher_code))
                        ->where('quota', '>', 0)
                        ->where('expires_at', '>=', now()->toDateString())
                        ->lockForUpdate()
                        ->first();

                    if ($voucher) {
                        $discount = $voucher->type === 'percentage' 
                            ? ($voucher->discount_value / 100) * $event->price 
                            : $voucher->discount_value;

                        $total_price = max(0, $total_price - $discount); // Harga terpotong!
                        $voucher->decrement('quota');
                    }
                }

                // 3. LOGIKA BYPASS ACARA GRATIS (Jika setelah didiskon harganya Rp 0)
                if ($total_price == 0) {
                    $transaction = Transaction::create([
                        'event_id'       => $event->id,
                        'order_id'       => $order_id,
                        'customer_name'  => $request->customer_name,
                        'customer_email' => $request->customer_email,
                        'customer_phone' => $request->customer_phone,
                        'total_price'    => 0,
                        'status'         => 'SUCCESS', // Otomatis sukses
                        'snap_token'     => null,      
                    ]);
                    
                    $event->decrement('stock');
                    
                    return [
                        'type' => 'free',
                        'transaction_id' => $transaction->id
                    ];
                }

                // 4. KODE UNTUK EVENT BERBAYAR (Menggunakan rincian item_details terbaru)
                $midtrans_params = [
                    'transaction_details' => [
                        'order_id' => $order_id,
                        'gross_amount' => (int) $total_price, 
                    ],
                    'item_details' => [
                        [
                            'id' => 'EVENT-' . $event->id,
                            'price' => (int) $total_price, // Gunakan harga fix setelah terpotong diskon
                            'quantity' => 1,
                            'name' => substr($event->title, 0, 50), // Batasi maks 50 karakter sesuai aturan Midtrans
                        ]
                    ],
                    'customer_details' => [
                        'first_name' => $request->customer_name,
                        'email' => $request->customer_email,
                        'phone' => $request->customer_phone,
                    ]
                ];

                // Buat snap token berdasarkan harga diskon baru
                $snapToken = \Midtrans\Snap::getSnapToken($midtrans_params);

                // Simpan transaksi berbayar ke database
                $transaction = Transaction::create([
                    'event_id'       => $event->id,
                    'order_id'       => $order_id,
                    'customer_name'  => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'total_price'    => $total_price,
                    'status'         => 'PENDING', 
                    'snap_token'     => $snapToken, // Simpan token terupdate      
                ]);

                $event->decrement('stock');

                return [
                    'type' => 'paid',
                    'transaction_id' => $transaction->id
                ];
            });

            // Respon pengalihan rute di luar closure Transaction sesuai jenis hasil transaksi
            if ($redirectData['type'] === 'free') {
                // Langsung bypass, lempar ke halaman sukses tiket tanpa ke payment.blade.php
                return redirect()->route('ticket', $redirectData['transaction_id'])
                                 ->with('success', 'Transaksi Gratis Berhasil!');
            } else {
                // Baru arahkan ke halaman payment.blade.php yang kamu punya
                return redirect()->route('checkout.payment', $redirectData['transaction_id']);
            }

        } catch (\Exception $e) {
            // Rollback otomatis jika transaksi DB gagal atau API Midtrans mengembalikan error
            return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }

    /**
     * 4. Menampilkan halaman tiket berdasarkan ID transaksi
     */
    public function ticket($id)
    {
        $transaction = Transaction::with('event')->findOrFail($id);
        
        return view('ticket', compact('transaction'));
    }
}