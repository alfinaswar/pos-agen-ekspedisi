@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .error-fade-in {
        animation: fadeIn 0.3s ease-in-out forwards;
    }
    .preview-bukti {
        max-width: 100%;
        max-height: 150px;
        object-fit: contain;
        border: 1px dashed #ced4da;
        border-radius: 0.375rem;
        padding: 4px;
        background-color: #f8f9fa;
    }
</style>

<!-- Content Header dengan Breadcrumb -->
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 h3 fw-bold text-dark">Edit Transaksi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}" class="text-decoration-none">Transaksi</a></li>
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
                            <i class="ti ti-edit me-2"></i>Form Edit Transaksi
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <!-- Method PUT/PATCH untuk Update -->
                        <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">
                                    <!-- Kode Transaksi (Readonly) -->
                                    <div class="mb-4">
                                        <label for="KodeTransaksi" class="form-label fw-semibold">
                                            <i class="ti ti-hash me-1 text-primary"></i> Kode Transaksi
                                        </label>
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control bg-light"
                                                   id="KodeTransaksi"
                                                   value="{{ old('KodeTransaksi', $transaksi->KodeTransaksi) }}"
                                                   readonly>
                                            <span class="input-group-text bg-light border-start-0">
                                                <i class="ti ti-lock text-muted"></i>
                                            </span>
                                        </div>
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Kode transaksi tidak dapat diubah.
                                        </div>
                                    </div>

                                    <!-- Tanggal -->
                                    <div class="mb-4">
                                        <label for="Tanggal" class="form-label fw-semibold">
                                            <i class="ti ti-calendar me-1 text-primary"></i> Tanggal & Waktu <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local"
                                               class="form-control @error('Tanggal') is-invalid @enderror"
                                               id="Tanggal"
                                               name="Tanggal"
                                               value="{{ old('Tanggal', \Carbon\Carbon::parse($transaksi->Tanggal)->format('Y-m-d\TH:i')) }}"
                                               required>
                                        @error('Tanggal')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Ekspedisi -->
                                    <div class="mb-4">
                                        <label for="Ekspedisi" class="form-label fw-semibold">
                                            <i class="ti ti-truck me-1 text-primary"></i> Ekspedisi
                                        </label>
                                        <select class="form-select @error('Ekspedisi') is-invalid @enderror"
                                                id="Ekspedisi"
                                                name="Ekspedisi">
                                            <option value="">-- Pilih Ekspedisi --</option>
                                            @if(isset($ekspedisis))
                                                @foreach($ekspedisis as $exp)
                                                    <option value="{{ $exp->id }}" {{ old('Ekspedisi', $transaksi->Ekspedisi) == $exp->id ? 'selected' : '' }}>
                                                        {{ $exp->NamaEkspedisi }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <!-- Fallback jika tidak ada relasi, gunakan nilai string langsung -->
                                                <option value="{{ old('Ekspedisi', $transaksi->Ekspedisi) }}" selected>
                                                    {{ old('Ekspedisi', $transaksi->Ekspedisi) }}
                                                </option>
                                            @endif
                                        </select>
                                        @error('Ekspedisi')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- No. Resi -->
                                    <div class="mb-4">
                                        <label for="NoResi" class="form-label fw-semibold">
                                            <i class="ti ti-barcode me-1 text-primary"></i> Nomor Resi
                                        </label>
                                        <input type="text"
                                               class="form-control @error('NoResi') is-invalid @enderror"
                                               id="NoResi"
                                               name="NoResi"
                                               value="{{ old('NoResi', $transaksi->NoResi) }}"
                                               placeholder="Masukkan nomor resi pengiriman">
                                        @error('NoResi')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Kolom Kanan -->
                                <div class="col-md-6">
                                    <!-- Metode Pembayaran -->
                                    <div class="mb-4">
                                        <label for="Metode" class="form-label fw-semibold">
                                            <i class="ti ti-wallet me-1 text-primary"></i> Metode Pembayaran <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('Metode') is-invalid @enderror"
                                                id="Metode"
                                                name="Metode"
                                                required>
                                            <option value="Tunai" {{ old('Metode', $transaksi->Metode) == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                            <option value="Non-Tunai" {{ old('Metode', $transaksi->Metode) == 'Non-Tunai' ? 'selected' : '' }}>Non-Tunai</option>
                                        </select>
                                        @error('Metode')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Pendapatan (Format Rupiah) -->
                                    <div class="mb-4">
                                        <label for="PendapatanFormatted" class="form-label fw-semibold">
                                            <i class="ti ti-cash me-1 text-primary"></i> Pendapatan <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('Pendapatan') is-invalid @enderror"
                                               id="PendapatanFormatted"
                                               placeholder="Rp 0"
                                               autocomplete="off">
                                        <input type="hidden" name="Pendapatan" id="PendapatanRaw" value="{{ old('Pendapatan', $transaksi->Pendapatan) }}">

                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Ketik angka, format Rupiah akan otomatis muncul.
                                        </div>
                                        @error('Pendapatan')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Kode Bayar (Conditional) -->
                                    <div class="mb-4" id="KodeBayarWrapper" style="display: none;">
                                        <label for="KodeBayar" class="form-label fw-semibold">
                                            <i class="ti ti-receipt me-1 text-primary"></i> Kode Bayar <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('KodeBayar') is-invalid @enderror"
                                               id="KodeBayar"
                                               name="KodeBayar"
                                               value="{{ old('KodeBayar', $transaksi->KodeBayar) }}"
                                               placeholder="Contoh: 8801928374 (VA) atau QRIS">
                                        @error('KodeBayar')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Bukti Bayar (Conditional) -->
                                    <div class="mb-4" id="BuktiBayarWrapper" style="display: none;">
                                        <label for="BuktiBayar" class="form-label fw-semibold">
                                            <i class="ti ti-photo me-1 text-primary"></i> Bukti Pembayaran
                                        </label>

                                        <!-- Preview File Lama -->
                                        @if($transaksi->BuktiBayar)
                                            <div class="mb-2 p-2 bg-light rounded border">
                                                <p class="mb-1 small text-muted fw-semibold">File Saat Ini:</p>
                                                @php
                                                    $ext = pathinfo($transaksi->BuktiBayar, PATHINFO_EXTENSION);
                                                @endphp
                                                @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                                    <a href="{{ Storage::url($transaksi->BuktiBayar) }}" target="_blank">
                                                        <img src="{{ Storage::url($transaksi->BuktiBayar) }}" class="preview-bukti" alt="Bukti Bayar">
                                                    </a>
                                                @else
                                                    <a href="{{ Storage::url($transaksi->BuktiBayar) }}" target="_blank" class="btn btn-sm btn-outline-secondary w-100 text-start">
                                                        <i class="ti ti-file-type-pdf me-2"></i> {{ basename($transaksi->BuktiBayar) }}
                                                    </a>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Input File Baru -->
                                        <input type="file"
                                               class="form-control @error('BuktiBayar') is-invalid @enderror"
                                               id="BuktiBayar"
                                               name="BuktiBayar"
                                               accept="image/*,.pdf">
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Kosongkan jika tidak ingin mengganti file. (Maks. 2MB)
                                        </div>
                                        @error('BuktiBayar')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Keterangan (Full Width) -->
                            <div class="mb-4">
                                <label for="Keterangan" class="form-label fw-semibold">
                                    <i class="ti ti-file-text me-1 text-primary"></i> Keterangan
                                </label>
                                <textarea class="form-control @error('Keterangan') is-invalid @enderror"
                                          id="Keterangan"
                                          name="Keterangan"
                                          rows="3"
                                          placeholder="Catatan tambahan mengenai transaksi ini...">{{ old('Keterangan', $transaksi->Keterangan) }}</textarea>
                                @error('Keterangan')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Perbarui Data
                                </button>
                                <a href="{{ route('transaksi.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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

<!-- JavaScript untuk Logika Form -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Logika Format Rupiah Otomatis
        const inputFormatted = document.getElementById('PendapatanFormatted');
        const inputRaw = document.getElementById('PendapatanRaw');

        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID').format(number);
        };

        // Set nilai awal dari database/old input
        const initialValue = inputRaw.value;
        if (initialValue && initialValue != '0') {
            inputFormatted.value = 'Rp ' + formatRupiah(initialValue);
        }

        inputFormatted.addEventListener('input', function(e) {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            inputRaw.value = rawValue;

            if (rawValue === '') {
                this.value = '';
            } else {
                this.value = 'Rp ' + formatRupiah(rawValue);
            }
        });

        // 2. Logika Show/Hide Field Non-Tunai
        const metodeSelect = document.getElementById('Metode');
        const kodeBayarWrapper = document.getElementById('KodeBayarWrapper');
        const buktiBayarWrapper = document.getElementById('BuktiBayarWrapper');
        const kodeBayarInput = document.getElementById('KodeBayar');

        function toggleNonTunaiFields() {
            if (metodeSelect.value === 'Non-Tunai') {
                kodeBayarWrapper.style.display = 'block';
                buktiBayarWrapper.style.display = 'block';
                kodeBayarInput.setAttribute('required', 'required');
            } else {
                kodeBayarWrapper.style.display = 'none';
                buktiBayarWrapper.style.display = 'none';
                kodeBayarInput.removeAttribute('required');
                // Jangan reset value di halaman edit agar data lama tidak hilang jika user iseng ganti ke Tunai lalu kembali
            }
        }

        // Jalankan saat halaman dimuat (PENTING untuk halaman Edit agar sesuai data DB)
        toggleNonTunaiFields();

        // Jalankan saat user mengganti pilihan
        metodeSelect.addEventListener('change', toggleNonTunaiFields);
    });
</script>
@endsection
