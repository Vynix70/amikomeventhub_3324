<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Transaction;

class EventController extends Controller
{
    // Menampilkan detail event
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return view('event-detail', compact('event'));
    }

    // Menampilkan halaman checkout (Menggunakan file checkout/create.blade.php)
    public function checkout($id)
    {
        $event = Event::findOrFail($id);
        
        // PERBAIKAN 1: Mengarahkan ke file create.blade.php di dalam folder checkout
        return view('checkout.create', compact('event'));
    }

    // Memproses data formulir checkout & simpan ke database
    public function store(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        // Validasi data input pembeli
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // Cek stok event sebagai pengaman tambahan
        if ($event->stock <= 0) {
            return redirect()->back()->with('error', 'Maaf, kuota tiket untuk event ini sudah habis!');
        }

        // Hitung total harga (Harga tiket + biaya layanan Rp 5.000)
        $total_price = $event->price + 5000;

        // Membuat Order ID unik acak
        $order_id = 'TRX-' . date('Ymd') . rand(1000, 9999);

        // Simpan data transaksi baru ke database
        $transaction = Transaction::create([
            'event_id'       => $event->id,
            'order_id'       => $order_id,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price'    => $total_price,
            'status'         => 'SUCCESS', 
            'snap_token'     => null,      
        ]);

        // Kurangi stok event jika diperlukan
        $event->decrement('stock');

        // PERBAIKAN 2: Mengalihkan parameter ID secara langsung (bersih tanpa dibungkus key array)
        return redirect()->route('ticket', $transaction->id)
                         ->with('success', 'Pembayaran Berhasil!');
    }

    // Menampilkan halaman tiket berdasarkan ID transaksi
    public function ticket($id)
    {
        // Cari transaksi berdasarkan ID dari segmen URL, jika tidak ada otomatis ke halaman 404
        $transaction = Transaction::with('event')->findOrFail($id);
        
        return view('ticket', compact('transaction'));
    }
}