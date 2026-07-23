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
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #f3f4f8 0%, #d1dde6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container-login {
            width: 100vw;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: unset;
        }
        .login-card {
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 12px 42px 0 rgba(44,62,80,0.12), 0 1.5px 4.5px rgba(44,62,80,0.08);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            padding: 0;
            margin: 24px 8px;
            transition: box-shadow 0.2s;
            animation: fadeIn 0.8s;
        }
        @keyframes fadeIn {
            from { opacity:0; transform: translateY(30px);}
            to { opacity:1; transform: translateY(0);}
        }
        .login-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            padding: 38px 24px 18px 24px;
            text-align: center;
            border-top-left-radius: 28px;
            border-top-right-radius: 28px;
        }
        .logo-icon {
            font-size: 50px;
            margin-bottom: 10px;
            color: #fff;
            background: rgba(255,255,255,0.09);
            border-radius: 50%;
            padding: 13px;
            display: inline-block;
        }
        .login-header h2 {
            font-weight: 800;
            font-size: 26px;
            margin-bottom: 6px;
            letter-spacing: 1.3px;
        }
        .login-header p {
            font-size: 15px;
            opacity: 0.92;
            margin-bottom: 0;
        }
        .login-body {
            padding: 30px 24px 24px 24px;
        }
        .form-label {
            font-weight: 600;
            color: #223248;
            margin-bottom: 7px;
        }
        .form-control {
            border-radius: 10px;
            border: 1.5px solid #dde7f0;
            padding: 12px 14px;
            font-size: 15px;
            box-shadow: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #f7fafd;
        }
        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0px 0px 0px 2.5px rgba(42,82,152,0.11);
            background: #fff;
        }
        .input-group-text {
            background: #f3f5fa;
            border: 1.5px solid #dde7f0;
            border-right: 0;
            border-radius: 10px 0 0 10px;
            color: #2a5298;
        }
        .input-group .form-control {
            border-left: 0;
            border-radius: 0 10px 10px 0;
        }
        .eye-btn {
            background: none;
            border: none;
            box-shadow: none;
            outline: none;
            color: #2a5298;
        }
        .btn-login {
            background: linear-gradient(135deg, #2a5298 0%, #1e3c72 100%);
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-weight: 700;
            font-size: 17px;
            width: 100%;
            color: #fff;
            margin-top: 8px;
            box-shadow: 0 4px 16px rgba(42, 82, 152, 0.14);
            transition: background 0.2s, transform 0.18s, box-shadow 0.18s;
        }
        .btn-login:hover, .btn-login:focus {
            background: linear-gradient(135deg, #254675 0%, #184092 100%);
            transform: translateY(-1.5px) scale(1.015);
            box-shadow: 0 7px 24px rgba(42, 82, 152, 0.19);
        }
        .alert {
            border-radius: 10px;
            font-size: 15px;
            padding: 10px 12px;
        }
        .footer-text {
            text-align: center;
            margin-top: 18px;
            color: #7f8fa6;
            font-size: 13px;
        }

        /* Responsive adjustments */
        @media (max-width: 575.98px) {
            .login-card {
                max-width: 100%;
                margin: 0 2vw;
                border-radius: 0;
                box-shadow: 0 3px 26px rgba(44,62,80,0.11);
            }
            .login-header, .login-body {
                padding-left: 7vw;
                padding-right: 7vw;
            }
            .login-header {
                padding-top: 22px;
                padding-bottom: 15px;
            }
        }
        @media (max-width: 400px) {
            .login-header, .login-body {
                padding-left: 2vw;
                padding-right: 2vw;
            }
        }
        ::selection {
            background: #DAE5FA;
        }
        input:-webkit-autofill, input:-webkit-autofill:focus {
            transition: background-color 5000s ease-in-out 0s;
            -webkit-text-fill-color: #223248 !important;
        }
    </style>
</head>
<body>
    <div class="container-login">
        <div class="login-card shadow">
            <div class="login-header">
                <span class="logo-icon"><i class="bi bi-box-seam"></i></span>
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
                <form method="POST" action="{{ route('login') }}" autocomplete="on">
                    @csrf

                    <div class="mb-3">
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
                                autocomplete="email"
                            >
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label d-flex justify-content-between align-items-center">
                            <span>Password</span>

                        </label>
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
                                autocomplete="current-password"
                            >
                            <button type="button" class="input-group-text eye-btn" onclick="togglePassword()" tabindex="-1" aria-label="Tampilkan Password">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-login w-100 mt-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </button>
                </form>
                <div class="footer-text">
                    <p class="mb-0 mt-3">&copy; {{ date('Y') }} POS Agen Ekspedisi. All rights reserved.</p>
                </div>
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
