<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmikomEventHub - Temukan Event Seru!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

    <!-- Navigation -->
    <nav
        class="glass sticky top-8 z-40 mx-4 mt-4 px-6 py-4 rounded-2xl border border-white/20 shadow-lg flex justify-between items-center">
        
        <!-- Logo -->
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">
                AH
            </div>
            <span class="text-xl font-bold tracking-tight">AmikomEventHub</span>
        </div>
        
        <!-- Menu Tengah (Desktop) -->
        <div class="hidden md:flex gap-8 font-medium">
            <a href="#" class="text-indigo-600">Jelajahi</a>
            <a href="#" class="hover:text-indigo-600 transition">Kategori</a>
            <a href="#" class="hover:text-indigo-600 transition">Tentang Kami</a>
        </div>
        
        <!-- Menu Autentikasi Kanan (Tailwind Version) -->
        <div class="flex items-center gap-4">
            @auth
                <!-- TAMPILAN JIKA USER SUDAH LOGIN (Hover Dropdown) -->
                <div class="relative group">
                    <button class="flex items-center gap-2 font-semibold text-slate-700 hover:text-indigo-600 transition focus:outline-none py-2">
                        <!-- Menampilkan foto profil asli dari Google User atau Inisial jika kosong -->
                        <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" 
                             alt="Avatar" class="rounded-full w-8 h-8 object-cover border border-slate-200">
                        <span class="text-sm hidden sm:inline">{{ auth()->user()->name }}</span>
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Content (Muncul otomatis saat di-hover) -->
                    <div class="absolute right-0 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 font-medium">
                            Riwayat Tiket
                        </a>
                        <hr class="border-slate-100 my-1">
                        
                        <!-- Form khusus untuk memicu rute POST logout secara aman -->
                        <form action="{{ route('logout') }}" method="POST" class="block w-full">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 font-bold transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Keluar / Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- TAMPILAN JIKA USER BELUM LOGIN -->
                <a href="{{ route('auth.google') }}" 
                   class="px-5 py-2.5 bg-white text-indigo-600 border border-indigo-200 rounded-xl font-bold shadow-sm hover:bg-indigo-50 hover:border-indigo-300 transition text-sm flex items-center gap-2">
                    <img src="https://developers.google.com/static/identity/images/g-logo.png" alt="Google" class="w-4 h-4">
                    Login via Google
                </a>
            @endauth
        </div>
    </nav>

    <!-- Konten Halaman -->
    @yield('content')

</body>

</html>