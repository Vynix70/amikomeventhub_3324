<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Sesuaikan dengan nama Model Transaksi / Pemesanan kamu
use Illuminate\Http\Request;

class MyTicketController extends Controller
{
    public function index()
    {
        // Ambil semua transaksi milik user yang sedang login beserta relasi event-nya
        $tickets = Transaction::with(['event', 'ticketTier'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('my-tickets.index', compact('tickets'));
    }
}