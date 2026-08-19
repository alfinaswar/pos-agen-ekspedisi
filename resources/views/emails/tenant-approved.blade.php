<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Tenant Disetujui</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background-color: #0d6efd; color: #ffffff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 32px 24px; color: #334155; line-height: 1.6; }
        .credentials-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0d6efd; border-radius: 6px; padding: 20px; margin: 24px 0; }
        .credentials-box h3 { margin-top: 0; color: #0f172a; font-size: 16px; }
        .credential-item { margin-bottom: 12px; }
        .credential-item:last-child { margin-bottom: 0; }
        .label { font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 600; letter-spacing: 0.5px; }
        .value { font-size: 16px; color: #0f172a; font-weight: 600; margin-top: 4px; word-break: break-all; }
        .btn { display: inline-block; background-color: #0d6efd; color: #ffffff !important; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; margin-top: 16px; }
        .footer { background-color: #f8fafc; padding: 20px 24px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Selamat Datang!</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Halo <strong>{{ $Nama }}</strong>,</p>
            <p>Kami senang menginformasikan bahwa pendaftaran tenant Anda telah <strong>berhasil disetujui</strong>. Akun Administrator Anda telah berhasil dibuat.</p>
            <p>Silakan gunakan kredensial di bawah ini untuk melakukan login pertama kali ke dalam sistem:</p>

            <!-- Credentials Box -->
            <div class="credentials-box">
                <h3>🔐 Informasi Login Anda</h3>

                <div class="credential-item">
                    <div class="label">Email / Username</div>
                    <div class="value">{{ $Email }}</div>
                </div>

                <div class="credential-item">
                    <div class="label">Password Sementara</div>
                    <div class="value" style="color: #dc2626;">{{ $Password }}</div>
                </div>
            </div>

            <p style="font-size: 14px; color: #64748b;">
                <strong>⚠️ Penting:</strong> Demi keamanan, kami sangat menyarankan Anda untuk segera mengubah password ini setelah berhasil login pertama kali.
            </p>

            <div style="text-align: center;">
                <a href="{{ $LoginUrl }}" class="btn">Login ke Dashboard</a>
            </div>

            <p style="margin-top: 32px;">Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim support kami.</p>
            <p>Salam hangat,<br><strong>Tim Manajemen Sistem</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon untuk tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Manajemen Tenant System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
