<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\Complaint;
use App\Models\Pengguna;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Servis;
use App\Models\Ulasan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    /**
     * GET /api/admin/stats
     * Statistik ringkas dashboard admin.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_produk' => Produk::count(),
            'total_pengguna' => Pengguna::where('role', '=', 'user')->count(),
            'total_pesanan' => Pesanan::count(),
            'total_servis' => Servis::count(),
            'pesanan_pending' => Pesanan::where('status', '=', 'pending')->count(),
            'servis_aktif' => Servis::whereNotIn('status', ['selesai', 'diambil'])->count(),
            'revenue_bulan' => Pesanan::where('status', '=', 'selesai')
                ->whereMonth('created_at', now()->month)
                ->sum('total_harga'),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }

    /**
     * GET /api/admin/pesanan-terbaru
     */
    public function pesananTerbaru(): JsonResponse
    {
        $pesanan = Pesanan::with(['pengguna', 'detail.produk'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $pesanan,
        ]);
    }

    /**
     * GET /api/admin/servis-terbaru
     */
    public function servisTerbaru(): JsonResponse
    {
        $servis = Servis::with('pengguna')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $servis,
        ]);
    }

    /**
     * GET /api/admin/pelanggan
     * Daftar semua pelanggan beserta statistik belanja mereka.
     */
    public function pelanggan(Request $request): JsonResponse
    {
        $adminRoles = ['admin', 'administrator', 'superadmin'];
        $validOrderRevenueStatuses = ['selesai', 'dibayar', 'lunas', 'paid', 'success', 'settlement', 'sukses'];

        $query = Pengguna::whereIn('role', array_merge(['user'], $adminRoles));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('no_hp', 'like', "%{$s}%");
            });
        }

        // ─── Global stats (untuk akun admin) ─────────────────────────────────
        $totalPesananDatabase = Pesanan::count();
        $totalServisDatabase = Servis::count();
        $totalPendapatanPesanan = (float) Pesanan::whereIn('status', $validOrderRevenueStatuses)
            ->sum('total_harga');

        // Tentukan ekspresi kolom biaya servis yang benar-benar ada di tabel
        $serviceAmountColumns = ['total_biaya', 'biaya_total', 'biaya_akhir', 'estimasi_biaya', 'estimasi', 'biaya_estimasi', 'total_pembayaran'];
        $existingServiceAmountColumns = array_values(array_filter(
            $serviceAmountColumns,
            fn ($col) => Schema::hasColumn('servis', $col)
        ));
        $serviceAmountExpression = count($existingServiceAmountColumns) > 0
            ? 'COALESCE('.implode(', ', array_map(fn ($c) => "`{$c}`", $existingServiceAmountColumns)).', 0)'
            : '0';

        $totalPendapatanServis = (float) Servis::where('status', 'selesai')
            ->selectRaw("SUM({$serviceAmountExpression}) as total")
            ->value('total');

        // ─── Ambil semua pengguna ─────────────────────────────────────────────
        $pelanggan = $query->orderBy('created_at', 'asc')->get();
        $allIds = $pelanggan->pluck('id_pengguna')->all();

        // ─── Bulk query pesanan per pengguna (1 query saja) ───────────────────
        $statusList = implode("','", $validOrderRevenueStatuses);
        $pesananStats = Pesanan::selectRaw("
                id_pengguna,
                COUNT(*) as total_pesanan,
                SUM(CASE WHEN status IN ('{$statusList}') THEN total_harga ELSE 0 END) as total_belanja_produk
            ")
            ->whereIn('id_pengguna', $allIds)
            ->groupBy('id_pengguna')
            ->get()
            ->keyBy('id_pengguna');

        // ─── Bulk query servis per pengguna (1 query saja) ────────────────────
        $servisStats = Servis::selectRaw("
                pengguna_id,
                COUNT(*) as total_servis,
                SUM(CASE WHEN status = 'selesai' THEN {$serviceAmountExpression} ELSE 0 END) as total_belanja_servis
            ")
            ->whereIn('pengguna_id', $allIds)
            ->groupBy('pengguna_id')
            ->get()
            ->keyBy('pengguna_id');

        // ─── Map hasil ke setiap pengguna ─────────────────────────────────────
        $pelanggan->each(function ($p) use (
            $adminRoles,
            $totalPesananDatabase, $totalServisDatabase,
            $totalPendapatanPesanan, $totalPendapatanServis,
            $pesananStats, $servisStats
        ) {
            $role = strtolower((string) ($p->role ?? ''));
            $isAdminDefkan = in_array($role, $adminRoles, true);

            if ($isAdminDefkan) {
                // Admin melihat stats keseluruhan toko
                $p->pesanan_count = $totalPesananDatabase;
                $p->total_pesanan = $totalPesananDatabase;
                $p->servis_count = $totalServisDatabase;
                $p->total_servis = $totalServisDatabase;
                $p->total_belanja_produk = (float) $totalPendapatanPesanan;
                $p->total_belanja_servis = (float) $totalPendapatanServis;
                $p->total_belanja = (float) ($totalPendapatanPesanan + $totalPendapatanServis);
                $p->__is_admin_defkan = true;

                return;
            }

            // User biasa — ambil dari hasil bulk query
            $pStat = $pesananStats->get($p->id_pengguna);
            $sStat = $servisStats->get($p->id_pengguna);

            $totalPesanan = (int) ($pStat->total_pesanan ?? 0);
            $belanjaProduk = (float) ($pStat->total_belanja_produk ?? 0);
            $totalServis = (int) ($sStat->total_servis ?? 0);
            $belanjaServis = (float) ($sStat->total_belanja_servis ?? 0);

            $p->pesanan_count = $totalPesanan;
            $p->total_pesanan = $totalPesanan;
            $p->servis_count = $totalServis;
            $p->total_servis = $totalServis;
            $p->total_belanja_produk = $belanjaProduk;
            $p->total_belanja_servis = $belanjaServis;
            $p->total_belanja = $belanjaProduk + $belanjaServis;
            $p->__is_admin_defkan = false;
        });

        return response()->json([
            'status' => 'success',
            'data' => $pelanggan,
        ]);
    }

    /**
     * GET /api/admin/pendapatan
     * Data pendapatan berdasarkan periode: harian, mingguan, bulanan, tahunan.
     */
    public function pendapatan(Request $request): JsonResponse
    {
        $period = $request->get('period', 'bulanan'); // harian | mingguan | bulanan | tahunan
        $now = Carbon::now();

        // Base query: pesanan yang sudah diproses/dikirim/selesai
        $baseQuery = Pesanan::whereIn('status', ['diproses', 'dikirim', 'selesai']);

        $data = [];
        $totalRevenue = 0;
        $totalOrders = 0;

        if ($period === 'harian') {
            // 30 hari terakhir
            for ($i = 29; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i); // copy() agar Carbon tidak dimutasi
                $revenue = (clone $baseQuery)->whereDate('created_at', $date->toDateString())->sum('total_harga');
                $orders = (clone $baseQuery)->whereDate('created_at', $date->toDateString())->count();
                $totalRevenue += $revenue;
                $totalOrders += $orders;
                $data[] = [
                    'label' => $date->format('d M'),
                    'date' => $date->toDateString(),
                    'revenue' => (float) $revenue,
                    'orders' => $orders,
                ];
            }
        } elseif ($period === 'mingguan') {
            // 12 minggu terakhir
            for ($i = 11; $i >= 0; $i--) {
                $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $revenue = (clone $baseQuery)->whereBetween('created_at', [$weekStart, $weekEnd])->sum('total_harga');
                $orders = (clone $baseQuery)->whereBetween('created_at', [$weekStart, $weekEnd])->count();
                $totalRevenue += $revenue;
                $totalOrders += $orders;
                $data[] = [
                    'label' => 'W'.$weekStart->weekOfYear.' ('.$weekStart->format('d/m').')',
                    'date' => $weekStart->toDateString().' - '.$weekEnd->toDateString(),
                    'revenue' => (float) $revenue,
                    'orders' => $orders,
                ];
            }
        } elseif ($period === 'bulanan') {
            // 12 bulan terakhir
            for ($i = 11; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i); // satu copy() cukup untuk year & month
                $revenue = (clone $baseQuery)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_harga');
                $orders = (clone $baseQuery)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
                $totalRevenue += $revenue;
                $totalOrders += $orders;
                $data[] = [
                    'label' => $month->translatedFormat('M Y'),
                    'date' => $month->format('Y-m'),
                    'revenue' => (float) $revenue,
                    'orders' => $orders,
                ];
            }
        } elseif ($period === 'tahunan') {
            // 5 tahun terakhir
            for ($i = 4; $i >= 0; $i--) {
                $year = $now->copy()->subYears($i);
                $revenue = (clone $baseQuery)->whereYear('created_at', $year->year)->sum('total_harga');
                $orders = (clone $baseQuery)->whereYear('created_at', $year->year)->count();
                $totalRevenue += $revenue;
                $totalOrders += $orders;
                $data[] = [
                    'label' => (string) $year->year,
                    'date' => (string) $year->year,
                    'revenue' => (float) $revenue,
                    'orders' => $orders,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => $period,
                'items' => $data,
                'total_revenue' => (float) $totalRevenue,
                'total_orders' => $totalOrders,
            ],
        ]);
    }

    /**
     * GET /api/admin/pendapatan/export
     * Export data pendapatan sebagai file CSV (UTF-8 with BOM untuk Excel).
     */
    public function pendapatanExport(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $period = $request->get('period', 'bulanan');

        // Gunakan kembali logika pendapatan()
        $response = $this->pendapatan($request);
        $json = json_decode($response->getContent(), true);
        $items = $json['data']['items'] ?? [];

        $filename = "pendapatan_{$period}_".now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Ambil total sekali di luar closure untuk menghindari capture yang tidak diperlukan
        $totalOrders = $json['data']['total_orders'] ?? 0;
        $totalRevenue = $json['data']['total_revenue'] ?? 0;

        $callback = function () use ($items, $totalOrders, $totalRevenue) {
            $handle = fopen('php://output', 'w');

            // BOM agar Excel membaca UTF-8 dengan benar
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['Periode', 'Tanggal/Range', 'Jumlah Pesanan', 'Pendapatan (Rp)'], ';');

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item['label'],
                    $item['date'],
                    $item['orders'],
                    number_format($item['revenue'], 0, ',', '.'),
                ], ';');
            }

            // Baris total
            fputcsv($handle, [
                'TOTAL',
                '',
                $totalOrders,
                number_format($totalRevenue, 0, ',', '.'),
            ], ';');

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * GET /api/admin/sidebar-counts
     * Jumlah notifikasi untuk badge menu sidebar admin.
     */
    public function sidebarCounts(): JsonResponse
    {
        $produkHabis = Produk::where('stok', '<=', 0)->count();
        $pesananBaru = Pesanan::where('status', '=', 'pending')->count();
        $servisBaru = Servis::where('status', '=', 'menunggu')->count();
        $chatUnread = ChatRoom::whereHas('messages', function ($q) {
            $q->where('is_admin', '=', false)->where('is_read', '=', false);
        })->count();
        $complaintMenunggu = Complaint::where('status', 'menunggu')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'produk_habis'       => $produkHabis,
                'pesanan_baru'       => $pesananBaru,
                'servis_baru'        => $servisBaru,
                'chat_unread'        => $chatUnread,
                'complaint_menunggu' => $complaintMenunggu,
            ],
        ]);
    }

    /**
     * GET /api/admin/dashboard-stats
     * Statistik lengkap untuk widget dashboard admin.
     */
    public function dashboardStats(Request $request): JsonResponse
    {
        // 1. Core counts
        $totalProduk = Produk::count();
        $totalPengguna = Pengguna::where('role', '=', 'user')->count();
        $totalPesanan = Pesanan::count();
        $servisAktif = Servis::whereNotIn('status', ['selesai', 'diambil'])->count();
        $pesananPending = Pesanan::where('status', '=', 'pending')->count();

        // 2. Month-over-month growth
        $prevProduk = Produk::where('created_at', '<', now()->startOfMonth())->count();
        $prevPengguna = Pengguna::where('role', '=', 'user')->where('created_at', '<', now()->startOfMonth())->count();
        $prevPesanan = Pesanan::where('created_at', '<', now()->startOfMonth())->count();

        $changeProduk = $this->calculatePercentageChange($totalProduk, $prevProduk);
        $changePengguna = $this->calculatePercentageChange($totalPengguna, $prevPengguna);
        $changePesanan = $this->calculatePercentageChange($totalPesanan, $prevPesanan);

        // Revenue bulan ini vs bulan lalu
        // FIX: now()->subMonth() dimutasi sekali, simpan ke variabel agar konsisten
        $bulanIni = now()->copy();
        $bulanLalu = now()->copy()->subMonth();

        $revPesananBulanIni = Pesanan::where('status', '=', 'selesai')
            ->whereMonth('created_at', $bulanIni->month)
            ->whereYear('created_at', $bulanIni->year)
            ->sum('total_harga');
        $revServisBulanIni = Servis::where('status', '=', 'selesai')
            ->whereMonth('created_at', $bulanIni->month)
            ->whereYear('created_at', $bulanIni->year)
            ->sum(DB::raw('COALESCE(total_biaya, estimasi_biaya, 0)'));
        $revenueBulanIni = $revPesananBulanIni + $revServisBulanIni;

        $revPesananBulanLalu = Pesanan::where('status', '=', 'selesai')
            ->whereMonth('created_at', $bulanLalu->month)
            ->whereYear('created_at', $bulanLalu->year)
            ->sum('total_harga');
        $revServisBulanLalu = Servis::where('status', '=', 'selesai')
            ->whereMonth('created_at', $bulanLalu->month)
            ->whereYear('created_at', $bulanLalu->year)
            ->sum(DB::raw('COALESCE(total_biaya, estimasi_biaya, 0)'));
        $revenueBulanLalu = $revPesananBulanLalu + $revServisBulanLalu;

        $changeRevenue = $this->calculatePercentageChange($revenueBulanIni, $revenueBulanLalu);

        // Pertumbuhan servis aktif sejak kemarin
        $servisAktifKemarin = Servis::whereNotIn('status', ['selesai', 'diambil'])
            ->where('created_at', '<', now()->startOfDay())
            ->count();
        $changeServis = $servisAktif - $servisAktifKemarin;

        // 3. Sales chart — dynamic range: 7, 30, atau 90 hari
        $salesFilter = (int) $request->get('sales_filter', 7);
        if (! in_array($salesFilter, [7, 30, 90])) {
            $salesFilter = 7;
        }

        $weeklySales = [];
        $totalWeeklyRevenue = 0;

        for ($i = $salesFilter - 1; $i >= 0; $i--) {
            $date = now()->copy()->subDays($i); // FIX: copy() agar tidak memutasi $now

            $revPesanan = Pesanan::where('status', '=', 'selesai')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_harga');

            $revServis = Servis::where('status', '=', 'selesai')
                ->whereDate('created_at', $date->toDateString())
                ->sum(DB::raw('COALESCE(total_biaya, estimasi_biaya, 0)'));

            $rev = $revPesanan + $revServis;

            $totalWeeklyRevenue += $rev;
            $weeklySales[] = [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('j M'),
                'revenue' => (float) $rev,
            ];
        }

        // FIX: hindari division by zero
        $avgWeeklyRevenue = $salesFilter > 0 ? $totalWeeklyRevenue / $salesFilter : 0;

        // 4. Service status breakdown (gabungkan dikerjakan + diperiksa dalam 1 query)
        $serviceBreakdown = [
            'dikerjakan' => Servis::whereIn('status', ['dikerjakan', 'diperiksa'])->count(),
            'menunggu' => Servis::where('status', '=', 'menunggu')->count(),
            'selesai' => Servis::where('status', '=', 'selesai')->count(),
            'diambil' => Servis::where('status', '=', 'diambil')->count(),
        ];
        $totalServisCount = array_sum($serviceBreakdown);

        // 5. Top 5 produk terlaris (filter: bulan_ini | tahun_ini | semua)
        $topFilter = $request->get('top_products_filter', 'bulan_ini');

        $topProductsQuery = DB::table('detail_pesanan')
            ->join('produk', 'detail_pesanan.id_produk', '=', 'produk.id_produk')
            ->join('pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->select(
                'produk.id_produk',
                'produk.nama_produk',
                'produk.foto',
                DB::raw('SUM(detail_pesanan.jumlah) as total_qty')
            )
            ->whereNull('produk.deleted_at')
            ->whereIn('pesanan.status', ['diproses', 'dikirim', 'selesai']);

        if ($topFilter === 'bulan_ini') {
            $topProductsQuery
                ->whereMonth('pesanan.created_at', now()->month)
                ->whereYear('pesanan.created_at', now()->year);
        } elseif ($topFilter === 'tahun_ini') {
            $topProductsQuery->whereYear('pesanan.created_at', now()->year);
        }

        $topProducts = $topProductsQuery
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.foto')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        if ($topProducts->isEmpty()) {
            $topProducts = Produk::limit(5)->get()->map(fn ($p) => (object) [
                'id_produk' => $p->id_produk,
                'nama_produk' => $p->nama_produk,
                'foto' => $p->foto,
                'total_qty' => 0,
            ]);
        }

        // Tambahkan foto_url yang benar ke setiap produk
        foreach ($topProducts as $p) {
            if ($p->foto) {
                $p->foto_url = str_starts_with($p->foto, 'http')
                    ? $p->foto
                    : asset('storage/'.$p->foto);
            } else {
                $p->foto_url = null;
            }
        }

        $servisMenungguCount = Servis::where('status', '=', 'menunggu')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'produk' => ['value' => $totalProduk,    'change' => $changeProduk],
                    'pengguna' => ['value' => $totalPengguna,  'change' => $changePengguna],
                    'pesanan' => ['value' => $totalPesanan,   'change' => $changePesanan],
                    'verifikasi' => ['value' => $pesananPending],
                    'servis' => ['value' => $servisAktif,    'change' => $changeServis],
                    'revenue' => ['value' => $revenueBulanIni, 'change' => $changeRevenue],
                ],
                'weekly_sales' => [
                    'items' => $weeklySales,
                    'total' => $totalWeeklyRevenue,
                    'average' => $avgWeeklyRevenue,
                ],
                'service_breakdown' => [
                    'items' => $serviceBreakdown,
                    'total' => $totalServisCount,
                ],
                'top_products' => $topProducts,
                'reminders' => [
                    'servis_menunggu' => $servisMenungguCount,
                    'pesanan_pending' => $pesananPending,
                ],
            ],
        ]);
    }

    /**
     * GET /api/admin/produk-stats
     * Statistik ringkas untuk halaman kelola produk.
     */
    public function produkStats(): JsonResponse
    {
        // FIX: gunakan selectRaw COUNT(DISTINCT ...) yang lebih reliable
        $totalProduk = Produk::count();
        $stokTersedia = Produk::where('stok', '>', 0)->count();
        $stokHabis = Produk::where('stok', '=', 0)->count();
        $kategori = (int) Produk::selectRaw('COUNT(DISTINCT kategori) as total')->value('total');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_produk' => $totalProduk,
                'stok_tersedia' => $stokTersedia,
                'stok_habis' => $stokHabis,
                'kategori' => $kategori,
            ],
        ]);
    }

    /**
     * Hitung persentase perubahan antara nilai saat ini dan sebelumnya.
     * Mengembalikan 100 jika sebelumnya 0 dan saat ini > 0,
     * atau 0 jika keduanya 0.
     */
    private function calculatePercentageChange(float $current, float $previous): int
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (int) round((($current - $previous) / $previous) * 100);
    }
}
