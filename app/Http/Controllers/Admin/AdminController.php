<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\Servis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * GET /api/admin/stats
     * Statistik dashboard admin
     */
    public function stats()
    {
        $stats = [
            'total_produk' => Produk::count(),
            'total_pengguna' => Pengguna::where('role', 'user')->count(),
            'total_pesanan' => Pesanan::count(),
            'total_servis' => Servis::count(),
            'pesanan_pending' => Pesanan::where('status', 'pending')->count(),
            'servis_aktif' => Servis::whereNotIn('status', ['selesai', 'diambil'])->count(),
            'revenue_bulan' => Pesanan::where('status', 'selesai')
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
    public function pesananTerbaru()
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
    public function servisTerbaru()
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
     * Daftar semua pelanggan (role=user)
     */
    public function pelanggan(Request $request)
    {
        $adminRoles = ['admin', 'administrator', 'superadmin'];
        $validOrderRevenueStatuses = ['selesai', 'dibayar', 'lunas', 'paid', 'success', 'settlement', 'sukses'];

        $query = Pengguna::whereIn('role', array_merge(['user'], $adminRoles))
            ->withCount(['pesanan', 'servis']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('no_hp', 'like', "%{$s}%");
            });
        }

        // Disamakan dengan card Total Pesanan di menu Kelola Pesanan.
        $totalPesananDatabase = Pesanan::count();

        // Disamakan dengan card Total Servis di menu Kelola Servis.
        $totalServisDatabase = Servis::count();

        // Pendapatan pesanan: mengikuti logika menu Kelola Pesanan, yaitu pesanan selesai/valid bayar.
        $totalPendapatanPesanan = (float) Pesanan::whereIn('status', $validOrderRevenueStatuses)
            ->sum('total_harga');

        // Pendapatan servis: mengikuti card Total Pendapatan di Kelola Servis, yaitu servis status selesai.
        $serviceAmountColumns = [
            'total_biaya',
            'biaya_total',
            'biaya_akhir',
            'estimasi_biaya',
            'estimasi',
            'biaya_estimasi',
            'total_pembayaran',
        ];

        $existingServiceAmountColumns = array_values(array_filter($serviceAmountColumns, function ($column) {
            return \Illuminate\Support\Facades\Schema::hasColumn('servis', $column);
        }));

        $serviceAmountExpression = count($existingServiceAmountColumns) > 0
            ? 'COALESCE(' . implode(', ', array_map(fn($column) => "`{$column}`", $existingServiceAmountColumns)) . ', 0)'
            : '0';

        $sumServiceRevenue = function ($query) use ($serviceAmountExpression) {
            return (float) $query
                ->where('status', 'selesai')
                ->selectRaw("SUM({$serviceAmountExpression}) as total")
                ->value('total');
        };

        $totalPendapatanServis = $sumServiceRevenue(Servis::query());

        $pelanggan = $query->orderBy('created_at', 'asc')->get();

        $pelanggan->each(function ($p) use ($adminRoles, $validOrderRevenueStatuses, $totalPesananDatabase, $totalServisDatabase, $totalPendapatanPesanan, $totalPendapatanServis, $sumServiceRevenue) {
            $role = strtolower((string) ($p->role ?? ''));
            $isAdminDefkan = in_array($role, $adminRoles, true);

            if ($isAdminDefkan) {
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

            $idPengguna = $p->id_pengguna;

            $totalPesananUser = Pesanan::where('id_pengguna', $idPengguna)->count();
            $totalServisUser = Servis::where('pengguna_id', $idPengguna)->count();

            $belanjaProdukUser = (float) Pesanan::where('id_pengguna', $idPengguna)
                ->whereIn('status', $validOrderRevenueStatuses)
                ->sum('total_harga');

            $belanjaServisUser = $sumServiceRevenue(
                Servis::where('pengguna_id', $idPengguna)
            );

            $p->pesanan_count = $totalPesananUser;
            $p->total_pesanan = $totalPesananUser;
            $p->servis_count = $totalServisUser;
            $p->total_servis = $totalServisUser;
            $p->total_belanja_produk = $belanjaProdukUser;
            $p->total_belanja_servis = $belanjaServisUser;
            $p->total_belanja = (float) ($belanjaProdukUser + $belanjaServisUser);
            $p->__is_admin_defkan = false;
        });

        return response()->json([
            'status' => 'success',
            'data' => $pelanggan,
        ]);
    }
    /**
     * GET /api/admin/pendapatan
     * Revenue data by period: harian, mingguan, bulanan, tahunan
     */
    public function pendapatan(Request $request)
    {
        $period = $request->get('period', 'bulanan'); // harian, mingguan, bulanan, tahunan
        $now = Carbon::now();

        // Base query: only completed orders
        $baseQuery = Pesanan::whereIn('status', ['diproses', 'dikirim', 'selesai']);

        $data = [];
        $totalRevenue = 0;
        $totalOrders = 0;

        if ($period === 'harian') {
            // Last 30 days
            for ($i = 29; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
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
            // Last 12 weeks
            for ($i = 11; $i >= 0; $i--) {
                $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $revenue = (clone $baseQuery)->whereBetween('created_at', [$weekStart, $weekEnd])->sum('total_harga');
                $orders = (clone $baseQuery)->whereBetween('created_at', [$weekStart, $weekEnd])->count();
                $totalRevenue += $revenue;
                $totalOrders += $orders;
                $data[] = [
                    'label' => 'W' . $weekStart->weekOfYear . ' (' . $weekStart->format('d/m') . ')',
                    'date' => $weekStart->toDateString() . ' - ' . $weekEnd->toDateString(),
                    'revenue' => (float) $revenue,
                    'orders' => $orders,
                ];
            }
        } elseif ($period === 'bulanan') {
            // Last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
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
            // Last 5 years
            for ($i = 4; $i >= 0; $i--) {
                $year = $now->copy()->subYears($i);
                $revenue = (clone $baseQuery)
                    ->whereYear('created_at', $year->year)
                    ->sum('total_harga');
                $orders = (clone $baseQuery)
                    ->whereYear('created_at', $year->year)
                    ->count();
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
     * Export revenue data as CSV
     */
    public function pendapatanExport(Request $request)
    {
        $period = $request->get('period', 'bulanan');

        // Re-use the same logic to build data
        $response = $this->pendapatan($request);
        $json = json_decode($response->getContent(), true);
        $items = $json['data']['items'] ?? [];

        $filename = "pendapatan_{$period}_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items, $period, $json) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8 support
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($handle, ['Periode', 'Tanggal/Range', 'Jumlah Pesanan', 'Pendapatan (Rp)'], ';');

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item['label'],
                    $item['date'],
                    $item['orders'],
                    number_format($item['revenue'], 0, ',', '.'),
                ], ';');
            }

            // Total row
            fputcsv($handle, [
                'TOTAL',
                '',
                $json['data']['total_orders'] ?? 0,
                number_format($json['data']['total_revenue'] ?? 0, 0, ',', '.'),
            ], ';');

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * GET /api/admin/sidebar-counts
     * Menghitung jumlah notifikasi untuk menu sidebar
     */
    public function sidebarCounts()
    {
        $produkHabis = Produk::where('stok', '<=', 0)->count();
        $pesananBaru = Pesanan::where('status', 'pending')->count();
        $servisBaru = Servis::where('status', 'menunggu')->count();
        $chatUnread = \App\Models\ChatRoom::whereHas('messages', function ($q) {
            $q->where('is_admin', false)->where('is_read', false);
        })->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'produk_habis' => $produkHabis,
                'pesanan_baru' => $pesananBaru,
                'servis_baru' => $servisBaru,
                'chat_unread' => $chatUnread,
            ]
        ]);
    }

    /**
     * GET /api/admin/dashboard-stats
     * Return comprehensive stats for the redesigned dashboard widgets
     */
    public function dashboardStats(Request $request)
    {
        // 1. Core Counts
        $totalProduk = Produk::count();
        $totalPengguna = Pengguna::where('role', 'user')->count();
        $totalPesanan = Pesanan::count();
        $servisAktif = Servis::whereNotIn('status', ['selesai', 'diambil'])->count();
        $pesananPending = Pesanan::where('status', 'pending')->count();

        // 2. Month-over-month and growth stats
        $prevProduk = Produk::where('created_at', '<', now()->startOfMonth())->count();
        $prevPengguna = Pengguna::where('role', 'user')->where('created_at', '<', now()->startOfMonth())->count();
        $prevPesanan = Pesanan::where('created_at', '<', now()->startOfMonth())->count();

        $changeProduk = $this->calculatePercentageChange($totalProduk, $prevProduk);
        $changePengguna = $this->calculatePercentageChange($totalPengguna, $prevPengguna);
        $changePesanan = $this->calculatePercentageChange($totalPesanan, $prevPesanan);

        // Revenue month vs last month
        $revPesananBulanIni = Pesanan::where('status', 'selesai')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');
        $revServisBulanIni = Servis::where('status', 'selesai')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum(DB::raw('COALESCE(total_biaya, estimasi_biaya, 0)'));
        $revenueBulanIni = $revPesananBulanIni + $revServisBulanIni;

        $revPesananBulanLalu = Pesanan::where('status', 'selesai')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_harga');
        $revServisBulanLalu = Servis::where('status', 'selesai')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum(DB::raw('COALESCE(total_biaya, estimasi_biaya, 0)'));
        $revenueBulanLalu = $revPesananBulanLalu + $revServisBulanLalu;

        $changeRevenue = $this->calculatePercentageChange($revenueBulanIni, $revenueBulanLalu);

        // Active services growth since yesterday
        $servisAktifKemarin = Servis::whereNotIn('status', ['selesai', 'diambil'])
            ->where('created_at', '<', now()->startOfDay())
            ->count();
        $changeServis = $servisAktif - $servisAktifKemarin;

        // 3. Sales chart data (dynamic range: 7, 30, 90 days)
        $salesFilter = (int) $request->get('sales_filter', 7);
        if (!in_array($salesFilter, [7, 30, 90])) {
            $salesFilter = 7;
        }

        $weeklySales = [];
        $totalWeeklyRevenue = 0;
        for ($i = $salesFilter - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            $revPesanan = Pesanan::where('status', 'selesai')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_harga');
                
            $revServis = Servis::where('status', 'selesai')
                ->whereDate('created_at', $date->toDateString())
                ->sum(DB::raw('COALESCE(total_biaya, estimasi_biaya, 0)'));
                
            $rev = $revPesanan + $revServis;
            
            $totalWeeklyRevenue += $rev;
            $weeklySales[] = [
                'date' => $date->toDateString(),
                'label' => $date->translatedFormat('j M'), // e.g. "27 Mei", "1 Jun"
                'revenue' => (float) $rev,
            ];
        }
        $avgWeeklyRevenue = $totalWeeklyRevenue / $salesFilter;

        // 4. Service status breakdown
        $serviceBreakdown = [
            'dikerjakan' => Servis::where('status', 'dikerjakan')->count() + Servis::where('status', 'diperiksa')->count(),
            'menunggu' => Servis::where('status', 'menunggu')->count(),
            'selesai' => Servis::where('status', 'selesai')->count(),
            'diambil' => Servis::where('status', 'diambil')->count(),
        ];
        $totalServisCount = array_sum($serviceBreakdown);

        // 5. Top 5 selling products (dynamic range: bulan_ini, tahun_ini, semua)
        $topFilter = $request->get('top_products_filter', 'bulan_ini');

        $topProductsQuery = DB::table('detail_pesanan')
            ->join('produk', 'detail_pesanan.id_produk', '=', 'produk.id_produk')
            ->join('pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->select('produk.id_produk', 'produk.nama_produk', 'produk.foto', DB::raw('SUM(detail_pesanan.jumlah) as total_qty'))
            ->whereNull('produk.deleted_at')
            ->whereIn('pesanan.status', ['diproses', 'dikirim', 'selesai']);

        if ($topFilter === 'bulan_ini') {
            $topProductsQuery->whereMonth('pesanan.created_at', now()->month)
                ->whereYear('pesanan.created_at', now()->year);
        } elseif ($topFilter === 'tahun_ini') {
            $topProductsQuery->whereYear('pesanan.created_at', now()->year);
        }

        $topProducts = $topProductsQuery->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.foto')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        if ($topProducts->isEmpty()) {
            $topProducts = Produk::limit(5)->get()->map(function ($p) {
                return (object) [
                    'id_produk' => $p->id_produk,
                    'nama_produk' => $p->nama_produk,
                    'foto' => $p->foto,
                    'total_qty' => 0
                ];
            });
        }

        // Add correct full URL to product photo
        foreach ($topProducts as $p) {
            if ($p->foto) {
                if (str_starts_with($p->foto, 'http://') || str_starts_with($p->foto, 'https://')) {
                    $p->foto_url = $p->foto;
                } else {
                    $p->foto_url = asset('storage/' . $p->foto);
                }
            } else {
                $p->foto_url = null;
            }
        }

        // Reminders
        $servisMenungguCount = Servis::where('status', 'menunggu')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => [
                    'produk' => [
                        'value' => $totalProduk,
                        'change' => $changeProduk
                    ],
                    'pengguna' => [
                        'value' => $totalPengguna,
                        'change' => $changePengguna
                    ],
                    'pesanan' => [
                        'value' => $totalPesanan,
                        'change' => $changePesanan
                    ],
                    'verifikasi' => [
                        'value' => $pesananPending
                    ],
                    'servis' => [
                        'value' => $servisAktif,
                        'change' => $changeServis
                    ],
                    'revenue' => [
                        'value' => $revenueBulanIni,
                        'change' => $changeRevenue
                    ]
                ],
                'weekly_sales' => [
                    'items' => $weeklySales,
                    'total' => $totalWeeklyRevenue,
                    'average' => $avgWeeklyRevenue
                ],
                'service_breakdown' => [
                    'items' => $serviceBreakdown,
                    'total' => $totalServisCount
                ],
                'top_products' => $topProducts,
                'reminders' => [
                    'servis_menunggu' => $servisMenungguCount,
                    'pesanan_pending' => $pesananPending
                ]
            ]
        ]);
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100);
    }

    public function produkStats()
    {
        $totalProduk = Produk::count();
        $stokTersedia = Produk::where('stok', '>', 0)->count();
        $stokHabis = Produk::where('stok', '=', 0)->count();
        $kategori = Produk::distinct('kategori')->count('kategori');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_produk' => $totalProduk,
                'stok_tersedia' => $stokTersedia,
                'stok_habis' => $stokHabis,
                'kategori' => $kategori
            ]
        ]);
    }
}

