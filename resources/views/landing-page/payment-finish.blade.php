{{-- Layout minimal, tanpa extends layout --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Pembayaran</title>
    <meta name="viewport" content="width=600, initial-scale=1">
    <style>
        body {
            background: #fafbfc;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .container {
            padding: 60px 0;
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px #0001;
        }
        .ok-big {
            width: 86px;
            height: 86px;
            margin-bottom: 24px;
        }
        .ok-big circle {
            fill: #eaf8f3;
            stroke: #53b383;
            stroke-width: 4;
        }
        .ok-big path {
            fill: none;
            stroke: #53b383;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .reg-code {
            margin: 35px 0 10px 0;
            background: #f3f5fa;
            border-radius: 8px;
            padding: 16px 10px 10px 10px;
        }
        .reg-code b {
            font-size: 1.4em;
            letter-spacing: 1px;
        }
        .btn-row {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            background: #398df4;
            color: #fff !important;
            padding: 10px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 1em;
            transition: background 0.15s;
        }
        .btn-blue:hover {
            background: #226dd9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">
            @if($pendaftaran->PaymentStatus === 'PAID')
                {{-- Ikon sukses --}}
                <svg class="ok-big" viewBox="0 0 84 84">
                    <circle cx="42" cy="42" r="40"/>
                    <path d="M27 43.5l10 10 20-22"/>
                </svg>
                <h2>Pembayaran Berhasil!</h2>
                <p>Terima kasih. Pendaftaran Anda sedang diproses.</p>
            @else
                <h2>Menunggu Konfirmasi Pembayaran</h2>
                <p>Jika Anda sudah melakukan pembayaran, status akan diperbarui otomatis dalam beberapa saat.</p>
            @endif

            <div class="reg-code">
                <small>NOMOR REGISTRASI</small><br>
                <b>{{ $pendaftaran->Kode ?? $pendaftaran->DokuInvoiceNumber }}</b>
            </div>

            <div class="btn-row">
                <a class="btn btn-blue" href="{{ url('/') }}">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</body>
</html>
