@extends('layouts.app')
@section('title', 'Edit Divisi')
@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
</style>
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Edit Divisi</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('divisi.index') }}">Divisi</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-building me-2"></i>Form Edit Divisi</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('divisi.update', $divisi->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <label for="Nama" class="form-label fw-semibold"><i class="ti ti-tag me-1 text-primary"></i> Nama Divisi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('Nama') is-invalid @enderror" id="Nama" name="Nama"
                                       value="{{ old('Nama', $divisi->Nama) }}" placeholder="Contoh: IT, HRD, Keuangan" required autofocus>
                                @error('Nama') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="Keterangan" class="form-label fw-semibold"><i class="ti ti-file-text me-1 text-primary"></i> Keterangan</label>
                                <textarea class="form-control @error('Keterangan') is-invalid @enderror" id="Keterangan" name="Keterangan" rows="3" placeholder="Deskripsi singkat tentang divisi ini...">{{ old('Keterangan', $divisi->Keterangan) }}</textarea>
                                <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Opsional: Jelaskan tugas atau lingkup kerja divisi ini.</div>
                                @error('Keterangan') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold"><i class="ti ti-device-floppy me-2"></i>Update</button>
                                <a href="{{ route('divisi.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold"><i class="ti ti-x me-2"></i>Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
