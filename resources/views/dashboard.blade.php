@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">

<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 0.75rem;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0,0,0,0.1) !important;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.75rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .mini-chart {
        height: 60px;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold mb-0">
                    <i class="ti ti-dashboard me-2 text-primary"></i>Dashboard
                </h2>
                <span class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Pendapatan -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Pendapatan</p>
                            <h3 class="mb-0 fw-bold text-primary">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
                            <small class="text-success">
                                <i class="ti ti-trending-up me-1"></i>+12.5% dari bulan lalu
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="ti ti-cash"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Transaksi Bulan Ini</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $totalTransaksi ?? 0 }}</h3>
                            <small class="text-muted">{{ $avgTransaksiPerHari ?? 0 }} transaksi/hari</small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="ti ti-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reimbursement Pending -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Reimbursement Pending</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $reimbursementPending ?? 0 }}</h3>
                            <small class="text-muted">Menunggu persetujuan</small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="ti ti-clock-hour-9"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kehadiran Hari Ini -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small text-uppercase fw-semibold">Kehadiran Hari Ini</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $kehadiranHariIni ?? 0 }} <small class="text-muted fs-6">/{{ $totalKaryawan ?? 0 }}</small></h3>
                            <small class="text-muted">{{ $persentaseHadir ?? 0 }}% hadir</small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="ti ti-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <!-- Pendapatan per Ekspedisi -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-chart-bar me-2 text-primary"></i>Pendapatan per Ekspedisi
                    </h5>
                    <!-- Dropdown Bulan -->
                    <form id="filterBulanForm" class="d-flex gap-2 align-items-center" style="margin-bottom:0;">
                        <label for="bulanSelect" class="mb-0 me-2 text-primary small">Bulan:</label>
                        <select id="bulanSelect" name="bulan" class="form-select form-select-sm" style="min-width:150px;">
                            @foreach($availableMonths as $key => $bulanOption)
                                @php
                                    // $availableMonths is just [1 => 'Januari', ...] so $key is the bulan value, $bulanOption is label
                                    $optionValue = $key;
                                    $optionLabel = $bulanOption;
                                @endphp
                                <option value="{{ $optionValue }}"
                                    @if($optionValue == $selectedMonth) selected @endif>
                                    {{ $optionLabel }}
                                </option>
                            @endforeach

                        </select>
                    </form>

                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="ekspedisiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Reimbursement -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-chart-pie me-2 text-primary"></i>Status Reimbursement
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="reimbursementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4 mb-4">
        <!-- Tren Pendapatan -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-trending-up me-2 text-primary"></i>Tren Pendapatan (7 Hari Terakhir)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Kehadiran -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-calendar-stats me-2 text-primary"></i>Statistik Kehadiran Minggu Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-4">
        <!-- Transaksi Terbaru -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-receipt me-2 text-primary"></i>Transaksi Terbaru
                    </h5>
                    <a href="{{ route('transaksi.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">User Input</th>
                                    <th>Ekspedisi</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru ?? [] as $trx)
                                <tr>
                                    <td class="ps-4 fw-semibold text-primary">{{ $trx->userCreate->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="ti ti-truck me-1"></i>{{ $trx->ekspedisi->NamaEkspedisi }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($trx->Pendapatan, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="ti ti-check"></i>
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="ti ti-database-off me-2"></i>Belum ada transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reimbursement Terbaru -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-receipt-off me-2 text-primary"></i>Pengajuan Reimbursement
                    </h5>
                    <a href="{{ route('reimbursement.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama</th>
                                    <th>Item</th>
                                    <th class="text-end">Nominal</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reimbursementTerbaru ?? [] as $reimb)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $reimb->getUser->name }}</td>
                                    <td><small class="text-muted">{{ Str::limit($reimb->Item, 30) }}</small></td>
                                    <td class="text-end fw-bold">Rp {{ number_format($reimb->Nominal, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = $reimb->Status === 'Dibayar' ? 'bg-success' : ($reimb->Status === 'Ditolak' ? 'bg-danger' : 'bg-warning text-dark');
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $reimb->Status }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="ti ti-database-off me-2"></i>Belum ada pengajuan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6c757d';

        // Data map for per-expedisi chart by bulan (from backend)
        let ekspedisiDataPerBulan = @json($ekspedisiPerBulanData);

        // Get initial data and labels for the selected month
        let selectedBulan = "{{ $selectedBulan }}";
        let ekspedisiLabels = ekspedisiDataPerBulan[selectedBulan]?.labels ?? [];
        let ekspedisiValues = ekspedisiDataPerBulan[selectedBulan]?.values ?? [];

        // 1. Pendapatan per Ekspedisi
        let ekspedisiChartCtx = document.getElementById('ekspedisiChart').getContext('2d');
        let ekspedisiChart = new Chart(ekspedisiChartCtx, {
            type: 'bar',
            data: {
                labels: ekspedisiLabels,
                datasets: [{
                    label: 'Pendapatan',
                    data: ekspedisiValues,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#6f42c1', '#dc3545'],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (val) => 'Rp ' + (val / 1000000).toFixed(1) + 'jt' }
                    }
                }
            }
        });

        // Handle bulan (month) selector for Pendapatan per Ekspedisi
        document.getElementById('bulanSelect').addEventListener('change', function() {
            let bulanValue = this.value;
            if(ekspedisiDataPerBulan[bulanValue]) {
                let labels = ekspedisiDataPerBulan[bulanValue].labels;
                let values = ekspedisiDataPerBulan[bulanValue].values;
                ekspedisiChart.data.labels = labels;
                ekspedisiChart.data.datasets[0].data = values;
                ekspedisiChart.update();
            }
        });

        // 2. Status Reimbursement
        new Chart(document.getElementById('reimbursementChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Menunggu', 'Dibayar', 'Ditolak'],
                datasets: [{
                    data: @json($reimbursementStatus),
                    backgroundColor: ['#ffc107', '#198754', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } } },
                cutout: '70%'
            }
        });

        // 3. Tren Pendapatan
        new Chart(document.getElementById('trendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($trendData),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true, tension: 0.4,
                    pointBackgroundColor: '#0d6efd', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (val) => 'Rp ' + (val / 1000000).toFixed(1) + 'jt' }
                    }
                }
            }
        });

        // 4. Statistik Kehadiran
        new Chart(document.getElementById('attendanceChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
                datasets: [
                    { label: 'Hadir', data: @json($attendanceHadir), backgroundColor: '#198754', borderRadius: 6 },
                    { label: 'Izin/Sakit', data: @json($attendanceIzin), backgroundColor: '#ffc107', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15 } } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>

@endsection
