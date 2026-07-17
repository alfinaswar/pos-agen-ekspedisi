@extends('layouts.app')

@section('content')
    <!-- Chart.js CSS (optional, mostly not needed unless custom style) -->
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
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                </ol>
            </nav>
        </div>

        <!-- Tabs: Pertegas gaya tab dengan border, background, dan icon  -->
        <ul class="nav nav-tabs mb-4 fw-semibold border border-primary-subtle rounded overflow-hidden" id="reportTabs" style="background: #f8f9fa;">
            <li class="nav-item flex-fill text-center">
                <a class="nav-link py-2 px-3 {{ $type === 'harian' ? 'active text-primary border-primary bg-white shadow-sm' : 'text-secondary' }}"
                   style="font-weight:600; letter-spacing: .5px;"
                   href="{{ route('laporan.index', ['type' => 'harian', 'tanggal' => $tanggal]) }}">
                    <i class="ti ti-calendar-stats me-1"></i>
                    Harian
                </a>
            </li>
            <li class="nav-item flex-fill text-center">
                <a class="nav-link py-2 px-3 {{ $type === 'bulanan' ? 'active text-primary border-primary bg-white shadow-sm' : 'text-secondary' }}"
                   style="font-weight:600; letter-spacing: .5px;"
                   href="{{ route('laporan.index', ['type' => 'bulanan', 'tanggal' => $tanggal]) }}">
                    <i class="ti ti-calendar-month me-1"></i>
                    Bulanan
                </a>
            </li>
            <li class="nav-item flex-fill text-center">
                <a class="nav-link py-2 px-3 {{ $type === 'per_ekspedisi' ? 'active text-primary border-primary bg-white shadow-sm' : 'text-secondary' }}"
                   style="font-weight:600; letter-spacing: .5px;"
                   href="{{ route('laporan.index', ['type' => 'per_ekspedisi', 'tanggal' => $tanggal]) }}">
                    <i class="ti ti-truck me-1"></i>
                    Per Ekspedisi
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
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date"
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
                            <table class="table table-striped table-bordered align-middle mb-0" id="laporanTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ekspedisi</th>
                                        <th class="text-center">Jumlah Transaksi</th>
                                        <th class="text-end">Total Pendapatan</th>
                                        <th class="text-end">Persentase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dataWithPercentage as $row)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">
                                                {{ $expeditionNames[$row->Ekspedisi] ?? 'Ekspedisi ' . $row->Ekspedisi }}
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

            <!-- Chart -->
            <div class="col-lg-5 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="ti ti-chart-bar me-2"></i>Grafik Pendapatan
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="incomeChart" style="height: 320px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('incomeChart').getContext('2d');

            // Labels dari server (mapping nama ekspedisi)
            const labels = @json($chartLabels).map(id => {
                const names = @json($expeditionNames);
                return names[id] || 'Ekspedisi ' + id;
            });

            const data = @json($chartData);

            // Warna untuk setiap bar
            const colors = [
                '#dc3545', // merah
                '#0d6efd', // biru
                '#ffc107', // kuning
                '#6f42c1', // ungu
                '#198754', // hijau
                '#fd7e14', // orange
            ];

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: data,
                        backgroundColor: colors.slice(0, data.length),
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
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
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + ' jt';
                                    } else if (value >= 1000) {
                                        return (value / 1000).toFixed(0) + ' rb';
                                    }
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
