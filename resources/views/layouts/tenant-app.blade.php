<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Organisasi - SaaS EventHub</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons (Untuk mempercantik menu navigasi) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
        }

        /* --- ARSITEKTUR SIDEBAR RESPONSIVE & MODERN --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: #ffffff;
            border-right: 1px solid rgba(0, 0, 0, 0.075);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Wrapper Konten Utama di sebelah kanan Sidebar */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Area Menu Sidebar agar bisa di-scroll secara independen jika menunya banyak */
        .sidebar-menu-wrapper {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1.25rem;
        }

        /* Desain Link Menu Aktif/Non-aktif */
        .list-group-item-action {
            color: #495057;
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid transparent !important;
        }
        .list-group-item-action:hover {
            background-color: rgba(13, 110, 253, 0.05);
            color: var(--primary-color);
        }
        .list-group-item-action.active {
            background-color: var(--primary-color) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
        }

        /* --- RESPONSIVE LAYOUT UNTUK GADGET / HP --- */
        @media (max-width: 991.98px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.075);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .sidebar-logout-footer {
                position: relative !important;
                border-top: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR NAVIGASI KIRI -->
    <aside class="sidebar">
        <!-- Brand/Header Sidebar -->
        <div class="p-4 border-bottom d-flex align-items-center justify-content-center bg-white">
            <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                <i class="fas fa-theater-masks"></i> Tenant Hub
            </h5>
        </div>
        
        <!-- List Menu -->
        <div class="sidebar-menu-wrapper">
            <div class="list-group list-group-flush">
                <a href="{{ route('tenant.dashboard') }}" 
                   class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 py-2.5 my-1 {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie fs-5 w-25 text-center"></i> 
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('tenant.events.index') }}" 
                   class="list-group-item list-group-item-action d-flex align-items-center gap-3 rounded-3 py-2.5 my-1 {{ request()->routeIs('tenant.events.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt fs-5 w-25 text-center"></i> 
                    <span>Kelola Event</span>
                </a>
            </div>
        </div>
        
        <!-- Bagian Footer / Tombol Keluar (Aman & Tidak Ketabrak) -->
        <div class="p-3 border-top bg-white sidebar-logout-footer">
            <form action="{{ route('tenant.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 py-2 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-semibold">
                    <i class="fas fa-sign-out-alt"></i> Keluar Panel
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA KANAN -->
    <div class="main-wrapper bg-light">
        
        <!-- Topbar Modern -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white px-4 py-3 mb-4 border-bottom shadow-sm">
            <div class="container-fluid px-0">
                <div class="navbar-text text-dark fw-medium d-flex align-items-center gap-2">
                    <span class="text-muted">Organisasi:</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-2 rounded-pill fw-bold">
                        <i class="fas fa-building me-1"></i> {{ Auth::guard('tenant')->user()->name }}
                    </span>
                </div>
                <div class="ms-auto">
                    <a href="/" class="btn btn-sm btn-outline-secondary px-3 py-2 rounded-3 fw-medium" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i> Lihat Web Publik
                    </a>
                </div>
            </div>
        </nav>

        <!-- Area Konten Utama & Notifikasi -->
        <main class="flex-grow-1 px-3 px-md-4">
            
            <!-- Alert System Global -->
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show rounded-3 p-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-check-circle fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show rounded-3 p-3 mb-4" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-exclamation-circle fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Slot Halaman Dashboard Anak -->
            @yield('content')
            
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>