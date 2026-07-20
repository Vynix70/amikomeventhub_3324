<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Organisasi - SaaS EventHub</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR NAVIGASI KIRI -->
            <div class="col-md-3 col-lg-2 px-0 bg-white sidebar">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-primary text-center">🎪 Tenant Hub</h5>
                </div>
                <div class="list-group list-group-flush p-3">
                    <a href="{{ route('tenant.dashboard') }}" class="list-group-item list-group-item-action border-0 rounded py-2.5 my-1 {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('tenant.events.index') }}" class="list-group-item list-group-item-action border-0 rounded py-2.5 my-1 {{ request()->routeIs('tenant.events.*') ? 'active' : '' }}">
                        📅 Kelola Event
                    </a>
                </div>
                <div class="p-3 position-absolute bottom-0 start-0 w-100 border-top bg-white">
                    <form action="{{ route('tenant.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 py-2">
                            👋 Keluar / Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- KONTEN UTAMA KANAN -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 bg-light">
                <!-- Topbar sederhana -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white mx-n4 px-4 py-3 mb-4 border-bottom shadow-sm">
                    <div class="container-fluid px-0">
                        <span class="navbar-text text-dark fw-semibold">
                            Organisasi: <span class="badge bg-info text-dark ms-1">{{ Auth::guard('tenant')->user()->name }}</span>
                        </span>
                        <div class="ms-auto">
                            <a href="/" class="btn btn-sm btn-outline-secondary" target="_blank">👁️ Lihat Web Publik</a>
                        </div>
                    </div>
                </nav>

                <!-- Pesan Notifikasi Sukses/Error Global -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Tempat Konten Dashboard / Form dimasukkan -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Supaya tombol dismiss alert dan dropdown bisa diklik) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>