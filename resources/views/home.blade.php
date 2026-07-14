@extends('layouts.app')

@section('title', 'Dashboard - POS Agen Ekspedisi')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label">Total Hari Ini</div>
            <div class="stat-value success">Rp 2.150.000</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label">Jumlah Transaksi</div>
            <div class="stat-value primary">34</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label">Rata-Rata / Transaksi</div>
            <div class="stat-value warning">Rp 63.235</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-label">Total Bulan Ini</div>
            <div class="stat-value">Rp 22.525.000</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="chart-card">
            <h6 class="chart-title">Pendapatan per Ekspedisi (Hari Ini)</h6>
            <canvas id="chartEkspedisi"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="chart-card">
            <h6 class="chart-title">Grafik Pendapatan (30 Hari Terakhir)</h6>
            <canvas id="chartPendapatan"></canvas>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="table-card">
    <div class="table-header">
        <h5 class="table-title">Transaksi Terakhir</h5>
        <a href="#" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-2"></i>Transaksi Baru
        </a>
    </div>
    <div class="table-responsive">
        <table class="table custom-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu</th>
                    <th>Nomor Resi</th>
                    <th>Ekspedisi</th>
                    <th>Layanan</th>
                    <th>Pendapatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>05/06/2024 09:15</td>
                    <td>0012783412</td>
                    <td>JNE</td>
                    <td>REG</td>
                    <td>Rp 34.000</td>
                    <td>
                        <button class="btn-action">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>05/06/2024 09:27</td>
                    <td>0012783489</td>
                    <td>SiCepat</td>
                    <td>BEST</td>
                    <td>Rp 21.000</td>
                    <td>
                        <button class="btn-action">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>05/06/2024 10:00</td>
                    <td>0012783521</td>
                    <td>J&T</td>
                    <td>EZ</td>
                    <td>Rp 29.000</td>
                    <td>
                        <button class="btn-action">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>05/06/2024 11:04</td>
                    <td>0012783588</td>
                    <td>AnterAja</td>
                    <td>REG</td>
                    <td>Rp 30.000</td>
                    <td>
                        <button class="btn-action">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>05/06/2024 12:19</td>
                    <td>0012783623</td>
                    <td>JNE</td>
                    <td>YES</td>
                    <td>Rp 42.000</td>
                    <td>
                        <button class="btn-action">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart Pendapatan per Ekspedisi (Dummy Data)
    const ctxEkspedisi = document.getElementById('chartEkspedisi').getContext('2d');
    new Chart(ctxEkspedisi, {
        type: 'doughnut',
        data: {
            labels: ['JNE', 'SiCepat', 'J&T', 'AnterAja', 'POS Indonesia'],
            datasets: [{
                data: [720000, 435000, 590000, 245000, 160000],
                backgroundColor: [
                    '#ef4444',
                    '#3b82f6',
                    '#f59e0b',
                    '#8b5cf6',
                    '#10b981'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });

    // Chart Pendapatan 30 Hari (Dummy Data)
    const ctxPendapatan = document.getElementById('chartPendapatan').getContext('2d');
    new Chart(ctxPendapatan, {
        type: 'line',
        data: {
            labels: [
                '07/05', '08/05', '09/05', '10/05', '11/05', '12/05', '13/05',
                '14/05', '15/05', '16/05', '17/05', '18/05', '19/05', '20/05',
                '21/05', '22/05', '23/05', '24/05', '25/05', '26/05', '27/05',
                '28/05', '29/05', '30/05', '31/05', '01/06', '02/06', '03/06',
                '04/06', '05/06'
            ],
            datasets: [{
                label: 'Pendapatan',
                data: [
                    540000, 550000, 501000, 465000, 584000, 560000, 615000, 590000, 610000, 545000,
                    570000, 522000, 515000, 560000, 575000, 555000, 585000, 530000, 570000, 615000,
                    595000, 600000, 570000, 690000, 715000, 622000, 698000, 573000, 684000, 720000
                ],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000) + 'rb';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush
