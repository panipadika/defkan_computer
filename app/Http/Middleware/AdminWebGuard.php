<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * AdminWebGuard
 *
 * Middleware proteksi server-side untuk route halaman admin (/admin/*).
 *
 * Cara kerja:
 *  1. Baca token dari cookie `admin_token` yang diset oleh JS saat login.
 *  2. Validasi token melalui tabel `personal_access_tokens` (Laravel Sanctum).
 *  3. Pastikan pengguna yang terkait memiliki role 'admin'.
 *  4. Jika tidak valid → redirect ke /login.
 *
 * Catatan: Cookie `admin_token` diset oleh `app.js` (loginSuccess) dan
 * `google-callback.blade.php` — TIDAK HttpOnly agar JS bisa menghapusnya saat logout.
 */
class AdminWebGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie('admin_token');

        // Tidak ada cookie → belum login, arahkan ke halaman login
        if (empty($token)) {
            return redirect()->route('login')
                ->with('info', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
        }

        // Cari token di database Sanctum
        $accessToken = PersonalAccessToken::findToken($token);

        if (! $accessToken) {
            // Token tidak dikenal / sudah dihapus (misalnya setelah logout dari device lain)
            return $this->denyAccess($request);
        }

        // Ambil model pengguna terkait (harus Pengguna, bukan User bawaan Laravel)
        $pengguna = $accessToken->tokenable;

        if (! $pengguna || ! ($pengguna instanceof \App\Models\Pengguna)) {
            return $this->denyAccess($request);
        }

        // Pastikan pengguna memiliki role admin
        if (! $pengguna->isAdmin()) {
            // Sudah login tapi bukan admin → kembali ke halaman utama
            return redirect('/')
                ->with('error', 'Akses ditolak. Halaman ini hanya untuk Admin.');
        }

        return $next($request);
    }

    /**
     * Hapus cookie yang tidak valid dan redirect ke login.
     */
    private function denyAccess(Request $request): Response
    {
        return redirect()->route('login')
            ->with('info', 'Sesi Anda tidak valid atau telah berakhir. Silakan login kembali.')
            ->withCookie(cookie()->forget('admin_token'));
    }
}
