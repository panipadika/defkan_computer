<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintAdminController extends Controller
{
    /**
     * GET /api/admin/complaint
     * Daftar semua complaint dengan filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Complaint::with(['pengguna:id_pengguna,nama,email,no_hp'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                    ->orWhere('deskripsi', 'like', "%{$s}%")
                    ->orWhereHas('pengguna', fn ($pq) => $pq->where('nama', 'like', "%{$s}%"));
            });
        }

        $perPage = $request->input('per_page', 15);
        $complaints = $query->paginate($perPage);

        // Stats ringkas
        $stats = Complaint::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN status = 'diproses' THEN 1 ELSE 0 END) as diproses,
            SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
        ")->first();

        return response()->json([
            'status' => 'success',
            'data'   => $complaints,
            'stats'  => [
                'total'    => (int) $stats->total,
                'menunggu' => (int) $stats->menunggu,
                'diproses' => (int) $stats->diproses,
                'selesai'  => (int) $stats->selesai,
                'ditolak'  => (int) $stats->ditolak,
            ],
        ]);
    }

    /**
     * GET /api/admin/complaint/{id}
     * Detail satu complaint.
     */
    public function show(int $id): JsonResponse
    {
        $complaint = Complaint::with(['pengguna:id_pengguna,nama,email,no_hp'])->find($id);

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

    /**
     * POST /api/admin/complaint/{id}/respond
     * Admin kirim respons dan update status complaint.
     */
    public function respond(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status'        => 'required|in:diproses,selesai,ditolak',
            'respons_admin' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $complaint = Complaint::find($id);

        if (! $complaint) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Komplain tidak ditemukan.',
            ], 404);
        }

        $complaint->update([
            'status'        => $request->status,
            'respons_admin' => $request->respons_admin,
            'respons_at'    => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Respons komplain berhasil dikirim.',
            'data'    => $complaint->fresh()->load('pengguna:id_pengguna,nama,email'),
        ]);
    }
}
