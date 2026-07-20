@extends('layouts.tenant-app')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-4">
        <a href="{{ route('tenant.events.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Daftar</a>
        <h3 class="fw-bold mt-2">➕ Buat Event Baru</h3>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    {{-- PENTING: Menambahkan enctype="multipart/form-data" agar bisa upload file --}}
                    <form action="{{ route('tenant.events.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama / Judul Event</label>
                            <input type="text" name="title" class="form-control" placeholder="Masukkan nama kegiatan" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Pelaksanaan</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lokasi / Tempat Acara</label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Aula Gedung 4 Amikom atau Zoom Meeting" required>
                        </div>

                        {{-- INPUT POSTER BARU --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Poster Event</label>
                            <input type="file" name="poster_path" class="form-control" accept="image/*" required>
                            <div class="form-text">Format gambar (.jpg, .jpeg, .png). Maksimal 2MB.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Harga Tiket (Rupiah)</label>
                                <input type="number" name="price" class="form-control" placeholder="0 untuk gratis" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kuota / Stok Tiket</label>
                                <input type="number" name="stock" class="form-control" placeholder="Jumlah kuota peserta" min="1" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 mt-2">Terbitkan Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection