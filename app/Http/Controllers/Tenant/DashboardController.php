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
        // 1. Mengambil ID Tenant yang sedang login
        $tenantId = Auth::guard('tenant')->id();

        // 2. Ambil seluruh event milik tenant ini (lengkap untuk ditampilkan di tabel Daftar Event)
        $events = Event::where('tenant_id', $tenantId)
            ->orderBy('date', 'desc')
            ->get();

        // Ambil daftar ID event untuk query transaksi
        $eventIds = $events->pluck('id');

        // 3. Hitung Total Pendapatan (Hanya dari transaksi sukses pada event milik tenant)
        $totalRevenue = Transaction::whereIn('event_id', $eventIds)
            ->whereIn('status', ['success', 'settlement'])
            ->sum('total_price');

        // 4. Hitung Jumlah Event Aktif (Tanggal pelaksanaan hari ini atau mendatang)
        $activeEvents = Event::where('tenant_id', $tenantId)
            ->where('date', '>=', now()->toDateString())
            ->count();

        // 5. Ambil 5 Transaksi Terbaru (Untuk widget/ringkasan "Transaksi Terbaru" di dashboard)
        $recentTransactions = Transaction::with(['event', 'ticketTier'])
            ->whereIn('event_id', $eventIds)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 6. Ambil Semua Riwayat Transaksi (Dengan pagination untuk modal atau tabel riwayat lengkap)
        $allTransactions = Transaction::with(['event', 'ticketTier'])
            ->whereIn('event_id', $eventIds)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tenant.dashboard', compact(
            'events',
            'totalRevenue',
            'activeEvents',
            'recentTransactions',
            'allTransactions'
        ));
    }
}