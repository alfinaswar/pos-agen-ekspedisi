@extends('layouts.app')

@section('title', 'Tambah Paket Harga')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Tambah Paket Harga</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master-paket-harga.index') }}" class="text-decoration-none">Paket Harga</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content pb-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-package me-2"></i>Form Tambah Paket</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('master-paket-harga.store') }}" method="POST" id="FormPaket">
                            @csrf
                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="NamaPaket" class="form-label fw-semibold"><i class="ti ti-tag me-1 text-primary"></i> Nama Paket <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('NamaPaket') is-invalid @enderror" id="NamaPaket" name="NamaPaket" value="{{ old('NamaPaket') }}" placeholder="Contoh: Paket Pro" required autofocus>
                                        @error('NamaPaket') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="KodePaket" class="form-label fw-semibold"><i class="ti ti-hash me-1 text-primary"></i> Kode Paket <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('KodePaket') is-invalid @enderror" id="KodePaket" name="KodePaket" value="{{ old('KodePaket') }}" placeholder="Contoh: PKG-PRO-01" required>
                                        @error('KodePaket') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="Harga" class="form-label fw-semibold"><i class="ti ti-currency-rupiah me-1 text-primary"></i> Harga (Rp) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('Harga') is-invalid @enderror" id="Harga" name="Harga" value="{{ old('Harga') }}" placeholder="0" required>
                                        <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Format angka akan otomatis dipisahkan ribuan.</div>
                                        @error('Harga') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="DurasiBulan" class="form-label fw-semibold"><i class="ti ti-calendar-time me-1 text-primary"></i> Durasi (Bulan) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('DurasiBulan') is-invalid @enderror" id="DurasiBulan" name="DurasiBulan" value="{{ old('DurasiBulan', 1) }}" min="1" required>
                                        @error('DurasiBulan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="Status" class="form-label fw-semibold"><i class="ti ti-toggle-left me-1 text-primary"></i> Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('Status') is-invalid @enderror" id="Status" name="Status" required>
                                            <option value="Aktif" {{ old('Status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Nonaktif" {{ old('Status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                        @error('Status') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="Deskripsi" class="form-label fw-semibold"><i class="ti ti-file-description me-1 text-primary"></i> Deskripsi Singkat</label>
                                        <textarea class="form-control @error('Deskripsi') is-invalid @enderror" id="Deskripsi" name="Deskripsi" rows="3" placeholder="Deskripsi umum paket...">{{ old('Deskripsi') }}</textarea>
                                        @error('Deskripsi') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Full Width: Fitur -->
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label for="Fitur" class="form-label fw-semibold"><i class="ti ti-list-check me-1 text-primary"></i> Daftar Fitur</label>
                                        <textarea class="form-control @error('Fitur') is-invalid @enderror" id="Fitur" name="Fitur" rows="4" placeholder="Masukkan setiap fitur dalam satu baris baru (Enter)&#10;Contoh:&#10;- Akses Dashboard&#10;- 5 User&#10;- Support 24/7">{{ old('Fitur') }}</textarea>
                                        <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Sistem akan otomatis memisahkan fitur berdasarkan baris baru.</div>
                                        @error('Fitur') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold"><i class="ti ti-device-floppy me-2"></i>Simpan Paket</button>
                                <a href="{{ route('master-paket-harga.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold"><i class="ti ti-x me-2"></i>Batal</a>
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
            // Format input Harga menjadi format ribuan secara real-time
            const InputHarga = document.getElementById('Harga');

            InputHarga.addEventListener('keyup', function() {
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

            // Hapus format titik sebelum form disubmit agar bisa disimpan sebagai angka
            const FormPaket = document.getElementById('FormPaket');
            FormPaket.addEventListener('submit', function() {
                InputHarga.value = InputHarga.value.replace(/\./g, '');
            });
        });
    </script>
@endpush
