@extends('layouts.admin', ['title' => 'Tambah Event'])

@section('content')
<header class="mb-10">
    <h1 class="text-3xl font-black">Tambah Event</h1>
    <p class="text-slate-500 font-medium">Isi detail event baru beserta tier harganya secara lengkap.</p>
</header>

<div class="bg-white rounded-[2.5rem] border border-slate-100 p-10 shadow-sm max-w-4xl">
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div class="grid grid-cols-2 gap-6">
            {{-- Judul Event --}}
            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Judul Event</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Masukkan judul event" required>
                @error('title') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Kategori --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                <select name="category_id" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Tanggal & Waktu Pelaksanaan --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal & Waktu Pelaksanaan</label>
                <input type="datetime-local" name="date" value="{{ old('date') }}" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('date') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Jelaskan detail mengenai acara ini..." required>{{ old('description') }}</textarea>
                @error('description') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Harga Dasar (Opsional/Fallback) --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Harga Dasar (Rp) <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                </label>
                <input type="number" name="price" value="{{ old('price', 0) }}" min="0" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-[10px] text-slate-400 mt-1">Digunakan sebagai harga default jika tier belum disetting.</p>
            </div>

            {{-- Stok Tiket Dasar (Opsional/Fallback) --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">
                    Total Stok Tiket Dasar <span class="text-xs text-slate-400 font-normal">(Opsional)</span>
                </label>
                <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-[10px] text-slate-400 mt-1">Kuota akumulasi utama jika tidak menggunakan tier khusus.</p>
            </div>

            {{-- Lokasi --}}
            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Event</label>
                <input type="text" name="location" value="{{ old('location') }}" placeholder="Ex: Grand Ballroom Hotel / Gedung Utama" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                @error('location') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Upload Poster --}}
            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Upload Poster Event</label>
                <input type="file" name="poster_path" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" required>
                @error('poster_path') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- ================= DYNAMIC PRICING TIERS SECTION ================= --}}
            <div class="col-span-2 border-t border-slate-100 pt-6 mt-4">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">🏷️ Dynamic Pricing Tier</h3>
                        <p class="text-xs text-slate-400">Atur tahapan harga tiket (Early Bird, Presale, Regular) berdasarkan rentang waktu.</p>
                    </div>
                    <button type="button" id="add-tier-btn" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold hover:bg-indigo-100 transition">
                        + Tambah Tier
                    </button>
                </div>

                <div id="tier-container" class="space-y-3">
                    {{-- Row Tier Default (Indeks 0) --}}
                    <div class="tier-row grid grid-cols-12 gap-2 p-4 bg-slate-50 border border-slate-100 rounded-2xl items-center">
                        <div class="col-span-12 lg:col-span-3">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Tier</label>
                            <input type="text" name="tiers[0][name]" placeholder="Ex: Early Bird" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="col-span-6 lg:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Harga (Rp)</label>
                            <input type="number" name="tiers[0][price]" placeholder="0" min="0" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="col-span-6 lg:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kuota</label>
                            <input type="number" name="tiers[0][quota]" placeholder="100" min="1" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="col-span-6 lg:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Mulai Jual</label>
                            <input type="datetime-local" name="tiers[0][start_date]" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="col-span-6 lg:col-span-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Selesai Jual</label>
                            <input type="datetime-local" name="tiers[0][end_date]" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <div class="col-span-12 lg:col-span-1 text-center pt-2 lg:pt-4">
                            <span class="text-slate-300 text-xs">-</span>
                        </div>
                    </div>
                </div>
                @error('tiers') <span class="text-xs text-red-500 font-semibold mt-2 block">{{ $message }}</span> @enderror
            </div>

        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-end items-center gap-4 mt-8 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.events.index') }}" class="px-6 py-3 font-bold text-slate-400 hover:text-slate-600 transition">Batal</a>
            <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition">
                Simpan Event
            </button>
        </div>
    </form>
</div>

{{-- Dynamic Tier JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    let tierIndex = 1;
    const container = document.getElementById('tier-container');
    const addBtn = document.getElementById('add-tier-btn');

    addBtn.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.classList.add('tier-row', 'grid', 'grid-cols-12', 'gap-2', 'p-4', 'bg-slate-50', 'border', 'border-slate-100', 'rounded-2xl', 'items-center', 'mt-3');
        newRow.innerHTML = `
            <div class="col-span-12 lg:col-span-3">
                <input type="text" name="tiers[${tierIndex}][name]" placeholder="Ex: Presale 1" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="col-span-6 lg:col-span-2">
                <input type="number" name="tiers[${tierIndex}][price]" placeholder="0" min="0" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="col-span-6 lg:col-span-2">
                <input type="number" name="tiers[${tierIndex}][quota]" placeholder="100" min="1" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="col-span-6 lg:col-span-2">
                <input type="datetime-local" name="tiers[${tierIndex}][start_date]" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="col-span-6 lg:col-span-2">
                <input type="datetime-local" name="tiers[${tierIndex}][end_date]" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            <div class="col-span-12 lg:col-span-1 text-center">
                <button type="button" class="remove-tier-btn text-xs font-bold text-red-500 hover:text-red-700 transition">Hapus</button>
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