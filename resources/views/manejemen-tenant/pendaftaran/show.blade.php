@extends('layouts.app-manajemen-tenant')

@section('title', 'Verifikasi Pendaftaran Tenant')

@section('content')
@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }

    /* Style Khusus Preview File */
    .file-preview-wrapper {
        transition: all 0.3s ease;
    }
    .file-preview-wrapper:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important;
    }
    .img-preview-zoom {
        transition: transform 0.3s ease;
        cursor: zoom-in;
    }
    .img-preview-zoom:hover {
        transform: scale(1.02);
    }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Verifikasi Pendaftaran Tenant</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pendaftaran-tenant.index') }}" class="text-decoration-none">Pendaftaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Verifikasi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content pb-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card shadow-sm border-0">
                                       <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-building-plus me-2"></i>Detail Pendaftaran
                        </h5>
                        <div class="d-flex gap-2 align-items-center justify-content-end ms-auto">
                            @php
                                $StatusBadge = match($PendaftaranTenant->Status) {
                                    'Y' => 'bg-success',
                                    'N' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                $StatusText = match($PendaftaranTenant->Status) {
                                    'Y' => 'Disetujui',
                                    'N' => 'Ditolak',
                                    default => 'Belum Diverifikasi'
                                };
                            @endphp
                            <span class="badge {{ $StatusBadge }} px-3 py-2 fs-6 float-end">
                                <i class="ti {{ $PendaftaranTenant->Status === 'Y' ? 'ti-check' : ($PendaftaranTenant->Status === 'N' ? 'ti-x' : 'ti-clock') }} me-1"></i>
                                {{ $StatusText }}
                            </span>
                        </div>


                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Kolom Kiri: Informasi Tenant -->
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-building me-1"></i> Informasi Tenant
                                </h6>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Kode Pendaftaran</label>
                                    <div class="fw-semibold text-dark">{{ $PendaftaranTenant->Kode ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Nama Tenant / Perusahaan</label>
                                    <div class="fw-semibold text-dark fs-5">{{ $PendaftaranTenant->Nama ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Email</label>
                                    <div class="fw-semibold text-dark"><i class="ti ti-mail me-1 text-primary"></i>{{ $PendaftaranTenant->Email ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Alamat</label>
                                    <div class="fw-semibold text-dark"><i class="ti ti-map-pin me-1 text-primary"></i>{{ $PendaftaranTenant->Alamat ?? '-' }}</div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Informasi PIC -->
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-user me-1"></i> Informasi PIC (Person In Charge)
                                </h6>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Nama PIC</label>
                                    <div class="fw-semibold text-dark">{{ $PendaftaranTenant->NamaPIC ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Email PIC</label>
                                    <div class="fw-semibold text-dark"><i class="ti ti-mail me-1 text-primary"></i>{{ $PendaftaranTenant->EmailPIC ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Alamat PIC</label>
                                    <div class="fw-semibold text-dark"><i class="ti ti-map-pin me-1 text-primary"></i>{{ $PendaftaranTenant->AlamatPIC ?? '-' }}</div>
                                </div>
                            </div>

                                                      <!-- Full Width: Bukti Pembayaran -->
                            <div class="col-12 mt-4 pt-3 border-top">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-receipt me-1"></i> Bukti Pembayaran
                                </h6>

                                @if($PendaftaranTenant->BuktiPembayaran)
                                    @php
                                        $FileName = basename($PendaftaranTenant->BuktiPembayaran);
                                        $Ext = strtolower(pathinfo($PendaftaranTenant->BuktiPembayaran, PATHINFO_EXTENSION));
                                    @endphp

                                    <div class="file-preview-wrapper p-4 bg-white border rounded-3 shadow-sm">

                                        {{-- Tampilan untuk GAMBAR --}}
                                        @if(in_array($Ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <div class="text-center">
                                                <a href="{{ asset('storage/' . $PendaftaranTenant->BuktiPembayaran) }}" target="_blank" class="d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $PendaftaranTenant->BuktiPembayaran) }}"
                                                         alt="Bukti Pembayaran"
                                                         class="img-fluid rounded-3 img-preview-zoom shadow-sm"
                                                         style="max-height: 450px; max-width: 100%; border: 1px solid #e9ecef;">

                                                    <!-- Overlay Hint -->
                                                    <div class="position-absolute top-50 start-50 translate-middle bg-dark bg-opacity-60 text-white rounded-pill px-3 py-2" style="opacity: 0; transition: opacity 0.3s ease;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                                        <i class="ti ti-zoom-in me-1"></i> Klik untuk memperbesar
                                                    </div>
                                                </a>
                                                <div class="mt-3 d-flex align-items-center justify-content-center gap-2 text-muted small">
                                                    <i class="ti ti-info-circle"></i>
                                                    <span>{{ $FileName }}</span>
                                                    <span class="text-muted">•</span>
                                                    <a href="{{ asset('storage/' . $PendaftaranTenant->BuktiPembayaran) }}" download class="text-primary text-decoration-none fw-semibold">
                                                        <i class="ti ti-download me-1"></i>Unduh
                                                    </a>
                                                </div>
                                            </div>

                                        {{-- Tampilan untuk PDF --}}
                                        @elseif($Ext === 'pdf')
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                                        <i class="ti ti-file-type-pdf" style="font-size: 2rem;"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3 overflow-hidden">
                                                    <h6 class="mb-1 fw-semibold text-dark text-truncate" title="{{ $FileName }}">
                                                        {{ $FileName }}
                                                    </h6>
                                                    <p class="mb-0 text-muted small">
                                                        <i class="ti ti-file me-1"></i> Dokumen PDF
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0 ms-3 d-flex gap-2">
                                                    <a href="{{ asset('storage/' . $PendaftaranTenant->BuktiPembayaran) }}" target="_blank" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                                        <i class="ti ti-eye"></i> Lihat
                                                    </a>
                                                    <a href="{{ asset('storage/' . $PendaftaranTenant->BuktiPembayaran) }}" download class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                                                        <i class="ti ti-download"></i>
                                                    </a>
                                                </div>
                                            </div>

                                        {{-- Tampilan untuk File Lainnya (Excel, Word, Zip, dll) --}}
                                        @else
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                                        <i class="ti ti-file" style="font-size: 2rem;"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3 overflow-hidden">
                                                    <h6 class="mb-1 fw-semibold text-dark text-truncate" title="{{ $FileName }}">
                                                        {{ $FileName }}
                                                    </h6>
                                                    <p class="mb-0 text-muted small">
                                                        <i class="ti ti-file me-1"></i> File {{ strtoupper($Ext) }}
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0 ms-3 d-flex gap-2">
                                                    <a href="{{ asset('storage/' . $PendaftaranTenant->BuktiPembayaran) }}" target="_blank" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                                        <i class="ti ti-download"></i> Unduh File
                                                    </a>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                @else
                                    <div class="text-center text-muted py-5 bg-light rounded-3 border border-dashed">
                                        <i class="ti ti-file-x" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <p class="mt-2 mb-0 fw-semibold">Tidak ada bukti pembayaran yang diunggah</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Catatan Verifikasi (Jika Ada) -->
                            @if($PendaftaranTenant->CatatanVerifikasi)
                            <div class="col-12 mt-4 pt-3 border-top">
                                <div class="p-3 bg-light rounded border-start border-4 border-warning">
                                    <label class="text-warning-emphasis small mb-1 d-block fw-bold">
                                        <i class="ti ti-message me-1"></i> Catatan Verifikasi:
                                    </label>
                                    <div class="text-dark fst-italic">"{{ $PendaftaranTenant->CatatanVerifikasi }}"</div>
                                    @if($PendaftaranTenant->UserVerif || $PendaftaranTenant->VerifikasiPada)
                                    <div class="mt-2 text-muted small">
                                        <i class="ti ti-user-check me-1"></i> {{ $PendaftaranTenant->UserVerif ?? '-' }}
                                        <i class="ti ti-clock ms-3 me-1"></i> {{ $PendaftaranTenant->VerifikasiPada ? $PendaftaranTenant->VerifikasiPada->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- ✅ FORM VERIFIKASI (Hanya jika belum diverifikasi) -->
                            @if($PendaftaranTenant->Status === 'N/A' || $PendaftaranTenant->Status === null)
                            <div class="col-12 mt-4">
                                <div class="card border-0 shadow-sm bg-warning bg-opacity-10 border border-warning border-opacity-50">
                                    <div class="card-header bg-transparent border-bottom border-warning border-opacity-25 py-3">
                                        <h6 class="mb-0 fw-bold text-warning-emphasis d-flex align-items-center">
                                            <i class="ti ti-shield-check fs-5 me-2"></i>
                                            Form Verifikasi Pendaftaran
                                        </h6>
                                        <small class="text-muted">Silakan periksa data dan bukti pembayaran sebelum menyetujui atau menolak.</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <form action="{{ route('pendaftaran-tenant.verifikasi', $PendaftaranTenant->id) }}" method="POST">
                                            @csrf
                                            <div class="row g-3 align-items-center">
                                                <div class="col-md-4 d-flex align-items-center" style="min-height: 100%;">
                                                    <div class="w-100">
                                                        <label for="Status" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                            Status Verifikasi <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="Status" id="Status" class="form-select form-select-sm fw-semibold" required>
                                                            <option value="">-- Pilih Status --</option>
                                                            <option value="Y">Y (Disetujui / Acc)</option>
                                                            <option value="N">N (Ditolak / Tidak Valid)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="CatatanVerifikasi" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                        Catatan Verifikasi
                                                    </label>
                                                    <textarea name="CatatanVerifikasi" id="CatatanVerifikasi" class="form-control form-control-sm" rows="2" style="resize: vertical;" placeholder="Opsional: Isi alasan jika ditolak atau catatan tambahan..."></textarea>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center" style="min-height: 100%;">
                                                    <button type="submit" class="btn btn-warning text-dark fw-semibold w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                                                        <i class="ti ti-check"></i> Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif


                        </div>
                    </div>


                     <!-- Card Footer: Action Buttons -->
                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4">
                        <div class="d-flex gap-2 justify-content-between align-items-center">
                            <a href="{{ route('pendaftaran-tenant.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
                                <i class="ti ti-arrow-left me-2"></i>Kembali
                            </a>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
