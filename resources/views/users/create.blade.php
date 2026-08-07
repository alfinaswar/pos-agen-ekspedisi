@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }
    .error-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
    .img-preview { max-height: 150px; object-fit: cover; border-radius: 8px; border: 2px dashed #dee2e6; display: none; margin-top: 10px; }
</style>

<!-- Content Header dengan Breadcrumb -->
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0 h3 fw-bold text-dark">Tambah User</h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
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
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-user-plus me-2"></i>Form Tambah User</h5>
                    </div>

                    <div class="card-body p-4">
                        <!-- TAMBAHKAN enctype="multipart/form-data" DI SINI -->
                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <!-- Nama -->
                                    <div class="mb-4">
                                        <label for="name" class="form-label fw-semibold"><i class="ti ti-user me-1 text-primary"></i> Nama Lengkap <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="name"
                                            name="name"
                                            value="{{ old('name') }}"
                                            required
                                            autofocus
                                            placeholder="Nama Lengkap"
                                        >
                                        @error('name') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold"><i class="ti ti-mail me-1 text-primary"></i> Email <span class="text-danger">*</span></label>
                                        <input
                                            type="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            required
                                            placeholder="nama@email.com"
                                        >
                                        @error('email') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Divisi -->
                                    <div class="mb-4">
                                        <label for="divisi" class="form-label fw-semibold"><i class="ti ti-building me-1 text-primary"></i> Divisi</label>
                                        <select class="form-select @error('divisi') is-invalid @enderror" id="divisi" name="divisi">
                                            <option value="">- Pilih Divisi -</option>
                                            @foreach($divisi as $d)
                                                <option value="{{ $d->id }}" {{ old('divisi') == $d->id ? 'selected' : '' }}>{{ $d->Nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('divisi') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Nomor HP -->
                                    <div class="mb-4">
                                        <label for="no_hp" class="form-label fw-semibold"><i class="ti ti-phone me-1 text-primary"></i> No HP</label>
                                        <input
                                            type="text"
                                            class="form-control @error('no_hp') is-invalid @enderror"
                                            id="no_hp"
                                            name="no_hp"
                                            value="{{ old('no_hp') }}"
                                            placeholder="08xxxxxxxxxx"
                                        >
                                        @error('no_hp') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <!-- Password -->
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-semibold"><i class="ti ti-lock me-1 text-primary"></i> Password <span class="text-danger">*</span></label>
                                        <input
                                            type="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            id="password"
                                            name="password"
                                            required
                                            placeholder="Minimal 8 karakter"
                                        >
                                        @error('password') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Konfirmasi Password -->
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label fw-semibold"><i class="ti ti-lock-check me-1 text-primary"></i> Konfirmasi Password <span class="text-danger">*</span></label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            required
                                            placeholder="Ulangi password"
                                        >
                                    </div>

                                    <!-- Role -->
                                    <div class="mb-4">
                                        <label for="role" class="form-label fw-semibold"><i class="ti ti-shield me-1 text-primary"></i> Role / Hak Akses <span class="text-danger">*</span></label>
                                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                            <option value="" disabled selected>-- Pilih Role --</option>
                                            <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="Leader" {{ old('role') == 'Leader' ? 'selected' : '' }}>Leader</option>
                                            <option value="Kasir" {{ old('role') == 'Kasir' ? 'selected' : '' }}>Kasir</option>
                                            <option value="Finance" {{ old('role') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                        </select>
                                        @error('role') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- ✅ TAMBAHAN: Upload Foto Profil -->
                                    <div class="mb-4">
                                        <label for="foto_profil" class="form-label fw-semibold"><i class="ti ti-camera me-1 text-primary"></i> Foto Profil</label>
                                        <input
                                            type="file"
                                            class="form-control @error('foto_profil') is-invalid @enderror"
                                            id="foto_profil"
                                            name="foto_profil"
                                            accept="image/*"
                                            onchange="previewImage(this, 'preview_profil')"
                                            placeholder="Pilih file foto profil"
                                        >
                                        <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Format: JPG, PNG. Maksimal 2MB.</div>
                                        <img id="preview_profil" class="img-preview mt-2 w-100">
                                        @error('foto_profil') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>

                                    <!-- ✅ TAMBAHAN: Upload Foto KTP -->
                                    <div class="mb-4">
                                        <label for="foto_ktp" class="form-label fw-semibold"><i class="ti ti-id me-1 text-primary"></i> Foto KTP</label>
                                        <input
                                            type="file"
                                            class="form-control @error('foto_ktp') is-invalid @enderror"
                                            id="foto_ktp"
                                            name="foto_ktp"
                                            accept="image/*"
                                            onchange="previewImage(this, 'preview_ktp')"
                                            placeholder="Pilih file foto KTP"
                                        >
                                        <div class="form-text text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Format: JPG, PNG. Maksimal 2MB.</div>
                                        <img id="preview_ktp" class="img-preview mt-2 w-100">
                                        @error('foto_ktp') <div class="invalid-feedback d-block error-fade-in"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold"><i class="ti ti-device-floppy me-2"></i>Simpan User</button>
                                <a href="{{ route('users.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold"><i class="ti ti-x me-2"></i>Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script sederhana untuk preview gambar sebelum di-upload
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endsection
