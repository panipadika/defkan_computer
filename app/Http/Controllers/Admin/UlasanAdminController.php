<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UlasanAdminController extends Controller
{
    /**
     * GET /api/admin/ulasan
     * Daftar semua ulasan dengan filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ulasan::with([
                'pengguna:id_pengguna,nama,email',
                'produk:id_produk,nama_produk',
                'servis:id,kode_servis,jenis_kerusakan',
            ])
            ->orderBy('created_at', 'desc');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('is_visible')) {
            $query->where('is_visible', (bool) $request->is_visible);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('komentar', 'like', "%{$s}%")
                    ->orWhereHas('pengguna', fn ($pq) => $pq->where('nama', 'like', "%{$s}%"))
                    ->orWhereHas('produk', fn ($dq) => $dq->where('nama_produk', 'like', "%{$s}%"));
            });
        }

        $perPage = $request->input('per_page', 15);
        $ulasan = $query->paginate($perPage);

        // Stats ringkas
        $stats = Ulasan::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN is_visible = 1 THEN 1 ELSE 0 END) as visible,
            SUM(CASE WHEN is_visible = 0 THEN 1 ELSE 0 END) as hidden,
            ROUND(AVG(rating), 1) as rata_rata
        ")->first();

        return response()->json([
            'status' => 'success',
            'data'   => $ulasan,
            'stats'  => [
                'total'     => (int) $stats->total,
                'visible'   => (int) $stats->visible,
                'hidden'    => (int) $stats->hidden,
                'rata_rata' => (float) $stats->rata_rata,
            ],
        ]);
    }

    /**
     * DELETE /api/admin/ulasan/{id}
     * Hapus ulasan yang tidak pantas.
     */
    public function destroy(int $id): JsonResponse
    {
        $ulasan = Ulasan::find($id);

        if (! $ulasan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ulasan tidak ditemukan.',
            ], 404);
        }

        $ulasan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Ulasan berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/admin/ulasan/{id}/visibility
     * Toggle tampilkan/sembunyikan ulasan.
     */
    public function toggleVisibility(int $id): JsonResponse
    {
        $ulasan = Ulasan::find($id);

        if (! $ulasan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ulasan tidak ditemukan.',
            ], 404);
        }

        $ulasan->update(['is_visible' => ! $ulasan->is_visible]);

        $aksi = $ulasan->is_visible ? 'ditampilkan' : 'disembunyikan';

        return response()->json([
            'status'     => 'success',
            'message'    => "Ulasan berhasil {$aksi}.",
            'is_visible' => $ulasan->is_visible,
        ]);
    }
}
