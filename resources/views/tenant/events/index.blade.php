@extends('layouts.tenant-app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">📅 Daftar Event Organisasi</h3>
            <p class="text-muted">Kelola seluruh event yang diselenggarakan oleh organisasi Anda di sini.</p>
        </div>
        <a href="{{ route('tenant.events.create') }}" class="btn btn-primary">
            ➕ Buat Event Baru
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <!-- HEADER POSTER -->
                            <th style="width: 100px;">Poster</th>
                            <th>Nama Event</th>
                            <th>Tanggal Pelaksanaan</th>
                            <th>Harga Tiket</th>
                            <th>Sisa Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            @php
                                // Menghitung total sisa kuota tiket dari relasi ticketTiers
                                $totalQuota = $event->ticketTiers->sum('quota');
                            @endphp
                            <tr>
                                <!-- BAGIAN TD POSTER (MULTI-SISTEM) -->
                                <td>
                                    @if(str_contains($event->poster_path, 'posters/'))
                                        <!-- Menampilkan gambar sistem BARU (Format Storage: posters/namafile.jpg) -->
                                        <img src="{{ asset('storage/' . $event->poster_path) }}" 
                                             alt="Poster {{ $event->title }}" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 100px; object-fit: cover;">
                                    @elseif(file_exists(public_path('assets/images/' . $event->poster_path)) && $event->poster_path)
                                        <!-- Menampilkan gambar sistem LAMA milik tenant (Format public/assets/images/namafile.jpg) -->
                                        <img src="{{ asset('assets/images/' . $event->poster_path) }}" 
                                             alt="Poster {{ $event->title }}" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 100px; object-fit: cover;">
                                    @elseif($event->poster_path)
                                        <!-- Menampilkan gambar milik Admin lama ATAU jika path langsung nama file di storage -->
                                        <img src="{{ asset('storage/posters/' . $event->poster_path) }}" 
                                             alt="Poster {{ $event->title }}" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 100px; object-fit: cover;">
                                    @else
                                        <!-- Fallback jika data poster benar-benar kosong/null -->
                                        <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded" 
                                             style="width: 80px; height: 100px; font-size: 0.75rem; text-align: center;">
                                            No Poster
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-bold">{{ $event->title }}</h6>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                                <td>Rp {{ number_format($event->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($totalQuota <= 0)
                                        <span class="badge bg-danger">Habis</span>
                                    @else
                                        <span class="badge bg-success">{{ $totalQuota }} Tiket</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('tenant.events.edit', $event->id) }}" class="btn btn-sm btn-warning">
                                            ✏️ Edit
                                        </a>
                                        <form action="{{ route('tenant.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Belum ada event yang dibuat. Klik tombol "Buat Event Baru" di atas untuk memulai.
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