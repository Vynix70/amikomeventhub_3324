@extends('layouts.admin')
@section('content')

    <main class="flex-1 p-10 overflow-y-auto">
        {{-- Notifikasi Sukses/Gagal --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-100 text-emerald-800 rounded-2xl font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-rose-100 text-rose-800 rounded-2xl font-medium">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-100 text-rose-800 rounded-2xl font-medium">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header Section --}}
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black">Kelola Kategori</h1>
                <p class="text-slate-500 font-medium">Klasifikasikan event Anda agar mudah ditemukan.</p>
            </div>
            <button onclick="openModal('add')"
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
                    <form action="{{ route('admin.categories.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori... (Tekan enter)"
                            class="w-full pl-12 pr-5 py-3 rounded-xl border-slate-200 border bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition">
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-8 py-4 w-20">No</th>
                            <th class="px-8 py-4">Nama Kategori</th>
                            <th class="px-8 py-4">Dibuat Pada</th>
                            <th class="px-8 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y border-t">
                        @forelse($categories as $index => $category)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-6 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-8 py-6">
                                    <span class="font-black text-slate-800 text-lg">{{ $category->name }}</span>
                                </td>
                                <td class="px-8 py-6 text-slate-500 font-medium">
                                    {{ $category->created_at->format('d M Y') }}
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex justify-end gap-2">
                                        {{-- Tombol Edit --}}
                                        <button onclick="openModal('edit', {{ $category->id }}, '{{ $category->name }}')" title="Edit" class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        
                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
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
                                <td colspan="4" class="px-8 py-6 text-center text-slate-400 font-medium">Belum ada data kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- MODAL FORM (untuk Tambah maupun Edit) --}}
    <div id="categoryModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[2rem] max-w-md w-full p-8 shadow-2xl transform transition-all">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modalTitle" class="text-2xl font-black text-slate-800">Tambah Kategori</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="modalForm" action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div id="methodField"></div> {{-- Tempat spoofing method PUT saat edit --}}

                <div class="mb-6">
                    <label for="categoryName" class="block text-sm font-bold text-slate-700 mb-2">Nama Kategori</label>
                    <input type="text" name="name" id="categoryName" required placeholder="Masukkan nama kategori..."
                        class="w-full px-5 py-3 rounded-xl border-slate-200 border bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition font-medium">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition shadow-lg shadow-emerald-100">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JavaScript Vanilla untuk kontrol Modal --}}
    <script>
        function openModal(mode, id = null, name = '') {
            const modal = document.getElementById('categoryModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalForm = document.getElementById('modalForm');
            const methodField = document.getElementById('methodField');
            const categoryName = document.getElementById('categoryName');

            if (mode === 'add') {
                modalTitle.innerText = 'Tambah Kategori';
                modalForm.action = "{{ route('admin.categories.store') }}";
                methodField.innerHTML = '';
                categoryName.value = '';
            } else if (mode === 'edit') {
                modalTitle.innerText = 'Edit Kategori';
                // Mengubah action form dinamis mengarah ke rute update /admin/categories/{id}
                modalForm.action = `/admin/categories/${id}`;
                methodField.innerHTML = `@method('PUT')`;
                categoryName.value = name;
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }
    </script>

@endsection