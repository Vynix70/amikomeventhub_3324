@extends('layouts.admin', ['title' => 'Kelola Event'])

@section('content')
<header class="flex justify-between items-center mb-10">
    <div>
        <h1 class="text-3xl font-black">Kelola Event</h1>
        <p class="text-slate-500 font-medium">Buat dan atur acara seru Anda di sini.</p>
    </div>
    <a href="{{ route('admin.events.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg hover:bg-indigo-700 transition">
        + Tambah Event Baru
    </a>
</header>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">No</th>
                    <th class="px-8 py-4">Poster</th>
                    <th class="px-8 py-4">Event</th>
                    <th class="px-8 py-4">Penyelenggara</th>
                    <th class="px-8 py-4">Harga / Stok</th>
                    <th class="px-8 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y border-t">
                @forelse($events as $index => $event)
                <tr class="hover:bg-slate-50/50 transition">
                    <td class="px-8 py-6 font-bold text-slate-400">{{ $index + 1 }}</td>
                    
                    {{-- KOLOM POSTER: Logika Multi-Sistem (Storage Baru, Asset Lama, & Fallback) --}}
                    <td class="px-8 py-6">
                        @if($event->poster_path && str_contains($event->poster_path, 'posters/'))
                            <!-- Format Storage Baru (storage/posters/...) -->
                            <img src="{{ asset('storage/' . $event->poster_path) }}" class="w-16 h-20 rounded-xl object-cover shadow-sm" alt="Poster {{ $event->title }}">
                        @elseif($event->poster_path && file_exists(public_path('assets/images/' . $event->poster_path)))
                            <!-- Format Asset Public Lama (public/assets/images/...) -->
                            <img src="{{ asset('assets/images/' . $event->poster_path) }}" class="w-16 h-20 rounded-xl object-cover shadow-sm" alt="Poster {{ $event->title }}">
                        @elseif($event->poster_path)
                            <!-- Fallback Direct Storage Path -->
                            <img src="{{ asset('storage/posters/' . $event->poster_path) }}" class="w-16 h-20 rounded-xl object-cover shadow-sm" alt="Poster {{ $event->title }}">
                        @else
                            <!-- Placeholder Jika Tanpa Poster -->
                            <div class="w-16 h-20 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 border">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                    </td>

                    {{-- JUDUL EVENT & KATEGORI --}}
                    <td class="px-8 py-6">
                        <p class="font-black text-slate-800">{{ $event->title }}</p>
                        <p class="text-xs text-slate-400">
                            {{ $event->category->name ?? 'Tanpa Kategori' }} • {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                        </p>
                    </td>

                    {{-- MENAMPILKAN PENYELENGGARA / TENANT --}}
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-2">
                            <span class="inline-block p-1.5 bg-slate-100 rounded-lg text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-bold text-slate-700 text-sm">
                                    {{ $event->user->name ?? $event->tenant->name ?? 'Admin' }}
                                </p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ (isset($event->user) || isset($event->tenant)) ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                    {{ (isset($event->user) || isset($event->tenant)) ? 'Tenant' : 'Internal' }}
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- HARGA / STOK --}}
                    <td class="px-8 py-6">
                        <p class="font-bold text-indigo-600">Rp {{ number_format($event->price, 0, ',', '.') }}</p>
                        
                        {{-- Ambil sisa kuota dinamis dari TicketTiers, fallback ke $event->stock jika tidak ada tier --}}
                        @if($event->ticketTiers && $event->ticketTiers->count() > 0)
                            <p class="text-xs text-slate-400">Stok: {{ $event->ticketTiers->sum('quota') }}</p>
                        @else
                            <p class="text-xs text-slate-400">Stok: {{ $event->stock }}</p>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-2">
                            <!-- Tombol Edit -->
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition" title="Edit Event">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </a>
                            
                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?')">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition" title="Hapus Event">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-slate-400 font-medium">
                        Belum ada event yang tersedia.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection