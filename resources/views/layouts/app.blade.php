<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Agen Ekspedisi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-RXf+QSDCUQs6QW1h0I1QnAty8gq6JWqo2KDh8Xc6pPvFM0E10Zo2Q01uJToz5r9lEX67rUf6x8IOk6fIRiVZlw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @stack('styles')
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-active: #3b82f6;
            --sidebar-hover: #334155;
            --header-bg: #ffffff;
            --primary-color: #2563eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            overflow-x: hidden;
        }

        /* --- SIDEBAR STYLES --- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-header i { font-size: 28px; color: var(--sidebar-active); }
        .sidebar-header h4 { margin: 0; font-size: 18px; font-weight: 700; }

        .sidebar-menu { list-style: none; padding: 20px 0; margin: 0; }
        .sidebar-menu li { margin: 4px 10px; }

        .sidebar-menu a, .sidebar-menu .dropdown-toggle {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }
        .sidebar-menu a:hover, .sidebar-menu .dropdown-toggle:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        /* Active State: Termasuk saat dropdown induknya sedang terbuka (.show) */
        .sidebar-menu a.active,
        .sidebar-menu .dropdown-toggle.active,
        .sidebar-menu .dropdown.show > .dropdown-toggle {
            background: var(--sidebar-active);
            color: white;
        }
        .sidebar-menu a i, .sidebar-menu .dropdown-toggle > i:first-child {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* --- DROPDOWN FIXES (Mencegah "Rembes" & Konflik Popper) --- */
        .sidebar-menu .dropdown-toggle::after { display: none; } /* Sembunyikan panah default Bootstrap */

        /* PENTING: paksa dropdown-menu jadi static, jangan biarkan Popper.js
           menghitung posisinya sebagai absolute — itu yang bikin menu
           ke-clip / gak muncul di dalam sidebar yang overflow-y: auto */
        .sidebar-menu .dropdown-menu {
            position: static !important;
            inset: auto !important;
            transform: none !important;
            float: none;
            width: 100%;
            background: transparent; /* Menyatu dengan sidebar */
            border: none;
            padding: 5px 0;
            margin-top: 5px;
            box-shadow: none;
        }
        .sidebar-menu .dropdown-item {
            color: #94a3b8;
            padding: 10px 15px 10px 52px !important; /* Indentasi sub-menu */
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            background: transparent;
        }
        .sidebar-menu .dropdown-item i {
            font-size: 16px;
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .sidebar-menu .dropdown-item:hover,
        .sidebar-menu .dropdown-item.active {
            background: rgba(255, 255, 255, 0.1); /* Highlight halus */
            color: white;
        }
        /* Animasi putar panah saat dropdown terbuka */
        .sidebar-menu .dropdown.show .dropdown-toggle .bi-chevron-down {
            transform: rotate(180deg);
        }
        .sidebar-menu .dropdown-toggle .bi-chevron-down {
            transition: transform 0.3s ease;
        }

        .sidebar-menu .logout {
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }

        /* --- MAIN CONTENT & HEADER --- */
        .main-content { margin-left: 260px; transition: all 0.3s ease; min-height: 100vh; }
        .top-header {
            background: var(--header-bg);
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .btn-toggle-sidebar {
            display: none; background: none; border: none; font-size: 24px; cursor: pointer; color: #333;
        }
        .header-right { display: flex; align-items: center; gap: 20px; }
        .header-date { color: #64748b; font-size: 14px; }
        .user-dropdown {
            display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 8px 15px;
            border-radius: 8px; transition: background 0.3s;
        }
        .user-dropdown:hover { background: #f1f5f9; }
        .user-avatar {
            width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color);
            color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;
        }
        .content-area { padding: 30px; }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .btn-toggle-sidebar { display: block; }
            .content-area { padding: 20px; }
        }
        @media (max-width: 768px) {
            .top-header { padding: 15px; }
            .header-date { display: none; }
            .user-dropdown span { display: none; }
        }
        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5); z-index: 999;
        }
        .sidebar-overlay.show { display: block; }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-box-seam-fill"></i>
            <h4>POS EKSPEDISI</h4>
        </div>

        <ul class="sidebar-menu">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Transaksi -->
            <li>
                <a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Transaksi</span>
                </a>
            </li>

            <!-- Laporan -->
            <li>
                <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Laporan</span>
                </a>
            </li>

            @php
                $sidebarUser = auth()->user();
            @endphp



            <!-- Absensi -->
            <li>
                <a href="{{ route('absensi.index') }}" class="{{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Absensi</span>
                </a>
            </li>

            <!-- Reimbursement -->
            <li>
                <a href="{{ route('reimbursement.index') }}" class="{{ request()->routeIs('reimbursement.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Reimbursement</span>
                </a>
            </li>
            <!-- DATA MASTER DROPDOWN (Khusus Admin) -->
            @if($sidebarUser && $sidebarUser->role == 'Admin')
            @php
                // Agar dropdown parent "Data Master" juga active jika childnya active
                $isDataMasterActive = in_array(request()->segment(1), ['ekspedisi', 'divisi', 'users']);
            @endphp
            <li class="dropdown {{ $isDataMasterActive ? 'show' : '' }}">
                <a class="dropdown-toggle {{ $isDataMasterActive ? 'active' : '' }}"
                   href="#" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="{{ $isDataMasterActive ? 'true' : 'false' }}">
                    <i class="bi bi-database"></i>
                    <span>Data Master</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size: 12px;"></i>
                </a>
                <ul class="dropdown-menu{{ $isDataMasterActive ? ' show' : '' }}">
                    <li>
                        <a class="dropdown-item {{ request()->segment(1) == 'ekspedisi' ? 'active' : '' }}" href="{{ route('ekspedisi.index') }}">
                            <i class="bi bi-truck"></i> Ekspedisi
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->segment(1) == 'divisi' ? 'active' : '' }}" href="{{ route('divisi.index') }}">
                            <i class="bi bi-diagram-3"></i> Divisi
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ request()->segment(1) == 'users' ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="bi bi-people"></i> User
                        </a>
                    </li>
                </ul>
            </li>
            @endif


            <!-- Logout -->
            <li class="logout">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="btn-toggle-sidebar" id="toggleSidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="header-right">
                <span class="header-date">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                <div class="user-dropdown dropdown">
                    <div class="user-avatar">
                        {{ optional(auth()->user())->name ? substr(auth()->user()->name, 0, 1) : '?' }}
                    </div>
                    <span>{{ optional(auth()->user())->name ?? 'Guest' }}</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Toggle Sidebar Mobile
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
