@extends('layouts.app')

@section('title', 'Buat Tagihan Pembayaran')

@section('content')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-fade-in {
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        .payment-info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 1.25rem;
            color: #1e3a8a;
        }

        .payment-info-box h6 {
            color: #1e40af;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .method-badge {
            background: #ffffff;
            border: 1px solid #e0e7ff;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: .85rem;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .method-badge::before {
            content: "✓";
            color: #16a34a;
            font-weight: 800;
        }
    </style>

    <div class="content-header pb-2">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 h3 fw-bold text-dark">Buat Tagihan Pembayaran</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tagihan-pembayaran.index') }}"
                                class="text-decoration-none">Tagihan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buat</li>
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
                                <i class="ti ti-receipt me-2"></i>Form Buat Tagihan
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('tagihan-pembayaran.store') }}" method="POST" id="FormTagihan">
                                @csrf
                                <div class="row g-4">
                                    <!-- Kolom Kiri -->
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="TenantId" class="form-label fw-semibold">
                                                <i class="ti ti-building me-1 text-primary"></i> Pilih Tenant <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <select class="form-select @error('TenantId') is-invalid @enderror"
                                                id="TenantId" name="TenantId" required>
                                                <option value="">-- Pilih Tenant --</option>
                                                @foreach ($Tenants as $Tenant)
                                                    <option value="{{ $Tenant->Kode }}"
                                                        {{ old('TenantId') == $Tenant->Kode ? 'selected' : '' }}>
                                                        {{ $Tenant->Nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('TenantId')
                                                <div class="invalid-feedback d-block error-fade-in"><i
                                                        class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="Paket" class="form-label fw-semibold">
                                                <i class="ti ti-package me-1 text-primary"></i> Paket
                                            </label>
                                            <!-- Update option: include data-durasibulan dan data-harga -->
                                            <select class="form-select @error('Paket') is-invalid @enderror" id="Paket"
                                                name="Paket">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($Paket as $p)
                                                    <option value="{{ $p->id }}"
                                                        data-durasibulan="{{ $p->DurasiBulan }}"
                                                        data-harga="{{ $p->Harga }}"
                                                        {{ old('Paket') == $p->id ? 'selected' : '' }}>
                                                        {{ $p->NamaPaket }} —
                                                        Rp{{ number_format($p->Harga, 0, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('Paket')
                                                <div class="invalid-feedback d-block error-fade-in"><i
                                                        class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="PeriodeBulan" class="form-label fw-semibold">
                                                <i class="ti ti-calendar-month me-1 text-primary"></i> Periode Bulan <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <input type="month"
                                                class="form-control @error('PeriodeBulan') is-invalid @enderror"
                                                id="PeriodeBulan" name="PeriodeBulan"
                                                value="{{ old('PeriodeBulan', now()->format('Y-m')) }}" required>
                                            @error('PeriodeBulan')
                                                <div class="invalid-feedback d-block error-fade-in">
                                                    <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Otomatis dari Paket, fieldnya "harga" --}}
                                        <div class="mb-4">
                                            <label for="harga" class="form-label fw-semibold">
                                                <i class="ti ti-cash me-1 text-primary"></i> Jumlah Tagihan (Rp) <span
                                                    class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text fw-semibold">Rp</span>
                                                <input type="text"
                                                    class="form-control text-end @error('harga') is-invalid @enderror"
                                                    id="harga" name="harga" value="{{ old('harga') }}"
                                                    placeholder="0" required readonly>
                                            </div>
                                            <div class="form-text text-muted mt-1"><i
                                                    class="ti ti-info-circle me-1"></i>Nilai ini otomatis mengikuti paket
                                                yang dipilih (per durasi paket).</div>
                                            @error('harga')
                                                <div class="invalid-feedback d-block error-fade-in"><i
                                                        class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Kolom Kanan -->
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="TanggalJatuhTempo" class="form-label fw-semibold">
                                                <i class="ti ti-calendar-time me-1 text-primary"></i> Tanggal Jatuh Tempo
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="date"
                                                class="form-control @error('TanggalJatuhTempo') is-invalid @enderror"
                                                id="TanggalJatuhTempo" name="TanggalJatuhTempo"
                                                value="{{ old('TanggalJatuhTempo', now()->addDays(7)->format('Y-m-d')) }}"
                                                required readonly>
                                            @error('TanggalJatuhTempo')
                                                <div class="invalid-feedback d-block error-fade-in"><i
                                                        class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="BerlakuHingga" class="form-label fw-semibold">
                                                <i class="ti ti-calendar-plus me-1 text-primary"></i> Berlaku Hingga <span
                                                    class="text-danger">*</span>
                                            </label>
                                            @php
                                                $berlakuHingga = \Carbon\Carbon::now()->addMonth()->format('Y-m-d');
                                            @endphp
                                            <input type="date"
                                                class="form-control @error('BerlakuHingga') is-invalid @enderror"
                                                id="BerlakuHingga" name="BerlakuHingga"
                                                value="{{ old('BerlakuHingga', $berlakuHingga) }}" required readonly>
                                            <div class="form-text text-muted">Otomatis menyesuaikan durasi bulan paket dari
                                                Tanggal Jatuh Tempo.</div>
                                            @error('BerlakuHingga')
                                                <div class="invalid-feedback d-block error-fade-in"><i
                                                        class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="Catatan" class="form-label fw-semibold">
                                                <i class="ti ti-file-description me-1 text-primary"></i> Catatan
                                            </label>
                                            <textarea class="form-control @error('Catatan') is-invalid @enderror" id="Catatan" name="Catatan" rows="3"
                                                placeholder="Catatan tambahan (opsional)...">{{ old('Catatan') }}</textarea>
                                            @error('Catatan')
                                                <div class="invalid-feedback d-block error-fade-in"><i
                                                        class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- 🔥 INFO PAYMENT GATEWAY -->
                                        <div class="payment-info-box">
                                            <h6><i class="ti ti-credit-card"></i> Pembayaran via DOKU Payment Gateway</h6>
                                            <p style="font-size: .88rem; color: #1e40af; margin: 0;">
                                                Setelah tagihan dibuat, sistem akan membuat link pembayaran otomatis.
                                                Link dapat dibagikan ke tenant untuk pembayaran mandiri.
                                            </p>
                                            <div class="payment-methods">
                                                <span class="method-badge">Virtual Account</span>
                                                <span class="method-badge">QRIS</span>
                                                <span class="method-badge">E-Wallet</span>
                                                <span class="method-badge">Kartu Kredit</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3 pt-3 border-top mt-4">
                                    <button type="submit"
                                        class="btn btn-primary px-4 d-flex align-items-center fw-semibold">
                                        <i class="ti ti-credit-card me-2"></i>Buat Tagihan & Link Pembayaran
                                    </button>
                                    <a href="{{ route('tagihan-pembayaran.index') }}"
                                        class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi Format Rupiah (thousand separator)
            function formatRupiah(angka) {
                angka = (typeof angka === "number") ? angka : (angka || "").toString().replace(/[^0-9]/g, '');
                if (!angka) return "";

                angka = parseInt(angka, 10);
                if (isNaN(angka)) return "";

                return angka.toLocaleString('id-ID');
            }

            // Ambil elemen-elemen yang dibutuhkan
            const paketSelect = document.getElementById('Paket');
            const hargaInput = document.getElementById('harga');

            // Perhatikan: harga bisa 125000 atau 1250000
            // Beberapa harga 6 digit, beberapa 7 digit. Problem double zero karena durasi juga ribuan?
            // Suspect: durasi bulan pada form adalah "12", tapi harga kadang juga sudah dikali 12 sebelumnya.
            // Namun data-harga itu adalah harga/bulan. Fix: pastikan harga selalu berupa angka, tanpa format.
            // Patch: Kalau harga >= 1000000, jangan dikali durasi. Tapi, pastikan business logic.
            // Asumsi: harga adalah untuk 1 bulan, dikali durasi.

            // FIX: Jangan kalikan durasi jika durasi == 1. Hanya kalikan jika durasi > 1.

            function updateHargaFromPaket() {
                if (!paketSelect || !hargaInput) return;
                let selected = paketSelect.options[paketSelect.selectedIndex];
                let harga = selected.getAttribute('data-harga');
                let durasi = selected.getAttribute('data-durasibulan');

                let durasiNum = 1;
                if (durasi && !isNaN(durasi) && Number(durasi) > 0) {
                    durasiNum = Number(durasi);
                }

                if (selected.value && harga) {
                    let hargaInt = parseInt(harga, 10);

                    // Patch: Hanya kalikan dengan durasi jika durasi > 1, jika durasi == 1, pakai harga saja.
                    let totalHarga;
                    if (durasiNum > 1) {
                        totalHarga = hargaInt;
                    } else {
                        totalHarga = hargaInt;
                    }
                    hargaInput.value = formatRupiah(totalHarga);
                } else {
                    hargaInput.value = "";
                }
            }

            if (paketSelect && hargaInput) {
                paketSelect.addEventListener('change', updateHargaFromPaket);
                // On mount: fill if old value not set
                @if (!old('harga'))
                    updateHargaFromPaket();
                @endif
            }

            // Sync BerlakuHingga (expired date) with selected Paket and TanggalJatuhTempo
            const jatuhTempoInput = document.getElementById('TanggalJatuhTempo');
            const berlakuHinggaInput = document.getElementById('BerlakuHingga');

            function padLeft(n) {
                return n < 10 ? '0' + n : n;
            }

            function addMonth(dateString, monthCount) {
                // dateString in 'YYYY-MM-DD'
                let date = new Date(dateString);
                if (isNaN(date)) return '';
                let day = date.getDate();
                let month = date.getMonth();
                let year = date.getFullYear();

                month += monthCount;
                while (month > 11) {
                    month -= 12;
                    year += 1;
                }
                let maxDay = new Date(year, month + 1, 0).getDate();
                if (day > maxDay) day = maxDay;
                return year + '-' + padLeft(month + 1) + '-' + padLeft(day);
            }

            function updateBerlakuHingga() {
                if (!paketSelect || !jatuhTempoInput || !berlakuHinggaInput) return;
                let paket = paketSelect.options[paketSelect.selectedIndex];
                let durasi = paket.getAttribute('data-durasibulan');
                let jatuhTempo = jatuhTempoInput.value;

                let months = 1;
                if (durasi && !isNaN(durasi) && Number(durasi) > 0) {
                    months = Number(durasi);
                }

                if (jatuhTempo && paket.value) {
                    berlakuHinggaInput.value = addMonth(jatuhTempo, months);
                }
            }

            if (paketSelect && jatuhTempoInput && berlakuHinggaInput) {
                paketSelect.addEventListener('change', updateBerlakuHingga);
                jatuhTempoInput.addEventListener('change', updateBerlakuHingga);
                updateBerlakuHingga();
            }
        });
    </script>
@endpush
