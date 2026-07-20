<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 flex min-h-screen">

<aside class="w-64 bg-indigo-900 text-indigo-100 flex flex-col p-6 space-y-8 sticky top-0 h-screen">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-900 font-bold text-xl">AH</div>
        <span class="text-xl font-bold text-white tracking-tight">AmikomEventHub</span>
    </div>

    <nav class="flex-1 space-y-2">
        <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-4 px-2">Main Menu</p>
        
        {{-- Menu Dashboard --}}
        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800' }} rounded-xl font-bold transition">
            Dashboard
        </a>
        
        {{-- Menu Kelola Event --}}
        <a href="{{ route('admin.events.index') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.events.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800' }} rounded-xl font-bold transition">
            Kelola Event
        </a>

        {{-- Menu Kelola Kategori --}}
        <a href="{{ route('admin.categories.index') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800' }} rounded-xl font-bold transition">
            Kelola Kategori
        </a>

        {{-- Menu Kelola Partner --}}
        <a href="{{ route('admin.partners.index') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.partners.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800' }} rounded-xl font-bold transition">
            Kelola Partner
        </a>

        {{-- Menu Kelola Transaksi --}}
        <a href="{{ route('admin.transactions.index') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.transactions.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800' }} rounded-xl font-bold transition">
            Kelola Transaksi
        </a>

        {{-- Menu Kelayakan Tenant (Baru Ditambahkan & Disesuaikan Stylenya) --}}
        <a href="{{ route('admin.tenants.index') }}" 
           class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.tenants.*') ? 'bg-indigo-800 text-white' : 'hover:bg-indigo-800' }} rounded-xl font-bold transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span>Kelayakan Tenant</span>
        </a>
    </nav>

    <div class="pt-6 border-t border-indigo-800">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 text-indigo-300 hover:text-white transition font-medium text-left">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<main class="flex-1 p-10 overflow-y-auto">
    @yield('content')
</main>

</body>
</html>