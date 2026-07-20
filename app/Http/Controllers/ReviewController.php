<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function store(Request $request, $eventId)
    {
        // 1. Validasi Input form ulasan
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ]);

        $event = Event::findOrFail($eventId);
        $userId = auth()->id();

        // 2. Cek apakah user beneran sudah beli tiket event ini dan statusnya lunas
        $hasBought = Transaction::where('event_id', $eventId)
            ->where('customer_email', auth()->user()->email)
            ->whereIn('status', ['success', 'settlement'])
            ->exists();

        if (!$hasBought) {
            return back()->with('error', 'Anda tidak dapat memberikan ulasan karena belum membeli tiket acara ini.');
        }

        // 3. Cek apakah acara sudah selesai (Min H+1 setelah acara selesai)
        // Asumsi: Kita bandingkan dengan tanggal pelaksanaan event ($event->date)
        if (Carbon::now()->startOfDay()->lte(Carbon::parse($event->date)->startOfDay())) {
            return back()->with('error', 'Ulasan baru bisa diberikan minimal sehari setelah acara selesai dilaksanakan.');
        }

        // 4. Cek apakah user tersebut sudah pernah memberi ulasan di event ini (Mencegah spam)
        $alreadyReviewed = Review::where('event_id', $eventId)->where('user_id', $userId)->exists();
        if ($alreadyReviewed) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk acara ini sebelumnya.');
        }

        // 5. Simpan Ulasan ke Database
        Review::create([
            'user_id' => $userId,
            'event_id' => $eventId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil disimpan.');
    }
}