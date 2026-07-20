<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS Agen Ekspedisi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-RXf+QSDCUQs6QW1h0I1QnAty8gq6JWqo2KDh8Xc6pPvFM0E10Zo2Q01uJToz5r9lEX67rUf6x8IOk6fIRiVZlw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @stack('styles')
    <style>
        /* ... Keep all the existing styles exactly as before ... */
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            overflow-x: hidden;
        }
        /* Sidebar Styles */
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
        .sidebar-header i {
            font-size: 28px;
            color: var(--sidebar-active);
        }
        .sidebar-header h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin: 5px 15px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            gap: 12px;
        }
        .sidebar-menu a:hover {
            background: var(--sidebar-hover);
            color: white;
        }
        .sidebar-menu a.active {
            background: var(--sidebar-active);
            color: white;
        }
        .sidebar-menu a i {
            font-size: 20px;
            width: 24px;
        }
        .sidebar-menu .logout {
            margin-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }
        /* Main Content */
        .main-content {
            margin-left: 260px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        /* Header */
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
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn-toggle-sidebar {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header-date {
            color: #64748b;
            font-size: 14px;
        }
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .user-dropdown:hover {
            background: #f1f5f9;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }
        .stat-value.success {
            color: var(--success-color);
        }
        .stat-value.warning {
            color: var(--warning-color);
        }
        .stat-value.primary {
            color: var(--primary-color);
        }
        /* Chart Cards */
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
        }
        /* Table Card */
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .table-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
        }
        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
        .custom-table {
            margin: 0;
        }
        .custom-table thead {
            background: #f8fafc;
        }
        .custom-table th {
            border: none;
            padding: 12px;
            font-weight: 600;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
        }
        .custom-table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: #f1f5f9;
        }
        .badge-ekspedisi {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }
        .btn-action {
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
        }
        /* Responsive */
        @media (max-width: 1200px) {
            .stat-value {
                font-size: 20px;
            }
        }
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .btn-toggle-sidebar {
                display: block;
            }
            .content-area {
                padding: 20px;
            }
        }
        @media (max-width: 768px) {
            .top-header {
                padding: 15px;
            }
            .header-date {
                display: none;
            }
            .stat-card {
                margin-bottom: 15px;
            }
            .chart-card {
                margin-bottom: 20px;
            }
            .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            .custom-table {
                font-size: 13px;
            }
            .custom-table th,
            .custom-table td {
                padding: 10px 8px;
            }
        }
        @media (max-width: 576px) {
            .content-area {
                padding: 15px;
            }
            .stat-value {
                font-size: 18px;
            }
            .user-dropdown span {
                display: none;
            }
        }
        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        .sidebar-overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="bi bi-box-seam"></i>
            <h4>POS EKSPEDISI</h4>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('dashboard.index') }}">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('transaksi.index') }}">
                    <i class="bi bi-receipt"></i>
                    <span>Transaksi</span>
                </a>
            </li>
            <!-- Menu Pendapatan dihapus -->
            <li>
                <a href="{{ route('laporan.index') }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Laporan</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ekspedisi.index') }}">
                    <i class="bi bi-truck"></i>
                    <span>Ekspedisi</span>
                </a>
            </li>
            <!-- Tambahan Menu Reimbursement -->
            <li>
                <a href="{{ route('reimbursement.index') }}">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Reimbursement</span>
                </a>
            </li>
            <!-- Tambahan Menu Absensi -->
            <li>
                <a href="{{ route('absensi.index') }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>Absensi</span>
                </a>
            </li>
            @php
                $user = auth()->user();
                // dd($user);
            @endphp

            {{-- ini level akses nya --}}
            @if($user && $user->role == 'Admin')
            <li>
                <a href="{{ route('users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>User</span>
                </a>
            </li>
            @endif
            <!-- Menu Pengaturan dihapus -->
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
        // Toggle Sidebar
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

        // Close sidebar on window resize if desktop
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
