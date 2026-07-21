<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TicketTier;
use App\Mail\EventTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MidtransWebhookController extends Controller
{
    /**
     * Memproses webhook/callback notifikasi transaksi dari Midtrans.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Mencari data transaksi berdasarkan order_id beserta relasinya
        $transaction = Transaction::with(['event', 'ticketTier'])->where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        // Cegah proses berulang jika status sudah lunas/sukses
        if ($transaction->status === 'settlement' || $transaction->status === 'success') {
            return response()->json(['message' => 'Already processed']);
        }

        // Logika penerjemahan status dari Midtrans API
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $transaction->status = 'challenge';
            } else if ($fraudStatus == 'accept') {
                $transaction->status = 'success';
                $this->processSuccess($transaction); // Potong stok/kuota & kirim email
            }
        } else if ($transactionStatus == 'settlement') {
            $transaction->status = 'settlement';
            $this->processSuccess($transaction); // Potong stok/kuota & kirim email
        } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $transaction->status = 'failed';
        } else if ($transactionStatus == 'pending') {
            $transaction->status = 'pending';
        }

        $transaction->save();
        return response()->json(['message' => 'OK']);
    }

    /**
     * Memproses pengurangan stok/kuota dan pengiriman E-Ticket ketika transaksi berhasil.
     */
    private function processSuccess(Transaction $transaction)
    {
        $qty = $transaction->quantity ?? $transaction->qty ?? 1;

        DB::transaction(function () use ($transaction, $qty) {
            // 1. Kurangi kuota di TicketTier (jika ada relasi ticketTier)
            if ($transaction->ticketTier) {
                // Gunakan decrement langsung atau hitung batas minimal 0 agar tidak minus
                $currentQuota = $transaction->ticketTier->quota ?? 0;
                if ($currentQuota >= $qty) {
                    $transaction->ticketTier->decrement('quota', $qty);
                } else {
                    $transaction->ticketTier->update(['quota' => 0]);
                    Log::warning("Kuota TicketTier ID {$transaction->ticket_tier_id} habis/kurang saat Order {$transaction->order_id} diproses.");
                }
            }

            // 2. Kurangi stok utama di Event (jika ada relasi event)
            if ($transaction->event) {
                $currentStock = $transaction->event->stock ?? 0;
                if ($currentStock >= $qty) {
                    $transaction->event->decrement('stock', $qty);
                } else {
                    $transaction->event->update(['stock' => 0]);
                    Log::warning("Stok Event ID {$transaction->event_id} habis/kurang saat Order {$transaction->order_id} diproses.");
                }
            }
        });

        // 3. Kirim Email E-Ticket ke pembeli
        try {
            if (!empty($transaction->customer_email)) {
                Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email E-Ticket untuk Order ' . $transaction->order_id . ': ' . $e->getMessage());
        }
    }
}