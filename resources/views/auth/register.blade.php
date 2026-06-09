@extends('layouts.app')

@section('content')
    <div class="auth-compact-page auth-register-page">
        <div class="auth-card auth-register-card">
            <div class="auth-decor auth-decor-left"></div>

            <div class="auth-card-content">
                <div class="auth-header">
                    <h2>Daftar Akun</h2>
                    <p class="text-muted">Bergabung dengan Defkan Computer</p>
                </div>

                <form id="register-form" class="auth-form">
                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Nama Lengkap</label>
                        <input type="text" id="nama" class="form-control auth-control" placeholder="Nama Lengkap" required>
                    </div>

                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Email</label>
                        <input type="email" id="email" class="form-control auth-control" placeholder="nama@email.com"
                            required>
                    </div>

                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Nomor WhatsApp</label>
                        <input type="text" id="no_hp" class="form-control auth-control" placeholder="081234567890">
                    </div>

                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Password</label>
                        <input type="password" id="password" class="form-control auth-control" placeholder="••••••••"
                            required>
                    </div>

                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" class="form-control auth-control"
                            placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary auth-main-btn" id="btn-register">
                        Daftar Sekarang
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
                    Daftar dengan Google
                </a>

                <p class="text-muted auth-bottom-text">
                    Sudah punya akun?
                    <a href="/login" class="text-primary-gradient">Masuk di sini</a>
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
            padding: 10px 16px;
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

        .auth-register-card {
            max-width: 418px;
        }

        .auth-card-content {
            position: relative;
            z-index: 1;
            padding: 20px 28px 18px;
        }

        .auth-decor {
            position: absolute;
            width: 96px;
            height: 96px;
            background: var(--primary, #2563eb);
            filter: blur(42px);
            opacity: .22;
            z-index: 0;
            pointer-events: none;
        }

        .auth-decor-left {
            top: -24px;
            left: -26px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 16px;
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
            margin: 7px 0 0;
            font-size: 13.5px;
            line-height: 1.3;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .auth-form-group {
            margin: 0 !important;
        }

        .auth-label {
            display: block;
            margin-bottom: 5px !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            color: var(--text-muted, #64748b) !important;
        }

        .auth-control {
            height: 37px !important;
            min-height: 37px !important;
            padding: 0 13px !important;
            border-radius: 9px !important;
            font-size: 13.5px !important;
            line-height: 37px !important;
        }

        .auth-main-btn,
        .auth-google-btn {
            width: 100%;
            height: 38px !important;
            min-height: 38px !important;
            border-radius: 9px !important;
            padding: 0 14px !important;
            justify-content: center;
            font-size: 13.5px !important;
            font-weight: 800 !important;
        }

        .auth-main-btn {
            margin-top: 3px;
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
            margin: 12px 0;
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
            margin: 12px 0 0;
            font-size: 12.7px;
            line-height: 1.3;
        }

        .auth-bottom-text a {
            text-decoration: none;
            font-weight: 800;
        }

        @media (max-height: 720px) {
            .auth-compact-page {
                min-height: calc(100dvh - 76px);
                height: calc(100dvh - 76px);
                padding: 7px 14px;
            }

            .auth-card-content {
                padding: 16px 24px 14px;
            }

            .auth-header {
                margin-bottom: 12px;
            }

            .auth-header h2 {
                font-size: 23px;
            }

            .auth-header p {
                margin-top: 5px;
                font-size: 12.5px;
            }

            .auth-form {
                gap: 7px;
            }

            .auth-label {
                margin-bottom: 4px !important;
                font-size: 11.5px !important;
            }

            .auth-control,
            .auth-main-btn,
            .auth-google-btn {
                height: 34px !important;
                min-height: 34px !important;
                line-height: 34px !important;
            }

            .auth-divider {
                margin: 9px 0;
            }

            .auth-bottom-text {
                margin-top: 9px;
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
                padding-top: 14px;
                padding-bottom: 14px;
                overflow: visible;
            }

            .auth-card-content {
                padding: 18px 20px 16px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (isLoggedIn()) {
                window.location.href = '/';
            }

            const form = document.getElementById('register-form');
            const btn = document.getElementById('btn-register');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btn.innerHTML = '<span class="loader" style="width: 16px; height: 16px; border-width: 2px;"></span> &nbsp; Proses...';
                btn.disabled = true;

                const nama = document.getElementById('nama').value;
                const email = document.getElementById('email').value;
                const no_hp = document.getElementById('no_hp').value;
                const password = document.getElementById('password').value;
                const password_confirmation = document.getElementById('password_confirmation').value;

                try {
                    const response = await apiFetch('/auth/register', {
                        method: 'POST',
                        body: { nama, email, no_hp, password, password_confirmation }
                    });

                    showToast('Pendaftaran berhasil!');
                    setTimeout(() => {
                        loginSuccess(response.data.access_token, response.data.user);
                    }, 1000);
                } catch (error) {
                    showToast(error.message, 'error');
                    btn.innerHTML = 'Daftar Sekarang';
                    btn.disabled = false;
                }
            });
        });
    </script>
@endpush