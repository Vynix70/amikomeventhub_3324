@extends('layouts.tenant-app') {{-- Sesuaikan dengan nama layout dashboard panitia kamu --}}

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard {{ Auth::guard('tenant')->user()->name }}</h2>
            <p class="text-muted">Selamat datang di panel kelola event organisasi Anda.</p>
        </div>
        <a href="{{ route('tenant.events.create') }}" class="btn btn-primary">
            ➕ Buat Event Baru
        </a>
    </div>

    <!-- ROW STATISTIK -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body p-4">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-2">Total Pendapatan</h6>
                    <h3 class="mb-0 fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body p-4">
                    <h6 class="text-white-50 text-uppercase fw-bold mb-2">Event Aktif</h6>
                    <h3 class="mb-0 fw-bold">{{ $activeEvents }} Acara</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- ROW TRANSAKSI TERBARU -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent border-0 pt-4">
            <h5 class="fw-bold mb-0">🛒 5 Transaksi Tiket Terakhir</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nota / ID</th>
                            <th>Nama Event</th>
                            <th>Email Pembeli</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                            <tr>
                                <td><code>#{{ $tx->reference }}</code></td>
                                <td><strong>{{ $tx->event->title }}</strong></td>
                                <td>{{ $tx->customer_email }}</td>
                                <td>Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td>
                                    @if(in_array($tx->status, ['success', 'settlement']))
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $tx->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $tx->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada transaksi tiket yang masuk untuk event Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection