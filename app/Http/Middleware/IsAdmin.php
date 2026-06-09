<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Cek apakah pengguna yang sedang login memiliki role 'admin'.
     *
     * Middleware ini digunakan pada route yang hanya boleh diakses admin.
     * Guard yang digunakan adalah 'sanctum' yang terikat ke model Pengguna.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Pengguna|null $pengguna */
        $pengguna = auth('sanctum')->user();

        if ($pengguna && $pengguna->isAdmin()) {
            return $next($request);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.',
        ], 403);
    }
}
