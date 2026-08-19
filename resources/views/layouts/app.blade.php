<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark" />
    <meta name="supported-color-schemes" content="light dark" />
    <title>@yield('title', 'POS Agen Ekspedisi')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous" media="print" onload="this.media='all'">

    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">

    <!-- AdminLTE 4 -->
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon_io/favicon.ico') }}">
    @stack('styles')
    @stack('css')


</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
<div class="app-wrapper">

    {{-- ==================== HEADER / NAVBAR ==================== --}}
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <!-- Kiri: toggle + judul halaman -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <span class="nav-link page-title-nav">@yield('page-title', 'Dashboard')</span>
                </li>
            </ul>

            <!-- Kanan -->
            <ul class="navbar-nav ms-auto align-items-center gap-1">

                {{-- Tanggal --}}
                <li class="nav-item d-none d-lg-block">
                    <span class="nav-link text-secondary small">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </span>
                </li>

                {{-- 🔔 Notifikasi Pengumuman --}}
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" data-bs-toggle="dropdown" href="#" aria-label="Pengumuman">
                        <i class="bi bi-bell-fill"></i>
                        @if(isset($UnreadCount) && $UnreadCount > 0)
                            <span class="navbar-badge badge text-bg-danger pulse-badge">
                                {{ $UnreadCount }}
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0">
                        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-megaphone text-primary me-1"></i> Pengumuman
                            </h6>
                            <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-link text-decoration-none">Lihat Semua</a>
                        </div>

                        <div class="announcement-scroll">
                            @forelse($RecentAnnouncements ?? [] as $Announcement)
                                @php
                                    $iconClass = 'bi-info-circle-fill text-primary';
                                    $bgClass   = 'bg-primary bg-opacity-10';
                                    if ($Announcement->Kategori === 'Darurat') {
                                        $iconClass = 'bi-exclamation-triangle-fill text-danger';
                                        $bgClass   = 'bg-danger bg-opacity-10';
                                    } elseif ($Announcement->Kategori === 'Penting') {
                                        $iconClass = 'bi-exclamation-circle-fill text-warning';
                                        $bgClass   = 'bg-warning bg-opacity-10';
                                    }
                                @endphp

                                <a href="{{ route('pengumuman.show', $Announcement->id) }}"
                                   class="dropdown-item d-flex gap-3 py-3 border-bottom">
                                    <div class="flex-shrink-0">
                                        <div class="{{ $bgClass }} rounded-circle p-2">
                                            <i class="bi {{ $iconClass }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <small class="fw-semibold text-truncate" style="max-width: 180px;" title="{{ $Announcement->Judul }}">
                                                {{ $Announcement->Judul }}
                                            </small>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($Announcement->CreatedAt)->diffForHumans() }}
                                            </small>
                                        </div>
                                        <p class="mb-0 small text-muted mt-1"
                                           style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {!! \Illuminate\Support\Str::limit(strip_tags($Announcement->Isi), 50) !!}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                    <small>Tidak ada pengumuman baru.</small>
                                </div>
                            @endforelse
                        </div>

                        <div class="px-3 py-2 border-top text-center bg-body-tertiary">
                            <button type="button" class="btn btn-sm btn-outline-secondary w-100" data-bs-dismiss="dropdown">
                                <i class="bi bi-x me-1"></i> Tutup
                            </button>
                        </div>
                    </div>
                </li>

                {{-- Fullscreen Toggle --}}
                <li class="nav-item">
                    <a class="nav-link" href="#" data-lte-toggle="fullscreen" aria-label="Toggle fullscreen">
                        <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                        <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
                    </a>
                </li>



                {{-- 👤 User Menu Dropdown --}}
                @php $user = auth()->user(); @endphp
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <span class="user-avatar-nav">
                            {{ $user ? substr($user->name, 0, 1) : '?' }}
                        </span>
                        <span class="d-none d-md-inline">
                            {{ $user->name ?? 'Guest' }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0">
                        <li class="user-header text-bg-primary p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="user-avatar-nav" style="width: 48px; height: 48px; font-size: 18px;">
                                    {{ $user ? substr($user->name, 0, 1) : '?' }}
                                </div>
                                <div class="text-white">
                                    <strong class="d-block">{{ $user->name ?? 'Guest' }}</strong>
                                    <small class="opacity-75">{{ $user->role ?? '-' }}</small>
                                </div>
                            </div>
                        </li>
                        <li class="user-footer p-2 d-flex justify-content-end">
                            <a href="#" class="btn btn-sm btn-outline-danger"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-1"></i> Sign out
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    {{-- ==================== SIDEBAR ==================== --}}
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!-- Sidebar Brand -->
        <div class="sidebar-brand">
            <a href="{{ route('dashboard.index') }}" class="brand-link logo-switch">
                <!--
                    Catatan rekomendasi ukuran:
                    - Logo icon (kecil): idealnya 48x48 px atau 56x56 px (bisa PNG transparan).
                    - Logo full (besar): idealnya minimal 180x48 px, lebih baik 200x56 px (disarankan lebar ~180-220 px dan tinggi ~48-56 px untuk sidebar).
                -->
                <img
                    src="{{ asset('img/logo/maurekap-icon-hd-transparent.png') }}"
                    alt="AdminLTE Logo Small"
                    class="brand-image-xl logo-xs opacity-75 shadow"
                    style="width: 48px; height: 48px;"
                />

                <img
                    src="{{ asset('img/logo/main-logo.png') }}"
                    alt="AdminLTE Logo Large"
                    class="brand-image-xs logo-xl opacity-100"
                    style="width: 200px; height: 56px; object-fit: contain;"
                />
            </a>
        </div>



        <!-- Sidebar Wrapper -->
        <div class="sidebar-wrapper">
            <nav class="mt-2" aria-label="Main navigation">
                <ul class="nav sidebar-menu flex-column"
                    data-lte-toggle="treeview"
                    role="menu"
                    data-accordion="false"
                    id="navigation">

                    @php $sidebarUser = auth()->user(); @endphp

                    {{-- Dashboard --}}
                    <li class="nav-item">
                        <a href="{{ route('dashboard.index') }}"
                           class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p><b>Dashboard</b></p>
                        </a>
                    </li>


                    {{-- Transaksi (selain Kurir) --}}
                    @if($sidebarUser && $sidebarUser->role !== 'Kurir')
                    <li class="nav-item">
                        <a href="{{ route('transaksi.index') }}"
                           class="nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-receipt"></i>
                            <p>Transaksi</p>
                        </a>
                    </li>
                    @endif

                    {{-- Laporan --}}
                    <li class="nav-item">
                        <a href="{{ route('laporan.index') }}"
                           class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                            <p>Laporan</p>
                        </a>
                    </li>

                    {{-- Pengumuman (Admin, Leader, Finance) --}}
                    @if($sidebarUser && in_array($sidebarUser->role, ['Admin', 'Leader', 'Finance']))
                    <li class="nav-item">
                        <a href="{{ route('pengumuman.index') }}"
                           class="nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-megaphone"></i>
                            <p>Pengumuman</p>
                        </a>
                    </li>
                    @endif

                    {{-- Pekerjaan Kurir --}}
                    @if($sidebarUser && in_array($sidebarUser->role, ['Kurir', 'Admin','Leader']))
                    <li class="nav-item">
                        <a href="{{ route('pekerjaan-kurir.index') }}"
                           class="nav-link {{ request()->routeIs('pekerjaan-kurir.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-box"></i>
                            <p>Pekerjaan Kurir</p>
                        </a>
                    </li>
                    @endif

                    {{-- Absensi --}}
                    <li class="nav-item">
                        <a href="{{ route('absensi.index') }}"
                           class="nav-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-calendar-check"></i>
                            <p>Absensi</p>
                        </a>
                    </li>

                    {{-- Reimbursement --}}
                    <li class="nav-item">
                        <a href="{{ route('reimbursement.index') }}"
                           class="nav-link {{ request()->routeIs('reimbursement.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-receipt-cutoff"></i>
                            <p>Reimbursement</p>
                        </a>
                    </li>

                    {{-- Data Master (Khusus Admin) --}}
                    @if($sidebarUser && $sidebarUser->role == 'Admin')
                    @php
                        $isDataMasterActive = in_array(request()->segment(1), ['ekspedisi', 'divisi', 'users']);
                    @endphp
                    <li class="nav-item {{ $isDataMasterActive ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isDataMasterActive ? 'active' : '' }}">
                            <i class="nav-icon bi bi-database"></i>
                            <p>
                                Data Master
                                <i class="nav-arrow bi bi-chevron-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('ekspedisi.index') }}"
                                   class="nav-link {{ request()->segment(1) == 'ekspedisi' ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Ekspedisi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('divisi.index') }}"
                                   class="nav-link {{ request()->segment(1) == 'divisi' ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>Divisi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}"
                                   class="nav-link {{ request()->segment(1) == 'users' ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-circle"></i>
                                    <p>User</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    {{-- Divider & Logout --}}
                    <li class="nav-header">AKUN</li>


                    @if ($sidebarUser && $sidebarUser->role == 'Admin')
                        <li class="nav-item">
                            <a href="{{ route('dashboard.manajemen-tenant') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-shield-lock"></i>
                                <p>Dashboard Superadmin</p>
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a href="#" class="nav-link"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="nav-icon bi bi-box-arrow-right"></i>
                            <p>Logout</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    {{-- ==================== MAIN CONTENT ==================== --}}
    <main class="app-main">
        <!-- App Content -->
        <div class="app-content">
            <div class="container-fluid mt-3">
                @yield('content')
            </div>
        </div>
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">POS Agen Ekspedisi &mdash; MAU REKAP</div>
        <strong>
            Copyright &copy; {{ date('Y') }}&nbsp;
            <a href="#" class="text-decoration-none">MAU REKAP</a>.
        </strong>
        All rights reserved.
    </footer>
</div>

{{-- Logout Form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

{{-- ==================== SCRIPTS ==================== --}}
<!-- OverlayScrollbars -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js" crossorigin="anonymous"></script>
<!-- Popper -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<!-- AdminLTE -->
<script src="{{ asset('assets/js/adminlte.js') }}"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    // OverlayScrollbars init untuk sidebar (non-mobile)
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
        const isMobile = window.innerWidth <= 992;

        if (sidebarWrapper && window.OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined && !isMobile) {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                scrollbars: {
                    theme: 'os-theme-light',
                    autoHide: 'leave',
                    clickScroll: true,
                },
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>
