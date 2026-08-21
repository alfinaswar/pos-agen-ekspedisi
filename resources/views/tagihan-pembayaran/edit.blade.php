@extends('layouts.app')

@section('title', 'Edit Tagihan Pembayaran')

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
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Edit Tagihan Pembayaran</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tagihan-pembayaran.index') }}" class="text-decoration-none">Tagihan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-edit me-2"></i>Form Edit Tagihan
                        </h5>
                        <span class="badge bg-info text-dark px-3 py-2">
                            No: {{ $TagihanPembayaran->NomorTagihan }}
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('tagihan-pembayaran.update', $TagihanPembayaran->id) }}" method="POST" id="FormTagihan" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
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
                                                <option value="{{ $Tenant->id }}" {{ old('TenantId', $TagihanPembayaran->TenantId) == $Tenant->id ? 'selected' : '' }}>
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
                                        <input type="month" class="form-control @error('PeriodeBulan') is-invalid @enderror" id="PeriodeBulan" name="PeriodeBulan" value="{{ old('PeriodeBulan', $TagihanPembayaran->PeriodeBulan) }}" required>
                                        @error('PeriodeBulan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="JumlahTagihan" class="form-label fw-semibold">
                                            <i class="ti ti-cash me-1 text-primary"></i> Jumlah Tagihan (Rp) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text fw-semibold">Rp</span>
                                            <input type="text" class="form-control text-end @error('JumlahTagihan') is-invalid @enderror" id="JumlahTagihan" name="JumlahTagihan" value="{{ old('JumlahTagihan', number_format($TagihanPembayaran->JumlahTagihan, 0, '', '.')) }}" placeholder="0" required>
                                        </div>
                                        <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Format angka akan otomatis dipisahkan ribuan.</div>
                                        @error('JumlahTagihan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="TanggalPembayaran" class="form-label fw-semibold">
                                            <i class="ti ti-calendar-check me-1 text-primary"></i> Tanggal Pembayaran <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control @error('TanggalPembayaran') is-invalid @enderror" id="TanggalPembayaran" name="TanggalPembayaran" value="{{ old('TanggalPembayaran', $TagihanPembayaran->TanggalPembayaran ? $TagihanPembayaran->TanggalPembayaran->format('Y-m-d') : now()->format('Y-m-d')) }}" readonly>
                                        <div class="form-text text-muted">Otomatis diisi hari ini atau tanggal pembayaran sebelumnya.</div>
                                        @error('TanggalPembayaran') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="BerlakuHingga" class="form-label fw-semibold">
                                            <i class="ti ti-calendar-plus me-1 text-primary"></i> Berlaku Hingga <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control @error('BerlakuHingga') is-invalid @enderror" id="BerlakuHingga" name="BerlakuHingga" value="{{ old('BerlakuHingga', $TagihanPembayaran->TanggalJatuhTempo ? $TagihanPembayaran->TanggalJatuhTempo->format('Y-m-d') : '') }}" readonly>
                                        <div class="form-text text-muted">Tanggal jatuh tempo tagihan.</div>
                                        @error('BerlakuHingga') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="BuktiPembayaran" class="form-label fw-semibold">
                                            <i class="ti ti-upload me-1 text-primary"></i> Bukti Pembayaran
                                        </label>
                                        <div id="dropzone" class="dropzone">
                                            <span><i class="ti ti-cloud-upload me-2"></i>Drag & Drop file di sini atau klik untuk pilih file.</span>
                                            <input type="file" style="display:none;" id="BuktiPembayaran" name="BuktiPembayaran" accept="image/*,application/pdf">
                                        </div>
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah file. Format: JPG, PNG, PDF. Maks. 2MB.
                                        </div>

                                        <!-- Tampilkan file lama jika ada -->
                                        @if($TagihanPembayaran->BuktiPembayaran)
                                            <div id="existing-file" class="mt-2 p-2 bg-light rounded border d-flex align-items-center gap-2">
                                                <i class="ti ti-file text-primary fs-4"></i>
                                                <div class="flex-grow-1">
                                                    <div class="small fw-semibold text-truncate" style="max-width: 200px;">{{ basename($TagihanPembayaran->BuktiPembayaran) }}</div>
                                                    <div class="small text-muted">File saat ini</div>
                                                </div>
                                                <a href="{{ asset('storage/' . $TagihanPembayaran->BuktiPembayaran) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i></a>
                                            </div>
                                        @endif

                                        <div id="file-name" class="mt-1 small text-primary fw-semibold"></div>
                                        @error('BuktiPembayaran') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="Catatan" class="form-label fw-semibold">
                                            <i class="ti ti-file-description me-1 text-primary"></i> Catatan
                                        </label>
                                        <textarea class="form-control @error('Catatan') is-invalid @enderror" id="Catatan" name="Catatan" rows="4" placeholder="Catatan tambahan (opsional)...">{{ old('Catatan', $TagihanPembayaran->Catatan) }}</textarea>
                                        @error('Catatan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Perbarui Tagihan
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
            // 1. Format angka dengan pemisah ribuan saat mengetik
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

            // 2. Drag & drop untuk BuktiPembayaran
            const Dropzone = document.getElementById('dropzone');
            const FileInput = document.getElementById('BuktiPembayaran');
            const FileNameDiv = document.getElementById('file-name');
            const ExistingFileDiv = document.getElementById('existing-file');

            if (Dropzone && FileInput) {
                Dropzone.addEventListener('click', function() {
                    FileInput.click();
                });

                Dropzone.addEventListener('dragover', function(Event) {
                    Event.preventDefault(); Event.stopPropagation();
                    Dropzone.classList.add('dragover');
                });

                Dropzone.addEventListener('dragleave', function(Event) {
                    Event.preventDefault(); Event.stopPropagation();
                    Dropzone.classList.remove('dragover');
                });

                Dropzone.addEventListener('drop', function(Event) {
                    Event.preventDefault(); Event.stopPropagation();
                    Dropzone.classList.remove('dragover');
                    if (Event.dataTransfer.files && Event.dataTransfer.files.length) {
                        FileInput.files = Event.dataTransfer.files;
                        UpdateFileName();
                    }
                });

                FileInput.addEventListener('change', UpdateFileName);

                function UpdateFileName() {
                    if (FileInput.files.length > 0) {
                        FileNameDiv.textContent = 'File baru: ' + FileInput.files[0].name;
                        if (ExistingFileDiv) {
                            ExistingFileDiv.style.opacity = '0.5';
                            ExistingFileDiv.style.textDecoration = 'line-through';
                        }
                    } else {
                        FileNameDiv.textContent = '';
                        if (ExistingFileDiv) {
                            ExistingFileDiv.style.opacity = '1';
                            ExistingFileDiv.style.textDecoration = 'none';
                        }
                    }
                }
            }

            // 3. Hapus format titik sebelum form disubmit agar bisa disimpan sebagai angka
            const FormTagihan = document.getElementById('FormTagihan');
            FormTagihan.addEventListener('submit', function() {
                if (InputJumlah) {
                    InputJumlah.value = InputJumlah.value.replace(/\./g, '');
                }
            });
        });
    </script>
@endpush
