@extends('layouts.app')

@section('title', 'Tambah Pekerjaan Kurir')

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

<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 h3 fw-bold text-dark">Tambah Pekerjaan Kurir</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pekerjaan-kurir.index') }}" class="text-decoration-none">Pekerjaan Kurir</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
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
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-truck me-2"></i>Form Tambah Pekerjaan Kurir
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('pekerjaan-kurir.store') }}" method="POST" enctype="multipart/form-data" id="FormPekerjaanKurir">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Tanggal -->
                                    <div class="mb-4">
                                        <label for="Tanggal" class="form-label fw-semibold">
                                            <i class="bi bi-calendar-event me-1 text-primary"></i> Tanggal <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('Tanggal') is-invalid @enderror"
                                            id="Tanggal"
                                            name="Tanggal"
                                            value="{{ old('Tanggal', date('Y-m-d')) }}"
                                            required>
                                        <div class="form-text text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Pilih tanggal pekerjaan.
                                        </div>
                                        @error('Tanggal')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Jam -->
                                    <div class="mb-4">
                                        <label for="Jam" class="form-label fw-semibold">
                                            <i class="bi bi-clock me-1 text-primary"></i> Jam <span class="text-danger">*</span>
                                        </label>
                                        <input type="time"
                                            class="form-control @error('Jam') is-invalid @enderror"
                                            id="Jam"
                                            name="Jam"
                                            value="{{ old('Jam') }}"
                                            required>
                                        <div class="form-text text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Pilih jam pekerjaan.
                                        </div>
                                        @error('Jam')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Pekerjaan -->
                                    <div class="mb-4">
                                        <label for="Pekerjaan" class="form-label fw-semibold">
                                            <i class="bi bi-briefcase me-1 text-primary"></i> Pekerjaan <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            class="form-select @error('Pekerjaan') is-invalid @enderror"
                                            id="Pekerjaan"
                                            name="Pekerjaan"
                                            required>
                                            <option value="" disabled {{ old('Pekerjaan') ? '' : 'selected' }}>Pilih jenis pekerjaan</option>
                                            <option value="Ambil Paket" {{ old('Pekerjaan') == 'Ambil Paket' ? 'selected' : '' }}>Ambil Paket</option>
                                            <option value="Antar Paket" {{ old('Pekerjaan') == 'Antar Paket' ? 'selected' : '' }}>Antar Paket</option>
                                            <option value="Lain-lain" {{ old('Pekerjaan') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                                        </select>
                                        <div class="form-text text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Pilih jenis pekerjaan kurir: Ambil Paket, Antar Paket, atau Lain-lain.
                                        </div>
                                        @error('Pekerjaan')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>


                                    <!-- DariLokasi -->
                                    <div class="mb-4">
                                        <label for="DariLokasi" class="form-label fw-semibold">
                                            <i class="bi bi-geo me-1 text-primary"></i> Dari Lokasi <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('DariLokasi') is-invalid @enderror"
                                            id="DariLokasi"
                                            name="DariLokasi"
                                            value="{{ old('DariLokasi') }}"
                                            placeholder="Lokasi asal pengambilan"
                                            required>
                                        <div class="form-text text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Alamat/lokasi asal pekerjaan kurir.
                                        </div>
                                        @error('DariLokasi')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Tujuan -->
                                    <div class="mb-4">
                                        <label for="Tujuan" class="form-label fw-semibold">
                                            <i class="bi bi-geo-alt me-1 text-primary"></i> Tujuan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('Tujuan') is-invalid @enderror"
                                            id="Tujuan"
                                            name="Tujuan"
                                            value="{{ old('Tujuan') }}"
                                            placeholder="Alamat tujuan pengantaran"
                                            required>
                                        <div class="form-text text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Alamat/lokasi tujuan pekerjaan kurir.
                                        </div>
                                        @error('Tujuan')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- JumlahPaket -->
                                    <div class="mb-4">
                                        <label for="JumlahPaket" class="form-label fw-semibold">
                                            <i class="bi bi-box-seam me-1 text-primary"></i> Jumlah Paket <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                            class="form-control @error('JumlahPaket') is-invalid @enderror"
                                            id="JumlahPaket"
                                            name="JumlahPaket"
                                            min="1"
                                            value="{{ old('JumlahPaket') }}"
                                            placeholder="Jumlah paket/berkas"
                                            required>
                                        <div class="form-text text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Jumlah paket/berkas yang diantar.
                                        </div>
                                        @error('JumlahPaket')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Durasi -->
                                    <div class="mb-4">
                                        <label for="Durasi" class="form-label fw-semibold">
                                            <i class="bi bi-hourglass me-1 text-primary"></i> Durasi (Menit) <span class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                            class="form-control @error('Durasi') is-invalid @enderror"
                                            id="Durasi"
                                            name="Durasi"
                                            min="1"
                                            value="{{ old('Durasi') }}"
                                            placeholder="Durasi pekerjaan (menit)"
                                            required>
                                        <div class="form-text text-muted mt-1">
                                            <i class="bi bi-info-circle me-1"></i>Durasi pekerjaan dalam menit.
                                        </div>
                                        @error('Durasi')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan (Quill Editor) -->
                            <div class="mb-4">
                                <label for="Keterangan" class="form-label fw-semibold">
                                    <i class="bi bi-chat-left-text me-1 text-primary"></i> Keterangan
                                </label>
                                <textarea id="Keterangan" name="Keterangan" class="d-none">{{ old('Keterangan') }}</textarea>
                                <div id="QuillEditor" class="bg-white"></div>
                                <div class="form-text text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Keterangan tambahan pekerjaan kurir. Opsional.
                                </div>
                                @error('Keterangan')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- BuktiFoto -->
                            <div class="mb-4">
                                <label for="BuktiFoto" class="form-label fw-semibold">
                                    <i class="bi bi-image me-1 text-primary"></i> Bukti Foto
                                </label>
                                <input type="file"
                                    class="form-control @error('BuktiFoto') is-invalid @enderror"
                                    id="BuktiFoto"
                                    name="BuktiFoto"
                                    accept="image/*">
                                <div class="form-text text-muted mt-1">
                                    <i class="bi bi-info-circle me-1"></i>Upload foto sebagai bukti pekerjaan.
                                </div>
                                @error('BuktiFoto')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div id="BuktiFotoPreview" class="mt-2"></div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="bi bi-device-floppy me-2"></i>Simpan
                                </button>
                                <a href="{{ route('pekerjaan-kurir.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
                                    <i class="bi bi-x me-2"></i>Batal
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
            // Quill Editor for Keterangan
            var QuillInstance = new Quill('#QuillEditor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                },
                placeholder: 'Tulis keterangan tambahan di sini...'
            });

            var KeteranganField = document.querySelector('textarea[name="Keterangan"]');
            QuillInstance.on('text-change', function() {
                KeteranganField.value = QuillInstance.root.innerHTML;
            });

            var InitialValue = `{!! old('Keterangan', '') !!}`;
            if (InitialValue) {
                QuillInstance.root.innerHTML = InitialValue;
                KeteranganField.value = InitialValue;
            }

            // Preview untuk BuktiFoto
            var BuktiFotoInput = document.getElementById('BuktiFoto');
            var BuktiFotoPreview = document.getElementById('BuktiFotoPreview');
            if(BuktiFotoInput) {
                BuktiFotoInput.addEventListener('change', function(event) {
                    BuktiFotoPreview.innerHTML = '';
                    if (BuktiFotoInput.files && BuktiFotoInput.files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            BuktiFotoPreview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-height:200px;">';
                        }
                        reader.readAsDataURL(BuktiFotoInput.files[0]);
                    }
                });
            }

            // Validation
            var FormPekerjaanKurir = document.getElementById('FormPekerjaanKurir');
            FormPekerjaanKurir.addEventListener('submit', function(Event) {
                KeteranganField.value = QuillInstance.root.innerHTML;
            });
        });
    </script>
@endpush
