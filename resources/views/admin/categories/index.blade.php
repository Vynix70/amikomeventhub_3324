@extends('layouts.admin')
@section('content')

    <main class="flex-1 p-10 overflow-y-auto">
        {{-- Header Section --}}
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black">Kelola Kategori</h1>
                <p class="text-slate-500 font-medium">Klasifikasikan event Anda agar mudah ditemukan.</p>
            </div>
            <button
                class="px-6 py-3 bg-emerald-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-100 hover:bg-emerald-700 active:scale-95 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Kategori
            </button>
        </header>

        {{-- Table Card --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 bg-slate-50/50 border-b flex gap-4">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" placeholder="Cari kategori..."
                        class="w-full pl-12 pr-5 py-3 rounded-xl border-slate-200 border bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4 w-20">No</th>
                            <th class="px-8 py-4">Nama Kategori</th>
                            <th class="px-8 py-4">Status Visual</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-t">
                        {{-- Row 1: Seminar --}}
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-6 font-bold text-slate-400">1</td>
                            <td class="px-8 py-6">
                                <span class="font-black text-slate-800 text-lg">Seminar</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold uppercase">Edukasi</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-end gap-2">
                                    <button title="Edit" class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button title="Hapus" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 2: Konser --}}
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-6 font-bold text-slate-400">2</td>
                            <td class="px-8 py-6">
                                <span class="font-black text-slate-800 text-lg">Konser</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold uppercase">Hiburan</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Row 3: Workshop --}}
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-8 py-6 font-bold text-slate-400">3</td>
                            <td class="px-8 py-6">
                                <span class="font-black text-slate-800 text-lg">Workshop</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase">Praktik</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

@endsection