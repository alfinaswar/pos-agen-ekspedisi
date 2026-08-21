@extends('layouts.app')

@section('title', 'Buat Tagihan Pembayaran')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
    .dropzone {
        border: 2px dashed #0d6efd;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        background: #f8fbff;
        color: #0d6efd;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .dropzone.dragover {
        border-color: #198754;
        background: #e6f9eb;
        color: #198754;
    }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Buat Tagihan Pembayaran</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tagihan-pembayaran.index') }}" class="text-decoration-none">Tagihan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buat</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content pb-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-10 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-receipt me-2"></i>Form Buat Tagihan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('tagihan-pembayaran.store') }}" method="POST" id="FormTagihan" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="TenantId" class="form-label fw-semibold">
                                            <i class="ti ti-building me-1 text-primary"></i> Pilih Tenant <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('TenantId') is-invalid @enderror" id="TenantId" name="TenantId" required>
                                            <option value="">-- Pilih Tenant --</option>
                                            @foreach($Tenants as $Tenant)
                                                <option value="{{ $Tenant->Kode }}" {{ old('TenantId') == $Tenant->Kode ? 'selected' : '' }}>
                                                    {{ $Tenant->Nama }}
                                                </option>

                                            @endforeach
                                        </select>
                                        @error('TenantId') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>



                                    <div class="mb-4">
                                        <label for="PeriodeBulan" class="form-label fw-semibold">
                                            <i class="ti ti-calendar-month me-1 text-primary"></i> Periode Bulan <span class="text-danger">*</span>
                                        </label>
                                        <input type="month" class="form-control @error('PeriodeBulan') is-invalid @enderror" id="PeriodeBulan" name="PeriodeBulan" value="{{ old('PeriodeBulan') }}" required>
                                        @error('PeriodeBulan')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="JumlahTagihan" class="form-label fw-semibold">
                                            <i class="ti ti-cash me-1 text-primary"></i> Jumlah Tagihan (Rp) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text fw-semibold">Rp</span>
                                            <input type="text" class="form-control text-end @error('JumlahTagihan') is-invalid @enderror" id="JumlahTagihan" name="JumlahTagihan" value="{{ old('JumlahTagihan') }}" placeholder="0" required>
                                        </div>
                                        <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Format angka akan otomatis dipisahkan ribuan.</div>
                                        @error('JumlahTagihan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="TanggalPembayaran" class="form-label fw-semibold">
                                            <i class="ti ti-calendar-check me-1 text-primary"></i> Tanggal Pembayaran <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control @error('TanggalPembayaran') is-invalid @enderror" id="TanggalPembayaran" name="TanggalPembayaran" value="{{ now()->format('Y-m-d') }}" readonly>
                                        <div class="form-text text-muted">Otomatis diisi hari ini.</div>
                                        @error('TanggalPembayaran') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="BerlakuHingga" class="form-label fw-semibold">
                                            <i class="ti ti-calendar-plus me-1 text-primary"></i> Berlaku Hingga <span class="text-danger">*</span>
                                        </label>
                                        @php
                                            $berlakuHingga = \Carbon\Carbon::now()->addMonth()->format('Y-m-d');
                                        @endphp
                                        <input type="date" class="form-control @error('BerlakuHingga') is-invalid @enderror" id="BerlakuHingga" name="BerlakuHingga" value="{{ $berlakuHingga }}" readonly>
                                        <div class="form-text text-muted">Otomatis 1 tahun dari tanggal pembayaran.</div>
                                        @error('BerlakuHingga') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="BuktiPembayaran" class="form-label fw-semibold">
                                            <i class="ti ti-upload me-1 text-primary"></i> Bukti Pembayaran <span class="text-danger">*</span>
                                        </label>
                                        <div id="dropzone" class="dropzone">
                                            <span><i class="ti ti-cloud-upload me-2"></i>Drag & Drop file di sini atau klik untuk pilih file.</span>
                                            <input type="file" style="display:none;" id="BuktiPembayaran" name="BuktiPembayaran" accept="image/*,application/pdf" required>
                                        </div>
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Format diterima: JPG, PNG, PDF. Maks. 2MB.
                                        </div>
                                        <div id="file-name" class="mt-1 small"></div>
                                        @error('BuktiPembayaran') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="Catatan" class="form-label fw-semibold">
                                            <i class="ti ti-file-description me-1 text-primary"></i> Catatan
                                        </label>
                                        <textarea class="form-control @error('Catatan') is-invalid @enderror" id="Catatan" name="Catatan" rows="4" placeholder="Catatan tambahan (opsional)...">{{ old('Catatan') }}</textarea>
                                        @error('Catatan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Simpan Tagihan
                                </button>
                                <a href="{{ route('tagihan-pembayaran.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
                                    <i class="ti ti-x me-2"></i>Batal
                                </a>
                            </div>
                        </form>
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
            // Format angka dengan pemisah ribuan saat mengetik (JumlahTagihan)
            const InputJumlah = document.getElementById('JumlahTagihan');
            if (InputJumlah) {
                InputJumlah.addEventListener('input', function() {
                    let Value = this.value.replace(/[^,\d]/g, '').toString();
                    let Split = Value.split(',');
                    let Sisa = Split[0].length % 3;
                    let Rupiah = Split[0].substr(0, Sisa);
                    let Ribuan = Split[0].substr(Sisa).match(/\d{3}/gi);

                    if (Ribuan) {
                        let Separator = Sisa ? '.' : '';
                        Rupiah += Separator + Ribuan.join('.');
                    }
                    Rupiah = Split[1] != undefined ? Rupiah + ',' + Split[1] : Rupiah;
                    this.value = Rupiah;
                });
            }

            // Drag & drop untuk BuktiPembayaran
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('BuktiPembayaran');
            const fileNameDiv = document.getElementById('file-name');

            if (dropzone && fileInput) {
                dropzone.addEventListener('click', function() {
                    fileInput.click();
                });

                dropzone.addEventListener('dragover', function(e) {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.classList.add('dragover');
                });
                dropzone.addEventListener('dragleave', function(e) {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.classList.remove('dragover');
                });
                dropzone.addEventListener('drop', function(e) {
                    e.preventDefault(); e.stopPropagation();
                    dropzone.classList.remove('dragover');
                    if (e.dataTransfer.files && e.dataTransfer.files.length) {
                        fileInput.files = e.dataTransfer.files;
                        updateFileName();
                    }
                });
                fileInput.addEventListener('change', updateFileName);

                function updateFileName() {
                    if (fileInput.files.length > 0) {
                        fileNameDiv.textContent = fileInput.files[0].name;
                    } else {
                        fileNameDiv.textContent = '';
                    }
                }
            }
        });
    </script>
@endpush
