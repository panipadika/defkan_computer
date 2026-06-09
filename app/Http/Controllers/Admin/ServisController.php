<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EstimasiServis;
use App\Models\Servis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServisController extends Controller
{
    /**
     * POST /api/servis
     * Daftarkan servis laptop baru.
     * Jika user sudah upload bukti pembayaran saat pengajuan, status pembayaran otomatis menjadi dibayar.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_kerusakan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'merek_laptop' => 'required|string|max:100',
            'no_wa' => 'required|string|max:20',

            // Field pembayaran servis
            'metode_pembayaran' => 'nullable|string|max:100',
            'status_pembayaran' => 'nullable|string|max:50',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'total_biaya' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        if (!$pengguna) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan. Silakan login ulang.',
            ], 401);
        }

        $estimasi = EstimasiServis::where('jenis_kerusakan', $request->jenis_kerusakan)->first();
        $estimasiBiaya = $estimasi ? (int) $estimasi->harga_estimasi : 0;
        $totalBiaya = (int) ($request->total_biaya ?? $estimasiBiaya);

        $buktiPembayaranPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPembayaranPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran_servis', 'public');
        }

        /*
         * Penting:
         * Jika ada bukti pembayaran, status harus dipaksa "dibayar".
         * Jangan memakai request status_pembayaran lebih dulu, karena dari frontend bisa saja masih "pending".
         */
        $statusPembayaran = $buktiPembayaranPath
            ? 'dibayar'
            : ($request->status_pembayaran ?: 'pending');

        $servis = Servis::create([
            'pengguna_id' => $pengguna->id_pengguna,
            'jenis_kerusakan' => $request->jenis_kerusakan,
            'deskripsi' => $request->deskripsi,
            'merek_laptop' => $request->merek_laptop,
            'no_wa' => $request->no_wa,

            'kode_servis' => strtoupper(uniqid('SRV-')),
            'status' => 'menunggu',
            'estimasi_biaya' => $estimasiBiaya,
            'estimasi_durasi' => $estimasi ? $estimasi->estimasi_durasi : '1-3 Hari Kerja',
            'tanggal_masuk' => now()->toDateString(),

            'total_biaya' => $totalBiaya,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pembayaran' => $statusPembayaran,
            'waktu_pembayaran' => $buktiPembayaranPath ? now() : null,
            'bukti_pembayaran' => $buktiPembayaranPath,
        ]);

        $servis->load('pengguna');

        return response()->json([
            'status' => 'success',
            'message' => $buktiPembayaranPath
                ? 'Servis berhasil diajukan dan bukti pembayaran berhasil dikirim.'
                : 'Servis berhasil didaftarkan. Silakan lakukan pembayaran untuk memproses servis.',
            'data' => $this->appendServisUrls($servis),
        ], 201);
    }

    /**
     * GET /api/servis/estimasi
     */
    public function estimasi()
    {
        $estimasi = EstimasiServis::orderBy('jenis_kerusakan')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil daftar estimasi servis',
            'data' => $estimasi,
        ], 200);
    }

    /**
     * GET /api/servis/track/{kode_servis}
     * Tracking status servis berdasarkan kode servis unik.
     */
    public function tracking($kode_servis)
    {
        $servis = Servis::where('kode_servis', strtoupper($kode_servis))
            ->with(['estimasi', 'pengguna'])
            ->first();

        if (!$servis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode servis tidak ditemukan. Pastikan kode yang Anda masukkan benar.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil status servis',
            'data' => $this->appendServisUrls($servis),
        ], 200);
    }

    /**
     * GET /api/servis (Admin only)
     * Ambil semua data servis dengan filter, search, pagination, dan statistik.
     */
    public function index(Request $request)
    {
        $query = Servis::with('pengguna');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_servis', 'like', "%{$search}%")
                    ->orWhere('merek_laptop', 'like', "%{$search}%")
                    ->orWhere('jenis_kerusakan', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', function ($pq) use ($search) {
                        $pq->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('no_hp', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        /** @var \Illuminate\Pagination\LengthAwarePaginator $servis */
        $servis = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $servis->getCollection()->transform(function ($item) {
            return $this->appendServisUrls($item);
        });

        $allServis = Servis::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "menunggu" THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN status = "diperiksa" THEN 1 ELSE 0 END) as diperiksa,
            SUM(CASE WHEN status = "dikerjakan" THEN 1 ELSE 0 END) as dikerjakan,
            SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = "diambil" THEN 1 ELSE 0 END) as diambil
        ')->first();

        return response()->json([
            'status' => 'success',
            'data' => $servis,
            'stats' => [
                'total' => (int) ($allServis->total ?? 0),
                'menunggu' => (int) ($allServis->menunggu ?? 0),
                'menunggu_konfirmasi' => (int) ($allServis->menunggu ?? 0),
                'diperiksa' => (int) ($allServis->diperiksa ?? 0),
                'dikerjakan' => (int) ($allServis->dikerjakan ?? 0),
                'selesai' => (int) ($allServis->selesai ?? 0),
                'diambil' => (int) ($allServis->diambil ?? 0),
            ],
        ], 200);
    }

    /**
     * GET /api/servis/export (Admin only)
     * Export daftar servis ke CSV.
     */
    public function export(Request $request)
    {
        $query = Servis::with('pengguna');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_servis', 'like', "%{$search}%")
                    ->orWhere('merek_laptop', 'like', "%{$search}%")
                    ->orWhere('jenis_kerusakan', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', function ($pq) use ($search) {
                        $pq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }

        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }

        $servisList = $query->orderBy('created_at', 'desc')->get();
        $filename = 'daftar_servis_' . now()->format('Ymd_His') . '.csv';

        $callback = function () use ($servisList) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Kode Servis',
                'Tanggal Masuk',
                'Nama Pelanggan',
                'No. HP',
                'Perangkat',
                'Kerusakan',
                'Estimasi Biaya',
                'Total Biaya',
                'Status Servis',
                'Status Pembayaran',
                'Metode Pembayaran',
                'Waktu Pembayaran',
                'Bukti Pembayaran',
                'Keterangan Teknisi',
            ], ';');

            foreach ($servisList as $s) {
                fputcsv($file, [
                    $s->kode_servis,
                    $s->created_at ? $s->created_at->format('Y-m-d H:i:s') : '',
                    $s->pengguna ? $s->pengguna->nama : '—',
                    $s->pengguna ? $s->pengguna->no_hp : ($s->no_wa ?: '—'),
                    $s->merek_laptop,
                    $s->jenis_kerusakan,
                    $s->estimasi_biaya,
                    $s->total_biaya ?: $s->estimasi_biaya,
                    ucfirst($s->status),
                    $this->getStatusPembayaranLabel($s->status_pembayaran),
                    $s->metode_pembayaran ?: '—',
                    $s->waktu_pembayaran ? $s->waktu_pembayaran->format('Y-m-d H:i:s') : '—',
                    $s->bukti_pembayaran ? asset('storage/' . $s->bukti_pembayaran) : '—',
                    $s->keterangan ?: '—',
                ], ';');
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * PATCH /api/servis/{id}/status (Admin only)
     * Update status dan/atau keterangan servis.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:menunggu,diperiksa,dikerjakan,selesai,diambil',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $servis = Servis::find($id);

        if (!$servis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data servis tidak ditemukan',
            ], 404);
        }

        $update = [];

        if ($request->filled('status')) {
            $update['status'] = $request->status;
        }

        if ($request->has('keterangan')) {
            $update['keterangan'] = $request->keterangan;
        }

        if (!empty($update)) {
            $servis->update($update);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Servis berhasil diperbarui.',
            'data' => $this->appendServisUrls($servis->fresh('pengguna')),
        ], 200);
    }

    /**
     * GET /api/servis-saya
     * Ambil semua daftar servis milik pengguna yang sedang login.
     */
    public function myServices(Request $request)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        if (!$pengguna) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan. Silakan login ulang.',
            ], 401);
        }

        $servis = Servis::with('pengguna')
            ->where('pengguna_id', $pengguna->id_pengguna)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return $this->appendServisUrls($item);
            });

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil riwayat servis Anda',
            'data' => $servis,
        ], 200);
    }

    /**
     * POST /api/servis/{id}/bayar
     * Upload bukti pembayaran servis setelah servis dibuat.
     */
    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'metode_pembayaran' => 'required|string|max:100',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        /** @var \App\Models\Pengguna|null $pengguna */
        $pengguna = $request->user();

        $query = Servis::query();

        if ($pengguna && ($pengguna->role ?? null) !== 'admin') {
            $query->where('pengguna_id', $pengguna->id_pengguna);
        }

        $servis = $query->find($id);

        if (!$servis) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data servis tidak ditemukan atau bukan milik akun Anda.',
            ], 404);
        }

        if ($request->hasFile('bukti_pembayaran')) {
            if ($servis->bukti_pembayaran && Storage::disk('public')->exists($servis->bukti_pembayaran)) {
                Storage::disk('public')->delete($servis->bukti_pembayaran);
            }

            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran_servis', 'public');

            $servis->update([
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_pembayaran' => 'dibayar',
                'waktu_pembayaran' => now(),
                'bukti_pembayaran' => $path,
                'total_biaya' => $servis->total_biaya ?: ($servis->estimasi_biaya ?: 0),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bukti pembayaran servis berhasil diunggah.',
            'data' => $this->appendServisUrls($servis->fresh('pengguna')),
        ], 200);
    }

    private function appendServisUrls($servis): array
    {
        $data = $servis->toArray();

        $data['bukti_pembayaran_url'] = $servis->bukti_pembayaran
            ? asset('storage/' . $servis->bukti_pembayaran)
            : null;

        $data['total_biaya_final'] = (int) ($servis->total_biaya ?: $servis->estimasi_biaya ?: 0);
        $data['status_pembayaran_label'] = $this->getStatusPembayaranLabel($servis->status_pembayaran);
        $data['metode_pembayaran_label'] = $servis->metode_pembayaran ?: 'Belum dipilih';

        return $data;
    }

    private function getStatusPembayaranLabel(?string $status): string
    {
        return match (strtolower($status ?? 'pending')) {
            'dibayar', 'paid', 'lunas' => 'Dibayar',
            'ditolak', 'gagal', 'failed' => 'Ditolak',
            default => 'Belum Dibayar',
        };
    }
}
