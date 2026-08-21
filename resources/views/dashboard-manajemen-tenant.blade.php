@extends(auth()->user()->role === 'Superadmin' ? 'layouts.app-manajemen-tenant' : 'layouts.app')

@section('content')

<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
        <div class="flex-grow-1">
            <h4 class="page-main-title m-0 fw-semibold">
                <i class="ti ti-dashboard me-2 text-primary"></i>Dashboard
            </h4>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item active" aria-current="page">Overview</li>
            </ol>
        </nav>
    </div>

    {{-- ============================================
         DASHBOARD SUPERADMIN
         ============================================ --}}

    <!-- Stats Cards Row 1 -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="ti ti-building text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0 small text-uppercase">Total Tenant</h6>
                            <h3 class="mb-0 fw-bold">{{ $Data['TotalTenant'] }}</h3>
                            <small class="text-success">
                                <i class="ti ti-check me-1"></i>{{ $Data['TenantAktif'] }} Aktif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="ti ti-cash text-success fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0 small text-uppercase">Total Pendapatan</h6>
                            <h3 class="mb-0 fw-bold text-success">Rp {{ number_format($Data['TotalPendapatan'], 0, ',', '.') }}</h3>
                            <small class="text-muted">
                                Bulan ini: Rp {{ number_format($Data['PendapatanBulanIni'], 0, ',', '.') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="ti ti-receipt text-warning fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0 small text-uppercase">Tagihan Belum Bayar</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $Data['TotalTagihanBelumBayar'] }}</h3>
                            <small class="text-danger">
                                Rp {{ number_format($Data['NominalBelumBayar'], 0, ',', '.') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="ti ti-user-plus text-info fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-0 small text-uppercase">Pendaftaran Pending</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $Data['PendaftaranPending'] }}</h3>
                            <small class="text-muted">
                                Hari ini: {{ $Data['PendaftaranHariIni'] }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Row -->
    @if($Data['SubscriptionAkanHabis'] > 0 || $Data['TotalTagihanTerlambat'] > 0)
        <div class="row g-3 mb-4">
            @if($Data['SubscriptionAkanHabis'] > 0)
                <div class="col-md-6">
                    <div class="alert alert-warning d-flex align-items-center mb-0">
                        <i class="ti ti-alert-triangle fs-2 me-3"></i>
                        <div>
                            <strong>{{ $Data['SubscriptionAkanHabis'] }} Tenant</strong> subscription akan habis dalam 7 hari!<br>
                            <small>Segera lakukan penagihan untuk memperpanjang.</small>
                        </div>
                    </div>
                </div>
            @endif
            @if($Data['TotalTagihanTerlambat'] > 0)
                <div class="col-md-6">
                    <div class="alert alert-danger d-flex align-items-center mb-0">
                        <i class="ti ti-clock-exclamation fs-2 me-3"></i>
                        <div>
                            <strong>{{ $Data['TotalTagihanTerlambat'] }} Tagihan</strong> terlambat pembayaran!<br>
                            <small>Perlu tindak lanjut segera.</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Charts & Tables Row -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="ti ti-chart-line me-2"></i>Pertumbuhan Tenant (6 Bulan)</h5>
                </div>
                <div class="card-body">
                    <canvas id="PertumbuhanChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="ti ti-trophy me-2"></i>Top 5 Tenant by Revenue</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Nama Tenant</th>
                                    <th class="text-end">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($Data['TopTenant'] as $Index => $Item)
                                    <tr>
                                        <td class="text-center">{{ $Index + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $Item->Tenant->Nama ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-success">Rp {{ number_format($Item->TotalRevenue, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
<script>
    @if(auth()->user()->role === 'Superadmin')
    // Chart Pertumbuhan Tenant
    const PertumbuhanCtx = document.getElementById('PertumbuhanChart').getContext('2d');
    new Chart(PertumbuhanCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($Data['PertumbuhanTenant'], 'Month')) !!},
            datasets: [{
                label: 'Jumlah Tenant Baru',
                data: {!! json_encode(array_column($Data['PertumbuhanTenant'], 'Count')) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
    @endif
</script>
@endpush
