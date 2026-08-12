@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 h3 fw-bold text-dark">Detail Transaksi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transaksi.index') }}" class="text-decoration-none">Transaksi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Toast/Alert for redirect back with success --}}
@if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; right: 1rem; top: 4.5rem;">
        <div id="success-toast" class="toast show align-items-center text-bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body fw-bold">
                    <i class="ti ti-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

<div class="content pb-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-10 col-md-12">

                <!-- Card Utama -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="ti ti-receipt me-2"></i>{{ $transaksi->KodeTransaksi }}
                        </h5>
                        <span class="badge {{ $transaksi->Metode === 'Tunai' ? 'bg-success' : 'bg-info text-dark' }} px-3 py-2">
                            <i class="ti {{ $transaksi->Metode === 'Tunai' ? 'ti-cash' : 'ti-credit-card' }} me-1"></i>
                            {{ $transaksi->Metode }}
                        </span>
                    </div>

                    <div class="card-body p-4">
                        <div class="row g-4">

                            <!-- Kolom Kiri: Info Utama -->
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-info-circle me-1"></i> Informasi Transaksi
                                </h6>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Tanggal Transaksi</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-calendar me-1 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($transaksi->Tanggal)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Ekspedisi</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-truck me-1 text-primary"></i>
                                        {{ $transaksi->ekspedisi->NamaEkspedisi ?? $transaksi->Ekspedisi ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">No. Resi</label>
                                    <div class="fw-semibold text-dark font-monospace">
                                        <i class="ti ti-hash me-1 text-primary"></i>
                                        {{ $transaksi->NoResi ?: '-' }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Divisi</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-building me-1 text-primary"></i>
                                        {{ $transaksi->getDivisi->Nama ?: '-' }}
                                    </div>
                                </div>

                                <!-- Tambahan: Nama Pengirim -->
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Nama Pengirim</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-user-check me-1 text-primary"></i>
                                        {{ $transaksi->NamaPengirim ?? '-' }}
                                    </div>
                                </div>

                                <!-- Tambahan: Kontak Pengirim -->
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Kontak Pengirim</label>
                                    <div class="fw-semibold text-dark">
                                        <i class="ti ti-phone me-1 text-primary"></i>
                                        {{ $transaksi->KontakPengirim ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Info Keuangan & Lainnya -->
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-cash me-1"></i> Rincian Keuangan
                                </h6>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Pendapatan Kotor</label>
                                    <div class="fw-semibold text-dark fs-5">
                                        Rp {{ number_format($transaksi->Pendapatan, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Diskon</label>
                                    <div class="fw-semibold text-danger">
                                        - Rp {{ number_format($transaksi->Diskon ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="mb-3 p-3 bg-primary bg-opacity-10 rounded border border-primary border-opacity-25">
                                    <label class="text-primary small mb-1 d-block fw-bold">Pendapatan Bersih</label>
                                    <div class="fw-bold text-primary fs-4">
                                        Rp {{ number_format($transaksi->PendapatanBersih, 0, ',', '.') }}
                                    </div>
                                </div>

                                @if($transaksi->Metode === 'Non-Tunai' && $transaksi->KodeBayar)
                                <div class="mb-3">
                                    <label class="text-muted small mb-1 d-block">Kode / Ref Pembayaran</label>
                                    <div class="fw-semibold text-dark font-monospace">
                                        <i class="ti ti-ticket me-1 text-primary"></i>
                                        {{ $transaksi->KodeBayar }}
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Full Width: Bukti Bayar & Keterangan -->
                            <div class="col-12 mt-4 pt-3 border-top">
                                <h6 class="text-uppercase text-muted fw-semibold mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="ti ti-paperclip me-1"></i> Lampiran & Keterangan
                                </h6>

                                @if($transaksi->BuktiBayar)
                                <div class="mb-4">
                                    <label class="text-muted small mb-2 d-block">Bukti Pembayaran</label>
                                    @php
                                        $ext = strtolower(pathinfo($transaksi->BuktiBayar, PATHINFO_EXTENSION));
                                    @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <a href="{{ asset('storage/' . $transaksi->BuktiBayar) }}" target="_blank" class="d-inline-block">
                                            <img src="{{ asset('storage/' . $transaksi->BuktiBayar) }}"
                                                 alt="Bukti Bayar"
                                                 class="img-fluid rounded border"
                                                 style="max-height: 200px; max-width: 100%; object-fit: contain; background: #f8f9fa;">
                                        </a>
                                    @elseif($ext === 'pdf')
                                        <a href="{{ asset('storage/' . $transaksi->BuktiBayar) }}" target="_blank" class="btn btn-outline-danger d-flex align-items-center gap-2" style="max-width: 250px;">
                                            <i class="ti ti-file-type-pdf fs-4"></i>
                                            <span class="text-truncate">Lihat File PDF</span>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $transaksi->BuktiBayar) }}" target="_blank" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="max-width: 250px;">
                                            <i class="ti ti-file fs-4"></i>
                                            <span class="text-truncate">Unduh File</span>
                                        </a>
                                    @endif
                                </div>
                                @endif

                                @if($transaksi->Keterangan)
                                <div class="mb-4">
                                    <label class="text-muted small mb-1 d-block">Keterangan Tambahan</label>
                                    <div class="p-3 bg-light rounded text-dark">
                                        {{ $transaksi->Keterangan }}
                                    </div>
                                </div>
                                @endif

                                <!-- ✅ BAGIAN AKSI FINANCE (DIBUAT MENONJOL) -->
                                <div class="mt-4">
                                    <div class="card border-0 shadow-sm bg-warning bg-opacity-10 border border-warning border-opacity-50">
                                        <div class="card-header bg-transparent border-bottom border-warning border-opacity-25 py-3">
                                            <h6 class="mb-0 fw-bold text-warning-emphasis d-flex align-items-center">
                                                <i class="ti ti-shield-check fs-5 me-2"></i>
                                                Verifikasi Finance
                                            </h6>
                                            <small class="text-muted">Bagian ini khusus untuk tim finance melakukan validasi transaksi.</small>
                                        </div>
                                        <div class="card-body p-3">
                                            <form action="{{ route('transaksi.updateStatus', $transaksi->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="row g-3">
                                                    <div class="col-md-4 d-flex align-items-center" style="align-items: center !important;">
                                                        <div class="w-100">
                                                            <label for="Status" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                                Status Verifikasi <span class="text-danger">*</span>
                                                            </label>
                                                            <select name="Status" id="Status" class="form-select form-select-sm fw-semibold @error('Status') is-invalid @enderror" required>
                                                                <option value="N/A" {{ old('Status', $transaksi->Status ?? 'N/A') === 'N/A' ? 'selected' : '' }}>N/A (Belum Diverifikasi)</option>
                                                                <option value="Y" {{ old('Status', $transaksi->Status) === 'Y' ? 'selected' : '' }}>Y (Disetujui / Valid)</option>
                                                                <option value="N" {{ old('Status', $transaksi->Status) === 'N' ? 'selected' : '' }}>N (Ditolak / Tidak Valid)</option>
                                                            </select>
                                                            @error('Status')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="Catatan" class="form-label fw-semibold small text-uppercase text-muted mb-1">
                                                            Catatan Finance
                                                        </label>
                                                        <textarea name="Catatan" id="Catatan" class="form-control form-control-sm" rows="6" style="resize: vertical;" placeholder="Isi catatan jika ada temuan atau alasan penolakan...">{{ $transaksi->Catatan }}</textarea>

                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center">
                                                        <button type="submit" class="btn btn-warning text-dark fw-semibold w-100 d-flex align-items-center justify-content-center gap-1 shadow-sm">
                                                            <i class="ti ti-check"></i> Simpan
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- ✅ AKHIR BAGIAN AKSI FINANCE -->

                                <div class="row mt-4 pt-3 border-top">
                                    <div class="col-md-3">
                                        <label class="text-muted small mb-1 d-block">Dibuat Oleh</label>
                                        <div class="fw-semibold text-dark">
                                            <i class="ti ti-user me-1 text-primary"></i>
                                            {{ $transaksi->userCreate->name ?? 'System' }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small mb-1 d-block">Waktu Dibuat</label>
                                        <div class="fw-semibold text-dark">
                                            <i class="ti ti-clock me-1 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($transaksi->created_at)->isoFormat('D MMMM YYYY, HH:mm') }}

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="text-muted small mb-1 d-block">Diverifikasi Oleh (Finance)</label>
                                        <div class="fw-semibold text-dark">
                                            <i class="ti ti-user-check me-1 text-primary"></i>
                                            {{ optional($transaksi->userFinance)->name ?? '-' }}

                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <label class="text-muted small mb-1 d-block">Waktu Verifikasi</label>
                                        <div class="fw-semibold text-dark">
                                            <i class="ti ti-clock-check me-1 text-primary"></i>
                                            {{ $transaksi->DicekPada ? \Carbon\Carbon::parse($transaksi->DicekPada)->isoFormat('D MMMM YYYY, HH:mm') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Action Buttons -->
                    <div class="card-footer bg-white border-top-0 pt-0 pb-4 px-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-light text-muted px-4 d-flex align-items-center border fw-semibold">
                                <i class="ti ti-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
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
            var toastEl = document.getElementById('success-toast');
            if (window.bootstrap && bootstrap.Toast) {
                var toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
                toast.show();
            } else if (typeof $ !== 'undefined' && $('.toast').toast) {
                $('#success-toast').toast({ delay: 3000, autohide: true });
                $('#success-toast').toast('show');
            } else {
                // fallback if no bootstrap js found
                setTimeout(function() {
                    toastEl.classList.remove('show');
                }, 3000);
            }
        });
    </script>

@endpush
