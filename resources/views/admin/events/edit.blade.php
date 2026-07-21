@extends('layouts.admin', ['title' => 'Edit Event'])

@section('content')
<header class="mb-10">
    <h1 class="text-3xl font-black text-slate-800">Edit Event</h1>
    <p class="text-slate-500 font-medium">Perbarui informasi event <span class="text-indigo-600">"{{ $event->title }}"</span></p>
</header>

<div class="bg-white rounded-[2.5rem] border border-slate-100 p-10 shadow-sm max-w-4xl">
    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-2 gap-6">
            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Judul Event</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}"
                    class="w-full px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none transition" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                <select name="category_id" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $event->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal & Waktu Execution</label>
                <input type="datetime-local" name="date" value="{{ old('date', date('Y-m-d\TH:i', strtotime($event->date))) }}"
                    class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none" required>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                <textarea name="description" rows="4" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none" required>{{ old('description', $event->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Harga Utama/Fallback (Rp)</label>
                <input type="number" name="price" value="{{ old('price', (int)$event->price) }}" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Total Stok Fallback</label>
                <input type="number" name="stock" value="{{ old('stock', $event->stock) }}" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none" required>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full px-5 py-3 rounded-xl border border-slate-200 outline-none" required>
            </div>

            <div class="col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Poster Event</label>
                <div class="flex items-start gap-6 p-4 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                    <div class="shrink-0 text-center">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Poster Saat Ini</p>
                        <img src="{{ asset('storage/'.$event->poster_path) }}" class="w-24 h-32 rounded-xl object-cover shadow-md border-2 border-white">
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-slate-500 mb-3 leading-relaxed">Pilih file baru jika ingin mengganti poster. Kosongkan jika tidak ingin mengubah gambar.</p>
                        <input type="file" name="poster_path" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>
            </div>

            <!-- SECTION DYNAMIC PRICING TIER -->
            <div class="col-span-2 border-t border-slate-100 pt-6 mt-4">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">🏷️ Skema Dynamic Pricing (Tier Tiket)</h3>
                        <p class="text-xs text-slate-400">Kelola tahapan harga tiket (Early Bird, Presale, Regular) beserta jadwal penjualannya.</p>
                    </div>
                    <button type="button" id="add-tier-btn" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold hover:bg-indigo-100 transition">
                        + Tambah Tier Tiket
                    </button>
                </div>

                <div id="tier-container" class="space-y-3">
                    @forelse($event->ticketTiers as $index => $tier)
                        <div class="tier-row grid grid-cols-12 gap-2 p-4 bg-slate-50 border border-slate-100 rounded-2xl items-center">
                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Tier</label>
                                <input type="text" name="tiers[{{ $index }}][name]" value="{{ old("tiers.$index.name", $tier->name) }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Harga (Rp)</label>
                                <input type="number" name="tiers[{{ $index }}][price]" value="{{ old("tiers.$index.price", (int)$tier->price) }}" min="0" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kuota</label>
                                <input type="number" name="tiers[{{ $index }}][quota]" value="{{ old("tiers.$index.quota", $tier->quota) }}" min="1" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Mulai Jual</label>
                                <input type="datetime-local" name="tiers[{{ $index }}][start_date]" value="{{ old("tiers.$index.start_date", \Carbon\Carbon::parse($tier->start_date)->format('Y-m-d\TH:i')) }}" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Selesai Jual</label>
                                <input type="datetime-local" name="tiers[{{ $index }}][end_date]" value="{{ old("tiers.$index.end_date", \Carbon\Carbon::parse($tier->end_date)->format('Y-m-d\TH:i')) }}" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-12 sm:col-span-1 text-center pt-2">
                                @if($index > 0)
                                    <button type="button" class="remove-tier-btn text-xs font-bold text-red-500 hover:underline">Hapus</button>
                                @else
                                    <span class="text-slate-300 text-xs hidden sm:inline">-</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <!-- Default Row jika sebelumnya event belum memiliki tier -->
                        <div class="tier-row grid grid-cols-12 gap-2 p-4 bg-slate-50 border border-slate-100 rounded-2xl items-center">
                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Nama Tier</label>
                                <input type="text" name="tiers[0][name]" value="Regular" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Harga (Rp)</label>
                                <input type="number" name="tiers[0][price]" value="{{ (int)$event->price }}" min="0" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Kuota</label>
                                <input type="number" name="tiers[0][quota]" value="{{ $event->stock }}" min="1" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Mulai Jual</label>
                                <input type="datetime-local" name="tiers[0][start_date]" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Selesai Jual</label>
                                <input type="datetime-local" name="tiers[0][end_date]" value="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i') }}" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200" required>
                            </div>
                            <div class="col-span-12 sm:col-span-1 text-center pt-2">
                                <span class="text-slate-300 text-xs hidden sm:inline">-</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-10 pt-6 border-t border-slate-100">
            <a href="{{ route('admin.events.index') }}" class="px-6 py-3 font-bold text-slate-400 hover:text-slate-600 transition">Batal</a>
            <button type="submit" class="px-10 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transform active:scale-95 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let tierIndex = {{ max(1, $event->ticketTiers->count()) }};
    const container = document.getElementById('tier-container');
    const addBtn = document.getElementById('add-tier-btn');

    addBtn.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.classList.add('tier-row', 'grid', 'grid-cols-12', 'gap-2', 'p-4', 'bg-slate-50', 'border', 'border-slate-100', 'rounded-2xl', 'items-center', 'mt-2');
        newRow.innerHTML = `
            <div class="col-span-12 sm:col-span-3">
                <input type="text" name="tiers[${tierIndex}][name]" placeholder="Ex: Presale 1" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
            </div>
            <div class="col-span-6 sm:col-span-2">
                <input type="number" name="tiers[${tierIndex}][price]" placeholder="0" min="0" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
            </div>
            <div class="col-span-6 sm:col-span-2">
                <input type="number" name="tiers[${tierIndex}][quota]" placeholder="100" min="1" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200" required>
            </div>
            <div class="col-span-6 sm:col-span-2">
                <input type="datetime-local" name="tiers[${tierIndex}][start_date]" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200" required>
            </div>
            <div class="col-span-6 sm:col-span-2">
                <input type="datetime-local" name="tiers[${tierIndex}][end_date]" class="w-full px-2 py-2 text-xs rounded-xl border border-slate-200" required>
            </div>
            <div class="col-span-12 sm:col-span-1 text-center">
                <button type="button" class="remove-tier-btn text-xs font-bold text-red-500 hover:underline">Hapus</button>
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