<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekomendasiLaptopController extends Controller
{
    /**
     * POST /api/rekomendasi/laptop
     * Input : { "software_ids": [1,3,5] }
     * Output: array of products yang memenuhi requirement tertinggi dari software yang dipilih,
     *         diurutkan dari yang paling cocok.
     */
    public function recommend(Request $request)
    {
        $request->validate([
            'software_ids'   => 'nullable|array',
            'software_ids.*' => 'integer|exists:software,id',
            'tipe'           => 'nullable|array',
            'tipe.*'         => 'string|in:kerja,sekolah,gaming',
            'budget_min'     => 'nullable|integer|min:0',
            'budget_max'     => 'nullable|integer|min:0',
        ]);

        $softwareIds = $request->input('software_ids', []);
        $tipes = $request->input('tipe', []);

        // Define default minimums based on tipes
        $defaultRam = 4;
        $defaultStorage = 256;
        $defaultCpu = 1;
        $defaultVga = 1;

        if (in_array('gaming', $tipes)) {
            $defaultRam = 8;
            $defaultStorage = 512;
            $defaultCpu = 2; // Core i5 / Ryzen 5
            $defaultVga = 2; // Dedicated
        }

        // 1. Hitung requirement MAKSIMUM dari semua software yang dipilih
        if (count($softwareIds) > 0) {
            $requirements = DB::table('requirement_software')
                ->whereIn('software_id', $softwareIds)
                ->select(
                    DB::raw('MAX(ram_min)     as ram_min'),
                    DB::raw('MAX(storage_min) as storage_min'),
                    DB::raw('MAX(cpu_min)     as cpu_min'),
                    DB::raw('MAX(vga_min)     as vga_min')
                )
                ->first();

            $minRam     = max($defaultRam, $requirements->ram_min ?? 4);
            $minStorage = max($defaultStorage, $requirements->storage_min ?? 128);
            $minCpu     = max($defaultCpu, intval($requirements->cpu_min ?? 1));
            $minVga     = max($defaultVga, intval($requirements->vga_min ?? 1));
        } else {
            $minRam     = $defaultRam;
            $minStorage = $defaultStorage;
            $minCpu     = $defaultCpu;
            $minVga     = $defaultVga;
        }

        // 2. Ambil semua produk aktif dengan filter budget dan tipe
        $query = Produk::where('stok', '>', 0);

        if ($request->filled('budget_min')) {
            $query->where('harga', '>=', $request->input('budget_min'));
        }
        if ($request->filled('budget_max')) {
            $query->where('harga', '<=', $request->input('budget_max'));
        }

        if (count($tipes) > 0) {
            $categories = [];
            foreach ($tipes as $t) {
                if ($t === 'kerja') {
                    $categories = array_merge($categories, ['Laptop Kantor / Kerja', 'Laptop Tipis & Ringan', 'Laptop Desain & Editing', 'Laptop Programming']);
                } elseif ($t === 'sekolah') {
                    $categories = array_merge($categories, ['Laptop Pelajar / Mahasiswa', 'Laptop Kantor / Kerja', 'Laptop Budget / Hemat', 'Laptop Tipis & Ringan']);
                } elseif ($t === 'gaming') {
                    $categories = array_merge($categories, ['Laptop Gaming', 'Laptop Desain & Editing']);
                }
            }
            $categories = array_unique($categories);
            if (count($categories) > 0) {
                $query->whereIn('kategori', $categories);
            }
        }

        $products = $query->get();

        // 3. Score dan sortir produk berdasarkan kecocokan spesifikasi (CPU, VGA, RAM, SSD)
        $scored = $products->map(function ($product) use ($minRam, $minStorage, $minCpu, $minVga) {
            $prodCpuTier = $this->getCpuTier($product->cpu);
            $prodVgaTier = $this->getVgaTier($product->vga);

            $meetsCpu = ($prodCpuTier >= $minCpu);
            $meetsVga = ($prodVgaTier >= $minVga);
            $meetsRam = ($product->ram >= $minRam);
            $meetsStorage = ($product->storage >= $minStorage);

            $meetsCritical = ($meetsCpu && $meetsVga);
            $isPerfect = ($meetsCpu && $meetsVga && $meetsRam && $meetsStorage);

            $matches = 0;
            if ($meetsCpu) $matches++;
            if ($meetsVga) $matches++;
            if ($meetsRam) $matches++;
            if ($meetsStorage) $matches++;

            return [
                'product'        => $product,
                'cpu_tier'       => $prodCpuTier,
                'vga_tier'       => $prodVgaTier,
                'meets_cpu'      => $meetsCpu ? 1 : 0,
                'meets_vga'      => $meetsVga ? 1 : 0,
                'meets_ram'      => $meetsRam ? 1 : 0,
                'meets_storage'  => $meetsStorage ? 1 : 0,
                'meets_critical' => $meetsCritical,
                'is_perfect'     => $isPerfect,
                'matches'        => $matches,
            ];
        });

        // 4. Tampilkan semua produk dalam budget & tipe (akan disortir berdasarkan jumlah kecocokan spec)
        // Kita tidak menyembunyikan laptop lain meskipun ada yang perfect, agar user tetap memiliki banyak opsi (terurut dari yang terbaik).
        $filtered = $scored;

        // Urutkan berdasarkan prioritas: Jumlah Matches -> CPU -> GPU -> RAM -> Storage -> Spesifikasi Aktual -> Harga Termurah
        $sorted = $filtered->sort(function ($a, $b) {
            // Prioritas 0: Jumlah kecocokan spesifikasi
            if ($a['matches'] !== $b['matches']) {
                return $b['matches'] <=> $a['matches'];
            }
            // Prioritas 1: Kecocokan CPU
            if ($a['meets_cpu'] !== $b['meets_cpu']) {
                return $b['meets_cpu'] <=> $a['meets_cpu'];
            }
            // Prioritas 2: Kecocokan GPU
            if ($a['meets_vga'] !== $b['meets_vga']) {
                return $b['meets_vga'] <=> $a['meets_vga'];
            }
            // Prioritas 3: Kecocokan RAM
            if ($a['meets_ram'] !== $b['meets_ram']) {
                return $b['meets_ram'] <=> $a['meets_ram'];
            }
            // Prioritas 4: Kecocokan Storage
            if ($a['meets_storage'] !== $b['meets_storage']) {
                return $b['meets_storage'] <=> $a['meets_storage'];
            }
            // Prioritas 5: Tier CPU Lebih Tinggi
            if ($a['cpu_tier'] !== $b['cpu_tier']) {
                return $b['cpu_tier'] <=> $a['cpu_tier'];
            }
            // Prioritas 6: Tier GPU Lebih Tinggi
            if ($a['vga_tier'] !== $b['vga_tier']) {
                return $b['vga_tier'] <=> $a['vga_tier'];
            }
            // Prioritas 7: Kapasitas RAM Lebih Besar
            if ($a['product']->ram !== $b['product']->ram) {
                return $b['product']->ram <=> $a['product']->ram;
            }
            // Prioritas 8: Kapasitas Storage Lebih Besar
            if ($a['product']->storage !== $b['product']->storage) {
                return $b['product']->storage <=> $a['product']->storage;
            }
            // Prioritas 9: Harga Lebih Murah
            return $a['product']->harga <=> $b['product']->harga;
        });

        $recommended = $sorted->map(function ($item) {
            $product = $item['product'];
            $product->is_perfect = $item['is_perfect'];
            $product->meets_critical = $item['meets_critical'] ? true : false;
            $product->meets_cpu = $item['meets_cpu'] ? true : false;
            $product->meets_vga = $item['meets_vga'] ? true : false;
            $product->meets_ram = $item['meets_ram'] ? true : false;
            $product->meets_storage = $item['meets_storage'] ? true : false;
            $product->matches_count = $item['matches'];
            return $product;
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => count($recommended) . ' laptop ditemukan yang cocok dengan kebutuhan Anda',
            'data'   => $recommended,
            'requirement' => [
                'ram_min'     => $minRam,
                'storage_min' => $minStorage,
                'cpu_min'     => $minCpu,
                'vga_min'     => $minVga,
            ],
        ]);
    }

    /**
     * Mengklasifikasikan CPU laptop ke dalam tier:
     * 1: Low (Core i3, Ryzen 3, Celeron, Pentium)
     * 2: Medium (Core i5, Ryzen 5)
     * 3: High (Core i7, Core i9, Ryzen 7, Ryzen 9)
     */
    private function getCpuTier($cpuString)
    {
        $cpu = strtolower($cpuString ?? '');
        if (str_contains($cpu, 'i7') || str_contains($cpu, 'ryzen 7') || str_contains($cpu, 'ryzen 9') || str_contains($cpu, 'i9') || str_contains($cpu, 'm1') || str_contains($cpu, 'm2') || str_contains($cpu, 'm3')) {
            return 3;
        }
        if (str_contains($cpu, 'i5') || str_contains($cpu, 'ryzen 5')) {
            return 2;
        }
        return 1;
    }

    /**
     * Mengklasifikasikan VGA/GPU laptop ke dalam tier:
     * 1: Integrated (Intel UHD, Intel Iris Xe, Radeon Graphics)
     * 2: Entry Dedicated (GTX 1650, GTX 1660, MX350, MX450, GeForce)
     * 3: High Dedicated (RTX 3050, RTX 3060, RTX 4050, RTX 4060, Radeon RX)
     */
    private function getVgaTier($vgaString)
    {
        $vga = strtolower($vgaString ?? '');
        if (str_contains($vga, 'rtx') || (str_contains($vga, 'rx') && !str_contains($vga, 'vega')) || (str_contains($vga, 'radeon rx') && !str_contains($vga, 'vega'))) {
            return 3;
        }
        if (str_contains($vga, 'gtx') || str_contains($vga, 'mx') || str_contains($vga, 'geforce')) {
            return 2;
        }
        return 1;
    }
}
