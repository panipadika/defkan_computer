<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Pesanan;
use App\Models\Servis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    /**
     * POST /api/complaint
     * Buat complaint baru (pesanan atau servis).
     */
    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $validator = Validator::make($request->all(), [
            'tipe'         => 'required|in:pesanan,servis',
            'id_referensi' => 'required|integer',
            'judul'        => 'required|string|max:200',
            'deskripsi'    => 'required|string|max:2000',
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

        // Validasi kepemilikan referensi
        if ($request->tipe === 'pesanan') {
            $referensi = Pesanan::where('id_pesanan', $request->id_referensi)
                ->where('id_pengguna', $pengguna->id_pengguna)
                ->first();

            if (! $referensi) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Pesanan tidak ditemukan atau bukan milik Anda.',
                ], 404);
            }
        } elseif ($request->tipe === 'servis') {
            $referensi = Servis::where('id', $request->id_referensi)
                ->where('pengguna_id', $pengguna->id_pengguna)
                ->first();

            if (! $referensi) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Servis tidak ditemukan atau bukan milik Anda.',
                ], 404);
            }
        }

        // Upload foto bukti jika ada
        $fotoPaths = [];
        if ($request->hasFile('foto_bukti')) {
            foreach ($request->file('foto_bukti') as $foto) {
                $fotoPaths[] = $foto->store('complaint', 'public');
            }
        }

        $complaint = Complaint::create([
            'pengguna_id'  => $pengguna->id_pengguna,
            'tipe'         => $request->tipe,
            'id_referensi' => $request->id_referensi,
            'judul'        => $request->judul,
            'deskripsi'    => $request->deskripsi,
            'foto_bukti'   => ! empty($fotoPaths) ? $fotoPaths : null,
            'status'       => 'menunggu',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Komplain Anda berhasil dikirim. Admin akan segera merespons.',
            'data'    => $complaint,
        ], 201);
    }

    /**
     * GET /api/complaint-saya
     * Daftar complaint milik pengguna yang login.
     */
    public function myComplaints(Request $request): JsonResponse
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $complaints = Complaint::where('pengguna_id', $pengguna->id_pengguna)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data'   => $complaints,
        ]);
    }

    /**
     * GET /api/complaint/{id}
     * Detail satu complaint + respons admin.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $complaint = Complaint::where('id', $id)
            ->where('pengguna_id', $pengguna->id_pengguna)
            ->first();

        if (! $complaint) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Komplain tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $complaint,
        ]);
    }
}
