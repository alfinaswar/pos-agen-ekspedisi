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
                        <!-- ✅ TAMBAHKAN enctype="multipart/form-data" DI SINI -->
                        <form action="{{ route('absensi.update', $absensi->id) }}" method="POST" enctype="multipart/form-data" id="FormAbsensiEdit">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Kolom Kiri: Data Karyawan -->
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="NamaDisplay" class="form-label fw-semibold"><i class="ti ti-user me-1 text-primary"></i> Nama <span class="text-danger">*</span></label>
                                        <!-- Display Only -->
                                        <select class="form-select" id="NamaDisplay" disabled style="background-color: #e9ecef; color: #6c757d;">
                                            @foreach($user as $u)
                                                @if($u->id == old('UserId', $absensi->Nama))
                                                    <option value="{{ $u->id }}" selected>{{ $u->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <!-- Hidden Input untuk dikirim ke database (Perbaikan dari name="Nama") -->
                                        <input type="hidden" name="Nama" value="{{ old('Nama', $absensi->Nama) }}">

                                        @error('Nama') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="Divisi" class="form-label fw-semibold"><i class="ti ti-building me-1 text-primary"></i> Divisi <span class="text-danger">*</span></label>
                                        <select class="form-select @error('Divisi') is-invalid @enderror" id="Divisi" name="Divisi" required style="pointer-events: none; background-color: #e9ecef;">
                                            @foreach($divisi as $item)
                                                <option value="{{ $item->id }}" {{ old('Divisi', $absensi->Divisi) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->Nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Divisi') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="NoHp" class="form-label fw-semibold"><i class="ti ti-phone me-1 text-primary"></i> No. HP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('NoHp') is-invalid @enderror" id="NoHp" name="NoHp" value="{{ old('NoHp', $absensi->NoHp) }}" placeholder="08xxxxxxxxxx" readonly required>
                                        @error('NoHp') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Data Absensi & Foto -->
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
                                            <input type="time" class="form-control @error('JamHadir') is-invalid @enderror" id="JamHadir" name="JamHadir" value="{{ old('JamHadir', $absensi->JamHadir ? substr($absensi->JamHadir, 0, 5) : '') }}">
                                            @error('JamHadir') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-6 mb-4">
                                            <label for="JamPulang" class="form-label fw-semibold"><i class="ti ti-logout me-1 text-primary"></i> Jam Pulang</label>
                                            <input type="time" class="form-control @error('JamPulang') is-invalid @enderror" id="JamPulang" name="JamPulang" value="{{ old('JamPulang', $absensi->JamPulang ? substr($absensi->JamPulang, 0, 5) : '') }}">
                                            @error('JamPulang') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <!-- ✅ FOTO ABSEN MASUK -->
                                    <div class="mb-4">
                                        <label for="FotoAbsenMasuk" class="form-label fw-semibold"><i class="ti ti-camera me-1 text-primary"></i> Foto Absen Masuk</label>

                                        @if(!empty($absensi->FotoAbsenMasuk))
                                            <div class="mb-2">
                                                <small class="text-muted d-block mb-1">Foto Saat Ini:</small>
                                                <a href="{{ asset('storage/' . $absensi->FotoAbsenMasuk) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $absensi->FotoAbsenMasuk) }}" alt="Foto Absen Masuk" class="img-thumbnail shadow-sm" style="max-height: 100px;">
                                                </a>
                                            </div>
                                        @endif

                                        <input type="file" class="form-control @error('FotoAbsenMasuk') is-invalid @enderror" id="FotoAbsenMasuk" name="FotoAbsenMasuk" accept="image/*">
                                        <div class="form-text text-warning mt-1 fw-semibold d-flex align-items-start">
                                            <i class="ti ti-alert-triangle me-1 mt-1"></i>
                                            <span>Foto wajib menggunakan <strong>GPS Map Camera</strong>, supaya terlihat lokasi, tanggal, dan jam saat absensi.</span>
                                        </div>
                                        <div id="PreviewMasuk" class="mt-2 d-none">
                                            <small class="text-primary fw-semibold d-block mb-1">Preview Foto Baru:</small>
                                            <img id="ImgPreviewMasuk" src="#" alt="Preview Masuk" class="img-thumbnail shadow-sm" style="max-height: 100px; max-width: 100%;">
                                        </div>
                                        @error('FotoAbsenMasuk') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- ✅ FOTO ABSEN KELUAR -->
                                    <div class="mb-4">
                                        <label for="FotoAbsenKeluar" class="form-label fw-semibold"><i class="ti ti-camera-off me-1 text-primary"></i> Foto Absen Keluar</label>

                                        @if(!empty($absensi->FotoAbsenKeluar))
                                            <div class="mb-2">
                                                <small class="text-muted d-block mb-1">Foto Saat Ini:</small>
                                                <a href="{{ asset('storage/' . $absensi->FotoAbsenKeluar) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $absensi->FotoAbsenKeluar) }}" alt="Foto Absen Keluar" class="img-thumbnail shadow-sm" style="max-height: 100px;">
                                                </a>
                                            </div>
                                        @endif

                                        <input type="file" class="form-control @error('FotoAbsenKeluar') is-invalid @enderror" id="FotoAbsenKeluar" name="FotoAbsenKeluar" accept="image/*">
                                        <div class="form-text text-warning mt-1 fw-semibold d-flex align-items-start">
                                            <i class="ti ti-alert-triangle me-1 mt-1"></i>
                                            <span>Foto wajib menggunakan <strong>GPS Map Camera</strong>, supaya terlihat lokasi, tanggal, dan jam saat absensi.</span>
                                        </div>
                                        <div id="PreviewKeluar" class="mt-2 d-none">
                                            <small class="text-primary fw-semibold d-block mb-1">Preview Foto Baru:</small>
                                            <img id="ImgPreviewKeluar" src="#" alt="Preview Keluar" class="img-thumbnail shadow-sm" style="max-height: 100px; max-width: 100%;">
                                        </div>
                                        @error('FotoAbsenKeluar') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
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
                                                <!-- ✅ DIUBAH MENJADI type="time" -->
                                                <input type="time" class="form-control @error('MulaiLembur') is-invalid @enderror" id="MulaiLembur" name="MulaiLembur" value="{{ old('MulaiLembur', $absensi->MulaiLembur ? substr($absensi->MulaiLembur, 0, 5) : '') }}">
                                                @error('MulaiLembur') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-6 mb-4">
                                                <label for="SelesaiLembur" class="form-label fw-semibold"><i class="ti ti-stop me-1 text-primary"></i> Selesai Lembur</label>
                                                <!-- ✅ DIUBAH MENJADI type="time" -->
                                                <input type="time" class="form-control @error('SelesaiLembur') is-invalid @enderror" id="SelesaiLembur" name="SelesaiLembur" value="{{ old('SelesaiLembur', $absensi->SelesaiLembur ? substr($absensi->SelesaiLembur, 0, 5) : '') }}">
                                                @error('SelesaiLembur') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="AlasanLembur" class="form-label fw-semibold">
                                                <i class="ti ti-message-2 me-1 text-primary"></i> Alasan Lembur <span class="text-danger">*</span>
                                            </label>
                                            <textarea class="form-control @error('AlasanLembur') is-invalid @enderror" id="AlasanLembur" name="AlasanLembur" rows="2" placeholder="Jelaskan alasan lembur...">{{ old('AlasanLembur', $absensi->AlasanLembur) }}</textarea>
                                            @error('AlasanLembur') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
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
        // 1. Logic Toggle Lembur (Strict PASCAL CASE)
        const LemburSelect = document.getElementById('Lembur');
        const LemburDetails = document.getElementById('LemburDetails');
        const InputMulai = document.getElementById('MulaiLembur');
        const InputSelesai = document.getElementById('SelesaiLembur');
        const AlasanLembur = document.getElementById('AlasanLembur');

        function ToggleLembur() {
            if (LemburSelect.value === 'Y') {
                LemburDetails.style.display = 'block';
                InputMulai.setAttribute('required', 'required');
                InputSelesai.setAttribute('required', 'required');
                if (AlasanLembur) AlasanLembur.setAttribute('required', 'required');
            } else {
                LemburDetails.style.display = 'none';
                InputMulai.removeAttribute('required');
                InputSelesai.removeAttribute('required');
                InputMulai.value = '';
                InputSelesai.value = '';
                if (AlasanLembur) {
                    AlasanLembur.removeAttribute('required');
                    AlasanLembur.value = '';
                }
            }
        }

        // Jalankan saat load (untuk handle old input dan edit value)
        ToggleLembur();

        // Jalankan saat user mengganti pilihan
        LemburSelect.addEventListener('change', ToggleLembur);

        // 2. Logic Preview Foto Absen Masuk
        const FotoAbsenMasuk = document.getElementById('FotoAbsenMasuk');
        const PreviewMasuk = document.getElementById('PreviewMasuk');
        const ImgPreviewMasuk = document.getElementById('ImgPreviewMasuk');

        if (FotoAbsenMasuk) {
            FotoAbsenMasuk.addEventListener('change', function(Event) {
                const File = this.files[0];
                if (File) {
                    const ReaderMasuk = new FileReader();
                    ReaderMasuk.onload = function(ReaderEvent) {
                        ImgPreviewMasuk.src = ReaderEvent.target.result;
                        PreviewMasuk.classList.remove('d-none');
                    }
                    ReaderMasuk.readAsDataURL(File);
                } else {
                    PreviewMasuk.classList.add('d-none');
                    ImgPreviewMasuk.src = '#';
                }
            });
        }

        // 3. Logic Preview Foto Absen Keluar
        const FotoAbsenKeluar = document.getElementById('FotoAbsenKeluar');
        const PreviewKeluar = document.getElementById('PreviewKeluar');
        const ImgPreviewKeluar = document.getElementById('ImgPreviewKeluar');

        if (FotoAbsenKeluar) {
            FotoAbsenKeluar.addEventListener('change', function(Event) {
                const File = this.files[0];
                if (File) {
                    const ReaderKeluar = new FileReader();
                    ReaderKeluar.onload = function(ReaderEvent) {
                        ImgPreviewKeluar.src = ReaderEvent.target.result;
                        PreviewKeluar.classList.remove('d-none');
                    }
                    ReaderKeluar.readAsDataURL(File);
                } else {
                    PreviewKeluar.classList.add('d-none');
                    ImgPreviewKeluar.src = '#';
                }
            });
        }
    });
</script>
@endsection
