@extends('layouts.app')

@section('title', 'Buat Tagihan Pembayaran')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
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
            <div class="col-xl-12 col-lg-12 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-receipt me-2"></i>Form Buat Tagihan
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('tagihan-pembayaran.store') }}" method="POST" id="FormTagihan">
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
                                                <option value="{{ $Tenant->id }}" {{ old('TenantId') == $Tenant->id ? 'selected' : '' }}>
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
                                        <input type="text" class="form-control @error('PeriodeBulan') is-invalid @enderror" id="PeriodeBulan" name="PeriodeBulan" value="{{ old('PeriodeBulan') }}" placeholder="Contoh: Oktober 2023" required>
                                        @error('PeriodeBulan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="TanggalJatuhTempo" class="form-label fw-semibold">
                                            <i class="ti ti-calendar-event me-1 text-primary"></i> Tanggal Jatuh Tempo <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control @error('TanggalJatuhTempo') is-invalid @enderror" id="TanggalJatuhTempo" name="TanggalJatuhTempo" value="{{ old('TanggalJatuhTempo') }}" required>
                                        @error('TanggalJatuhTempo') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
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
            const InputJumlah = document.getElementById('JumlahTagihan');

            // Format angka dengan pemisah ribuan saat mengetik
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
        });
    </script>
@endpush
