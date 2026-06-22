<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Servis;
use App\Models\Ulasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UlasanController extends Controller
{
    /**
     * POST /api/ulasan
     * Submit ulasan baru. Sekali submit, tidak bisa diedit.
     */
    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $validator = Validator::make($request->all(), [
            'tipe'       => 'required|in:produk,servis',
            'id_pesanan' => 'required_if:tipe,produk|nullable|integer',
            'id_produk'  => 'required_if:tipe,produk|nullable|integer',
            'id_servis'  => 'required_if:tipe,servis|nullable|integer',
            'rating'     => 'required|integer|min:1|max:5',
            'komentar'   => 'nullable|string|max:1000',
            'foto_bukti'   => 'nullable|array|max:3',
            'foto_bukti.*' => 'image|mimes:jpeg,png,jpg|max:3072',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── Validasi ulasan produk ────────────────────────────────────────────
        if ($request->tipe === 'produk') {
            // Pastikan pesanan milik user & statusnya selesai
            $pesanan = Pesanan::where('id_pesanan', $request->id_pesanan)
                ->where('id_pengguna', $pengguna->id_pengguna)
                ->where('status', 'selesai')
                ->first();

            if (! $pesanan) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Pesanan tidak ditemukan, bukan milik Anda, atau belum berstatus selesai.',
                ], 403);
            }

            // Pastikan produk ada dalam detail pesanan tersebut
            $produkAda = $pesanan->detail()->where('id_produk', $request->id_produk)->exists();
            if (! $produkAda) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Produk tidak ditemukan dalam pesanan ini.',
                ], 422);
            }

            // Cek apakah sudah pernah review produk ini di pesanan ini (sekali saja)
            $sudahReview = Ulasan::where('pengguna_id', $pengguna->id_pengguna)
                ->where('id_pesanan', $request->id_pesanan)
                ->where('id_produk', $request->id_produk)
                ->exists();

            if ($sudahReview) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Anda sudah memberikan ulasan untuk produk ini.',
                ], 409);
            }
        }

        // ── Validasi ulasan servis ────────────────────────────────────────────
        if ($request->tipe === 'servis') {
            // Pastikan servis milik user & statusnya diambil
            $servis = Servis::where('id', $request->id_servis)
                ->where('pengguna_id', $pengguna->id_pengguna)
                ->where('status', 'diambil')
                ->first();

            if (! $servis) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Servis tidak ditemukan, bukan milik Anda, atau belum berstatus diambil.',
                ], 403);
            }

            // Cek apakah sudah pernah review servis ini (sekali saja)
            $sudahReview = Ulasan::where('pengguna_id', $pengguna->id_pengguna)
                ->where('id_servis', $request->id_servis)
                ->exists();

            if ($sudahReview) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Anda sudah memberikan ulasan untuk servis ini.',
                ], 409);
            }
        }

        // ── Upload foto bukti ─────────────────────────────────────────────────
        $fotoPaths = [];
        if ($request->hasFile('foto_bukti')) {
            foreach ($request->file('foto_bukti') as $foto) {
                $fotoPaths[] = $foto->store('ulasan', 'public');
            }
        }

        // ── Simpan ulasan ─────────────────────────────────────────────────────
        $ulasan = Ulasan::create([
            'pengguna_id' => $pengguna->id_pengguna,
            'tipe'        => $request->tipe,
            'id_pesanan'  => $request->tipe === 'produk' ? $request->id_pesanan : null,
            'id_produk'   => $request->tipe === 'produk' ? $request->id_produk : null,
            'id_servis'   => $request->tipe === 'servis' ? $request->id_servis : null,
            'rating'      => $request->rating,
            'komentar'    => $request->komentar,
            'foto_bukti'  => ! empty($fotoPaths) ? $fotoPaths : null,
            'is_visible'  => true, // Langsung tampil
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Ulasan berhasil dikirim. Terima kasih atas masukan Anda!',
            'data'    => $ulasan->load(['pengguna:id_pengguna,nama', 'produk:id_produk,nama_produk']),
        ], 201);
    }

    /**
     * GET /api/ulasan-saya
     * Daftar ulasan milik pengguna yang login.
     */
    public function myReviews(Request $request): JsonResponse
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $ulasan = Ulasan::with(['produk:id_produk,nama_produk,foto', 'servis:id,kode_servis,jenis_kerusakan'])
            ->where('pengguna_id', $pengguna->id_pengguna)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data'   => $ulasan,
        ]);
    }

    /**
     * GET /api/produk/{id}/ulasan  (Publik)
     * Semua ulasan untuk satu produk.
     */
    public function produkReviews(Request $request, int $id): JsonResponse
    {
        $ulasan = Ulasan::with(['pengguna:id_pengguna,nama'])
            ->where('id_produk', $id)
            ->where('tipe', 'produk')
            ->visible()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Hitung rata-rata rating
        $rataRating = Ulasan::where('id_produk', $id)
            ->where('tipe', 'produk')
            ->visible()
            ->avg('rating');

        return response()->json([
            'status' => 'success',
            'data'   => $ulasan,
            'meta'   => [
                'rata_rating' => round((float) $rataRating, 1),
            ],
        ]);
    }

    /**
     * GET /api/ulasan/publik  (Publik, tanpa auth)
     * Review terbaru untuk ditampilkan di beranda (rating >= 4, visible).
     */
    public function publik(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 6), 12);

        $ulasan = Ulasan::with([
                'pengguna:id_pengguna,nama',
                'produk:id_produk,nama_produk,foto',
                'servis:id,kode_servis,jenis_kerusakan',
            ])
            ->publik()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($u) {
                // Samarkan nama: "Budi Santoso" → "Budi S."
                if ($u->pengguna) {
                    $namaParts = explode(' ', trim($u->pengguna->nama));
                    $u->pengguna->nama_samar = count($namaParts) > 1
                        ? $namaParts[0] . ' ' . strtoupper(substr($namaParts[1], 0, 1)) . '.'
                        : $namaParts[0];
                }
                return $u;
            });

        return response()->json([
            'status' => 'success',
            'data'   => $ulasan,
        ]);
    }
}
