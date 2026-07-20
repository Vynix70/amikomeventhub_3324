@extends('layouts.tenant-app')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-4">
        <a href="{{ route('tenant.events.index') }}" class="btn btn-sm btn-outline-secondary">← Kembali ke Daftar</a>
        <h3 class="fw-bold mt-2">✏️ Edit Event: {{ $event->title }}</h3>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    {{-- PENTING: Gunakan method POST dengan tambahan @method('PUT') untuk proses update --}}
                    <form action="{{ route('tenant.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama / Judul Event</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" placeholder="Masukkan nama kegiatan" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Tanggal Pelaksanaan</label>
                                <input type="date" name="date" class="form-control" value="{{ old('date', \Carbon\Carbon::parse($event->date)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lokasi / Tempat Acara</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $event->location) }}" placeholder="Contoh: Aula Gedung 4 Amikom atau Zoom Meeting" required>
                        </div>

                        {{-- BAGIAN INPUT POSTER BARU YANG SUDAH DISESUAIKAN --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Poster Event</label>
                            
                            <!-- Cek menggunakan properti poster_path dan folder assets/images -->
                            @if($event->poster_path && file_exists(public_path('assets/images/' . $event->poster_path)))
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-1">Poster saat ini:</small>
                                    <img src="{{ asset('assets/images/' . $event->poster_path) }}" alt="Poster Saat Ini" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            @endif

                            <input type="file" name="poster_path" class="form-control" accept="image/*">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah poster. Format (.jpg, .jpeg, .png), maks 2MB.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Harga Tiket (Rupiah)</label>
                                <input type="number" name="price" class="form-control" value="{{ old('price', $event->price) }}" placeholder="0 untuk gratis" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kuota / Stok Tiket</label>
                                <input type="number" name="stock" class="form-control" value="{{ old('stock', $event->stock) }}" placeholder="Jumlah kuota peserta" min="1" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 mt-2">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection