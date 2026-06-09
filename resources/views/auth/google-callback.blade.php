@extends('layouts.app')

@section('title', 'Proses Login Google — Defkan Computer')

@section('content')
<div class="container flex-center" style="min-height: 80vh;">
    <div class="glass-panel" style="width: 100%; max-width: 420px; text-align: center;">
        <div style="font-size: 56px; margin-bottom: 16px; animation: pulse 1.5s infinite;">🔐</div>
        <h2 style="font-size: 22px; margin-bottom: 8px;">Memproses Login Google...</h2>
        <p class="text-muted" style="font-size: 14px; margin-bottom: 24px;">Harap tunggu, kami sedang memverifikasi akun Anda.</p>
        <div class="loader" style="width: 40px; height: 40px; border-width: 4px; margin: 0 auto;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const data = {
        token: @json($token),
        user: @json($user)
    };

    if (data.token && data.user) {
        localStorage.setItem('auth_token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));
        window.location.href = '/';
    } else {
        showToast('Login Google gagal. Silakan coba lagi.', 'error');
        setTimeout(() => { window.location.href = '/login'; }, 2000);
    }
})();
</script>
@endpush
