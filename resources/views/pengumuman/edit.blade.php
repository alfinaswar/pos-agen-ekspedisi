@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .error-fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
    }
    /* Kustomisasi Quill agar menyatu dengan Bootstrap 5 */
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        border-color: #dee2e6;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
        border-color: #dee2e6;
        font-family: inherit;
        font-size: 0.95rem;
    }
    .ql-editor {
        min-height: 150px;
    }
</style>

<!-- Content Header dengan Breadcrumb -->
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 h3 fw-bold text-dark">Edit Pengumuman</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pengumuman.index') }}" class="text-decoration-none">Pengumuman</a></li>
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
                            <i class="ti ti-megaphone me-2"></i>Form Edit Pengumuman
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('pengumuman.update', $Pengumuman->id) }}" method="POST" enctype="multipart/form-data" id="FormPengumuman">
                            @csrf
                            @method('PUT')

                            <!-- Judul Pengumuman -->
                            <div class="mb-4">
                                <label for="Judul" class="form-label fw-semibold">
                                    <i class="ti ti-heading me-1 text-primary"></i> Judul Pengumuman <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('Judul') is-invalid @enderror"
                                       id="Judul"
                                       name="Judul"
                                       value="{{ old('Judul', $Pengumuman->Judul) }}"
                                       placeholder="Contoh: Maintenance Sistem, Cuti Bersama"
                                       required
                                       autofocus>
                                <div class="form-text text-muted mt-1">
                                    <i class="ti ti-info-circle me-1"></i>Masukkan judul singkat untuk pengumuman.
                                </div>
                                @error('Judul')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Isi Pengumuman (Quill Editor) -->
                            <div class="mb-4">
                                <label for="Isi" class="form-label fw-semibold">
                                    <i class="ti ti-file-description me-1 text-primary"></i> Isi Pengumuman <span class="text-danger">*</span>
                                </label>

                                <textarea id="Isi" name="Isi" class="d-none">{{ old('Isi', $Pengumuman->Isi) }}</textarea>
                                <div id="QuillEditor" class="bg-white"></div>

                                <div class="form-text text-muted mt-1">
                                    <i class="ti ti-info-circle me-1"></i>Jelaskan secara rinci isi pengumuman.
                                </div>
                                @error('Isi')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- UPLOAD GAMBAR (NULLABLE/OPSIONAL) -->
                            <div class="mb-4">
                                <label for="Gambar" class="form-label fw-semibold">
                                    <i class="ti ti-photo me-1 text-primary"></i> Gambar Pendukung
                                </label>
                                <input type="file"
                                       class="form-control @error('Gambar') is-invalid @enderror"
                                       id="Gambar"
                                       name="Gambar"
                                       accept="image/png, image/jpeg, image/jpg, image/webp">
                                <div class="form-text text-muted mt-1">
                                    <i class="ti ti-info-circle me-1"></i>Opsional. Format: JPG, PNG, WEBP. Maksimal 2MB.
                                </div>

                                <!-- Area Pratinjau Gambar & Gambar Eksisting -->
                                <div class="mt-3 {{ $Pengumuman->Gambar || old('Gambar') ? '' : 'd-none' }}" id="PreviewContainer">
                                    <label class="form-label small text-muted mb-1">Pratinjau:</label>
                                    <div class="position-relative d-inline-block">
                                        @if($Pengumuman->Gambar && !old('Gambar'))
                                            <img id="ImagePreview" src="{{ asset('storage/'.$Pengumuman->Gambar) }}" alt="Pratinjau Gambar" class="img-fluid rounded border bg-light" style="max-height: 200px; max-width: 100%; object-fit: contain;">
                                        @else
                                            <img id="ImagePreview" src="#" alt="Pratinjau Gambar" class="img-fluid rounded border bg-light" style="max-height: 200px; max-width: 100%; object-fit: contain;">
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm" id="RemoveImageBtn" title="Hapus Gambar">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                </div>
                                @if($Pengumuman->Gambar && !old('Gambar'))
                                    <input type="hidden" name="ExistingGambar" value="{{ $Pengumuman->Gambar }}">
                                @endif

                                @error('Gambar')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Tanggal Pengumuman -->
                            <div class="mb-4">
                                <label for="Tanggal" class="form-label fw-semibold">
                                    <i class="ti ti-calendar-event me-1 text-primary"></i> Tanggal Pengumuman <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                       class="form-control @error('Tanggal') is-invalid @enderror"
                                       id="Tanggal"
                                       name="Tanggal"
                                       value="{{ old('Tanggal', $Pengumuman->Tanggal ?? date('Y-m-d')) }}"
                                       required>
                                <div class="form-text text-muted mt-1">
                                    <i class="ti ti-info-circle me-1"></i>Pilih tanggal yang sesuai untuk pengumuman ini.
                                </div>
                                @error('Tanggal')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Perbarui
                                </button>
                                <a href="{{ route('pengumuman.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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

@push('scripts')
    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Quill Editor
            var QuillInstance = new Quill('#QuillEditor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                },
                placeholder: 'Tulis detail pengumuman di sini...'
            });

            // 2. Sinkronisasi Quill ke Hidden Textarea
            var IsiField = document.querySelector('textarea[name="Isi"]');
            QuillInstance.on('text-change', function() {
                IsiField.value = QuillInstance.root.innerHTML;
            });

            var InitialValue = `{!! old('Isi', $Pengumuman->Isi ?? '') !!}`;
            if (InitialValue) {
                QuillInstance.root.innerHTML = InitialValue;
                IsiField.value = InitialValue;
            }

            // LOGIKA UPLOAD & PREVIEW GAMBAR
            const InputGambar = document.getElementById('Gambar');
            const PreviewContainer = document.getElementById('PreviewContainer');
            const ImagePreview = document.getElementById('ImagePreview');
            const RemoveImageBtn = document.getElementById('RemoveImageBtn');

            // Jika sudah gambar eksisting, biarkan preview eksisting
            // Saat user memilih file
            InputGambar.addEventListener('change', function() {
                const File = this.files[0];
                if (File) {
                    // Validasi ukuran file di frontend (Maksimal 2MB = 2 * 1024 * 1024 bytes)
                    if (File.size > 2 * 1024 * 1024) {
                        alert('Ukuran gambar terlalu besar! Maksimal 2MB.');
                        this.value = ''; // Reset input
                        ResetPreview();
                        return;
                    }

                    const Reader = new FileReader();
                    Reader.onload = function(Event) {
                        ImagePreview.src = Event.target.result;
                        PreviewContainer.classList.remove('d-none');
                    }
                    Reader.readAsDataURL(File);
                } else {
                    if ('{{ $Pengumuman->Gambar }}') {
                        // Jika ada gambar lama
                        ImagePreview.src = '{{ asset("storage/" . $Pengumuman->Gambar) }}';
                        PreviewContainer.classList.remove('d-none');
                    } else {
                        ResetPreview();
                    }
                }
            });

            // Saat user klik tombol hapus (X) pada preview
            RemoveImageBtn.addEventListener('click', function() {
                InputGambar.value = ''; // Kosongkan input file
                ResetPreview();
            });

            // Fungsi helper untuk menyembunyikan preview
            function ResetPreview() {
                ImagePreview.src = '#';
                PreviewContainer.classList.add('d-none');
            }

            // 4. Validasi Manual Sebelum Submit
            var FormPengumuman = document.getElementById('FormPengumuman');
            FormPengumuman.addEventListener('submit', function(Event) {
                IsiField.value = QuillInstance.root.innerHTML;

                var PlainText = QuillInstance.getText().trim();
                if (PlainText === '') {
                    Event.preventDefault();
                    alert('Isi pengumuman tidak boleh kosong!');
                    QuillInstance.focus();
                }
            });
        });
    </script>
@endpush
