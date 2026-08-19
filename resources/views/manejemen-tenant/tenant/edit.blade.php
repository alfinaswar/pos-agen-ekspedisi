@extends('layouts.app')

@section('title', 'Edit Tenant')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Edit Tenant</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tenant.index') }}" class="text-decoration-none">Tenant</a></li>
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
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-edit me-2"></i>Form Edit Tenant</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('tenant.update', $Tenant->id) }}" method="POST" id="FormTenant">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Kolom Kiri: Informasi Dasar -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="Nama" class="form-label fw-semibold"><i class="ti ti-building me-1 text-primary"></i> Nama Tenant <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('Nama') is-invalid @enderror" id="Nama" name="Nama" value="{{ old('Nama', $Tenant->Nama) }}" required autofocus>
                                        @error('Nama') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="Kode" class="form-label fw-semibold"><i class="ti ti-hash me-1 text-primary"></i> Kode Tenant <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('Kode') is-invalid @enderror" id="Kode" name="Kode" value="{{ old('Kode', $Tenant->Kode) }}" required>
                                        @error('Kode') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="Email" class="form-label fw-semibold"><i class="ti ti-mail me-1 text-primary"></i> Email</label>
                                        <input type="email" class="form-control @error('Email') is-invalid @enderror" id="Email" name="Email" value="{{ old('Email', $Tenant->Email) }}">
                                        @error('Email') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="Telepon" class="form-label fw-semibold"><i class="ti ti-phone me-1 text-primary"></i> Telepon</label>
                                        <input type="text" class="form-control @error('Telepon') is-invalid @enderror" id="Telepon" name="Telepon" value="{{ old('Telepon', $Tenant->Telepon) }}">
                                        @error('Telepon') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="Alamat" class="form-label fw-semibold"><i class="ti ti-map-pin me-1 text-primary"></i> Alamat</label>
                                        <textarea class="form-control @error('Alamat') is-invalid @enderror" id="Alamat" name="Alamat" rows="3">{{ old('Alamat', $Tenant->Alamat) }}</textarea>
                                        @error('Alamat') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="TanggalJoin" class="form-label fw-semibold"><i class="ti ti-calendar-event me-1 text-primary"></i> Tanggal Join <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('TanggalJoin') is-invalid @enderror" id="TanggalJoin" name="TanggalJoin" value="{{ old('TanggalJoin', $Tenant->TanggalJoin->format('Y-m-d')) }}" required>
                                        @error('TanggalJoin') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Subscription & Referal -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="KodeReferal" class="form-label fw-semibold"><i class="ti ti-ticket me-1 text-primary"></i> Kode Referal</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('KodeReferal') is-invalid @enderror" id="KodeReferal" name="KodeReferal" value="{{ old('KodeReferal', $Tenant->KodeReferal) }}">
                                            <button type="button" class="btn btn-outline-primary" id="BtnGenerateReferal" title="Generate Kode Acak">
                                                <i class="ti ti-refresh"></i>
                                            </button>
                                        </div>
                                        @error('KodeReferal') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="StatusSubscription" class="form-label fw-semibold"><i class="ti ti-credit-card me-1 text-primary"></i> Status Subscription <span class="text-danger">*</span></label>
                                        <select class="form-select @error('StatusSubscription') is-invalid @enderror" id="StatusSubscription" name="StatusSubscription" required>
                                            <option value="Aktif" {{ old('StatusSubscription', $Tenant->StatusSubscription) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Nonaktif" {{ old('StatusSubscription', $Tenant->StatusSubscription) == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                            <option value="Expired" {{ old('StatusSubscription', $Tenant->StatusSubscription) == 'Expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                        @error('StatusSubscription') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="TanggalMulaiSubscription" class="form-label fw-semibold"><i class="ti ti-calendar-plus me-1 text-primary"></i> Tanggal Mulai Subscription</label>
                                        <input type="date" class="form-control @error('TanggalMulaiSubscription') is-invalid @enderror" id="TanggalMulaiSubscription" name="TanggalMulaiSubscription" value="{{ old('TanggalMulaiSubscription', $Tenant->TanggalMulaiSubscription ? $Tenant->TanggalMulaiSubscription->format('Y-m-d') : '') }}">
                                        @error('TanggalMulaiSubscription') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="TanggalAkhirSubscription" class="form-label fw-semibold"><i class="ti ti-calendar-off me-1 text-primary"></i> Tanggal Akhir Subscription</label>
                                        <input type="date" class="form-control @error('TanggalAkhirSubscription') is-invalid @enderror" id="TanggalAkhirSubscription" name="TanggalAkhirSubscription" value="{{ old('TanggalAkhirSubscription', $Tenant->TanggalAkhirSubscription ? $Tenant->TanggalAkhirSubscription->format('Y-m-d') : '') }}">
                                        @error('TanggalAkhirSubscription') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold"><i class="ti ti-device-floppy me-2"></i>Perbarui Data</button>
                                <a href="{{ route('tenant.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold"><i class="ti ti-x me-2"></i>Batal</a>
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
            const BtnGenerateReferal = document.getElementById('BtnGenerateReferal');
            const InputKodeReferal = document.getElementById('KodeReferal');

            BtnGenerateReferal.addEventListener('click', function() {
                const RandomString = Math.random().toString(36).substring(2, 10).toUpperCase();
                InputKodeReferal.value = RandomString;
            });
        });
    </script>
@endpush
