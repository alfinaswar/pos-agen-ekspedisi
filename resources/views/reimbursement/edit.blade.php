@extends('layouts.app')

@section('title', 'Update Status Reimbursement')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
    .preview-bukti { max-width: 100%; max-height: 150px; object-fit: contain; border: 1px dashed #ced4da; border-radius: 0.375rem; padding: 4px; background-color: #f8f9fa; }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb  -2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Update Status Reimbursement</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reimbursement.index') }}" class="text-decoration-none">Reimbursement</a></li>
                    <li class="breadcrumb-item active">Update Status</li>
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
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-edit me-2"></i>Form Update (Owner)</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('reimbursement.update', $reimbursement->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Info Box Status Saat Ini -->
                            <div class="alert alert-light border d-flex align-items-center mb-4">
                                <i class="ti ti-info-circle text-primary me-2 fs-4"></i>
                                <div>
                                    <strong>Pengaju:</strong> {{ $reimbursement->Nama }} <br>
                                    <strong>Status Saat Ini:</strong>
                                    @php
                                        $badge = $reimbursement->Status === 'Menunggu' ? 'bg-warning text-dark' : ($reimbursement->Status === 'Ditolak' ? 'bg-danger' : 'bg-success');
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $reimbursement->Status }}</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="Tanggal" class="form-label fw-semibold"><i class="ti ti-calendar me-1 text-primary"></i> Tanggal</label>
                                        <input type="date" class="form-control" id="Tanggal" name="Tanggal" value="{{ old('Tanggal', $reimbursement->Tanggal) }}" readonly>
                                    </div>
                                    <div class="mb-4">
                                        <label for="Nama" class="form-label fw-semibold"><i class="ti ti-user me-1 text-primary"></i> Nama Pengaju <span class="text-danger">*</span></label>

                                        <select class="form-select @error('Nama') is-invalid @enderror" id="Nama" name="Nama" style="pointer-events: none; background-color: #f8f9fa;">
                                            @foreach($user as $u)
                                                <option value="{{ $u->id }}" {{ (old('Nama', $reimbursement->Nama) == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>
                                            @endforeach
                                        </select>



                                        @error('Nama') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="NominalFormatted" class="form-label fw-semibold"><i class="ti ti-cash me-1 text-primary"></i> Nominal</label>
                                        <input type="text" class="form-control bg-light" id="NominalFormatted" readonly>
                                        <input type="hidden" name="Nominal" id="NominalRaw" value="{{ old('Nominal', $reimbursement->Nominal) }}">
                                    </div>

                                    <!-- Field Khusus Owner: Ubah Status -->
                                    @if(auth()->user() && auth()->user()->role === 'Admin')
                                    <div class="mb-4">
                                        <label for="Status" class="form-label fw-semibold text-danger"><i class="ti ti-shield-check me-1"></i> Ubah Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('Status') is-invalid @enderror" id="Status" name="Status" required>
                                            <option value="Menunggu" {{ old('Status', $reimbursement->Status) == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                            <option value="Ditolak" {{ old('Status', $reimbursement->Status) == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                            <option value="Dibayar" {{ old('Status', $reimbursement->Status) == 'Dibayar' ? 'selected' : '' }}>Dibayar</option>
                                        </select>
                                        @error('Status') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    @else
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-danger"><i class="ti ti-shield-check me-1"></i> Status</label>
                                        <input type="text" class="form-control bg-light" value="{{ $reimbursement->Status }}" readonly>
                                        <input type="hidden" name="Status" value="{{ $reimbursement->Status }}">
                                    </div>
                                    @endif

                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold"><i class="ti ti-file-text me-1 text-primary"></i> Detail Item</label>
                                <textarea class="form-control bg-light" name="Item" rows="3" readonly>{{ old('Item', $reimbursement->Item) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="BuktiUpload" class="form-label fw-semibold"><i class="ti ti-photo me-1 text-primary"></i> Bukti Pembayaran</label>
                                @if($reimbursement->BuktiUpload)
                                    <div class="mb-2 p-2 bg-light rounded border">
                                        <p class="mb-1 small text-muted fw-semibold">File Saat Ini:</p>
                                        @php $ext = pathinfo($reimbursement->BuktiUpload, PATHINFO_EXTENSION); @endphp
                                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                            <a href="{{ Storage::url($reimbursement->BuktiUpload) }}" target="_blank">
                                                <img src="{{ Storage::url($reimbursement->BuktiUpload) }}" class="preview-bukti" alt="Bukti">
                                            </a>
                                        @else
                                            <a href="{{ Storage::url($reimbursement->BuktiUpload) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 text-start">
                                                <i class="ti ti-file-type-pdf me-2"></i> {{ basename($reimbursement->BuktiUpload) }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('BuktiUpload') is-invalid @enderror" id="BuktiUpload" name="BuktiUpload" accept="image/*,.pdf">
                                <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Kosongkan jika tidak ingin mengganti file.</div>
                                @error('BuktiUpload') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Simpan Perubahan
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
    });
</script>
@endsection
