@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">

    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-file-analytics me-2 text-primary"></i>Laporan Pendapatan
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                </ol>
            </nav>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4 fw-semibold border border-primary-subtle rounded overflow-hidden" id="reportTabs" style="background: #f8f9fa;">
            <li class="nav-item flex-fill text-center">
                <a class="nav-link py-2 px-3 {{ $type === 'harian' ? 'active text-primary border-primary bg-white shadow-sm' : 'text-secondary' }}"
                   href="{{ route('laporan.index', ['type' => 'harian', 'tanggal' => date('Y-m-d')]) }}">
                    <i class="ti ti-calendar-stats me-1"></i> Harian
                </a>
            </li>
            <li class="nav-item flex-fill text-center">
                <a class="nav-link py-2 px-3 {{ $type === 'bulanan' ? 'active text-primary border-primary bg-white shadow-sm' : 'text-secondary' }}"
                   href="{{ route('laporan.index', ['type' => 'bulanan', 'tanggal' => date('Y-m')]) }}">
                    <i class="ti ti-calendar-month me-1"></i> Bulanan
                </a>
            </li>
            <li class="nav-item flex-fill text-center">
                <a class="nav-link py-2 px-3 {{ $type === 'per_user' ? 'active text-primary border-primary bg-white shadow-sm' : 'text-secondary' }}"
                   href="{{ route('laporan.index', ['type' => 'per_user', 'tanggal' => date('Y-m')]) }}">
                    <i class="ti ti-users me-1"></i> Per User
                </a>
            </li>
            <li class="nav-item flex-fill text-center">
                <a class="nav-link py-2 px-3 {{ $type === 'per_divisi' ? 'active text-primary border-primary bg-white shadow-sm' : 'text-secondary' }}"
                   href="{{ route('laporan.index', ['type' => 'per_divisi', 'tanggal' => date('Y-m')]) }}">
                    <i class="ti ti-building me-1"></i> Per Divisi
                </a>
            </li>
        </ul>

        <!-- Filter Form -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('laporan.index') }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                {{ $type === 'harian' ? 'Pilih Tanggal' : 'Pilih Bulan' }}
                            </label>
                            <input type="{{ $type === 'harian' ? 'date' : 'month' }}"
                                class="form-control"
                                name="tanggal"
                                value="{{ $tanggal }}"
                                required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search me-1"></i>Tampilkan
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('laporan.export', ['type' => $type, 'tanggal' => $tanggal]) }}"
                                class="btn btn-success w-100">
                                <i class="ti ti-file-export me-1"></i>Export Excel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Content -->
        <div class="row">
            <!-- Table -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-list me-2"></i>Data Laporan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle mb-0" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            @if($type === 'per_user') Nama User
                                            @elseif($type === 'per_divisi') Nama Divisi
                                            @else Nama Ekspedisi @endif
                                        </th>
                                        <th class="text-center">Jumlah Transaksi</th>
                                        <th class="text-end">Total Pendapatan Bersih</th>
                                        <th class="text-end">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dataWithPercentage as $row)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">
                                                @if($type === 'per_user')
                                                    {{ $row->userCreate->name ?? 'Tidak Diketahui' }}
                                                @elseif($type === 'per_divisi')
                                                    {{-- ✅ Tombol Klik untuk Drill-down Ekspedisi --}}
                                                    <button type="button"
                                                            class="btn btn-sm btn-link text-dark fw-semibold text-decoration-none p-0 d-flex align-items-center hover-primary"
                                                            onclick="showEkspedisiBreakdown('{{ addslashes($row->getDivisi->Nama ?? 'Tanpa Divisi') }}', {{ json_encode($row->ekspedisi_breakdown) }})"
                                                            title="Klik untuk lihat detail ekspedisi">
                                                        <i class="ti ti-chart-pie me-1 text-primary"></i>
                                                        {{ $row->getDivisi->Nama ?? 'Tanpa Divisi' }}
                                                    </button>
                                                @else
                                                    {{ $expeditionNames[$row->Ekspedisi] ?? 'Ekspedisi ' . $row->Ekspedisi }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary rounded-pill">{{ $row->jumlah_transaksi }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-primary">
                                                Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold">{{ $row->persentase }}%</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="ti ti-database-off me-2"></i>Tidak ada data untuk periode ini
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class="text-center">{{ $totalTransaksi }}</th>
                                        <th class="text-end">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
                                        <th class="text-end">100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart (Single Canvas) -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold" id="chartTitle">
                            <i class="ti ti-chart-bar me-2"></i>Grafik Pendapatan Bersih
                        </h5>
                        {{-- Tombol Kembali (Hidden by default) --}}
                        <button type="button" id="btnResetChart" class="btn btn-sm btn-outline-secondary d-none" onclick="resetChart()">
                            <i class="ti ti-arrow-left me-1"></i>Kembali
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="incomeChart" style="height: 320px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script (Consolidated) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            let mainChart = null; // Variable global untuk instance chart

            // Data awal dari Backend
            const originalLabels = @json($chartLabels);
            const originalData = @json($chartData);

            // Helper: Generate warna konsisten berdasarkan string
            function stringToColor(str) {
                let hash = 0;
                for (let i = 0; i < str.length; i++) {
                    hash = str.charCodeAt(i) + ((hash << 5) - hash);
                }
                const h = Math.abs(hash) % 360;
                const s = 60 + (Math.abs(hash) % 20);
                const l = 60;
                return `hsl(${h},${s}%,${l}%)`;
            }

            // 1. Fungsi Render Chart Utama (Pendapatan Bersih)
            function renderMainChart() {
                const colors = originalLabels.map(label => stringToColor(label));

                if (mainChart) mainChart.destroy();

                mainChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: originalLabels,
                        datasets: [{
                            label: 'Pendapatan Bersih (Rp)',
                            data: originalData,
                            backgroundColor: colors,
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) return (value / 1000000).toFixed(1) + ' jt';
                                        if (value >= 1000) return (value / 1000).toFixed(0) + ' rb';
                                        return value;
                                    }
                                }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // 2. Fungsi Tampilkan Breakdown Ekspedisi (Drill-down)
            window.showEkspedisiBreakdown = function(divisiName, breakdownData) {
                // Update UI Header
                document.getElementById('chartTitle').innerHTML = `<i class="ti ti-chart-pie me-2"></i>Transaksi: ${divisiName}`;
                document.getElementById('btnResetChart').classList.remove('d-none');

                // Siapkan Data Breakdown
                const labels = breakdownData.map(item => item.name);
                const data = breakdownData.map(item => item.jumlah);
                const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#6610f2'];

                // Hancurkan chart lama & Buat chart baru (Instant Update)
                if (mainChart) mainChart.destroy();

                mainChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: data,
                            backgroundColor: colors.slice(0, labels.length),
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        animation: { duration: 400 }, // Animasi halus saat transisi
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' Transaksi';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Jumlah Transaksi' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            };

            // 3. Fungsi Reset ke Chart Utama
            window.resetChart = function() {
                document.getElementById('chartTitle').innerHTML = `<i class="ti ti-chart-bar me-2"></i>Grafik Pendapatan Bersih`;
                document.getElementById('btnResetChart').classList.add('d-none');
                renderMainChart();
            };

            // Inisialisasi awal
            renderMainChart();
        });
    </script>
@endsection
