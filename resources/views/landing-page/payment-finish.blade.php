<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran — Maurekap</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #2563EB;
            --blue-600: #1D4ED8;
            --blue-700: #1E40AF;
            --blue-50: #EFF6FF;
            --blue-100: #DBEAFE;
            --navy: #0F1B33;
            --ink: #0F172A;
            --muted: #5B6B84;
            --line: #E5EAF3;
            --green: #16A34A;
            --green-50: #DCFCE7;
            --orange: #F97316;
            --red: #EF4444;
            --bg: #F6F8FC;
            --font: 'Plus Jakarta Sans', system-ui, sans-serif;
            --mono: 'JetBrains Mono', ui-monospace, monospace;
            --shadow: 0 10px 30px rgba(15, 27, 51, .08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container { width: min(640px, 92%); margin-inline: auto; }
        .mono { font-family: var(--mono); }

        .mini-header { background: #fff; border-bottom: 1px solid var(--line); padding: 18px 0; }
        .mini-header .container { display: flex; align-items: center; justify-content: space-between; }
        .logo { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.1rem; color: var(--navy); text-decoration: none; }
        .logo-badge { width: 32px; height: 32px; border-radius: 8px; background: var(--blue); display: grid; place-items: center; color: #fff; font-size: .85rem; font-weight: 800; }

        .main-wrap { flex: 1; padding: clamp(40px, 8vw, 80px) 0; display: flex; align-items: center; }

        .success {
            width: 100%; background: #fff; border: 1px solid var(--line);
            border-radius: 20px; box-shadow: var(--shadow);
            padding: clamp(28px, 5vw, 48px); text-align: center;
        }
        h2 { color: var(--navy); font-size: clamp(1.4rem, 1.1rem + 1.6vw, 1.9rem); font-weight: 800; margin-bottom: 10px; }
        .success > p { color: var(--muted); font-size: .95rem; max-width: 44ch; margin-inline: auto; }

        .ok-big { width: 84px; height: 84px; margin: 0 auto 22px; }
        .ok-big circle { stroke: var(--green); stroke-width: 2.5; fill: var(--green-50); stroke-dasharray: 260; stroke-dashoffset: 260; animation: draw 1s .1s ease forwards; }
        .ok-big path { stroke: var(--green); stroke-width: 4; fill: none; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 60; stroke-dashoffset: 60; animation: draw .6s .8s ease forwards; }
        @keyframes draw { to { stroke-dashoffset: 0; } }

        .reg-code { margin: 24px auto; padding: 14px 22px; background: var(--blue-50); border: 1.5px dashed var(--blue); border-radius: 12px; display: inline-block; }
        .reg-code small { display: block; font-size: .68rem; letter-spacing: .14em; color: var(--muted); font-weight: 700; margin-bottom: 4px; }
        .reg-code b { font-family: var(--mono); font-size: 1.15rem; color: var(--blue-700); letter-spacing: .04em; }

        .pending-icon { width: 84px; height: 84px; margin: 0 auto 22px; border-radius: 50%; background: var(--orange); color: #fff; display: grid; place-items: center; animation: pulse 2s ease-in-out infinite; }
        .pending-icon svg { width: 40px; height: 40px; }
        @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: .85; } }

        .status-bar {
            margin: 20px auto; padding: 14px 20px; background: #FEF3C7; border: 1px solid #FCD34D;
            border-radius: 10px; color: #92400E; font-size: .88rem; font-weight: 600; max-width: 400px;
        }
        .status-bar.success-state { background: var(--green-50); border-color: var(--green); color: #166534; }
        .status-bar.error-state { background: #FEE2E2; border-color: var(--red); color: #991B1B; }

        .credentials { margin: 26px auto 0; padding: 22px; background: linear-gradient(135deg, #EFF6FF, #DBEAFE); border: 1px solid var(--blue); border-radius: 14px; text-align: left; max-width: 440px; }
        .credentials h3 { color: var(--blue-700); font-size: 1rem; font-weight: 800; margin-bottom: 4px; }
        .credentials .sub { font-size: .82rem; color: var(--muted); margin-bottom: 16px; }
        .cred-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 14px; background: #fff; border: 1px solid var(--line); border-radius: 10px; margin-bottom: 10px; }
        .cred-row .label { font-size: .75rem; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .cred-row .value { font-family: var(--mono); font-size: .92rem; color: var(--navy); font-weight: 500; overflow: hidden; text-overflow: ellipsis; }
        .cred-row .value.email { font-family: var(--font); font-size: .88rem; }
        .copy-btn { background: var(--blue-50); color: var(--blue); border: 1px solid var(--line); padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: .72rem; font-weight: 700; transition: .2s; }
        .copy-btn:hover { background: var(--blue); color: #fff; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 9px; font-family: var(--font); font-weight: 700; font-size: .95rem; padding: 14px 28px; border-radius: 10px; border: 2px solid transparent; cursor: pointer; transition: .22s; text-decoration: none; white-space: nowrap; }
        .btn-blue { background: var(--blue); color: #fff; box-shadow: 0 8px 20px rgba(37, 99, 235, .28); }
        .btn-blue:hover { background: var(--blue-600); transform: translateY(-2px); }
        .btn-outline { background: #fff; color: var(--blue); border-color: var(--blue); }
        .btn-outline:hover { background: var(--blue-50); }
        .btn-ghost { background: transparent; color: var(--muted); border-color: var(--line); padding: 10px 20px; font-size: .85rem; }
        .btn-row { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 20px; }

        .spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid #92400E; border-top-color: transparent; border-radius: 50%; animation: spin 1s linear infinite; vertical-align: middle; margin-right: 6px; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(100px); background: var(--navy); color: #fff; padding: 12px 24px; border-radius: 10px; font-size: .85rem; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,.2); opacity: 0; transition: all .3s ease; z-index: 100; }
        .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

        .mini-footer { background: var(--navy); color: #9FB0C9; padding: 20px 0; font-size: .78rem; text-align: center; }
        .mini-footer a { color: #fff; text-decoration: none; margin: 0 10px; }

        @media (max-width: 480px) {
            .cred-row { flex-direction: column; align-items: stretch; gap: 6px; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>

<header class="mini-header">
    <div class="container">
        <a class="logo" href="{{ url('/') }}">
            <span class="logo-badge">M</span> MAUREKAP
        </a>
        <a class="btn btn-ghost" href="{{ route('login') }}">Login</a>
    </div>
</header>

<main class="main-wrap">
    <div class="container">

        @php
            $isPaid    = $pendaftaran->PaymentStatus === 'PAID';
            $isFailed  = $pendaftaran->PaymentStatus === 'FAILED';
            $isExpired = $pendaftaran->PaymentStatus === 'EXPIRED';
            $isPending = !$isPaid && !$isFailed && !$isExpired;
        @endphp

        @if($isPaid && isset($credentials) && $credentials)
            {{-- ═══ SUKSES + KREDENSIAL TERSEDIA ═══ --}}
            <div class="success">
                <svg class="ok-big" viewBox="0 0 84 84">
                    <circle cx="42" cy="42" r="40"/>
                    <path d="M27 43.5l10 10 20-22"/>
                </svg>
                <h2>Pembayaran Berhasil! 🎉</h2>
                <p>Akun Maurekap Anda sudah aktif dan siap digunakan.</p>

                <div class="reg-code">
                    <small>NOMOR REGISTRASI</small>
                    <b>{{ $pendaftaran->Kode ?? $pendaftaran->DokuInvoiceNumber }}</b>
                </div>

                <div class="credentials">
                    <h3>🔐 Kredensial Login Anda</h3>
                    <p class="sub">Gunakan data di bawah untuk login ke dashboard Maurekap:</p>

                    <div class="cred-row">
                        <span class="label">Email</span>
                        <span class="value email" id="emailValue">{{ $credentials['email'] }}</span>
                        <button class="copy-btn" data-copy="emailValue">Salin</button>
                    </div>
                    <div class="cred-row">
                        <span class="label">Password</span>
                        <span class="value" id="passValue">{{ $credentials['password'] }}</span>
                        <button class="copy-btn" data-copy="passValue">Salin</button>
                    </div>
                    <div class="cred-row">
                        <span class="label">Tenant</span>
                        <span class="value email">{{ $credentials['tenant'] ?? '-' }}</span>
                    </div>
                </div>

                <div class="btn-row">
                    <a class="btn btn-blue" href="{{ route('login') }}?email={{ urlencode($credentials['email']) }}">
                        Login Sekarang →
                    </a>
                </div>
                <a class="btn btn-ghost" href="{{ url('/') }}" style="margin-top:16px;">← Kembali ke Beranda</a>
            </div>

       @elseif($isPaid)
    {{-- ═══ SUKSES: Tampilkan Kredensial (Fresh atau Recovery) ═══ --}}
    <div class="success">
        <svg class="ok-big" viewBox="0 0 84 84">
            <circle cx="42" cy="42" r="40"/>
            <path d="M27 43.5l10 10 20-22"/>
        </svg>

        <h2>Pembayaran Berhasil! 🎉</h2>

        @if(isset($credentials['is_recovered']) && $credentials['is_recovered'])
            <p style="color: #b45309; background: #fef3c7; border: 1px solid #fde68a; padding: 10px; border-radius: 8px; font-size: .9rem; margin-bottom: 20px;">
                ⚠️ <strong>Sesi Anda telah berakhir.</strong><br>
                Password baru telah dibuat otomatis untuk Anda. Harap simpan dan segera ganti setelah login.
            </p>
        @else
            <p>Akun Maurekap Anda sudah aktif dan siap digunakan.</p>
        @endif

        <div class="reg-code">
            <small>NOMOR REGISTRASI</small>
            <b>{{ $pendaftaran->Kode ?? $pendaftaran->DokuInvoiceNumber }}</b>
        </div>

        @if($credentials)
            <div class="credentials">
                <h3>🔐 Kredensial Login Anda</h3>
                <p class="sub">Gunakan data di bawah untuk login ke dashboard Maurekap:</p>

                <div class="cred-row">
                    <span class="label">Email</span>
                    <span class="value email" id="emailValue">{{ $credentials['email'] }}</span>
                    <button class="copy-btn" data-copy="emailValue">Salin</button>
                </div>
                <div class="cred-row">
                    <span class="label">Password</span>
                    <span class="value" id="passValue">{{ $credentials['password'] }}</span>
                    <button class="copy-btn" data-copy="passValue">Salin</button>
                </div>
                <div class="cred-row">
                    <span class="label">Tenant</span>
                    <span class="value email">{{ $credentials['tenant'] ?? '-' }}</span>
                </div>
            </div>

            <div class="btn-row">
                <a class="btn btn-blue" href="{{ route('login') }}?email={{ urlencode($credentials['email']) }}">
                    Login Sekarang →
                </a>
            </div>
        @else
            {{-- Fallback jika user belum ter-create (sangat jarang, race condition) --}}
            <div class="status-bar">
                <span class="spinner"></span> Akun sedang diproses, silakan refresh halaman dalam beberapa detik.
            </div>
            <script>setTimeout(() => window.location.reload(), 3000);</script>
        @endif

        <a class="btn btn-ghost" href="{{ url('/') }}" style="margin-top:16px;">← Kembali ke Beranda</a>
    </div>

        @elseif($isFailed || $isExpired)
            {{-- ═══ GAGAL / KADALUARSA ═══ --}}
            <div class="success">
                <div class="pending-icon" style="background: var(--red);">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
                <h2>{{ $isFailed ? 'Pembayaran Gagal' : 'Pembayaran Kadaluarsa' }}</h2>
                <p>{{ $isFailed ? 'Silakan coba lagi dengan metode lain.' : 'Link pembayaran sudah expired. Silakan daftar ulang.' }}</p>
                <div class="reg-code">
                    <small>INVOICE</small>
                    <b>{{ $pendaftaran->DokuInvoiceNumber }}</b>
                </div>
                <div class="btn-row">
                    <a class="btn btn-blue" href="{{ route('pendaftaran-tenant.create') }}">Daftar Ulang</a>
                    <a class="btn btn-outline" href="{{ url('/') }}">Beranda</a>
                </div>
            </div>

        @else
            {{-- ═══ PENDING — DENGAN POLLING ═══ --}}
            <div class="success">
                <div class="pending-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>

                <h2>Menunggu Pembayaran</h2>
                <p>Silakan selesaikan pembayaran di halaman DOKU. Status akan otomatis diperbarui.</p>

                <div class="reg-code">
                    <small>NOMOR INVOICE</small>
                    <b>{{ $pendaftaran->DokuInvoiceNumber }}</b>
                </div>

                <div class="status-bar" id="statusBar">
                    <span class="spinner"></span>
                    <span id="statusText">Memeriksa status pembayaran...</span>
                </div>

                @if($pendaftaran->DokuPaymentUrl)
                    <div class="btn-row">
                        <a class="btn btn-blue" href="{{ $pendaftaran->DokuPaymentUrl }}" target="_blank" rel="noopener">
                            Buka Halaman Pembayaran
                        </a>
                    </div>
                @endif

                <p style="margin-top:16px; font-size:.82rem; color:var(--muted);">
                    💡 Halaman ini otomatis mengecek status setiap <strong>3 detik</strong>.<br>
                    Anda bisa tetap di halaman ini sambil menunggu.
                </p>

                <a class="btn btn-ghost" href="{{ url('/') }}" style="margin-top:12px;">← Kembali ke Beranda</a>
            </div>

            {{-- ═══ SCRIPT POLLING ═══ --}}
            <script>
            (function() {
                var checkUrl    = "{{ route('pendaftaran.payment.status', $pendaftaran->id) }}";
                var statusBar   = document.getElementById('statusBar');
                var statusText  = document.getElementById('statusText');
                var attempts    = 0;
                var maxAttempts = 120; // 6 menit (3 detik x 120)
                var interval    = 3000; // 3 detik

                function updateStatus(message, type) {
                    if (statusText) statusText.innerHTML = message;
                    if (statusBar) {
                        statusBar.className = 'status-bar';
                        if (type === 'success') statusBar.classList.add('success-state');
                        if (type === 'error')   statusBar.classList.add('error-state');
                    }
                }

                function showToast(msg) {
                    var toast = document.getElementById('toast');
                    if (!toast) {
                        toast = document.createElement('div');
                        toast.id = 'toast';
                        toast.className = 'toast';
                        document.body.appendChild(toast);
                    }
                    toast.textContent = msg;
                    toast.classList.add('show');
                }

                var poller = setInterval(async function() {
                    attempts++;
                    try {
                        var res  = await fetch(checkUrl, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        var data = await res.json();

                        if (!data.success) {
                            updateStatus('Gagal memeriksa status...', 'error');
                            return;
                        }

                        var d = data.data;
                        console.log('[' + attempts + '] Status:', d.payment_status, '| Provisioned:', d.is_provisioned);

                        if (d.payment_status === 'PAID' && d.is_provisioned) {
                            // SUKSES! Reload untuk tampilkan kredensial
                            clearInterval(poller);
                            updateStatus('✅ Pembayaran berhasil! Memuat akun Anda...', 'success');
                            showToast('🎉 Pembayaran berhasil! Akun Anda sudah aktif.');
                            setTimeout(function() { window.location.reload(); }, 2000);

                        } else if (d.payment_status === 'PAID' && !d.is_provisioned) {
                            updateStatus('<span class="spinner"></span> Membuat akun Anda... ⚡', 'success');

                        } else if (d.payment_status === 'FAILED') {
                            clearInterval(poller);
                            updateStatus('❌ Pembayaran gagal.', 'error');
                            setTimeout(function() { window.location.reload(); }, 2000);

                        } else if (d.payment_status === 'EXPIRED') {
                            clearInterval(poller);
                            updateStatus('⏰ Pembayaran kadaluarsa.', 'error');
                            setTimeout(function() { window.location.reload(); }, 2000);

                        } else {
                            updateStatus('<span class="spinner"></span> Menunggu pembayaran... (cek ke-' + attempts + ')');
                        }

                        if (attempts >= maxAttempts) {
                            clearInterval(poller);
                            updateStatus('Waktu habis. Silakan refresh manual atau cek email.', 'error');
                        }
                    } catch (err) {
                        console.error('Poll error:', err);
                        updateStatus('Koneksi bermasalah, mencoba lagi...');
                    }
                }, interval);
            })();
            </script>
        @endif

    </div>
</main>

<footer class="mini-footer">
    <div class="container">
        © 2026 Maurekap — PT Maurekap Teknologi Logistik<br>
        <a href="#">Kebijakan Privasi</a> · <a href="#">Syarat & Ketentuan</a> · <a href="mailto:support@maurekap.id">support@maurekap.id</a>
    </div>
</footer>

<div class="toast" id="toast"></div>

<script>
// Copy to clipboard
document.querySelectorAll('.copy-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var targetId = btn.getAttribute('data-copy');
        var target   = document.getElementById(targetId);
        if (target) {
            navigator.clipboard.writeText(target.textContent.trim()).then(function() {
                showToast('✓ Disalin ke clipboard');
                btn.textContent = 'Tersalin!';
                setTimeout(function() { btn.textContent = 'Salin'; }, 2000);
            });
        }
    });
});
function showToast(msg) {
    var toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.classList.add('show');
    setTimeout(function() { toast.classList.remove('show'); }, 2500);
}
</script>

</body>
</html>
