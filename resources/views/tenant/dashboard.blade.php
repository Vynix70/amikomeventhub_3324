@extends('layouts.tenant-app') {{-- Sesuaikan dengan nama layout dashboard panitia kamu --}}

@section('content')
<!-- PENGAMAN UTAMA: Wrapper dengan padding top ekstra (pt-5 & mt-3) untuk menghindari tabrakan dengan navbar/tombol logout atas -->
<div class="tenant-content-wrapper pt-5 mt-3 px-3 px-md-4">
    <div class="container-fluid">
        
        <!-- HEADER SECTION -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4 pb-3 border-bottom border-light">
            <div>
                <h2 class="fw-black text-dark mb-1">
                    👋 Halo, {{ Auth::guard('tenant')->user()->name }}
                </h2>
                <p class="text-muted mb-0">Selamat datang kembali! Berikut ringkasan performa event organisasi Anda hari ini.</p>
            </div>
            <a href="{{ route('tenant.events.create') }}" class="btn btn-primary btn-lg px-4 py-2 rounded-3 fw-bold shadow-sm border-0 dynamic-hover-btn">
                <i class="fas fa-plus me-2"></i> Buat Event Baru
            </a>
        </div>

        <!-- ROW STATISTIK -->
        <div class="row g-4 mb-4">
            <!-- Card Pendapatan -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative bg-white">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 text-primary">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-bold small mb-1 tracking-wider">Total Pendapatan</h6>
                            <h3 class="mb-0 fw-black text-dark">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0 bg-primary" style="height: 4px;"></div>
                </div>
            </div>
            
            <!-- Card Event Aktif -->
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative bg-white">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 text-success">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-bold small mb-1 tracking-wider">Event Aktif</h6>
                            <h3 class="mb-0 fw-black text-dark">{{ $activeEvents }} <span class="fs-5 fw-normal text-muted">Acara</span></h3>
                        </div>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0 bg-success" style="height: 4px;"></div>
                </div>
            </div>
        </div>

        <!-- ROW TRANSAKSI TERBARU -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-4">🛒</span>
                    <h5 class="fw-bold text-dark mb-0">5 Transaksi Tiket Terakhir</h5>
                </div>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-medium">Pembaruan Realtime</span>
            </div>
            
            <div class="card-body px-4 pb-4">
                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase tracking-wider small fw-bold text-muted">
                            <tr>
                                <th class="py-3 px-3">Nota / ID</th>
                                <th class="py-3">Nama Event</th>
                                <th class="py-3">Email Pembeli</th>
                                <th class="py-3">Total Bayar</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 px-3 text-end">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="text-secondary font-medium">
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td class="px-3">
                                        <span class="badge bg-light text-secondary border font-monospace py-2 px-3 rounded-3 fs-7">
                                            #{{ $tx->reference }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $tx->event->title }}</div>
                                    </td>
                                    <td>{{ $tx->customer_email }}</td>
                                    <td class="fw-bold text-dark">
                                        Rp {{ number_format($tx->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if(in_array(strtolower($tx->status), ['success', 'settlement']))
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-20 px-3 py-2 rounded-pill fw-bold">
                                                <i class="fas fa-check-circle me-1"></i> Lunas
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-20 px-3 py-2 rounded-pill fw-bold text-capitalize">
                                                <i class="fas fa-clock me-1"></i> {{ $tx->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 text-end text-muted small">
                                        {{ $tx->created_at->format('d M Y') }}
                                        <div class="text-[11px] text-muted-opacity-50 font-monospace">{{ $tx->created_at->format('H:i') }} WIB</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="mb-2 fs-2">📭</div>
                                        <p class="mb-0 fw-medium">Belum ada transaksi tiket yang masuk untuk event Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Style Tambahan Khusus untuk mempercantik aksen UI dan mengunci Layout --}}
<style>
    .fw-black { font-weight: 800 !important; }
    .tracking-wider { letter-spacing: 0.05em; }
    .fs-7 { font-size: 0.825rem; }
    
    /* Mencegah tabrakan layout pada resolusi layar tertentu */
    .tenant-content-wrapper {
        min-height: calc(100vh - 70px);
        position: relative;
        z-index: 1;
    }
    
    /* Efek interaktif tombol dan kartu */
    .dynamic-hover-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.15) !important;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
    }
    
    /* Varian warna badge Bootstrap 5 lama fallback */
    .bg-success-subtle { bg-opacity: 0.15; background-color: rgba(25, 135, 84, 0.15) !important; }
    .bg-warning-subtle { bg-opacity: 0.15; background-color: rgba(255, 193, 7, 0.15) !important; }
    .text-warning-emphasis { color: #664d03 !important; }
</style>
@endsection