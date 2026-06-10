@extends('layouts.app')

@section('content')
    <div class="auth-compact-page auth-forgot-page">
        <div class="auth-card auth-forgot-card">
            <div class="auth-decor auth-decor-right"></div>

            <div class="auth-card-content">
                <div class="auth-header">
                    <h2>Lupa Kata Sandi?</h2>
                    <p class="text-muted">Masukkan email Anda untuk menerima link reset kata sandi</p>
                </div>

                <form id="forgot-password-form" class="auth-form">
                    <div class="form-group auth-form-group">
                        <label class="form-label auth-label">Email</label>
                        <input type="email" id="email" class="form-control auth-control" placeholder="nama@email.com"
                            required>
                    </div>

                    <button type="submit" class="btn btn-primary auth-main-btn" id="btn-submit">
                        Kirim Link Reset
                    </button>
                </form>

                <p class="text-muted auth-bottom-text">
                    Kembali ke
                    <a href="/login" class="text-primary-gradient">Halaman Masuk</a>
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

        .auth-forgot-card {
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

        .auth-main-btn {
            width: 100%;
            height: 40px !important;
            min-height: 40px !important;
            border-radius: 9px !important;
            padding: 0 14px !important;
            justify-content: center;
            font-size: 14px !important;
            font-weight: 800 !important;
            margin-top: 2px;
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
            .auth-main-btn {
                height: 37px !important;
                min-height: 37px !important;
                line-height: 37px !important;
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
        document.addEventListener('DOMContentLoaded', () => {
            if (isLoggedIn()) {
                window.location.href = '/';
            }

            const form = document.getElementById('forgot-password-form');
            const btn = document.getElementById('btn-submit');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                btn.innerHTML = '<span class="loader" style="width: 16px; height: 16px; border-width: 2px;"></span> &nbsp; Mengirim...';
                btn.disabled = true;

                const email = document.getElementById('email').value;

                try {
                    const response = await apiFetch('/auth/forgot-password', {
                        method: 'POST',
                        body: { email }
                    });

                    showToast(response.message || 'Link reset password berhasil dikirim!');
                    document.getElementById('email').value = '';
                } catch (error) {
                    showToast(error.message || 'Terjadi kesalahan.', 'error');
                } finally {
                    btn.innerHTML = 'Kirim Link Reset';
                    btn.disabled = false;
                }
            });
        });
    </script>
@endpush
