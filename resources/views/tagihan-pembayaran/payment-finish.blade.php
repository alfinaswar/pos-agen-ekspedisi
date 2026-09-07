@extends('layouts.app')

@section('title', 'Status Pembayaran Tagihan')

@section('content')
    <div class="content pb-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-5 text-center">

                            @php
                                $isPaid = $tagihan->PaymentStatus === 'PAID';
                                $isFailed = $tagihan->PaymentStatus === 'FAILED';
                                $isExpired = $tagihan->PaymentStatus === 'EXPIRED';
                            @endphp

                            @if ($isPaid)
                                <div class="mb-4">
                                    <div
                                        style="width: 80px; height: 80px; margin: 0 auto; background: #dcfce7; border-radius: 50%; display: grid; place-items: center;">
                                        <i class="ti ti-check" style="font-size: 40px; color: #16a34a;"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-success mb-2">Pembayaran Berhasil! 🎉</h3>
                                <p class="text-muted">Tagihan telah dibayar dan status otomatis diperbarui menjadi Lunas.
                                </p>

                                <div class="alert alert-light border mt-4 text-start">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Nomor Tagihan</small>
                                            <strong class="font-monospace">{{ $tagihan->NomorTagihan }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Tenant</small>
                                            <strong>{{ $tagihan->tenant->Nama ?? '-' }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Jumlah</small>
                                            <strong>Rp {{ number_format($tagihan->JumlahTagihan, 0, ',', '.') }}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Channel</small>
                                            <strong>{{ $tagihan->PaymentChannel ?? '-' }}</strong>
                                        </div>
                                        <div class="col-12">
                                            <small class="text-muted d-block">Dibayar Pada</small>
                                            <strong>{{ $tagihan->PaidAt?->format('d M Y, H:i') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @elseif($isFailed)
                                <div class="mb-4">
                                    <div
                                        style="width: 80px; height: 80px; margin: 0 auto; background: #fee2e2; border-radius: 50%; display: grid; place-items: center;">
                                        <i class="ti ti-x" style="font-size: 40px; color: #dc2626;"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-danger mb-2">Pembayaran Gagal</h3>
                                <p class="text-muted">Transaksi pembayaran tidak berhasil diproses.</p>
                            @elseif($isExpired)
                                <div class="mb-4">
                                    <div
                                        style="width: 80px; height: 80px; margin: 0 auto; background: #fef3c7; border-radius: 50%; display: grid; place-items: center;">
                                        <i class="ti ti-clock" style="font-size: 40px; color: #d97706;"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-warning mb-2">Pembayaran Kadaluarsa</h3>
                                <p class="text-muted">Link pembayaran sudah tidak aktif.</p>
                            @else
                                <div class="mb-4">
                                    <div
                                        style="width: 80px; height: 80px; margin: 0 auto; background: #dbeafe; border-radius: 50%; display: grid; place-items: center; animation: pulse 2s infinite;">
                                        <i class="ti ti-clock" style="font-size: 40px; color: #2563eb;"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-primary mb-2">Menunggu Pembayaran</h3>
                                <p class="text-muted">Bagikan link pembayaran berikut ke tenant:</p>

                                <div class="alert alert-info border text-start mt-4">
                                    <small class="text-muted d-block">Link Pembayaran</small>
                                    <div class="input-group mt-2">
                                        <input type="text" class="form-control font-monospace" id="paymentLink"
                                            value="{{ $tagihan->DokuPaymentUrl }}" readonly>
                                        <button class="btn btn-outline-primary" type="button" onclick="copyLink()">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                        <a href="{{ $tagihan->DokuPaymentUrl }}" target="_blank" class="btn btn-primary">
                                            <i class="ti ti-external-link"></i>
                                        </a>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        ⏰ Berlaku hingga: {{ $tagihan->PaymentExpiredAt?->format('d M Y, H:i') ?? '-' }}
                                    </small>
                                </div>

                                <div class="alert alert-light border mt-3">
                                    <small class="text-muted d-block mb-1">Detail Tagihan</small>
                                    <div class="row g-2 text-start small">
                                        <div class="col-6"><strong>Nomor:</strong> {{ $tagihan->NomorTagihan }}</div>
                                        <div class="col-6"><strong>Tenant:</strong> {{ $tagihan->tenant->Nama ?? '-' }}
                                        </div>
                                        <div class="col-6"><strong>Jumlah:</strong> Rp
                                            {{ number_format($tagihan->JumlahTagihan, 0, ',', '.') }}</div>
                                        <div class="col-6"><strong>Jatuh Tempo:</strong>
                                            {{ $tagihan->TanggalJatuhTempo?->format('d M Y') }}</div>
                                    </div>
                                </div>

                                <p id="statusText" class="text-muted small mt-3">
                                    <i class="ti ti-refresh"></i> Memeriksa status otomatis setiap 5 detik...
                                </p>
                            @endif

                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <a href="{{ route('tagihan-pembayaran.index') }}" class="btn btn-primary">
                                    <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar Tagihan
                                </a>
                                <a href="{{ route('tagihan-pembayaran.create') }}" class="btn btn-outline-primary">
                                    <i class="ti ti-plus me-1"></i> Buat Tagihan Lain
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyLink() {
                const input = document.getElementById('paymentLink');
                input.select();
                navigator.clipboard.writeText(input.value);
                alert('Link pembayaran berhasil disalin!');
            }

            @if ($tagihan->PaymentStatus === 'PENDING')
                // Polling untuk cek status pembayaran
                (function() {
                    const checkUrl = "{{ route('tagihan-pembayaran.payment-status', $tagihan->id) }}";
                    let attempts = 0;
                    const maxAttempts = 120; // 10 menit

                    const poller = setInterval(async function() {
                        attempts++;
                        try {
                            const res = await fetch(checkUrl);
                            const data = await res.json();

                            if (data.success && data.data.payment_status === 'PAID') {
                                clearInterval(poller);
                                document.getElementById('statusText').innerHTML =
                                    '✅ Pembayaran terdeteksi! Memuat ulang...';
                                setTimeout(() => window.location.reload(), 1500);
                            } else if (data.success && ['FAILED', 'EXPIRED'].includes(data.data
                                .payment_status)) {
                                clearInterval(poller);
                                setTimeout(() => window.location.reload(), 1000);
                            }

                            if (attempts >= maxAttempts) clearInterval(poller);
                        } catch (e) {
                            console.error('Poll error:', e);
                        }
                    }, 5000);
                })();
            @endif
        </script>

        <style>
            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.05);
                }
            }
        </style>
    @endpush
@endsection
