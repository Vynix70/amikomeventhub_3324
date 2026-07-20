<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil ID Tenant yang saat ini sedang login
        $tenantId = Auth::guard('tenant')->id();

        // 1. Ambil ID dari semua event yang dibuat oleh organisasi ini saja
        $eventIds = Event::where('tenant_id', $tenantId)->pluck('id');

        // 2. Hitung statistik pendapatan hanya dari tiket event milik organisasi ini
        $totalRevenue = Transaction::whereIn('event_id', $eventIds)
            ->whereIn('status', ['success', 'settlement'])
            ->sum('total_price');

        $activeEvents = Event::where('tenant_id', $tenantId)
            ->where('date', '>=', now()->toDateString())
            ->count();

        $recentTransactions = Transaction::with('event')
            ->whereIn('event_id', $eventIds)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('tenant.dashboard', compact('totalRevenue', 'activeEvents', 'recentTransactions'));
    }
}