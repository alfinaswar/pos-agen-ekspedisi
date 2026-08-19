@extends('layouts.app')

@section('title', 'Verifikasi Tagihan Pembayaran')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }

    /* Style Khusus Preview File */
    .file-preview-wrapper { transition: all 0.3s ease; }
    .file-preview-wrapper:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important; }
    .img-preview-zoom { transition: transform 0.3s ease; cursor: zoom-in; }
    .img-preview-zoom:hover { transform: scale(1.02); }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 h3 fw-bold text-dark">
                    <i class="ti ti-checklist me-2"></i>
                    Verifikasi Tagihan Pembayaran
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none">
                            <i class="ti ti-home"></i> Home
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('tagihan-pembayaran.index') }}" class="text-decoration-none">
                            <i class="ti ti-file-invoice"></i> Tagihan
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="ti ti-check"></i> Verifikasi
                    </li>
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
                    <!-- Card Header -->
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-receipt me-2"></i>Detail Tagihan
                        </h5>
                        @php
                            $StatusBadge = match($TagihanPembayaran->StatusPembayaran) {
                                'Lunas' => 'bg-success',
                                'Terlambat' => 'bg-danger',
                                default => 'bg-warning text-dark'
                            };
                            $StatusText = match($TagihanPembayaran->StatusPembayaran) {
                                'Lunas' => 'Lunas',
                                'Terlambat' => 'Terlambat',
                                default => 'Belum Bayar'
                            };
                        @endphp
                        <div style="position:absolute; right:2rem; top:2rem;" class="d-flex gap-2 align-items-center">
                            <span class="badge {{ $StatusBadge }} px-3 py-2 fs-6">
                                <i class="ti {{ $TagihanPembayaran->StatusPembayaran === 'Lunas' ? 'ti-check' : 'ti-clock' }} me-1"></i>
                                {{ $StatusText }}
                            </span>
                            @if($TagihanPembayaran->StatusPembayaran !== 'Lunas')
                            <a href="#FormVerifikasi" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-check"></i> Verifikasi & Setujui
                            </a>
                            @endif
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
                                    <label class="text-muted small mb-1 d-block">Nama Tenant</label>
                                    <div class="fw-semibold text-dark fs-5">{{ $TagihanPembayaran->Tenant->Nama ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Email Tenant</label>
                                    <div class="fw-semibold text-dark"><i class="ti ti-mail me-1 text-primary"></i>{{ $TagihanPembayaran->Tenant->Email ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Telepon Tenant</label>
                                    <div class="fw-semibold text-dark"><i class="ti ti-phone me-1 text-primary"></i>{{ $TagihanPembayaran->Tenant->Telepon ?? '-' }}</div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Detail Tagihan -->
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-file-invoice me-1"></i> Detail Tagihan
                                </h6>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Nomor Tagihan</label>
                                    <div class="fw-bold text-primary fs-5">{{ $TagihanPembayaran->NomorTagihan }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Periode</label>
                                    <div class="fw-semibold text-dark">{{ $TagihanPembayaran->PeriodeBulan ?? '-' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Jatuh Tempo</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-calendar-event me-1 text-primary"></i>
                                        @php
                                            use Carbon\Carbon;
                                            $jatuhTempo = $TagihanPembayaran->TanggalJatuhTempo
                                                ? Carbon::parse($TagihanPembayaran->TanggalJatuhTempo)->format('d F Y')
                                                : '-';
                                        @endphp
                                        {{ $jatuhTempo }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Jumlah Tagihan</label>
                                    <div class="fw-bold text-dark fs-4">
                                        Rp {{ number_format($TagihanPembayaran->JumlahTagihan, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Full Width: Bukti Pembayaran -->
                            <div class="col-12 mt-4 pt-3 border-top">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-photo me-1"></i> Bukti Pembayaran
                                </h6>

                                @if($TagihanPembayaran->BuktiPembayaran)
                                    @php
                                        $FileName = basename($TagihanPembayaran->BuktiPembayaran);
                                        $Ext = strtolower(pathinfo($TagihanPembayaran->BuktiPembayaran, PATHINFO_EXTENSION));
                                    @endphp

                                    <div class="file-preview-wrapper p-4 bg-white border rounded-3 shadow-sm">
                                        @if(in_array($Ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <div class="text-center">
                                                <a href="{{ asset('storage/' . $TagihanPembayaran->BuktiPembayaran) }}" target="_blank" class="d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $TagihanPembayaran->BuktiPembayaran) }}"
                                                         alt="Bukti Pembayaran"
                                                         class="img-fluid rounded-3 img-preview-zoom shadow-sm"
                                                         style="max-height: 450px; max-width: 100%; border: 1px solid #e9ecef;">
                                                    <div class="position-absolute top-50 start-50 translate-middle bg-dark bg-opacity-60 text-white rounded-pill px-3 py-2" style="opacity: 0; transition: opacity 0.3s ease;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                                                        <i class="ti ti-zoom-in me-1"></i> Klik untuk memperbesar
                                                    </div>
                                                </a>
                                                <div class="mt-3 d-flex align-items-center justify-content-center gap-2 text-muted small">
                                                    <i class="ti ti-info-circle"></i>
                                                    <span>{{ $FileName }}</span>
                                                    <span class="text-muted">•</span>
                                                    <a href="{{ asset('storage/' . $TagihanPembayaran->BuktiPembayaran) }}" download class="text-primary text-decoration-none fw-semibold">
                                                        <i class="ti ti-download me-1"></i>Unduh
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif($Ext === 'pdf')
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                                        <i class="ti ti-file-type-pdf" style="font-size: 2rem;"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3 overflow-hidden">
                                                    <h6 class="mb-1 fw-semibold text-dark text-truncate" title="{{ $FileName }}">{{ $FileName }}</h6>
                                                    <p class="mb-0 text-muted small"><i class="ti ti-file me-1"></i> Dokumen PDF</p>
                                                </div>
                                                <div class="flex-shrink-0 ms-3 d-flex gap-2">
                                                    <a href="{{ asset('storage/' . $TagihanPembayaran->BuktiPembayaran) }}" target="_blank" class="btn btn-primary btn-sm d-flex align-items-center gap-1"><i class="ti ti-eye"></i> Lihat</a>
                                                    <a href="{{ asset('storage/' . $TagihanPembayaran->BuktiPembayaran) }}" download class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="ti ti-download"></i></a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                                        <i class="ti ti-file" style="font-size: 2rem;"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3 overflow-hidden">
                                                    <h6 class="mb-1 fw-semibold text-dark text-truncate" title="{{ $FileName }}">{{ $FileName }}</h6>
                                                    <p class="mb-0 text-muted small"><i class="ti ti-file me-1"></i> File {{ strtoupper($Ext) }}</p>
                                                </div>
                                                <div class="flex-shrink-0 ms-3">
                                                    <a href="{{ asset('storage/' . $TagihanPembayaran->BuktiPembayaran) }}" download class="btn btn-primary btn-sm d-flex align-items-center gap-1"><i class="ti ti-download"></i> Unduh File</a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5 bg-light rounded-3 border border-dashed">
                                        <i class="ti ti-file-x" style="font-size: 3rem; opacity: 0.5;"></i>
                                        <p class="mt-2 mb-0 fw-semibold">Belum ada bukti pembayaran yang diunggah</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Catatan Verifikasi (Jika Sudah Lunas) -->
                            @if($TagihanPembayaran->StatusPembayaran === 'Lunas' && $TagihanPembayaran->Catatan)
                            <div class="col-12 mt-4 pt-3 border-top">
                                <div class="p-3 bg-success bg-opacity-10 rounded border-start border-4 border-success">
                                    <label class="text-success-emphasis small mb-1 d-block fw-bold">
                                        <i class="ti ti-message me-1"></i> Catatan Verifikasi:
                                    </label>
                                    <div class="text-dark fst-italic">"{{ $TagihanPembayaran->Catatan }}"</div>
                                    @if($TagihanPembayaran->UserUpdate || $TagihanPembayaran->TanggalPembayaran)
                                    <div class="mt-2 text-muted small">
                                        <i class="ti ti-user-check me-1"></i> {{ $TagihanPembayaran->UserUpdate ?? '-' }}
                                        <i class="ti ti-clock ms-3 me-1"></i> {{ $TagihanPembayaran->TanggalPembayaran ? $TagihanPembayaran->TanggalPembayaran->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- ✅ FORM VERIFIKASI (Hanya jika Belum Lunas) -->
                            @if($TagihanPembayaran->StatusPembayaran !== 'Lunas')
                            <div class="col-12 mt-4" id="FormVerifikasi">
                                <div class="card border-0 shadow-sm bg-success bg-opacity-10 border border-success border-opacity-50">
                                    <div class="card-header bg-transparent border-bottom border-success border-opacity-25 py-3">
                                        <h6 class="mb-0 fw-bold text-success-emphasis d-flex align-items-center">
                                            <i class="ti ti-shield-check fs-5 me-2"></i>
                                            Form Verifikasi Pembayaran
                                        </h6>
                                        <small class="text-muted">Konfirmasi pembayaran ini akan mengubah status menjadi <strong>Lunas</strong>.</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <form action="{{ route('tagihan-pembayaran.konfirmasi.proses', $TagihanPembayaran->id) }}" method="POST" enctype="multipart/form-data" id="FormVerifikasiTagihan">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label for="TanggalPembayaran" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                        Tanggal Pembayaran <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="date" class="form-control form-control-sm fw-semibold" id="TanggalPembayaran" name="TanggalPembayaran" value="{{ date('Y-m-d') }}" required>
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="BuktiPembayaran" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                        Upload Bukti Transfer (Opsional)
                                                    </label>
                                                    <input type="file" class="form-control form-control-sm" id="BuktiPembayaran" name="BuktiPembayaran" accept="image/png, image/jpeg, image/jpg, image/webp, application/pdf">
                                                    <div class="form-text text-muted small mt-1"><i class="ti ti-info-circle me-1"></i>Maks. 2MB. JPG, PNG, atau PDF.</div>

                                                    <!-- Preview Area -->
                                                    <div class="mt-2 d-none" id="PreviewContainer">
                                                        <div class="position-relative d-inline-block">
                                                            <img id="ImagePreview" src="#" alt="Pratinjau" class="img-fluid rounded border bg-light" style="max-height: 150px; max-width: 100%; object-fit: contain;">
                                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm" id="RemoveImageBtn" title="Hapus"><i class="ti ti-x"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="Catatan" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                        Catatan
                                                    </label>
                                                    <textarea name="Catatan" id="Catatan" class="form-control form-control-sm" rows="2" style="resize: vertical;" placeholder="Catatan tambahan..."></textarea>
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <button type="submit" class="btn btn-success text-white fw-semibold px-4 d-flex align-items-center justify-content-center gap-1 shadow-sm w-100 w-md-auto">
                                                        <i class="ti ti-check"></i> Konfirmasi & Setujui Lunas
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
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('tagihan-pembayaran.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic Preview Gambar Bukti Pembayaran
            const InputBukti = document.getElementById('BuktiPembayaran');
            const PreviewContainer = document.getElementById('PreviewContainer');
            const ImagePreview = document.getElementById('ImagePreview');
            const RemoveImageBtn = document.getElementById('RemoveImageBtn');

            if (InputBukti) {
                InputBukti.addEventListener('change', function(Event) {
                    const File = this.files[0];
                    if (File) {
                        if (File.size > 2 * 1024 * 1024) {
                            alert('Ukuran file terlalu besar! Maksimal 2MB.');
                            this.value = '';
                            ResetPreview();
                            return;
                        }
                        const Reader = new FileReader();
                        Reader.onload = function(ReaderEvent) {
                            ImagePreview.src = ReaderEvent.target.result;
                            PreviewContainer.classList.remove('d-none');
                        }
                        Reader.readAsDataURL(File);
                    } else {
                        ResetPreview();
                    }
                });

                RemoveImageBtn.addEventListener('click', function() {
                    InputBukti.value = '';
                    ResetPreview();
                });

                function ResetPreview() {
                    ImagePreview.src = '#';
                    PreviewContainer.classList.add('d-none');
                }
            }
        });
    </script>
@endpush
