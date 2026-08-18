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
        /* Smooth scroll untuk list pengumuman */
.dropdown-menu ul {
    scroll-behavior: smooth;
}

/* Hover effect untuk item pengumuman */
.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
    transition: all 0.2s ease;
}
   /* Hide scrollbar for Chrome, Safari and Opera */
    .dropdown-menu .list-unstyled::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .dropdown-menu .list-unstyled {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
/* Animasi badge */
/* .badge {
    animation: pulse 2s infinite;
} */
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
}
    </style>
    @stack('css')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('img/logo/maurekap-icon-hd-transparent.png') }}" alt="Logo" style="height: 44px; width: auto; margin-bottom: 7px;">
            <h2 style="font-size: 1.2rem; font-weight: bold; letter-spacing: 2px;">MAU REKAP</h2>
        </div>



        <ul class="sidebar-menu">
             @php
                $sidebarUser = auth()->user();
            @endphp
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Transaksi -->
            @if($sidebarUser && $sidebarUser->role !== 'Kurir')
            <li>
                <a href="{{ route('transaksi.index') }}" class="{{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Transaksi</span>
                </a>
            </li>
            @endif


            <!-- Laporan -->
            <li>
                <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Laporan</span>
                </a>
            </li>



            <!-- Pengumuman: Muncul jika Admin, Leader, Finance -->
            @if($sidebarUser && in_array($sidebarUser->role, ['Admin', 'Leader', 'Finance']))
            <li>
                <a href="{{ route('pengumuman.index') }}" class="{{ request()->routeIs('pengumumam.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i>
                    <span>Pengumuman</span>
                </a>
            </li>
            @endif

            <!-- Tambahkan Menu Pekerjaan Kurir -->
            @if($sidebarUser && in_array($sidebarUser->role, ['Kurir', 'Admin']))
            <li>
                <a href="{{ route('pekerjaan-kurir.index') }}" class="{{ request()->routeIs('pekerjaan-kurir.*') ? 'active' : '' }}">
                    <i class="bi bi-box"></i>
                    <span>Pekerjaan Kurir</span>
                </a>
            </li>
            @endif


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
    <div class="header-right d-flex align-items-center gap-3">

        {{-- Tanggal --}}
        <span class="header-date d-none d-md-block">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>

        {{-- 🔔 ICON PENGUMUMAN / NOTIFIKASI DINAMIS --}}
        <div class="dropdown">
            <button class="btn btn-light position-relative rounded-circle p-2"
                    type="button"
                    id="dropdownAnnouncement"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="Pengumuman">
                <i class="bi bi-bell-fill fs-5 text-secondary"></i>

                {{-- Badge notifikasi dinamis --}}
                @if(isset($UnreadCount) && $UnreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px; min-width: 18px; height: 18px; padding: 3px;">
                        {{ $UnreadCount }}
                    </span>
                @endif
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 320px; max-width: 95vw;">
                <li class="px-3 py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-megaphone me-2 text-primary"></i>Pengumuman</h6>
                        <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-link text-decoration-none p-0">Lihat Semua</a>
                    </div>
                </li>
                <li>
                    <ul class="list-unstyled mb-0" style="max-height: 300px; overflow-y: auto;">

                        @forelse($RecentAnnouncements ?? [] as $Announcement)
                            <li>
                                <a href="{{ route('pengumuman.show', $Announcement->id) }}" class="dropdown-item d-flex gap-3 py-3 border-bottom">
                                    <div class="flex-shrink-0">
                                        @php
                                            // Logika Warna & Ikon berdasarkan Kategori (Pascal Case)
                                            $IconClass = 'bi-info-circle-fill text-primary';
                                            $BgClass = 'bg-primary bg-opacity-10';

                                            if ($Announcement->Kategori === 'Darurat') {
                                                $IconClass = 'bi-exclamation-triangle-fill text-danger';
                                                $BgClass = 'bg-danger bg-opacity-10';
                                            } elseif ($Announcement->Kategori === 'Penting') {
                                                $IconClass = 'bi-exclamation-circle-fill text-warning';
                                                $BgClass = 'bg-warning bg-opacity-10';
                                            }
                                        @endphp

                                        <div class="{{ $BgClass }} rounded-circle p-2">
                                            <i class="bi {{ $IconClass }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <small class="fw-semibold text-truncate" style="max-width: 160px;" title="{{ $Announcement->Judul }}">
                                                {{ $Announcement->Judul }}
                                            </small>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($Announcement->CreatedAt)->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p class="mb-0 small text-muted mt-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {!! \Illuminate\Support\Str::limit(strip_tags($Announcement->Isi), 50) !!}

                                        </p>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li>
                                <div class="dropdown-item text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    <small>Tidak ada pengumuman baru.</small>
                                </div>
                            </li>
                        @endforelse

                    </ul>
                </li>
                <li class="px-3 py-2 border-top bg-light text-center">
                    <button type="button" class="btn btn-sm btn-danger w-100" data-bs-dismiss="dropdown">
                        <i class="bi bi-x me-1"></i> Tutup
                    </button>
                </li>

            </ul>
        </div>
        {{-- END ICON PENGUMUMAN --}}

        {{-- User Dropdown (Tetap sama) --}}
        <div class="user-dropdown dropdown">
            <div class="user-avatar">
                {{ optional(auth()->user())->name ? substr(auth()->user()->name, 0, 1) : '?' }}
            </div>
            <div class="d-flex flex-column">
                <span>{{ optional(auth()->user())->name ?? 'Guest' }}</span>
                <small class="text-muted" style="font-size: 12px;">
                    @if(auth()->check())
                        {{ auth()->user()->role }}
                    @else
                        -
                    @endif
                </small>
            </div>
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
