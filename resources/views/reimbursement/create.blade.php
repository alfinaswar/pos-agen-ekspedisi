@extends('layouts.app')

@section('title', 'Ajukan Reimbursement')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Ajukan Reimbursement</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reimbursement.index') }}" class="text-decoration-none">Reimbursement</a></li>
                    <li class="breadcrumb-item active">Ajukan</li>
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
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-receipt-off me-2"></i>Form Pengajuan</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('reimbursement.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="Tanggal" class="form-label fw-semibold"><i class="ti ti-calendar me-1 text-primary"></i> Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('Tanggal') is-invalid @enderror" id="Tanggal" name="Tanggal" value="{{ old('Tanggal', date('Y-m-d')) }}" required>
                                        @error('Tanggal') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>


                                    <div class="mb-4">
                                        <label for="Nama" class="form-label fw-semibold">
                                            <i class="ti ti-user me-1 text-primary"></i> Nama Pengaju <span class="text-danger">*</span>
                                        </label>
                                        @php
                                            $authUserId = auth()->user()->id;
                                        @endphp
                                        <select class="form-select @error('Nama') is-invalid @enderror" id="Nama" name="Nama" style="pointer-events: none; background-color: #f8f9fa;" required>
                                            @foreach($user as $u)
                                                <option value="{{ $u->id }}" {{ (old('Nama', $authUserId) == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('Nama')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>


                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="NominalFormatted" class="form-label fw-semibold"><i class="ti ti-cash me-1 text-primary"></i> Nominal <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('Nominal') is-invalid @enderror" id="NominalFormatted" placeholder="Rp 0" autocomplete="off">
                                        <input type="hidden" name="Nominal" id="NominalRaw" value="{{ old('Nominal', 0) }}">
                                        @error('Nominal') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="BuktiUpload" class="form-label fw-semibold"><i class="ti ti-photo me-1 text-primary"></i> Upload Bukti / Struk</label>
                                        <input type="file" class="form-control @error('BuktiUpload') is-invalid @enderror" id="BuktiUpload" name="BuktiUpload" accept="image/*,.pdf">
                                        <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Format: JPG, PNG, atau PDF (Maks. 2MB).</div>
                                        @error('BuktiUpload') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="Item" class="form-label fw-semibold"><i class="ti ti-file-text me-1 text-primary"></i> Detail Item / Keterangan <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('Item') is-invalid @enderror" id="Item" name="Item" rows="3" placeholder="Jelaskan detail pengeluaran..." required>{{ old('Item') }}</textarea>
                                @error('Item') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-send me-2"></i>Kirim Pengajuan
                                </button>
                                <a href="{{ route('reimbursement.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputFormatted = document.getElementById('NominalFormatted');
        const inputRaw = document.getElementById('NominalRaw');
        const formatRupiah = (number) => new Intl.NumberFormat('id-ID').format(number);

        const initialValue = inputRaw.value;
        if (initialValue && initialValue != '0') inputFormatted.value = 'Rp ' + formatRupiah(initialValue);

        inputFormatted.addEventListener('input', function() {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            inputRaw.value = rawValue;
            this.value = rawValue === '' ? '' : 'Rp ' + formatRupiah(rawValue);
        });
    });
</script>
@endsection
