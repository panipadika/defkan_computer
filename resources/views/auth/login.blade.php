@extends('layouts.app')

@section('content')
    <div class="auth-compact-page auth-login-page">
        <div class="auth-card auth-login-card">
            <div class="auth-decor auth-decor-right"></div>

            <div class="auth-card-content">
                <div class="auth-header">
                    <h2>Selamat Datang</h2>
                    <p class="text-muted">Masuk ke akun Defkan Computer Anda</p>
                </div>

                <form id="login-form" class="auth-form">
                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Email</label>
                        <input type="email" id="email" class="form-control auth-control" placeholder="nama@email.com"
                            required>
                    </div>

                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Password</label>
                        <div class="auth-password-wrap">
                            <input type="password" id="password" class="form-control auth-control" placeholder="••••••••"
                                required>
                            <button type="button" id="toggle-password" onclick="togglePasswordVisibility()"
                                class="auth-eye-btn" title="Lihat kata sandi">
                                <i data-lucide="eye" class="icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-forgot-row">
                        <a href="https://wa.me/6283897628556?text=Halo%20Defkan%20Computer%2C%20saya%20lupa%20kata%20sandi%20akun%20saya.%20Mohon%20bantuannya."
                            target="_blank" class="text-primary-gradient auth-small-link">
                            Lupa kata sandi?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary auth-main-btn" id="btn-login">
                        Masuk
                    </button>
                </form>

                <div class="auth-divider">
                    <div></div>
                    <span class="text-muted">atau</span>
                    <div></div>
                </div>

                <a href="{{ route('google.redirect') }}" class="btn btn-outline auth-google-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05" />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335" />
                    </svg>
                    Masuk dengan Google
                </a>

                <p class="text-muted auth-bottom-text">
                    Belum punya akun?
                    <a href="/register" class="text-primary-gradient">Daftar di sini</a>
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        html,
        body {
            overflow: hidden !important;
        }

        .auth-compact-page {
            min-height: calc(100dvh - 86px);
            height: calc(100dvh - 86px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            overflow: hidden;
        }

        .auth-card {
            width: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 22px;
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, rgba(226, 232, 240, .95));
            box-shadow: 0 16px 42px rgba(15, 23, 42, .08);
        }

        .auth-login-card {
            max-width: 420px;
        }

        .auth-card-content {
            position: relative;
            z-index: 1;
            padding: 26px 30px 24px;
        }

        .auth-decor {
            position: absolute;
            width: 96px;
            height: 96px;
            background: var(--primary, #2563eb);
            filter: blur(42px);
            opacity: .25;
            z-index: 0;
            pointer-events: none;
        }

        .auth-decor-right {
            top: -24px;
            right: -26px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .auth-header h2 {
            margin: 0;
            font-size: 27px;
            line-height: 1.12;
            font-weight: 900;
            letter-spacing: -.03em;
            color: var(--text-main, #0f172a);
        }

        .auth-header p {
            margin: 8px 0 0;
            font-size: 14px;
            line-height: 1.35;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .auth-form-group {
            margin: 0 !important;
        }

        .auth-label {
            display: block;
            margin-bottom: 6px !important;
            font-size: 12.5px !important;
            font-weight: 800 !important;
            color: var(--text-muted, #64748b) !important;
        }

        .auth-control {
            height: 40px !important;
            min-height: 40px !important;
            padding: 0 14px !important;
            border-radius: 9px !important;
            font-size: 14px !important;
            line-height: 40px !important;
        }

        .auth-password-wrap {
            position: relative;
        }

        .auth-password-wrap .auth-control {
            padding-right: 42px !important;
        }

        .auth-eye-btn {
            position: absolute;
            right: 9px;
            top: 50%;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            border: none;
            background: transparent;
            cursor: pointer;
            color: var(--text-muted, #64748b);
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .auth-eye-btn svg,
        .auth-eye-btn .icon {
            width: 18px !important;
            height: 18px !important;
        }

        .auth-forgot-row {
            text-align: right;
            margin-top: -2px;
        }

        .auth-small-link {
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 700;
        }

        .auth-main-btn,
        .auth-google-btn {
            width: 100%;
            height: 40px !important;
            min-height: 40px !important;
            border-radius: 9px !important;
            padding: 0 14px !important;
            justify-content: center;
            font-size: 14px !important;
            font-weight: 800 !important;
        }

        .auth-main-btn {
            margin-top: 2px;
        }

        .auth-google-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            margin: 17px 0;
        }

        .auth-divider div {
            flex: 1;
            height: 1px;
            background: var(--glass-border, #e5e7eb);
        }

        .auth-divider span {
            padding: 0 12px;
            font-size: 12px;
        }

        .auth-bottom-text {
            text-align: center;
            margin: 17px 0 0;
            font-size: 13px;
            line-height: 1.35;
        }

        .auth-bottom-text a {
            text-decoration: none;
            font-weight: 800;
        }

        @media (max-height: 720px) {
            .auth-compact-page {
                min-height: calc(100dvh - 76px);
                height: calc(100dvh - 76px);
                padding: 8px 14px;
            }

            .auth-card-content {
                padding: 20px 26px 18px;
            }

            .auth-header {
                margin-bottom: 16px;
            }

            .auth-header h2 {
                font-size: 24px;
            }

            .auth-header p {
                margin-top: 6px;
                font-size: 13px;
            }

            .auth-form {
                gap: 9px;
            }

            .auth-control,
            .auth-main-btn,
            .auth-google-btn {
                height: 37px !important;
                min-height: 37px !important;
                line-height: 37px !important;
            }

            .auth-divider {
                margin: 12px 0;
            }

            .auth-bottom-text {
                margin-top: 12px;
            }
        }

        @media (max-width: 576px) {

            html,
            body {
                overflow: auto !important;
            }

            .auth-compact-page {
                height: auto;
                min-height: calc(100dvh - 76px);
                align-items: flex-start;
                padding-top: 18px;
                padding-bottom: 18px;
                overflow: visible;
            }

            .auth-card-content {
                padding: 22px 20px 20px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.innerHTML = '<i data-lucide="eye-off" class="icon"></i>';
                toggleBtn.title = 'Sembunyikan kata sandi';
            } else {
                passwordInput.type = 'password';
                toggleBtn.innerHTML = '<i data-lucide="eye" class="icon"></i>';
                toggleBtn.title = 'Lihat kata sandi';
            }
            lucide.createIcons();
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (isLoggedIn()) {
                window.location.href = '/';
            }

            const form = document.getElementById('login-form');
            const btn = document.getElementById('btn-login');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btn.innerHTML = '<span class="loader" style="width: 16px; height: 16px; border-width: 2px;"></span> &nbsp; Proses...';
                btn.disabled = true;

                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                try {
                    const response = await apiFetch('/auth/login', {
                        method: 'POST',
                        body: { email, password }
                    });

                    showToast('Login berhasil!');
                    setTimeout(() => {
                        loginSuccess(response.data.access_token, response.data.user);
                    }, 1000);
                } catch (error) {
                    showToast(error.message, 'error');
                    btn.innerHTML = 'Masuk';
                    btn.disabled = false;
                }
            });
        });
    </script>
@endpush