@extends('layouts.admin')
@section('content')

    <main class="flex-1 p-10 overflow-y-auto">
        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-100 text-emerald-800 rounded-2xl font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-100 text-rose-800 rounded-2xl font-medium">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header Section --}}
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black">Kelola Partner</h1>
                <p class="text-slate-500 font-medium">Kelola hubungan kerja sama institusi dan sponsorship.</p>
            </div>
            <button onclick="openPartnerModal('add')"
                class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Partner
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
                    <form action="{{ route('admin.partners.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama partner..."
                            class="w-full pl-12 pr-5 py-3 rounded-xl border-slate-200 border bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition">
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4 w-20">No</th>
                            <th class="px-8 py-4">Logo</th>
                            <th class="px-8 py-4">Nama Partner</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-t">
                        @forelse($partners as $index => $partner)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-6 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-8 py-6">
                                    @if($partner->logo_url)
                                        <img src="{{ asset('storage/' . $partner->logo_url) }}" alt="Logo {{ $partner->name }}" class="h-12 w-24 object-contain bg-slate-50 p-1 rounded-lg border">
                                    @else
                                        <span class="text-xs text-slate-400 italic">No Logo</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    <span class="font-black text-slate-800 text-lg">{{ $partner->name }}</span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2">
                                        {{-- Edit Button --}}
                                        <button onclick="openPartnerModal('edit', {{ $partner->id }}, '{{ $partner->name }}')" title="Edit" class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        
                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus partner ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus" class="p-2.5 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-6 text-center text-slate-400 font-medium">Belum ada data partner.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- MODAL PARTNER FORM (Mendukung File Upload multipart/form-data) --}}
    <div id="partnerModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[2rem] max-w-md w-full p-8 shadow-2xl transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalPartnerTitle" class="text-2xl font-black text-slate-800">Tambah Partner</h3>
                <button onclick="closePartnerModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="modalPartnerForm" action="{{ route('admin.partners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodPartnerField"></div>

                <div class="mb-4">
                    <label for="partnerName" class="block text-sm font-bold text-slate-700 mb-2">Nama Partner</label>
                    <input type="text" name="name" id="partnerName" required placeholder="Contoh: Universitas Amikom"
                        class="w-full px-5 py-3 rounded-xl border-slate-200 border bg-white focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium">
                </div>

                <div class="mb-6">
                    <label for="partnerLogo" class="block text-sm font-bold text-slate-700 mb-2">Logo Partner</label>
                    <input type="file" name="logo" id="partnerLogo"
                        class="w-full px-4 py-2.5 rounded-xl border-slate-200 border bg-slate-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                    <p id="modalLogoHelp" class="text-xs text-slate-400 mt-1">Format: JPG, PNG, SVG (Maks. 2MB).</p>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closePartnerModal()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition shadow-lg shadow-indigo-100">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPartnerModal(mode, id = null, name = '') {
            const modal = document.getElementById('partnerModal');
            const title = document.getElementById('modalPartnerTitle');
            const form = document.getElementById('modalPartnerForm');
            const methodField = document.getElementById('methodPartnerField');
            const partnerName = document.getElementById('partnerName');
            const partnerLogo = document.getElementById('partnerLogo');
            const logoHelp = document.getElementById('modalLogoHelp');

            if (mode === 'add') {
                title.innerText = 'Tambah Partner';
                form.action = "{{ route('admin.partners.store') }}";
                methodField.innerHTML = '';
                partnerName.value = '';
                partnerLogo.required = true; // Logo wajib diisi saat tambah baru
                logoHelp.innerText = "Format: JPG, PNG, SVG (Maks. 2MB).";
            } else if (mode === 'edit') {
                title.innerText = 'Edit Partner';
                form.action = `/admin/partners/${id}`;
                methodField.innerHTML = `@method('PUT')`;
                partnerName.value = name;
                partnerLogo.required = false; // Logo opsional saat edit (kosongkan jika tidak ingin ganti logo)
                logoHelp.innerText = "Kosongkan jika tidak ingin mengubah logo.";
            }

            modal.classList.remove('hidden');
        }

        function closeModalPartner() {
            document.getElementById('partnerModal').classList.add('hidden');
        }
    </script>
@endsection