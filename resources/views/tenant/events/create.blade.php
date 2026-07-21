@extends('layouts.tenant-app')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-4">
        <a href="{{ route('tenant.events.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Daftar</a>
        <h3 class="fw-bold mt-2">➕ Buat Event Baru</h3>
    </div>

    <div class="row">
        <div class="col-md-10 col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('tenant.events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- INFORMASI UTAMA EVENT --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama / Judul Event</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="Masukkan nama kegiatan" required>
                        </div>

                        {{-- TANGGAL & JAM ACARA --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Pelaksanaan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="fas fa-calendar-day"></i>
                                    </span>
                                    <input type="date" name="date" value="{{ old('date') }}" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Jam Mulai Acara</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-muted">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <input type="time" name="time" value="{{ old('time') }}" class="form-control" required>
                                </div>
                                <div class="form-text">Waktu pelaksanaan acara (WIB/Zona lokal).</div>
                            </div>
                        </div>

                        {{-- KATEGORI & LOKASI --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Lokasi / Tempat Acara</label>
                                <input type="text" name="location" value="{{ old('location') }}" class="form-control" placeholder="Contoh: Aula Gedung 4 Amikom atau Zoom Meeting" required>
                            </div>
                        </div>

                        {{-- POSTER EVENT --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Poster Event</label>
                            <input type="file" name="poster_path" class="form-control" accept="image/*" required>
                            <div class="form-text">Format gambar (.jpg, .jpeg, .png). Maksimal 2MB.</div>
                        </div>

                        {{-- SECTION DYNAMIC PRICING TIER --}}
                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">🏷️ Skema Dynamic Pricing (Tier Tiket)</h5>
                                    <p class="text-muted small mb-0">Atur tahapan harga tiket (misal: Early Bird, Presale 1, Regular) beserta rentang tanggal penjualannya.</p>
                                </div>
                                <button type="button" id="add-tier-btn" class="btn btn-sm btn-primary">
                                    + Tambah Tier Tiket
                                </button>
                            </div>

                            <div id="tier-container" class="d-flex flex-column gap-3">
                                <!-- Baris Tier Pertama (Default) -->
                                <div class="tier-row card bg-light border-0 p-3">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-3">
                                            <label class="form-label text-uppercase fw-bold text-muted small mb-1">Nama Tier</label>
                                            <input type="text" name="tiers[0][name]" value="{{ old('tiers.0.name', 'Early Bird') }}" placeholder="Ex: Early Bird" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label text-uppercase fw-bold text-muted small mb-1">Harga (Rp)</label>
                                            <input type="number" name="tiers[0][price]" value="{{ old('tiers.0.price') }}" placeholder="0" min="0" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label text-uppercase fw-bold text-muted small mb-1">Kuota</label>
                                            <input type="number" name="tiers[0][quota]" value="{{ old('tiers.0.quota') }}" placeholder="100" min="1" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label text-uppercase fw-bold text-muted small mb-1">Mulai Jual</label>
                                            <input type="datetime-local" name="tiers[0][start_date]" value="{{ old('tiers.0.start_date') }}" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <label class="form-label text-uppercase fw-bold text-muted small mb-1">Selesai Jual</label>
                                            <input type="datetime-local" name="tiers[0][end_date]" value="{{ old('tiers.0.end_date') }}" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="col-md-1 text-center mt-3 mt-md-0">
                                            <span class="text-muted small d-none d-md-inline">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <button type="submit" class="btn btn-primary px-4 py-2">Terbitkan Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JAVASCRIPT UNTUK TAMBAH/HAPUS BARIS TIER -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let tierIndex = 1;
    const container = document.getElementById('tier-container');
    const addBtn = document.getElementById('add-tier-btn');

    addBtn.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.className = 'tier-row card bg-light border-0 p-3 mt-2';
        newRow.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="tiers[${tierIndex}][name]" placeholder="Ex: Presale 1" class="form-control form-control-sm" required>
                </div>
                <div class="col-6 col-md-2">
                    <input type="number" name="tiers[${tierIndex}][price]" placeholder="0" min="0" class="form-control form-control-sm" required>
                </div>
                <div class="col-6 col-md-2">
                    <input type="number" name="tiers[${tierIndex}][quota]" placeholder="100" min="1" class="form-control form-control-sm" required>
                </div>
                <div class="col-6 col-md-2">
                    <input type="datetime-local" name="tiers[${tierIndex}][start_date]" class="form-control form-control-sm" required>
                </div>
                <div class="col-6 col-md-2">
                    <input type="datetime-local" name="tiers[${tierIndex}][end_date]" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-link text-danger p-0 text-decoration-none fw-bold small remove-tier-btn">Hapus</button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        tierIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-tier-btn')) {
            e.target.closest('.tier-row').remove();
        }
    });
});
</script>
@endsection