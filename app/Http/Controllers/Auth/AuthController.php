<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Register pengguna baru secara manual.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'     => 'required|string|max:100',
            'email'    => 'required|string|email|max:100|unique:pengguna',
            'password' => 'required|string|min:6|confirmed', // butuh field password_confirmation
            'no_hp'    => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $pengguna = Pengguna::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'no_hp'    => $request->no_hp,
            'role'     => Pengguna::ROLE_USER,
        ]);

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil',
            'data'    => [
                'user'         => $pengguna,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]
        ], 201);
    }

    /**
     * POST /api/auth/login
     * Login secara manual.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $pengguna = Pengguna::where('email', $request->email)->first();

        // Verifikasi keberadaan user dan kecocokan password
        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Hapus token lama jika ingin membatasi 1 device (opsional, saat ini dihapus untuk keamanan)
        $pengguna->tokens()->delete();

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'data'    => [
                'user'         => $pengguna,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ]
        ], 200);
    }

    /**
     * POST /api/auth/logout
     * Hapus token user yang sedang aktif.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil',
        ], 200);
    }

    /**
     * PUT /api/profil
     * Update profil pengguna yang sedang login.
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $validator = Validator::make($request->all(), [
            'nama'  => 'sometimes|required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Update data
        $pengguna->nama  = $request->input('nama', $pengguna->nama);
        $pengguna->no_hp = $request->input('no_hp', $pengguna->no_hp);

        // Jika ingin mengganti password juga bisa ditambahkan di sini, tapi sebaiknya pisah endpoint
        if ($request->has('password') && !empty($request->password)) {
            $passValidator = Validator::make($request->all(), [
                'password' => 'string|min:6|confirmed',
            ]);
            if ($passValidator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $passValidator->errors()->first(),
                ], 422);
            }
            $pengguna->password = Hash::make($request->password);
        }

        $pengguna->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data'    => $pengguna,
        ], 200);
    }
}
