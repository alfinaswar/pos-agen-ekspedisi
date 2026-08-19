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
                    <span class="nav-link page-title-nav">@yield('page-title', 'Pendaftaran')</span>
                </li>
            </ul>

            <!-- Kanan -->
            <ul class="navbar-nav ms-auto align-items-center gap-1">
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
            <a href="#" class="brand-link logo-switch">
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
                      <li class="nav-item">
                        <a href="{{ route('dashboard.manajemen-tenant') }}"
                           class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p><b>Dashboard</b></p>
                        </a>
                    </li>

                    {{-- Hanya menu Pendaftaran --}}
                    <li class="nav-item">
                        <a href="{{ route('pendaftaran-tenant.index') }}"
                           class="nav-link {{ request()->routeIs('pendaftaran.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-person-plus"></i>
                            <p>Pendaftaran</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tenant.index') }}"
                           class="nav-link {{ request()->routeIs('tenant.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-building"></i>
                            <p>Tenant</p>
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
