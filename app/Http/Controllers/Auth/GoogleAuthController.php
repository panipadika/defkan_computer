<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * GET /api/auth/google  atau  GET /auth/google (web)
     * Langsung redirect browser ke halaman login Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * GET /api/auth/google/callback  atau  GET /auth/google/callback (web)
     * Menangani response callback dari Google setelah login berhasil.
     *
     * Flow:
     *  1. Ambil data user dari Google
     *  2. Cek apakah email sudah terdaftar di tabel pengguna
     *  3. Jika sudah ada → update google_id | Jika belum → buat akun baru
     *  4. Buat API token menggunakan Laravel Sanctum
     *  5. Return token + data pengguna
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $pengguna = Pengguna::where('email', $googleUser->getEmail())->first();

            if ($pengguna) {
                $pengguna->update([
                    'google_id' => $googleUser->getId(),
                    'nama'      => $googleUser->getName(),
                ]);
            } else {
                $pengguna = Pengguna::create([
                    'nama'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'role'      => Pengguna::ROLE_USER,
                ]);
            }

            // Hapus token lama, buat token baru
            $pengguna->tokens()->delete();
            $token = $pengguna->createToken('google_token')->plainTextToken;

            // ✅ FIX: Return Blade view yang menyimpan token ke localStorage
            // Bukan raw JSON yang tidak bisa dibaca oleh frontend
            return view('auth.google-callback', [
                'token' => $token,
                'user'  => [
                    'id_pengguna' => $pengguna->id_pengguna,
                    'nama'        => $pengguna->nama,
                    'email'       => $pengguna->email,
                    'role'        => $pengguna->role,
                    'no_hp'       => $pengguna->no_hp,
                ],
            ]);

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }
    }

    /**
     * GET /api/me
     * Mendapatkan profil pengguna yang sedang login.
     */
    public function me(Request $request)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil data profil',
            'data'    => [
                'id_pengguna' => $pengguna->id_pengguna,
                'nama'        => $pengguna->nama,
                'email'       => $pengguna->email,
                'role'        => $pengguna->role,
                'no_hp'       => $pengguna->no_hp,
            ],
        ], 200);
    }
}
