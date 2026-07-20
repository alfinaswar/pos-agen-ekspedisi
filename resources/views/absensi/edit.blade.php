@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
</style>

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Edit Absensi</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('absensi.index') }}" class="text-decoration-none">Absensi</a></li>
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
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-clock-hour-9 me-2"></i>Form Edit Absensi</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('absensi.update', $absensi->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Kolom Kiri: Data Karyawan -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="Nama" class="form-label fw-semibold"><i class="ti ti-user me-1 text-primary"></i> Nama <span class="text-danger">*</span></label>
                                        <select class="form-select @error('Nama') is-invalid @enderror" id="Nama" name="Nama" required autofocus style="pointer-events: none; background-color: #e9ecef; color: #6c757d;">
                                            <option value="">-- Pilih Nama Karyawan --</option>
                                            @foreach($user as $u)
                                                <option value="{{ $u->id }}" {{ old('Nama', $absensi->Nama) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                            @endforeach
                                        </select>

                                        @error('Nama') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>


                                    <div class="mb-4">
                                        <label for="Divisi" class="form-label fw-semibold"><i class="ti ti-building me-1 text-primary"></i> Divisi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('Divisi') is-invalid @enderror" id="Divisi" name="Divisi" value="{{ old('Divisi', $absensi->Divisi) }}" placeholder="Contoh: IT, HRD, Gudang" required>
                                        @error('Divisi') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="NoHp" class="form-label fw-semibold"><i class="ti ti-phone me-1 text-primary"></i> No. HP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('NoHp') is-invalid @enderror" id="NoHp" name="NoHp" value="{{ old('NoHp', $absensi->NoHp) }}" placeholder="08xxxxxxxxxx" required>
                                        @error('NoHp') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Data Absensi -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="Tanggal" class="form-label fw-semibold"><i class="ti ti-calendar me-1 text-primary"></i> Tanggal <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('Tanggal') is-invalid @enderror" id="Tanggal" name="Tanggal" value="{{ old('Tanggal', $absensi->Tanggal ? \Carbon\Carbon::parse($absensi->Tanggal)->format('Y-m-d') : date('Y-m-d')) }}" required>
                                        @error('Tanggal') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="Status" class="form-label fw-semibold"><i class="ti ti-clipboard-check me-1 text-primary"></i> Status Kehadiran <span class="text-danger">*</span></label>
                                        <select class="form-select @error('Status') is-invalid @enderror" id="Status" name="Status" required>
                                            <option value="H" {{ old('Status', $absensi->Status) == 'H' ? 'selected' : '' }}>Hadir (H)</option>
                                            <option value="I" {{ old('Status', $absensi->Status) == 'I' ? 'selected' : '' }}>Izin (I)</option>
                                            <option value="S" {{ old('Status', $absensi->Status) == 'S' ? 'selected' : '' }}>Sakit (S)</option>
                                            <option value="TK" {{ old('Status', $absensi->Status) == 'TK' ? 'selected' : '' }}>Tanpa Keterangan (TK)</option>
                                        </select>
                                        @error('Status') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-6 mb-4">
                                            <label for="JamHadir" class="form-label fw-semibold"><i class="ti ti-login me-1 text-primary"></i> Jam Hadir</label>
                                            <input type="time" class="form-control @error('JamHadir') is-invalid @enderror" id="JamHadir" name="JamHadir" value="{{ old('JamHadir', $absensi->JamHadir) }}">
                                            @error('JamHadir') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-6 mb-4">
                                            <label for="JamPulang" class="form-label fw-semibold"><i class="ti ti-logout me-1 text-primary"></i> Jam Pulang</label>
                                            <input type="time" class="form-control @error('JamPulang') is-invalid @enderror" id="JamPulang" name="JamPulang" value="{{ old('JamPulang', $absensi->JamPulang) }}">
                                            @error('JamPulang') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="Lembur" class="form-label fw-semibold"><i class="ti ti-hourglass me-1 text-primary"></i> Lembur? <span class="text-danger">*</span></label>
                                        <select class="form-select @error('Lembur') is-invalid @enderror" id="Lembur" name="Lembur" required>
                                            <option value="N" {{ old('Lembur', $absensi->Lembur) == 'N' ? 'selected' : '' }}>Tidak</option>
                                            <option value="Y" {{ old('Lembur', $absensi->Lembur) == 'Y' ? 'selected' : '' }}>Ya</option>
                                        </select>
                                        @error('Lembur') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Conditional Lembur -->
                                    <div id="LemburDetails" style="display: none;">
                                        <div class="row">
                                            <div class="col-6 mb-4">
                                                <label for="MulaiLembur" class="form-label fw-semibold"><i class="ti ti-play me-1 text-primary"></i> Mulai Lembur</label>
                                                <input type="text" class="form-control @error('MulaiLembur') is-invalid @enderror" id="MulaiLembur" name="MulaiLembur" value="{{ old('MulaiLembur', $absensi->MulaiLembur) }}" placeholder="Cth: 17:00">
                                                @error('MulaiLembur') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-6 mb-4">
                                                <label for="SelesaiLembur" class="form-label fw-semibold"><i class="ti ti-stop me-1 text-primary"></i> Selesai Lembur</label>
                                                <input type="text" class="form-control @error('SelesaiLembur') is-invalid @enderror" id="SelesaiLembur" name="SelesaiLembur" value="{{ old('SelesaiLembur', $absensi->SelesaiLembur) }}" placeholder="Cth: 20:00">
                                                @error('SelesaiLembur') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Update
                                </button>
                                <a href="{{ route('absensi.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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
        const lemburSelect = document.getElementById('Lembur');
        const lemburDetails = document.getElementById('LemburDetails');
        const inputMulai = document.getElementById('MulaiLembur');
        const inputSelesai = document.getElementById('SelesaiLembur');

        function toggleLembur() {
            if (lemburSelect.value === 'Y') {
                lemburDetails.style.display = 'block';
                inputMulai.setAttribute('required', 'required');
                inputSelesai.setAttribute('required', 'required');
            } else {
                lemburDetails.style.display = 'none';
                inputMulai.removeAttribute('required');
                inputSelesai.removeAttribute('required');
                inputMulai.value = '';
                inputSelesai.value = '';
            }
        }

        // Jalankan saat load (untuk handle old input dan/atau edit value)
        toggleLembur();

        // Jalankan saat user mengganti pilihan
        lemburSelect.addEventListener('change', toggleLembur);
    });
</script>
@endsection
