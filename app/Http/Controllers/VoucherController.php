<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Event;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function applyVoucher(Request $request)
    {
        // Validasi input request
        $request->validate([
            'code' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        // 1. Cari kode voucher di database (Case-Insensitive)
        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Kode kupon tidak ditemukan!'], 404);
        }

        // 2. Cek apakah kuota voucher sudah habis
        if ($voucher->quota <= 0) {
            return response()->json(['success' => false, 'message' => 'Maaf, kuota kupon ini sudah habis!'], 400);
        }

        // 3. Cek apakah voucher sudah kadaluarsa
        if (Carbon::parse($voucher->expires_at)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Maaf, kupon ini sudah kadaluarsa!'], 400);
        }

        // 4. Ambil data event untuk kalkulasi harga
        $event = Event::find($request->event_id);
        $original_price = $event->price;
        $service_fee = $original_price == 0 ? 0 : 5000; // Biaya layanan (jika bukan event gratis)
        $current_total = $original_price + $service_fee;

        // 5. Hitung nilai potongan harga
        $discount = 0;
        if ($voucher->type === 'percentage') {
            $discount = ($voucher->discount_value / 100) * $original_price;
        } else {
            $discount = $voucher->discount_value;
        }

        // Pastikan nilai diskon tidak melebihi harga total tiket
        if ($discount > $current_total) {
            $discount = $current_total;
        }

        $final_price = $current_total - $discount;

        // Kembalikan data sukses dalam format JSON untuk dibaca oleh JavaScript di Blade
        return response()->json([
            'success' => true,
            'message' => 'Kupon berhasil diterapkan!',
            'discount' => $discount,
            'final_price' => $final_price,
            'code' => $voucher->code
        ]);
    }
}