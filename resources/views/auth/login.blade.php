<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - POS Agen Ekspedisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            /* Ganti background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #f5f7fa 0%, #c3cfe2 100%);
            /* Alternatif: background: url('https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=1050&q=80') no-repeat center center fixed; background-size: cover; */
        }

        .login-card {
            background: white;
            border-radius: 32px;
            box-shadow: 0 15px 60px rgba(30,60,114,0.15), 0 1.5px 6px rgba(30,60,114,0.07);
            overflow: hidden;
            max-width: 480px; /* Diperbesar */
            width: 100%;
        }

        .login-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 50px 36px 32px 36px;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 32px;
        }

        .login-header p {
            margin: 12px 0 0 0;
            opacity: 0.92;
            font-size: 15px;
        }

        .login-body {
            padding: 44px 36px 32px 36px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #e1e8ed;
            padding: 14px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 0.2rem rgba(42, 82, 152, 0.12);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 2px solid #e1e8ed;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #6c757d;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .btn-login {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-weight: 700;
            font-size: 18px;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            color: #fff;
            margin-top: 8px;
            display: block;
        }

        .btn-login:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 5px 22px rgba(42, 82, 152, 0.31);
        }

        .alert {
            border-radius: 12px;
            font-size: 15px;
        }

        .logo-icon {
            font-size: 56px;
            margin-bottom: 18px;
        }

        .footer-text {
            text-align: center;
            margin-top: 28px;
            color: #6c757d;
            font-size: 13px;
        }

        .eye-btn {
            background: none;
            border: none;
            box-shadow: none;
            outline: none;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-box-seam logo-icon"></i>
            <h2>POS AGEN EKSPEDISI</h2>
            <p>Sistem Pencatatan Pendapatan & Laporan</p>
        </div>

        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Masukkan Email"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Masukkan Password"
                            required
                        >
                        <button type="button" class="input-group-text eye-btn" onclick="togglePassword()" tabindex="-1">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    Login
                </button>
            </form>

            <div class="footer-text">
                <p class="mb-0">&copy; 2026 POS Agen Ekspedisi. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
