@extends('layouts.app')

@section('title', 'Edit Ekspedisi')

@section('content')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .error-fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
    }
</style>

<!-- Content Header dengan Breadcrumb -->
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 h3 fw-bold text-dark">Edit Ekspedisi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ekspedisi.index') }}" class="text-decoration-none">Ekspedisi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Form Content -->
<div class="content pb-5">
    <div class="container-fluid">
        <!-- col-md-12 wajib, dibatasi max xl-8 dan lg-10 agar tetap rapi di layar besar -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-10 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-truck me-2"></i>Form Edit Ekspedisi
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('ekspedisi.update', $ekspedisi->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Nama Ekspedisi -->
                            <div class="mb-4">
                                <label for="NamaEkspedisi" class="form-label fw-semibold">
                                    <i class="ti ti-truck me-1 text-primary"></i> Nama Ekspedisi <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('NamaEkspedisi') is-invalid @enderror"
                                       id="NamaEkspedisi"
                                       name="NamaEkspedisi"
                                       value="{{ old('NamaEkspedisi', $ekspedisi->NamaEkspedisi) }}"
                                       placeholder="Contoh: JNE, J&T, SiCepat"
                                       required
                                       autofocus>
                                <div class="form-text text-muted mt-1">
                                    <i class="ti ti-info-circle me-1"></i>Masukkan nama jasa ekspedisi atau kurir yang digunakan.
                                </div>
                                @error('NamaEkspedisi')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <label for="Deskripsi" class="form-label fw-semibold">
                                    <i class="ti ti-file-description me-1 text-primary"></i> Deskripsi
                                </label>
                                <textarea class="form-control @error('Deskripsi') is-invalid @enderror"
                                          id="Deskripsi"
                                          name="Deskripsi"
                                          rows="4"
                                          placeholder="Jelaskan detail layanan, area jangkauan, atau catatan tambahan...">{{ old('Deskripsi', $ekspedisi->Deskripsi) }}</textarea>
                                <div class="form-text text-muted mt-1">
                                    <i class="ti ti-info-circle me-1"></i>Opsional: Tambahkan informasi pendukung seperti estimasi waktu atau area layanan.
                                </div>
                                @error('Deskripsi')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Update
                                </button>
                                <a href="{{ route('ekspedisi.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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
