@extends('layouts.app')

@section('title', 'Pembayaran Tagihan — Maurekap')

@section('content')
<style>
    :root {
        --brand: #2563EB;
        --brand-dark: #1e40af;
        --brand-light: #eff6ff;
        --success: #16a34a;
        --success-light: #dcfce7;
        --danger: #dc2626;
        --danger-light: #fee2e2;
        --warning: #d97706;
        --warning-light: #fef3c7;
        --ink: #0f172a;
        --muted: #64748b;
        --line: #e2e8f0;
    }

    /* ── Hero Icon Animations ── */
    @keyframes pulse-soft {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.05); opacity: .85; }
    }
    @keyframes draw-circle {
        to { stroke-dashoffset: 0; }
    }
    @keyframes draw-check {
        to { stroke-dashoffset: 0; }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes confetti {
        0% { transform: translateY(0) rotate(0); opacity: 1; }
        100% { transform: translateY(120px) rotate(360deg); opacity: 0; }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .fade-up { animation: fadeUp .5s ease-out forwards; }
    .fade-up-delay-1 { animation-delay: .1s; opacity: 0; }
    .fade-up-delay-2 { animation-delay: .2s; opacity: 0; }
    .fade-up-delay-3 { animation-delay: .3s; opacity: 0; }

    /* ── Main Card ── */
    .pay-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(15, 23, 42, .08);
        overflow: hidden;
        border: 1px solid var(--line);
    }

    /* ── Hero Section ── */
    .pay-hero {
        padding: 48px 32px 32px;
        text-align: center;
        position: relative;
    }
    .pay-hero.pending { background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%); }
    .pay-hero.success { background: linear-gradient(180deg, #dcfce7 0%, #ffffff 100%); }
    .pay-hero.failed  { background: linear-gradient(180deg, #fee2e2 0%, #ffffff 100%); }
    .pay-hero.expired { background: linear-gradient(180deg, #fef3c7 0%, #ffffff 100%); }

    /* ── Status Icon ── */
    .status-icon {
        width: 96px;
        height: 96px;
        margin: 0 auto 20px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        position: relative;
    }
    .status-icon.pending {
        background: #dbeafe;
        animation: pulse-soft 2s ease-in-out infinite;
    }
    .status-icon.pending i { color: var(--brand); font-size: 46px; }

    .status-icon.success { background: var(--success-light); }
    .status-icon.failed { background: var(--danger-light); }
    .status-icon.expired { background: var(--warning-light); }

    .ok-svg { width: 52px; height: 52px; }
    .ok-svg circle {
        stroke: var(--success);
        stroke-width: 2.5;
        fill: none;
        stroke-dasharray: 160;
        stroke-dashoffset: 160;
        animation: draw-circle 1s .2s ease forwards;
    }
    .ok-svg path {
        stroke: var(--success);
        stroke-width: 4;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-dasharray: 40;
        stroke-dashoffset: 40;
        animation: draw-check .5s .9s ease forwards;
    }

    .fail-svg { width: 52px; height: 52px; }
    .fail-svg line {
        stroke: var(--danger);
        stroke-width: 4;
        stroke-linecap: round;
        stroke-dasharray: 40;
        stroke-dashoffset: 40;
        animation: draw-check .5s .3s ease forwards;
    }

    .expired-svg { width: 52px; height: 52px; }
    .expired-svg circle {
        stroke: var(--warning);
        stroke-width: 2.5;
        fill: none;
        stroke-dasharray: 160;
        stroke-dashoffset: 160;
        animation: draw-circle .8s .1s ease forwards;
    }
    .expired-svg line {
        stroke: var(--warning);
        stroke-width: 3;
        stroke-linecap: round;
        stroke-dasharray: 30;
        stroke-dashoffset: 30;
        animation: draw-check .4s .7s ease forwards;
    }

    /* ── Typography ── */
    .pay-title {
        font-size: clamp(1.4rem, 1.1rem + 1.4vw, 1.9rem);
        font-weight: 800;
        color: var(--ink);
        margin-bottom: 8px;
        letter-spacing: -.01em;
    }
    .pay-subtitle {
        color: var(--muted);
        font-size: .95rem;
        max-width: 44ch;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* ── Status Badge ── */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        margin-bottom: 16px;
    }
    .status-badge.pending { background: #dbeafe; color: var(--brand-dark); }
    .status-badge.success { background: var(--success-light); color: #166534; }
    .status-badge.failed { background: var(--danger-light); color: #991b1b; }
    .status-badge.expired { background: var(--warning-light); color: #92400e; }

    /* ── Body Content ── */
    .pay-body { padding: 32px; }

    /* ── Detail Card ── */
    .detail-card {
        background: #f8fafc;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 4px 0;
        margin-bottom: 20px;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        border-bottom: 1px solid var(--line);
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-row.highlight {
        background: #ffffff;
        margin: 8px 0;
        padding: 16px 20px;
        border-radius: 10px;
        border: 1.5px solid var(--brand);
        border-bottom: 1.5px solid var(--brand);
    }
    .detail-label {
        color: var(--muted);
        font-size: .85rem;
        font-weight: 500;
    }
    .detail-value {
        color: var(--ink);
        font-weight: 600;
        font-size: .92rem;
        text-align: right;
    }
    .detail-value.mono {
        font-family: 'Courier New', monospace;
        letter-spacing: .3px;
    }
    .detail-value.amount {
        color: var(--brand);
        font-size: 1.15rem;
        font-weight: 800;
    }

    /* ── Countdown ── */
    .countdown-wrap {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .countdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .countdown-label {
        font-size: .85rem;
        color: var(--brand-dark);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .countdown-time {
        font-family: 'Courier New', monospace;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--brand-dark);
        letter-spacing: 1px;
    }
    .countdown-bar {
        height: 6px;
        background: #bfdbfe;
        border-radius: 6px;
        overflow: hidden;
    }
    .countdown-bar .fill {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        border-radius: 6px;
        transition: width 1s linear;
    }

    /* ── Trust Badges ── */
    .trust-row {
        display: flex;
        justify-content: center;
        gap: 24px;
        padding: 16px 0;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .trust-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .78rem;
        color: var(--muted);
        font-weight: 600;
    }
    .trust-item i { font-size: 16px; }
    .trust-item.secure i { color: var(--success); }
    .trust-item.encrypted i { color: var(--brand); }
    .trust-item.support i { color: #8b5cf6; }

    /* ── CTA Buttons ── */
    .cta-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 16px 24px;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: #fff !important;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .3);
        transition: all .2s;
        margin-bottom: 12px;
    }
    .cta-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(37, 99, 235, .4);
    }
    .cta-primary.success-btn {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        box-shadow: 0 8px 20px rgba(22, 163, 74, .3);
    }
    .cta-primary.success-btn:hover { box-shadow: 0 12px 28px rgba(22, 163, 74, .4); }
    .cta-primary.danger-btn {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        box-shadow: 0 8px 20px rgba(220, 38, 38, .3);
    }
    .cta-secondary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 13px 20px;
        background: #fff;
        color: var(--ink) !important;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: .9rem;
        border: 1.5px solid var(--line);
        transition: all .2s;
    }
    .cta-secondary:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .btn-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* ── Payment Methods ── */
    .payment-methods {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .method-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        background: #f1f5f9;
        border: 1px solid var(--line);
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 600;
        color: #475569;
    }
    .method-chip i { font-size: 14px; }

    /* ── Info Boxes ── */
    .info-box {
        padding: 14px 16px;
        border-radius: 10px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 20px;
        font-size: .88rem;
    }
    .info-box.info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
    }
    .info-box.warning {
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #92400e;
    }
    .info-box.danger {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }
    .info-box.success {
        background: var(--success-light);
        border: 1px solid #86efac;
        color: #166534;
    }
    .info-box i {
        font-size: 20px;
        margin-top: 2px;
        flex: none;
    }

    /* ── Polling Indicator ── */
    .polling-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: .82rem;
        color: var(--muted);
        padding: 10px;
    }
    .polling-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand);
        animation: pulse-soft 1.5s ease-in-out infinite;
    }

    /* ── Next Steps (Success) ── */
    .next-steps {
        text-align: left;
        margin: 20px 0;
    }
    .next-steps h6 {
        font-size: .85rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .next-step-item {
        display: flex;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px dashed var(--line);
    }
    .next-step-item:last-child { border-bottom: none; }
    .step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--brand);
        color: #fff;
        font-size: .75rem;
        font-weight: 800;
        display: grid;
        place-items: center;
        flex: none;
    }
    .step-text {
        font-size: .88rem;
        color: #334155;
        line-height: 1.5;
        padding-top: 2px;
    }

    /* ── Confetti (Success) ── */
    .confetti-container {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 200px;
        overflow: hidden;
        pointer-events: none;
    }
    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 2px;
        animation: confetti 2s ease-out forwards;
    }

    /* ── Responsive ── */
    @media (max-width: 576px) {
        .pay-hero { padding: 32px 20px 24px; }
        .pay-body { padding: 24px 20px; }
        .btn-row { grid-template-columns: 1fr; }
        .detail-row { flex-direction: column; gap: 4px; align-items: flex-start; }
        .detail-value { text-align: left; width: 100%; }
        .trust-row { gap: 12px; }
    }
</style>

<div class="content pb-5">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-10">

                @php
                    $isPaid    = $tagihan->PaymentStatus === 'PAID';
                    $isFailed  = $tagihan->PaymentStatus === 'FAILED';
                    $isExpired = $tagihan->PaymentStatus === 'EXPIRED';
                    $isPending = !$isPaid && !$isFailed && !$isExpired;

                    $periodeDate = $tagihan->PeriodeBulan
                        ? \Carbon\Carbon::parse($tagihan->PeriodeBulan . '-01')
                        : null;
                @endphp

                <div class="pay-card">

                    {{-- ═══════════════════════════════════════════════
                         STATE: PAID — Pembayaran Berhasil
                    ═══════════════════════════════════════════════ --}}
                    @if($isPaid)
                        <div class="pay-hero success">
                            <div class="confetti-container" id="confettiBox"></div>

                            <div class="status-icon success fade-up">
                                <svg class="ok-svg" viewBox="0 0 52 52">
                                    <circle cx="26" cy="26" r="24"/>
                                    <path d="M14 27l7 7 16-16"/>
                                </svg>
                            </div>

                            <span class="status-badge success fade-up fade-up-delay-1">
                                <i class="ti ti-shield-check"></i> Terbayar Lunas
                            </span>

                            <h2 class="pay-title fade-up fade-up-delay-1">Pembayaran Berhasil! 🎉</h2>
                            <p class="pay-subtitle fade-up fade-up-delay-2">
                                Terima kasih! Pembayaran Anda telah kami terima dan langganan Anda kini <strong>aktif</strong>.
                            </p>
                        </div>

                        <div class="pay-body">
                            {{-- Detail Tagihan --}}
                            <div class="detail-card fade-up fade-up-delay-2">
                                <div class="detail-row">
                                    <span class="detail-label">Nomor Tagihan</span>
                                    <span class="detail-value mono">{{ $tagihan->NomorTagihan }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Periode</span>
                                    <span class="detail-value">{{ $periodeDate?->translatedFormat('F Y') ?? '-' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Metode Pembayaran</span>
                                    <span class="detail-value">{{ $tagihan->PaymentChannel ?? 'DOKU' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Tanggal Pembayaran</span>
                                    <span class="detail-value">{{ $tagihan->PaidAt?->format('d M Y, H:i') ?? '-' }}</span>
                                </div>
                                <div class="detail-row highlight">
                                    <span class="detail-label">Total Dibayar</span>
                                    <span class="detail-value amount">Rp {{ number_format($tagihan->JumlahTagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            @if($tagihan->BerlakuHingga)
                                <div class="info-box success fade-up fade-up-delay-2">
                                    <i class="ti ti-shield-check"></i>
                                    <div>
                                        <strong>Langganan Aktif</strong>
                                        <div style="font-size: .85rem; margin-top: 2px;">
                                            Berlaku hingga <strong>{{ \Carbon\Carbon::parse($tagihan->BerlakuHingga)->format('d M Y') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Next Steps --}}


                            <a href="{{ route('home') }}" class="cta-primary success-btn fade-up fade-up-delay-3">
                                <i class="ti ti-home"></i> Kembali ke Dashboard
                            </a>

                            <a href="{{ route('tagihan-pembayaran.index') }}" class="cta-secondary fade-up fade-up-delay-3">
                                <i class="ti ti-list"></i> Lihat Riwayat Pembayaran
                            </a>
                        </div>
                    @endif

                    {{-- ═══════════════════════════════════════════════
                         STATE: PENDING — Menunggu Pembayaran
                    ═══════════════════════════════════════════════ --}}
                    @if($isPending)
                        <div class="pay-hero pending">
                            <div class="status-icon pending fade-up">
                                <i class="ti ti-credit-card"></i>
                            </div>

                            <span class="status-badge pending fade-up fade-up-delay-1">
                                <i class="ti ti-hourglass"></i> Menunggu Pembayaran
                            </span>

                            <h2 class="pay-title fade-up fade-up-delay-1">Selesaikan Pembayaran Anda</h2>
                            <p class="pay-subtitle fade-up fade-up-delay-2">
                                Klik tombol di bawah untuk melanjutkan ke halaman pembayaran yang aman.
                            </p>
                        </div>

                        <div class="pay-body">
                            {{-- Detail Tagihan --}}
                            <div class="detail-card fade-up fade-up-delay-2">
                                <div class="detail-row">
                                    <span class="detail-label">Nomor Tagihan</span>
                                    <span class="detail-value mono">{{ $tagihan->NomorTagihan }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Periode</span>
                                    <span class="detail-value">{{ $periodeDate?->translatedFormat('F Y') ?? '-' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Jatuh Tempo</span>
                                    <span class="detail-value">{{ $tagihan->TanggalJatuhTempo?->format('d M Y') ?? '-' }}</span>
                                </div>
                                <div class="detail-row highlight">
                                    <span class="detail-label">Total Tagihan</span>
                                    <span class="detail-value amount">Rp {{ number_format($tagihan->JumlahTagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Countdown --}}
                            @if($tagihan->PaymentExpiredAt)
                                <div class="countdown-wrap fade-up fade-up-delay-2" id="countdownWrap">
                                    <div class="countdown-header">
                                        <span class="countdown-label">
                                            <i class="ti ti-clock"></i> Pembayaran berlaku hingga
                                        </span>
                                        <span class="countdown-time" id="countdownTime">--:--:--</span>
                                    </div>
                                    <div class="countdown-bar">
                                        <div class="fill" id="countdownFill" style="width: 100%;"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- CTA --}}
                            <a href="{{ $tagihan->DokuPaymentUrl }}" target="_blank" rel="noopener" class="cta-primary fade-up fade-up-delay-3">
                                <i class="ti ti-credit-card"></i> Bayar Sekarang via DOKU
                                <i class="ti ti-external-link" style="font-size: 16px;"></i>
                            </a>

                            <div class="info-box info fade-up fade-up-delay-3">
                                <i class="ti ti-info-circle"></i>
                                <div>
                                    <strong>Petunjuk Pembayaran:</strong>
                                    <div style="font-size: .85rem; margin-top: 2px;">
                                        Klik tombol di atas, pilih metode pembayaran, lalu selesaikan transaksi.
                                        Halaman ini akan otomatis terupdate setelah pembayaran berhasil.
                                    </div>
                                </div>
                            </div>

                            {{-- Payment Methods --}}
                            <div class="payment-methods fade-up fade-up-delay-3">
                                <span class="method-chip"><i class="ti ti-building-bank"></i> Virtual Account</span>
                                <span class="method-chip"><i class="ti ti-qrcode"></i> QRIS</span>
                                <span class="method-chip"><i class="ti ti-wallet"></i> E-Wallet</span>
                                <span class="method-chip"><i class="ti ti-credit-card"></i> Kartu Kredit</span>
                            </div>

                            {{-- Trust Badges --}}
                            <div class="trust-row fade-up fade-up-delay-3">
                                <div class="trust-item secure">
                                    <i class="ti ti-shield-lock"></i>
                                    <span>100% Aman</span>
                                </div>
                                <div class="trust-item encrypted">
                                    <i class="ti ti-lock"></i>
                                    <span>Terenkripsi SSL</span>
                                </div>
                                <div class="trust-item support">
                                    <i class="ti ti-headset"></i>
                                    <span>Support 24/7</span>
                                </div>
                            </div>

                            {{-- Polling Indicator --}}
                            <div class="polling-indicator" id="pollingStatus">
                                <span class="polling-dot"></span>
                                <span>Memeriksa status pembayaran otomatis...</span>
                            </div>

                            <a href="{{ route('tagihan-pembayaran.index') }}" class="cta-secondary fade-up fade-up-delay-3">
                                <i class="ti ti-arrow-left"></i> Kembali ke Daftar Tagihan
                            </a>
                        </div>
                    @endif

                    {{-- ═══════════════════════════════════════════════
                         STATE: FAILED — Pembayaran Gagal
                    ═══════════════════════════════════════════════ --}}
                    @if($isFailed)
                        <div class="pay-hero failed">
                            <div class="status-icon failed fade-up">
                                <svg class="fail-svg" viewBox="0 0 52 52">
                                    <line x1="14" y1="14" x2="38" y2="38"/>
                                    <line x1="38" y1="14" x2="14" y2="38"/>
                                </svg>
                            </div>

                            <span class="status-badge failed fade-up fade-up-delay-1">
                                <i class="ti ti-alert-circle"></i> Pembayaran Gagal
                            </span>

                            <h2 class="pay-title fade-up fade-up-delay-1">Pembayaran Tidak Berhasil</h2>
                            <p class="pay-subtitle fade-up fade-up-delay-2">
                                Transaksi Anda tidak dapat diproses. Jangan khawatir — dana Anda aman dan tidak ditarik.
                            </p>
                        </div>

                        <div class="pay-body">
                            <div class="detail-card fade-up fade-up-delay-2">
                                <div class="detail-row">
                                    <span class="detail-label">Nomor Tagihan</span>
                                    <span class="detail-value mono">{{ $tagihan->NomorTagihan }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Periode</span>
                                    <span class="detail-value">{{ $periodeDate?->translatedFormat('F Y') ?? '-' }}</span>
                                </div>
                                <div class="detail-row highlight">
                                    <span class="detail-label">Total Tagihan</span>
                                    <span class="detail-value amount">Rp {{ number_format($tagihan->JumlahTagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="info-box danger fade-up fade-up-delay-2">
                                <i class="ti ti-alert-triangle"></i>
                                <div>
                                    <strong>Mengapa pembayaran gagal?</strong>
                                    <div style="font-size: .85rem; margin-top: 2px;">
                                        Saldo tidak cukup, kartu ditolak, atau transaksi dibatalkan.
                                        Silakan coba lagi dengan metode pembayaran lain.
                                    </div>
                                </div>
                            </div>

                            @if(Route::has('tagihan-pembayaran.retry'))
                                <a href="{{ route('tagihan-pembayaran.retry', $tagihan->id) }}" class="cta-primary danger-btn fade-up fade-up-delay-3">
                                    <i class="ti ti-refresh"></i> Coba Bayar Lagi
                                </a>
                            @endif

                            <a href="{{ route('tagihan-pembayaran.index') }}" class="cta-secondary fade-up fade-up-delay-3">
                                <i class="ti ti-list"></i> Lihat Daftar Tagihan
                            </a>
                        </div>
                    @endif

                    {{-- ═══════════════════════════════════════════════
                         STATE: EXPIRED — Kadaluarsa
                    ═══════════════════════════════════════════════ --}}
                    @if($isExpired)
                        <div class="pay-hero expired">
                            <div class="status-icon expired fade-up">
                                <svg class="expired-svg" viewBox="0 0 52 52">
                                    <circle cx="26" cy="26" r="24"/>
                                    <line x1="26" y1="14" x2="26" y2="28"/>
                                    <line x1="26" y1="28" x2="34" y2="32"/>
                                </svg>
                            </div>

                            <span class="status-badge expired fade-up fade-up-delay-1">
                                <i class="ti ti-clock-x"></i> Kadaluarsa
                            </span>

                            <h2 class="pay-title fade-up fade-up-delay-1">Link Pembayaran Kadaluarsa</h2>
                            <p class="pay-subtitle fade-up fade-up-delay-2">
                                Waktu pembayaran untuk tagihan ini telah berakhir. Silakan hubungi admin untuk membuat pembayaran baru.
                            </p>
                        </div>

                        <div class="pay-body">
                            <div class="detail-card fade-up fade-up-delay-2">
                                <div class="detail-row">
                                    <span class="detail-label">Nomor Tagihan</span>
                                    <span class="detail-value mono">{{ $tagihan->NomorTagihan }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Periode</span>
                                    <span class="detail-value">{{ $periodeDate?->translatedFormat('F Y') ?? '-' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Kadaluarsa pada</span>
                                    <span class="detail-value">{{ $tagihan->PaymentExpiredAt?->format('d M Y, H:i') ?? '-' }}</span>
                                </div>
                                <div class="detail-row highlight">
                                    <span class="detail-label">Total Tagihan</span>
                                    <span class="detail-value amount">Rp {{ number_format($tagihan->JumlahTagihan, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="info-box warning fade-up fade-up-delay-2">
                                <i class="ti ti-info-circle"></i>
                                <div>
                                    <strong>Perlu bantuan?</strong>
                                    <div style="font-size: .85rem; margin-top: 2px;">
                                        Hubungi tim support kami di <strong>support@maurekap.id</strong> atau WhatsApp admin untuk membuat link pembayaran baru.
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('tagihan-pembayaran.index') }}" class="cta-primary fade-up fade-up-delay-3">
                                <i class="ti ti-list"></i> Lihat Tagihan Lainnya
                            </a>

                            <a href="{{ route('home') }}" class="cta-secondary fade-up fade-up-delay-3">
                                <i class="ti ti-home"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    @endif

                </div>

                {{-- Footer Info --}}
                <div class="text-center mt-4" style="font-size: .78rem; color: var(--muted);">
                    <p class="mb-1">Dipersembahkan oleh <strong>Maurekap</strong> · Pembayaran aman oleh <strong>DOKU</strong></p>
                    <p class="mb-0">Butuh bantuan? <a href="mailto:support@maurekap.id" style="color: var(--brand);">support@maurekap.id</a></p>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ═══════════════════════════════════════
    // Countdown Real-Time (PENDING state)
    // ═══════════════════════════════════════
    @if($isPending && $tagihan->PaymentExpiredAt)
        (function() {
            const expiredAt = new Date(@json($tagihan->PaymentExpiredAt->toIso8601String())).getTime();
            const createdAt = new Date(@json($tagihan->created_at->toIso8601String())).getTime();
            const totalDuration = Math.max(1, expiredAt - createdAt);

            function pad(n) { return n < 10 ? '0' + n : n; }

            function updateCountdown() {
                const now = Date.now();
                const remaining = Math.max(0, expiredAt - now);

                // Update time display
                const hours = Math.floor(remaining / 3600000);
                const mins = Math.floor((remaining % 3600000) / 60000);
                const secs = Math.floor((remaining % 60000) / 1000);
                const timeEl = document.getElementById('countdownTime');
                if (timeEl) timeEl.textContent = `${pad(hours)}:${pad(mins)}:${pad(secs)}`;

                // Update progress bar
                const percent = (remaining / totalDuration) * 100;
                const fill = document.getElementById('countdownFill');
                if (fill) fill.style.width = percent + '%';

                if (remaining <= 0) {
                    clearInterval(countdownTimer);
                    window.location.reload();
                }
            }

            updateCountdown();
            var countdownTimer = setInterval(updateCountdown, 1000);
        })();
    @endif

    // ═══════════════════════════════════════
    // Polling: Cek Status Otomatis (PENDING)
    // ═══════════════════════════════════════
    @if($isPending)
        (function() {
            const checkUrl = "{{ route('tagihan-pembayaran.payment-status', $tagihan->id) }}";
            let attempts = 0;
            const maxAttempts = 180; // 15 menit
            const statusEl = document.getElementById('pollingStatus');

            const poller = setInterval(async function() {
                attempts++;
                try {
                    const res = await fetch(checkUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();

                    if (!data.success) return;

                    const status = data.data.payment_status;
                    console.log(`[Poll #${attempts}] Status:`, status);

                    if (status === 'PAID') {
                        clearInterval(poller);
                        if (statusEl) statusEl.innerHTML = '<i class="ti ti-check" style="color: var(--success);"></i> <span>Pembayaran terdeteksi! Memuat ulang...</span>';
                        setTimeout(() => window.location.reload(), 1200);
                    } else if (['FAILED', 'EXPIRED'].includes(status)) {
                        clearInterval(poller);
                        if (statusEl) statusEl.innerHTML = '<i class="ti ti-alert-circle"></i> <span>Status berubah, memuat ulang...</span>';
                        setTimeout(() => window.location.reload(), 1000);
                    }

                    if (attempts >= maxAttempts) {
                        clearInterval(poller);
                        if (statusEl) statusEl.innerHTML = '<i class="ti ti-clock"></i> <span>Polling dihentikan. Silakan refresh manual.</span>';
                    }
                } catch (e) {
                    console.error('Poll error:', e);
                }
            }, 5000);
        })();
    @endif

    // ═══════════════════════════════════════
    // Confetti Effect (Success state)
    // ═══════════════════════════════════════
    @if($isPaid)
        (function() {
            const colors = ['#2563eb', '#16a34a', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];
            const container = document.getElementById('confettiBox');
            if (!container) return;

            for (let i = 0; i < 40; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = Math.random() * 60 + '%';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = (Math.random() * 1) + 's';
                confetti.style.animationDuration = (1.5 + Math.random() * 1.5) + 's';
                confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                container.appendChild(confetti);
            }
        })();
    @endif
</script>
@endpush
@endsection
