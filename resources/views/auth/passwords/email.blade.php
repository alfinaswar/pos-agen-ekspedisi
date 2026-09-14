<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - POS Agen Ekspedisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh; margin: 0; padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #f3f4f8 0%, #d1dde6 100%);
            display: flex; align-items: center; justify-content: center;
        }
        .container-login {
            width: 100vw; min-height: 100vh; display: flex;
            align-items: center; justify-content: center; background: unset;
        }
        .login-card {
            background: #fff; border-radius: 28px;
            box-shadow: 0 12px 42px 0 rgba(44,62,80,0.12), 0 1.5px 4.5px rgba(44,62,80,0.08);
            overflow: hidden; width: 100%; max-width: 450px; padding: 0;
            margin: 24px 8px; transition: box-shadow 0.2s; animation: fadeIn 0.8s;
        }
        @keyframes fadeIn {
            from { opacity:0; transform: translateY(30px);}
            to { opacity:1; transform: translateY(0);}
        }
        .login-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff; padding: 38px 24px 24px 24px; text-align: center;
            border-top-left-radius: 28px; border-top-right-radius: 28px;
        }
        .login-header h2 {
            font-weight: 800; font-size: 26px; margin-bottom: 6px; letter-spacing: 1.3px;
        }
        .login-header p {
            font-size: 15px; opacity: 0.92; margin-bottom: 0;
        }
        .login-body { padding: 30px 24px 24px 24px; }
        .form-label { font-weight: 600; color: #223248; margin-bottom: 7px; }
        .form-control {
            border-radius: 10px; border: 1.5px solid #dde7f0; padding: 12px 14px;
            font-size: 15px; box-shadow: none; transition: border-color 0.2s, box-shadow 0.2s;
            background: #f7fafd;
        }
        .form-control:focus {
            border-color: #2a5298; box-shadow: 0px 0px 0px 2.5px rgba(42,82,152,0.11); background: #fff;
        }
        .input-group-text {
            background: #f3f5fa; border: 1.5px solid #dde7f0; border-right: 0;
            border-radius: 10px 0 0 10px; color: #2a5298;
        }
        .input-group .form-control { border-left: 0; border-radius: 0 10px 10px 0; }
        .btn-login {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%); border: none;
            border-radius: 10px; padding: 13px; font-weight: 700; font-size: 17px;
            width: 100%; color: #fff; margin-top: 8px;
            box-shadow: 0 4px 16px rgba(42, 82, 152, 0.14);
            transition: background 0.2s, transform 0.18s, box-shadow 0.18s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #254675 0%, #184092 100%);
            transform: translateY(-1.5px) scale(1.015);
            box-shadow: 0 7px 24px rgba(42, 82, 152, 0.19);
        }
        .alert { border-radius: 10px; font-size: 15px; padding: 10px 12px; }
        .footer-text { text-align: center; margin-top: 18px; color: #7f8fa6; font-size: 13px; }
        .back-link { color: #2a5298; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; transition: color 0.2s; }
        .back-link:hover { color: #1e3c72; }
        @media (max-width: 575.98px) {
            .login-card { max-width: 100%; margin: 0 2vw; border-radius: 0; }
            .login-header, .login-body { padding-left: 7vw; padding-right: 7vw; }
        }
    </style>
</head>
<body>
    <div class="container-login">
        <div class="login-card shadow">
            <div class="login-header">
                <img src="{{ asset('img/logo/maurekap-icon-hd-transparent.png') }}" alt="Logo" style="height: 80px; margin-bottom: 10px;">
                <h2>Lupa Password</h2>
                <p>Masukkan email Anda untuk menerima tautan reset</p>
            </div>
            <div class="login-body">
                @if (session('status'))
                    <div class="alert alert-success fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="Masukkan Email Terdaftar" required autofocus>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-login w-100 mt-2">
                        <i class="bi bi-send me-1"></i> Kirim Tautan Reset
                    </button>
                </form>

                <div class="footer-text">
                    <a href="{{ route('login') }}" class="back-link">
                        <i class="bi bi-arrow-left"></i> Kembali ke Login
                    </a>
                    <p class="mb-0 mt-3">&copy; {{ date('Y') }} POS Agen Ekspedisi. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
