@extends('layouts.admin')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 flex items-center gap-2.5">
                <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Pengawasan Kelayakan Penyelenggara
            </h1>
            <p class="text-slate-500 mt-1 text-sm">
                Tinjau verifikasi identitas, setujui hak akses pembuatan event, atau tangguhkan berkas tenant secara berkala.
            </p>
        </div>
    </div>

    <!-- Notifikasi Sukses -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-xl font-medium text-sm flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Mini Stats Overview (New Visual Element) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Penyelenggara</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $tenants->total() ?? $tenants->count() }}</h3>
            </div>
            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 border border-slate-100">📋</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Verified (Aktif)</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $tenants->where('status', 'verified')->count() }}</h3>
            </div>
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100">🛡️</div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Review</p>
                <h3 class="text-2xl font-bold text-amber-500 mt-1">{{ $tenants->where('status', 'pending')->count() }}</h3>
            </div>
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 border border-amber-100 animate-pulse">⏳</div>
        </div>
    </div>

    <!-- Tabel Monitoring Container -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold text-xs uppercase tracking-wider">
                        <th class="px-6 py-4">Nama Penyelenggara</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Tanggal Bergabung</th>
                        <th class="px-6 py-4">Status Kelayakan</th>
                        <th class="px-6 py-4 text-right">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium text-sm">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            
                            <!-- Nama Tenant -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900 text-sm sm:text-base">{{ $tenant->name }}</div>
                                <div class="text-xs font-mono text-slate-400 mt-0.5">ID: TNT-{{ $tenant->id }}</div>
                            </td>
                            
                            <!-- Kontak -->
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-normal">{{ $tenant->email ?? 'Tidak ada email' }}</div>
                                <div class="text-xs text-slate-400 font-normal mt-0.5">{{ $tenant->phone ?? '-' }}</div>
                            </td>
                            
                            <!-- Tanggal Daftar -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500">
                                <div>{{ $tenant->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $tenant->created_at->format('H:i') }} WIB</div>
                            </td>
                            
                            <!-- Status Kelayakan Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tenant->status == 'verified')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Verified
                                    </span>
                                @elseif($tenant->status == 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        Pending Review
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Aksi Tombol (Lebih Rapi & Sejajar Kanan) -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    
                                    <!-- Form Setujui -->
                                    @if($tenant->status !== 'verified')
                                        <form action="{{ route('admin.tenants.update_status', $tenant->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="verified">
                                            <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition active:scale-95 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                Setujui
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Form Tolak -->
                                    @if($tenant->status !== 'rejected')
                                        <form action="{{ route('admin.tenants.update_status', $tenant->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-slate-200 hover:border-rose-200 rounded-lg text-xs font-bold transition active:scale-95 flex items-center gap-1" onclick="return confirm('Apakah Anda yakin menolak kelayakan tenant ini?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Tolak
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <!-- Tombol Tinjau Ulang -->
                                    @if($tenant->status !== 'pending')
                                        <form action="{{ route('admin.tenants.update_status', $tenant->id) }}" method="POST" class="inline m-0">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H17"></path></svg>
                                                Reset
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                <div class="text-4xl mb-3">📂</div>
                                <p class="text-sm font-medium">Belum ada data penyelenggara (Tenant) yang terdaftar di sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Area -->
        @if($tenants->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
</main>
@endsection