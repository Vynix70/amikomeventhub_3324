@extends('layouts.tenant-app')

@section('content')
<!-- WRAPPER DENGAN PADDING AMAN AGAR TIDAK NABRAK NAVBAR ATAS -->
<div class="tenant-content-wrapper pt-5 mt-3 px-3 px-md-4">
    <div class="container-fluid">
        
        <div class="mb-4">
            <a href="{{ route('tenant.events.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <h3 class="fw-bold mt-3 text-dark">✏️ Edit Event: {{ $event->title }}</h3>
        </div>

        <div class="row">
            <div class="col-lg-10 col-xl-9">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white mb-4">
                    <div class="card-body p-4">
                        <form action="{{ route('tenant.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            {{-- NAMA / JUDUL EVENT --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama / Judul Event</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" placeholder="Masukkan nama kegiatan" required>
                            </div>

                            <div class="row">
                                {{-- TANGGAL PELAKSANAAN --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Tanggal Pelaksanaan</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fas fa-calendar-day"></i></span>
                                        <input type="date" name="date" class="form-control" value="{{ old('date', \Carbon\Carbon::parse($event->date)->format('Y-m-d')) }}" required>
                                    </div>
                                </div>
                                
                                {{-- JAM MULAI ACARA --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Jam Mulai Acara</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-muted"><i class="fas fa-clock"></i></span>
                                        <input type="time" name="time" class="form-control" value="{{ old('time', \Carbon\Carbon::parse($event->date)->format('H:i')) }}" required>
                                    </div>
                                    <div class="form-text text-muted">Sesuaikan dengan waktu pelaksanaan acara.</div>
                                </div>
                            </div>

                            {{-- KATEGORI --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- LOKASI --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Lokasi / Tempat Acara</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}" placeholder="Contoh: Aula Gedung 4 Amikom atau Zoom Meeting" required>
                            </div>

                            {{-- POSTER EVENT --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Poster Event</label>
                                
                                @if($event->poster_path && file_exists(public_path('assets/images/' . $event->poster_path)))
                                    <div class="mb-3 p-2 border rounded-3 bg-light d-inline-block d-block">
                                        <small class="text-muted d-block mb-1 fw-bold">Poster saat ini:</small>
                                        <img src="{{ asset('assets/images/' . $event->poster_path) }}" alt="Poster Saat Ini" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 180px;">
                                    </div>
                                @endif

                                <input type="file" name="poster_path" class="form-control" accept="image/*">
                                <div class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah poster. Format (.jpg, .jpeg, .png), maks 2MB.</div>
                            </div>

                            <!-- SECTION EDIT DYNAMIC PRICING TIER -->
                            <div class="border-top pt-4 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="fw-bold mb-0 text-dark">🏷️ Edit Skema Dynamic Pricing</h5>
                                        <small class="text-muted">Ubah rentang waktu, harga, atau kuota masing-masing tier tiket.</small>
                                    </div>
                                    <button type="button" id="add-tier-btn" class="btn btn-sm btn-outline-primary rounded-3 fw-bold">
                                        <i class="fas fa-plus me-1"></i> Tambah Tier Tiket
                                    </button>
                                </div>

                                <div id="tier-container" class="d-flex flex-column gap-3">
                                    @foreach($event->ticketTiers as $index => $tier)
                                        <div class="tier-row card border bg-light p-3 rounded-3">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-12 col-md-3">
                                                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Nama Tier</label>
                                                    <input type="text" name="tiers[{{ $index }}][name]" value="{{ old("tiers.$index.name", $tier->name) }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Harga (Rp)</label>
                                                    <input type="number" name="tiers[{{ $index }}][price]" value="{{ old("tiers.$index.price", (int)$tier->price) }}" min="0" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Kuota</label>
                                                    <input type="number" name="tiers[{{ $index }}][quota]" value="{{ old("tiers.$index.quota", $tier->quota) }}" min="1" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Mulai Jual</label>
                                                    <input type="datetime-local" name="tiers[{{ $index }}][start_date]" value="{{ old("tiers.$index.start_date", \Carbon\Carbon::parse($tier->start_date)->format('Y-m-d\TH:i')) }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <label class="form-label small fw-bold text-uppercase text-muted mb-1">Selesai Jual</label>
                                                    <input type="datetime-local" name="tiers[{{ $index }}][end_date]" value="{{ old("tiers.$index.end_date", \Carbon\Carbon::parse($tier->end_date)->format('Y-m-d\TH:i')) }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-12 col-md-1 text-center mt-2 mt-md-0">
                                                    @if($index > 0)
                                                        <button type="button" class="btn btn-sm btn-link text-danger remove-tier-btn p-0 fw-bold text-decoration-none">
                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                        </button>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <a href="{{ route('tenant.events.index') }}" class="btn btn-light px-4 rounded-3 fw-bold">Batal</a>
                                <button type="submit" class="btn btn-primary px-4 rounded-3 fw-bold shadow-sm">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let tierIndex = {{ $event->ticketTiers->count() }};
    const container = document.getElementById('tier-container');
    const addBtn = document.getElementById('add-tier-btn');

    addBtn.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.className = 'tier-row card border bg-light p-3 rounded-3 mt-2';
        newRow.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <input type="text" name="tiers[${tierIndex}][name]" placeholder="Ex: Regular" class="form-control form-control-sm" required>
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
                <div class="col-12 col-md-1 text-center mt-2 mt-md-0">
                    <button type="button" class="btn btn-sm btn-link text-danger remove-tier-btn p-0 fw-bold text-decoration-none">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        tierIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target && e.target.closest('.remove-tier-btn')) {
            e.target.closest('.tier-row').remove();
        }
});
</script>
@endsection