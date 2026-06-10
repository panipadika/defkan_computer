<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * POST /api/auth/forgot-password
     * Kirim email link reset password.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:pengguna,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar di sistem kami.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Kirim link reset password
            $status = Password::broker()->sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Link reset password telah dikirim ke email Anda.',
                ], 200);
            }

            // Mapping error status dari Laravel ke Bahasa Indonesia
            $errorMessage = 'Gagal mengirim email reset password. Silakan coba beberapa saat lagi.';
            if ($status === Password::INVALID_USER) {
                $errorMessage = 'Pengguna dengan email tersebut tidak ditemukan.';
            } elseif ($status === Password::RESET_THROTTLED) {
                $errorMessage = 'Permintaan reset terlalu sering. Harap tunggu beberapa saat sebelum mencoba lagi.';
            }

            return response()->json([
                'status' => 'error',
                'message' => $errorMessage,
            ], 400);
        } catch (\Throwable $e) {
            // Log detail error
            \Log::error('SMTP/Mail Error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghubungi server email (SMTP). Silakan periksa konfigurasi mail server Anda di panel Railway: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/auth/reset-password
     * Proses reset password baru.
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.required' => 'Token reset tidak valid atau tidak ditemukan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Eksekusi reset password
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password Anda berhasil diperbarui. Silakan login kembali.',
            ], 200);
        }

        // Mapping error status dari Laravel ke Bahasa Indonesia
        $errorMessage = 'Gagal memperbarui password.';
        if ($status === Password::INVALID_USER) {
            $errorMessage = 'Pengguna dengan email tersebut tidak ditemukan.';
        } elseif ($status === Password::INVALID_TOKEN) {
            $errorMessage = 'Token reset password tidak valid atau sudah kedaluwarsa.';
        } elseif ($status === Password::RESET_THROTTLED) {
            $errorMessage = 'Terlalu banyak percobaan reset password. Silakan tunggu beberapa saat.';
        }

        return response()->json([
            'status' => 'error',
            'message' => $errorMessage,
        ], 400);
    }
}
