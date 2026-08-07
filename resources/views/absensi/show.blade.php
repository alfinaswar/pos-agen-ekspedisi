@extends('layouts.app')

@section('title', 'Detail Absensi')

@section('content')
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 h3 fw-bold text-dark">Detail Absensi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}" class="text-decoration-none">Absensi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="container-fluid mb-3">
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="ti ti-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<div class="content pb-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card shadow-sm border-0">

                    <!-- Header Card -->
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-clock-hour-9 me-2"></i>Data Absensi
                        </h5>
                        @php
                            $statusBadge = match($absensi->Status ?? 'N/A') {
                                'Y' => 'bg-success',
                                'N' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            $statusText = match($absensi->Status ?? 'N/A') {
                                'Y' => 'Disetujui (Y)',
                                'N' => 'Ditolak (N)',
                                default => 'Belum Diverifikasi (N/A)'
                            };
                        @endphp
                        <span class="badge {{ $statusBadge }} px-3 py-2 fs-6">
                            <i class="ti {{ $absensi->Status === 'Y' ? 'ti-check' : ($absensi->Status === 'N' ? 'ti-x' : 'ti-clock') }} me-1"></i>
                            {{ $statusText }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Kolom Kiri: Info Karyawan & Waktu -->
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-user me-1"></i> Informasi Karyawan
                                </h6>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Nama</label>
                                    <div class="fw-semibold text-dark fs-5">{{ $absensi->getUser->name ?? $absensi->Nama }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Divisi</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-building me-1 text-primary"></i>
                                        {{ $absensi->getDivisi->Nama ?? ($absensi->getDivisi->Nama ?? '-') }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Tanggal</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-calendar me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($absensi->Tanggal)->isoFormat('dddd, D MMMM YYYY') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Detail Kehadiran -->
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-clock me-1"></i> Detail Kehadiran
                                </h6>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="text-muted small mb-1 d-block">Jam Hadir</label>
                                        <div class="fw-semibold text-success fs-5">
                                            <i class="ti ti-login me-1"></i> {{ $absensi->JamHadir ?: '-' }}
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="text-muted small mb-1 d-block">Jam Pulang</label>
                                        <div class="fw-semibold text-danger fs-5">
                                            <i class="ti ti-logout me-1"></i> {{ $absensi->JamPulang ?: '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Status Lembur</label>
                                    <div class="fw-semibold text-dark">
                                        @if($absensi->Lembur === 'Y')
                                            <span class="badge bg-warning text-dark">Ya</span>
                                            <span class="ms-2 text-muted small">({{ $absensi->MulaiLembur ?: '-' }} s/d {{ $absensi->SelesaiLembur ?: '-' }})</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- <!-- Full Width: Catatan Finance/Leader (Jika Ada) -->
                            @if($absensi->Catatan)
                            <div class="col-12 mt-4 pt-3 border-top">
                                <div class="p-3 bg-light rounded border-start border-4 border-warning">
                                    <label class="text-warning-emphasis small mb-1 d-block fw-bold">
                                        <i class="ti ti-message me-1"></i> Catatan Persetujuan:
                                    </label>
                                    <div class="text-dark fst-italic">"{{ $absensi->Catatan }}"</div>
                                </div>
                            </div>
                            @endif --}}

                            <!-- ✅ BAGIAN AKSI PERSETUJUAN LEADER (Hanya untuk Admin & Leader) -->
                            @if(in_array(auth()->user()->role, ['Admin', 'Leader']))
                            <div class="col-12 mt-4">
                                <div class="card border-0 shadow-sm bg-warning bg-opacity-10 border border-warning border-opacity-50">
                                    <div class="card-header bg-transparent border-bottom border-warning border-opacity-25 py-3">
                                        <h6 class="mb-0 fw-bold text-warning-emphasis d-flex align-items-center">
                                            <i class="ti ti-shield-check fs-5 me-2"></i>
                                            Aksi Persetujuan Leader
                                        </h6>
                                        <small class="text-muted">Ubah status untuk mengunci (Acc) atau membuka kunci (Revisi) data bagi Kasir.</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <form action="{{ route('absensi.approve', $absensi->id) }}" method="POST">
                                            @csrf
                                            <div class="row g-3 align-items-center justify-content-center">
                                                <div class="col-md-4 d-flex flex-column justify-content-center">
                                                    <label for="Status" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                        Status Verifikasi <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="Status" id="Status" class="form-select form-select-sm fw-semibold" required>
                                                        <option value="N/A" {{ ($absensi->StatusVerif ?? 'N/A') === 'N/A' ? 'selected' : '' }}>N/A (Belum Diverifikasi / Buka Kunci)</option>
                                                        <option value="Y" {{ ($absensi->StatusVerif ?? 'N/A') === 'Y' ? 'selected' : '' }}>Y (Disetujui / Acc & Kunci)</option>
                                                        <option value="N" {{ ($absensi->StatusVerif ?? 'N/A') === 'N' ? 'selected' : '' }}>N (Ditolak / Revisi & Buka Kunci)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Catatan" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                        Catatan Leader
                                                    </label>
                                                    <textarea name="Catatan" id="Catatan" class="form-control form-control-sm" rows="6" style="resize: vertical;" placeholder="Opsional: Isi alasan jika ditolak atau catatan tambahan...">{{ old('Catatan', $absensi->Catatan) }}</textarea>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center justify-content-center" style="height: 100%;">
                                                    <button type="submit" class="btn btn-warning text-dark fw-semibold w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                                                        <i class="ti ti-check"></i> Simpan
                                                    </button>
                                                </div>
                                            </div>

                                        </form>

                                        @if($absensi->DisetujuiPada)
                                        <div class="mt-3 pt-2 border-top border-warning border-opacity-25">
                                            <small class="text-muted">
                                                <i class="ti ti-user-check me-1"></i> Diverifikasi terakhir oleh: <strong>{{ $absensi->UserLeader }}</strong>
                                                pada {{ \Carbon\Carbon::parse($absensi->DisetujuiPada)->isoFormat('D MMMM YYYY, HH:mm') }}
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                            <!-- ✅ AKHIR BAGIAN AKSI PERSETUJUAN LEADER -->

                        </div>
                    </div>

                    <!-- Card Footer: Action Buttons -->
                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('absensi.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
                                <i class="ti ti-arrow-left me-2"></i>Kembali
                            </a>
                            @if(in_array(auth()->user()->role, ['Admin', 'Leader', 'Kasir']) && ($absensi->Status ?? 'N/A') !== 'Y')
                            <a href="{{ route('absensi.edit', $absensi->id) }}" class="btn btn-warning text-white px-4 d-flex align-items-center fw-semibold">
                                <i class="ti ti-edit me-2"></i>Edit Data
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
