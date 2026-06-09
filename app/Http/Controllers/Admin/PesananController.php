<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Produk;
use App\Models\Keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PesananController extends Controller
{
    /**
     * GET /api/pesanan  (Admin only)
     * Ambil semua pesanan + detail + produk + ekspedisi.
     */
    public function index(Request $request)
    {
        $query = Pesanan::with(['detail.produk', 'ekspedisi', 'pengguna']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by ID, nama pelanggan, atau nama produk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_pesanan', 'like', "%{$search}%")
                    ->orWhereHas('pengguna', fn($pq) => $pq->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('detail.produk', fn($dq) => $dq->where('nama_produk', 'like', "%{$search}%"));
            });
        }

        // Filter tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }

        $perPage = $request->input('per_page', 10);
        $pesanan = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Stats (selalu dari seluruh data, tanpa filter)
        $allPesanan = Pesanan::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "diproses" THEN 1 ELSE 0 END) as diproses,
            SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = "selesai" THEN total_harga ELSE 0 END) as total_pendapatan
        ')->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil semua pesanan',
            'data' => $pesanan,
            'stats' => [
                'total' => (int) $allPesanan->total,
                'pending' => (int) $allPesanan->pending,
                'diproses' => (int) $allPesanan->diproses,
                'selesai' => (int) $allPesanan->selesai,
                'total_pendapatan' => (float) $allPesanan->total_pendapatan,
            ],
        ], 200);
    }

    /**
     * GET /api/pesanan/{id}
     * Detail satu pesanan (user hanya bisa lihat miliknya, admin bisa semua).
     */
    public function show(Request $request, $id)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $pesanan = Pesanan::with(['detail.produk', 'ekspedisi'])->find($id);

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        // User biasa hanya boleh lihat pesanannya sendiri
        if (!$pengguna->isAdmin() && $pesanan->id_pengguna !== $pengguna->id_pengguna) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke pesanan ini',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil detail pesanan',
            'data' => $pesanan,
        ], 200);
    }

    /**
     * POST /api/pesanan
     * Buat pesanan + detail (checkout).
     * Bisa checkout langsung (items) atau dari keranjang (dari_keranjang: true).
     */
    public function store(Request $request)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $validator = Validator::make($request->all(), [
            'id_layanan_ekspedisi' => 'required|exists:layanan_ekspedisi,id_layanan_ekspedisi',
            'alamat_pengiriman' => 'required|string|max:500',
            'dari_keranjang' => 'sometimes|boolean',
            // Jika checkout langsung (bukan dari keranjang)
            'items' => 'required_if:dari_keranjang,false|array|min:1',
            'items.*.id_produk' => 'required_if:dari_keranjang,false|exists:produk,id_produk',
            'items.*.jumlah' => 'required_if:dari_keranjang,false|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Tentukan sumber item: dari keranjang atau dari request langsung
            $dariKeranjang = $request->boolean('dari_keranjang', false);

            if ($dariKeranjang) {
                // Ambil item dari keranjang pengguna
                $query = Keranjang::with('produk')
                    ->where('pengguna_id', $pengguna->id_pengguna);

                if ($request->has('cart_item_ids') && is_array($request->cart_item_ids)) {
                    $query->whereIn('id', $request->cart_item_ids);
                }

                $keranjangItems = $query->get();

                if ($keranjangItems->isEmpty()) {
                    throw new \Exception('Keranjang belanja Anda kosong atau item terpilih tidak ditemukan', 400);
                }

                $itemsToProcess = $keranjangItems->map(fn($k) => [
                    'id_produk' => $k->id_produk,
                    'jumlah' => $k->jumlah,
                ])->toArray();
            } else {
                $itemsToProcess = $request->items;
            }

            // Hitung total harga & validasi stok
            $totalHarga = 0;
            $processedItems = [];

            foreach ($itemsToProcess as $item) {
                $produk = Produk::find($item['id_produk']);

                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['id_produk']} Tidak Ditemukan", 404);
                }

                if ($produk->stok < $item['jumlah']) {
                    throw new \Exception(
                        "Stok produk '{$produk->nama_produk}' Tidak Mencukupi (Tersisa: {$produk->stok})",
                        400
                    );
                }

                $subtotal = $produk->harga * $item['jumlah'];
                $totalHarga += $subtotal;

                // Kurangi stok
                $produk->decrement('stok', $item['jumlah']);

                $processedItems[] = [
                    'id_produk' => $produk->id_produk,
                    'jumlah' => $item['jumlah'],
                    'harga' => $produk->harga,
                ];
            }

            // Get shipping service details to calculate grand total
            $ekspedisi = \App\Models\LayananEkspedisi::find($request->id_layanan_ekspedisi);
            $biayaOngkir = $ekspedisi ? (float) $ekspedisi->biaya_ongkir : 0;

            // Calculate insurance: 0.2% of total items price ONLY if NOT PICKUP
            $isPickup = ($ekspedisi && $ekspedisi->kode_layanan === 'PICKUP');
            $asuransi = $isPickup ? 0 : round($totalHarga * 0.002);

            $grandTotal = $totalHarga + $biayaOngkir + $asuransi;

            // Simpan ke tabel pesanan
            $pesanan = Pesanan::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'total_harga' => $grandTotal,
                'status' => 'pending',
                'id_layanan_ekspedisi' => $request->id_layanan_ekspedisi,
                'alamat_pengiriman' => $request->alamat_pengiriman,
            ]);

            // Simpan ke detail_pesanan
            foreach ($processedItems as $item) {
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item['id_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                ]);
            }

            // Jika checkout dari keranjang → kosongkan keranjang untuk item terpilih
            if ($dariKeranjang) {
                $delQuery = Keranjang::where('pengguna_id', $pengguna->id_pengguna);
                if ($request->has('cart_item_ids') && is_array($request->cart_item_ids)) {
                    $delQuery->whereIn('id', $request->cart_item_ids);
                }
                $delQuery->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.',
                'data' => $pesanan->load(['detail.produk', 'ekspedisi']),
                'instruksi_pembayaran' => [
                    'pesan' => 'Harap segera lakukan pembayaran agar pesanan Anda dapat diproses.',
                    'metode' => [
                        [
                            'nama' => 'SeaBank',
                            'nomor_rekening' => '901358215435',
                            'atas_nama' => 'Insan Fadillah',
                            'instruksi' => 'Transfer ke rekening SeaBank dengan nomor di atas.'
                        ],
                        [
                            'nama' => 'DANA',
                            'nomor_rekening' => '083897628556',
                            'atas_nama' => 'Insan Fadillah',
                            'instruksi' => 'Kirim ke nomor DANA di atas.'
                        ],
                        [
                            'nama' => 'QRIS',
                            'nomor_rekening' => 'NMID: ID102650634862',
                            'atas_nama' => 'GUMELAR LAPTOP - ELECTRONICS',
                            'instruksi' => 'Scan QRIS Gumelar Laptop / Defkan Computer menggunakan aplikasi m-Banking atau E-Wallet apa saja.'
                        ]
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            $code = $e->getCode();
            $code = ($code >= 400 && $code < 600) ? $code : 500;

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $code);
        }
    }

    /**
     * GET /api/pesanan-saya
     * Ambil semua pesanan milik pengguna yang sedang login.
     */
    public function myOrders(Request $request)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $pesanan = Pesanan::with(['detail.produk', 'ekspedisi'])
            ->where('id_pengguna', $pengguna->id_pengguna)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil mengambil riwayat pesanan Anda',
            'data' => $pesanan,
        ], 200);
    }

    /**
     * PATCH /api/pesanan/{id}/status  (Admin only)
     * Update status pesanan (diproses, dikirim, selesai, dll).
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,diproses,dikirim,selesai,dibatalkan',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $pesanan = Pesanan::find($id);

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        // Lakukan pengembalian stok jika pesanan dibatalkan
        if ($request->status === 'dibatalkan' && $pesanan->status !== 'dibatalkan') {
            foreach ($pesanan->detail as $item) {
                if ($item->produk) {
                    $item->produk->increment('stok', $item->jumlah);
                }
            }
        }
        // Jika sebelumnya dibatalkan lalu dihidupkan kembali (selain dibatalkan)
        else if ($pesanan->status === 'dibatalkan' && $request->status !== 'dibatalkan') {
            foreach ($pesanan->detail as $item) {
                if ($item->produk) {
                    // Pastikan stok mencukupi sebelum memulihkan pesanan
                    if ($item->produk->stok < $item->jumlah) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Stok produk '{$item->produk->nama_produk}' tidak mencukupi untuk memulihkan pesanan ini (Tersisa: {$item->produk->stok}).",
                        ], 400);
                    }
                    $item->produk->decrement('stok', $item->jumlah);
                }
            }
        }

        $pesanan->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Status pesanan #{$pesanan->id_pesanan} berhasil diperbarui menjadi: {$request->status}",
            'data' => $pesanan->fresh()->load(['detail.produk', 'ekspedisi']),
        ], 200);
    }

    /**
     * POST /api/pesanan/{id}/bayar
     * Upload bukti pembayaran untuk pesanan.
     */
    public function uploadBuktiPembayaran(Request $request, $id)
    {
        /** @var \App\Models\Pengguna $pengguna */
        $pengguna = $request->user();

        $pesanan = Pesanan::find($id);

        if (!$pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        // Hanya pemilik pesanan yang boleh upload bukti bayar
        if (!$pengguna->isAdmin() && $pesanan->id_pengguna !== $pengguna->id_pengguna) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak berhak mengakses pesanan ini',
            ], 403);
        }

        // Hanya boleh mengunggah bukti pembayaran untuk pesanan yang masih pending
        if ($pesanan->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda hanya dapat mengunggah bukti pembayaran untuk pesanan yang berstatus pending.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'metode_pembayaran' => 'required|string|max:100',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Proses upload file
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        $pesanan->update([
            'metode_pembayaran' => $request->metode_pembayaran,
            'bukti_pembayaran' => $path,
            'waktu_pembayaran' => now(),
            // Ubah status otomatis dari pending menjadi diproses (opsional, tergantung alur)
            'status' => $pesanan->status === 'pending' ? 'diproses' : $pesanan->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Bukti pembayaran berhasil diunggah. Pesanan sedang diproses.',
            'data' => $pesanan->fresh(),
        ], 200);
    }

    /**
     * GET /api/pesanan/export (Admin only)
     * Export daftar pesanan ke CSV.
     */
    public function export(Request $request)
    {
        $query = Pesanan::with(['detail.produk', 'ekspedisi', 'pengguna']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by ID, nama pelanggan, atau nama produk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_pesanan', 'like', "%{$search}%")
                  ->orWhereHas('pengguna', fn($pq) => $pq->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('detail.produk', fn($dq) => $dq->where('nama_produk', 'like', "%{$search}%"));
            });
        }

        // Filter tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('created_at', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('created_at', '<=', $request->sampai_tanggal);
        }

        $pesanans = $query->orderBy('created_at', 'desc')->get();
        $filename = "daftar_pesanan_" . now()->format('Ymd_His') . ".csv";

        $callback = function() use ($pesanans) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel support
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Header Row
            fputcsv($file, [
                'ID Pesanan', 
                'Tanggal', 
                'Nama Pelanggan', 
                'No HP', 
                'Email', 
                'Metode Pembayaran', 
                'Total Harga', 
                'Status Pesanan', 
                'Alamat Pengiriman', 
                'Ekspedisi / Layanan',
                'Detail Produk'
            ], ';');

            foreach ($pesanans as $p) {
                // Compile product details into a single readable string
                $detailStr = '';
                if ($p->detail && $p->detail->count() > 0) {
                    $details = [];
                    foreach ($p->detail as $item) {
                        $prodName = $item->produk ? $item->produk->nama_produk : 'Produk Dihapus';
                        $details[] = "{$prodName} ({$item->jumlah}x @ Rp " . number_format($item->harga, 0, ',', '.') . ")";
                    }
                    $detailStr = implode(' | ', $details);
                }

                fputcsv($file, [
                    '#' . $p->id_pesanan,
                    $p->created_at ? $p->created_at->format('Y-m-d H:i:s') : '',
                    $p->pengguna ? $p->pengguna->nama : '—',
                    $p->pengguna ? $p->pengguna->no_hp : '—',
                    $p->pengguna ? $p->pengguna->email : '—',
                    $p->metode_pembayaran ?: '—',
                    $p->total_harga,
                    ucfirst($p->status),
                    $p->alamat_pengiriman ?: '—',
                    $p->ekspedisi ? ($p->ekspedisi->nama_ekspedisi . ' (' . $p->ekspedisi->layanan . ')') : '—',
                    $detailStr
                ], ';');
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
