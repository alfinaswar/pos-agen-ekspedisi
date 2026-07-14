@extends('layouts.app')

@section('title', 'Tambah Ekspedisi')
@section('page-title', 'Tambah Ekspedisi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Form Tambah Ekspedisi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('ekspedisi.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="NamaEkspedisi" class="form-label">Nama Ekspedisi <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('NamaEkspedisi') is-invalid @enderror"
                               id="NamaEkspedisi"
                               name="NamaEkspedisi"
                               value="{{ old('NamaEkspedisi') }}"
                               placeholder="Masukkan nama ekspedisi"
                               required
                               autofocus>
                        @error('NamaEkspedisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="Deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control @error('Deskripsi') is-invalid @enderror"
                                  id="Deskripsi"
                                  name="Deskripsi"
                                  rows="4"
                                  placeholder="Masukkan deskripsi ekspedisi">{{ old('Deskripsi') }}</textarea>
                        @error('Deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Simpan
                        </button>
                        <a href="{{ route('ekspedisi.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
