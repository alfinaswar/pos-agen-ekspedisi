@extends('layouts.app')

@section('title', 'Tambah Transaksi')

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
                <h1 class="m-0 h3 fw-bold text-dark">Tambah Transaksi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}" class="text-decoration-none">Transaksi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main Form Content -->
<div class="content pb-5">
    <div class="container-fluid">
        <!-- col-md-12 wajib, dibatasi max xl-8 dan lg-10 agar tetap rapi di layar besar -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-10 col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-receipt me-2"></i>Form Tambah Transaksi
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <!-- Tambahkan enctype untuk upload file BuktiBayar -->
                        <form action="{{ route('transaksi.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Kolom Kiri -->
                                <div class="col-md-6">

                                    <!-- Tanggal -->
                                    <div class="mb-4">
                                        <label for="Tanggal" class="form-label fw-semibold">
                                            <i class="ti ti-calendar me-1 text-primary"></i> Tanggal & Waktu <span class="text-danger">*</span>
                                        </label>
                                        <input type="datetime-local"
                                               class="form-control @error('Tanggal') is-invalid @enderror"
                                               id="Tanggal"
                                               name="Tanggal"
                                               value="{{ old('Tanggal', date('Y-m-d\TH:i')) }}"
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
                                            @foreach($ekspedisis as $exp)
                                                <option value="{{ $exp->id }}" {{ old('Ekspedisi') == $exp->NamaEkspedisi ? 'selected' : '' }}>
                                                    {{ $exp->NamaEkspedisi }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Pilih jasa ekspedisi yang digunakan.
                                        </div>
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
                                               value="{{ old('NoResi') }}"
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
                                            <option value="Tunai" {{ old('Metode', 'Tunai') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                            <option value="Non-Tunai" {{ old('Metode') == 'Non-Tunai' ? 'selected' : '' }}>Non-Tunai</option>
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
                                        <!-- Input Tampilan (Formatted) -->
                                        <input type="text"
                                               class="form-control @error('Pendapatan') is-invalid @enderror"
                                               id="PendapatanFormatted"
                                               placeholder="Rp 0"
                                               autocomplete="off">
                                        <!-- Input Tersembunyi (Raw Value untuk Submit) -->
                                        <input type="hidden" name="Pendapatan" id="PendapatanRaw" value="{{ old('Pendapatan', 0) }}">

                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Ketik angka, format Rupiah akan otomatis muncul.
                                        </div>
                                        @error('Pendapatan')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Kode Bayar (Conditional: Hanya muncul jika Non-Tunai) -->
                                    <div class="mb-4" id="KodeBayarWrapper" style="display: none;">
                                        <label for="KodeBayar" class="form-label fw-semibold">
                                            <i class="ti ti-receipt me-1 text-primary"></i> Kode Bayar <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('KodeBayar') is-invalid @enderror"
                                               id="KodeBayar"
                                               name="KodeBayar"
                                               value="{{ old('KodeBayar') }}"
                                               placeholder="Contoh: 8801928374 (VA) atau QRIS">
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Wajib diisi untuk metode Non-Tunai.
                                        </div>
                                        @error('KodeBayar')
                                            <div class="invalid-feedback d-block error-fade-in">
                                                <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Bukti Bayar (Conditional: Hanya muncul jika Non-Tunai) -->
                                    <div class="mb-4" id="BuktiBayarWrapper" style="display: none;">
                                        <label for="BuktiBayar" class="form-label fw-semibold">
                                            <i class="ti ti-photo me-1 text-primary"></i> Bukti Pembayaran
                                        </label>
                                        <input type="file"
                                               class="form-control @error('BuktiBayar') is-invalid @enderror"
                                               id="BuktiBayar"
                                               name="BuktiBayar"
                                               accept="image/*,.pdf">
                                        <div class="form-text text-muted mt-1">
                                            <i class="ti ti-info-circle me-1"></i>Format: JPG, PNG, atau PDF (Maks. 2MB).
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
                                          placeholder="Catatan tambahan mengenai transaksi ini...">{{ old('Keterangan') }}</textarea>
                                @error('Keterangan')
                                    <div class="invalid-feedback d-block error-fade-in">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3 pt-3 border-top mt-4">
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                    <i class="ti ti-device-floppy me-2"></i>Simpan
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

        // Fungsi format Rupiah
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID').format(number);
        };

        // Set nilai awal jika ada old input (misal validasi gagal)
        const initialValue = inputRaw.value;
        if (initialValue && initialValue != '0') {
            inputFormatted.value = 'Rp ' + formatRupiah(initialValue);
        }

        inputFormatted.addEventListener('input', function(e) {
            // Hapus semua karakter kecuali angka
            let rawValue = this.value.replace(/[^0-9]/g, '');

            // Update hidden input dengan raw value (tanpa titik)
            inputRaw.value = rawValue;

            // Format tampilan
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
                kodeBayarInput.setAttribute('required', 'required'); // Jadikan wajib
            } else {
                kodeBayarWrapper.style.display = 'none';
                buktiBayarWrapper.style.display = 'none';
                kodeBayarInput.removeAttribute('required'); // Hapus wajib
                kodeBayarInput.value = ''; // Reset nilai
            }
        }

        // Jalankan saat halaman dimuat (untuk handle old input)
        toggleNonTunaiFields();

        // Jalankan saat user mengganti pilihan
        metodeSelect.addEventListener('change', toggleNonTunaiFields);
    });
</script>
@endsection
