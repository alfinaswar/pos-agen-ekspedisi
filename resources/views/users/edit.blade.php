@extends('layouts.app')

@section('title', 'Edit User')

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
                <h1 class="m-0 h3 fw-bold text-dark">Edit User</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Form Content -->
<div class="content pb-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-10 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-edit me-2"></i>Form Edit User
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <!-- Nama -->
                                    <div class="mb-4">
                                        <label for="name" class="form-label fw-semibold">
                                            <i class="ti ti-user me-1 text-primary"></i> Nama Lengkap <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $user->name) }}"
                                               placeholder="Masukkan nama lengkap"
                                               required
                                               autofocus>
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Nama akan digunakan untuk login dan identifikasi.
                                        </div>
                                        @error('name')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold">
                                            <i class="ti ti-mail me-1 text-primary"></i> Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email', $user->email) }}"
                                               placeholder="contoh@email.com"
                                               required>
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Pastikan email aktif dan belum terdaftar di sistem.
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Divisi -->
                                    <div class="mb-4">
                                        <label for="divisi" class="form-label fw-semibold">
                                            <i class="ti ti-building me-1 text-primary"></i> Divisi
                                        </label>
                                        <input type="text"
                                               class="form-control @error('divisi') is-invalid @enderror"
                                               id="divisi"
                                               name="divisi"
                                               value="{{ old('divisi', $user->divisi) }}"
                                               placeholder="Misal: Keuangan, Operasional, HRD">
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Optional. Diisi bila ingin mengelompokkan user berdasarkan divisi.
                                        </div>
                                        @error('divisi')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Nomor HP -->
                                    <div class="mb-4">
                                        <label for="no_hp" class="form-label fw-semibold">
                                            <i class="ti ti-phone me-1 text-primary"></i> No HP
                                        </label>
                                        <input type="text"
                                               class="form-control @error('no_hp') is-invalid @enderror"
                                               id="no_hp"
                                               name="no_hp"
                                               value="{{ old('no_hp', $user->no_hp) }}"
                                               placeholder="Contoh: 081212345678">
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Optional. Bisa diisi untuk kontak user.
                                        </div>
                                        @error('no_hp')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <!-- Password (Opsional) -->
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-semibold">
                                            <i class="ti ti-lock me-1 text-primary"></i> Password Baru
                                        </label>
                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               placeholder="Kosongkan jika tidak ingin mengubah">
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Kosongkan jika tidak ingin mengubah password.
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Konfirmasi Password (Opsional) -->
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label fw-semibold">
                                            <i class="ti ti-lock-check me-1 text-primary"></i> Konfirmasi Password Baru
                                        </label>
                                        <input type="password"
                                               class="form-control"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Ulangi password baru">
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Ketik ulang password baru yang sama persis.
                                        </div>
                                    </div>

                                    <!-- Role -->
                                    <div class="mb-4">
                                        <label for="role" class="form-label fw-semibold">
                                            <i class="ti ti-shield me-1 text-primary"></i> Role / Hak Akses <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('role') is-invalid @enderror"
                                                id="role"
                                                name="role"
                                                required>
                                            <option value="" disabled {{ old('role', $user->role) ? '' : 'selected' }}>-- Pilih Role --</option>
                                            <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin - Akses penuh semua menu</option>
                                            <option value="Leader" {{ old('role', $user->role) == 'Leader' ? 'selected' : '' }}>Leader - Lihat laporan & rekap</option>
                                            <option value="Kasir" {{ old('role', $user->role) == 'Kasir' ? 'selected' : '' }}>Kasir - Hanya input transaksi & lihat laporan</option>
                                            <option value="Viewer" {{ old('role', $user->role) == 'Viewer' ? 'selected' : '' }}>Viewer - Hanya lihat laporan</option>
                                        </select>

                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Tentukan tingkat akses pengguna di sistem.
                                        </div>
                                        @error('role')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Info Tambahan -->
                            <div class="alert alert-info d-flex align-items-center mb-4">
                                <i class="ti ti-info-circle me-2 fs-5"></i>
                                <div>
                                    <small>
                                        <strong>Dibuat:</strong> {{ $user->created_at->format('d M Y, H:i') }} |
                                        <strong>Terakhir Diupdate:</strong> {{ $user->updated_at->format('d M Y, H:i') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Perbarui Data
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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
