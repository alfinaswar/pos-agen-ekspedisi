<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mengarahkan ke Pembayaran — Maurekap</title>
    <meta http-equiv="refresh" content="3;url={{ $pendaftaran->DokuPaymentUrl }}">
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; display: flex;
               align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 16px; max-width: 480px;
                text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,.08); }
        .spinner { width: 48px; height: 48px; border: 4px solid #dbeafe;
                   border-top-color: #2563eb; border-radius: 50%;
                   animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { to { transform: rotate(360deg); } }
        h2 { color: #0f172a; margin: 0 0 8px; }
        p { color: #64748b; margin: 8px 0; }
        .tip { background: #eff6ff; border: 1px solid #dbeafe; border-radius: 10px;
               padding: 16px; margin-top: 20px; font-size: 14px; color: #1e40af; text-align: left; }
        .tip b { display: block; margin-bottom: 4px; color: #1e40af; }
        .btn { display: inline-block; background: #2563eb; color: white; padding: 12px 24px;
               border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <h2>Mengarahkan ke DOKU Payment...</h2>
        <p>Anda akan diarahkan ke halaman pembayaran dalam 3 detik.</p>

        <div class="tip">
            <b>💡 Setelah selesai bayar:</b>
            Halaman DOKU akan otomatis kembali ke Maurekap.
            Kalau tidak otomatis, klik tombol <b>"Kembali ke Merchant"</b> di halaman DOKU.
        </div>

        <a href="{{ $pendaftaran->DokuPaymentUrl }}" class="btn">
            Klik di sini kalau tidak otomatis →
        </a>
    </div>

    <script>
        // Auto redirect dalam 3 detik
        setTimeout(function() {
            window.location.href = @json($pendaftaran->DokuPaymentUrl);
        }, 3000);
    </script>
</body>
</html>
