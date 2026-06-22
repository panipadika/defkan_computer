<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya seed jika tabel produk masih kosong (agar tidak menimpa data produk production)
        if (DB::table('produk')->count() > 0) {
            $this->command->warn("⚠️  Tabel produk sudah berisi data, skip ProdukSeeder.");
            return;
        }

        $produkList = [
            // Ultrabooks
            [
                'nama_produk' => 'ASUS VivoBook 14 - Core i5 Gen10',
                'harga' => 4500000,
                'stok' => 3,
                'kategori' => 'Ultrabook',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'Intel Iris Xe',
                'cpu' => 'Core i5-1035G1',
                'merek' => 'ASUS',
                'foto' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop slim tipis ringan, cocok untuk pelajar & mahasiswa. Layar 14" FHD, baterai tahan lama, kondisi mulus 90%.'
            ],
            [
                'nama_produk' => 'Apple MacBook Air M1 2020',
                'harga' => 9500000,
                'stok' => 2,
                'kategori' => 'Ultrabook',
                'ram' => 8,
                'storage' => 256,
                'vga' => 'Apple M1 7-Core GPU',
                'cpu' => 'Apple M1',
                'merek' => 'Apple',
                'foto' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'MacBook Air dengan chip Apple M1 yang sangat efisien dan bertenaga. Layar Retina, tanpa kipas (silent), kondisi fisik sangat mulus.'
            ],
            [
                'nama_produk' => 'Xiaomi Mi Notebook Air - Core i7',
                'harga' => 7200000,
                'stok' => 1,
                'kategori' => 'Ultrabook',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce MX450 2GB',
                'cpu' => 'Core i7-11370H',
                'merek' => 'Xiaomi',
                'foto' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Ultrabook premium dengan bodi full metal, layar 2.5K super jernih. Cocok untuk produktivitas tingkat tinggi.'
            ],
            [
                'nama_produk' => 'ASUS ZenBook 13 OLED - Ryzen 7',
                'harga' => 8900000,
                'stok' => 2,
                'kategori' => 'Ultrabook',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'Radeon Graphics',
                'cpu' => 'Ryzen 7 5700U',
                'merek' => 'ASUS',
                'foto' => 'https://images.unsplash.com/photo-1496181130204-755241524eab?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'ZenBook ultra tipis dengan layar ASUS OLED yang luar biasa kontras dan akurasi warnanya. Ringan dibawa bepergian.'
            ],
            [
                'nama_produk' => 'Lenovo Yoga Slim 7 Carbon - Core i7',
                'harga' => 10500000,
                'stok' => 1,
                'kategori' => 'Ultrabook',
                'ram' => 16,
                'storage' => 1024,
                'vga' => 'Intel Iris Xe',
                'cpu' => 'Core i7-1165G7',
                'merek' => 'Lenovo',
                'foto' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop premium super ringan dengan bahan carbon fiber. Layar 2.8K OLED, SSD super lega 1TB, kondisi like new.'
            ],
            [
                'nama_produk' => 'Acer Swift 3 Infinity 4 - Ryzen 5',
                'harga' => 4800000,
                'stok' => 2,
                'kategori' => 'Ultrabook',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'AMD Radeon RX Vega 6',
                'cpu' => 'Ryzen 5 4500U',
                'merek' => 'Acer',
                'foto' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop tipis Acer dengan performa Ryzen 5 yang gesit. Desain elegan berbahan aluminium alloy.'
            ],

            // Laptop Gaming
            [
                'nama_produk' => 'ASUS ROG Strix G15 - RTX 3060',
                'harga' => 9800000,
                'stok' => 1,
                'kategori' => 'Laptop Gaming',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce RTX 3060 6GB',
                'cpu' => 'Ryzen 7 5800H',
                'merek' => 'ASUS',
                'foto' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Gaming beast! RTX 3060 dengan Ryzen 7, layar 144Hz refresh rate. Lancar untuk game AAA berat dan rendering 3D.'
            ],
            [
                'nama_produk' => 'Acer Nitro 5 - GTX 1650 Ti',
                'harga' => 6500000,
                'stok' => 2,
                'kategori' => 'Laptop Gaming',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce GTX 1650 Ti 4GB',
                'cpu' => 'Core i5-10300H',
                'merek' => 'Acer',
                'foto' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop gaming budget dengan sistem pendingin ganda Nitro Sense. Bisa main game AAA di setting medium-high.'
            ],
            [
                'nama_produk' => 'Lenovo Legion 5 - RTX 3070',
                'harga' => 13500000,
                'stok' => 1,
                'kategori' => 'Laptop Gaming',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce RTX 3070 8GB',
                'cpu' => 'Ryzen 7 5800H',
                'merek' => 'Lenovo',
                'foto' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop gaming papan atas dengan build quality solid and pendingin Legion Coldfront 3.0. TGP tinggi untuk performa maksimal.'
            ],
            [
                'nama_produk' => 'MSI GF63 Thin - GTX 1650 Max-Q',
                'harga' => 6200000,
                'stok' => 2,
                'kategori' => 'Laptop Gaming',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce GTX 1650 Max-Q 4GB',
                'cpu' => 'Core i5-10500H',
                'merek' => 'MSI',
                'foto' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop gaming yang sangat tipis dan ringan di kelasnya. Desain brushed metal yang elegan, keyboard backlit merah.'
            ],
            [
                'nama_produk' => 'ASUS TUF Gaming F15 - RTX 3050',
                'harga' => 8500000,
                'stok' => 3,
                'kategori' => 'Laptop Gaming',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce RTX 3050 4GB',
                'cpu' => 'Core i5-11400H',
                'merek' => 'ASUS',
                'foto' => 'https://images.unsplash.com/photo-1624705002806-5d72df19c3ad?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop gaming dengan ketahanan militer (MIL-STD-810H). Desain tangguh, siap untuk gaming esports sehari-hari.'
            ],
            [
                'nama_produk' => 'HP Victus 16 - RTX 4050',
                'harga' => 11200000,
                'stok' => 2,
                'kategori' => 'Laptop Gaming',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce RTX 4050 6GB',
                'cpu' => 'Ryzen 5 5600H',
                'merek' => 'HP',
                'foto' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop gaming generasi terbaru dengan layar lebar 16.1 inci FHD 144Hz. Didukung RTX 4050 berkinerja tinggi.'
            ],
            [
                'nama_produk' => 'Gigabyte G5 - RTX 4060',
                'harga' => 12800000,
                'stok' => 1,
                'kategori' => 'Laptop Gaming',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce RTX 4060 8GB',
                'cpu' => 'Core i5-12500H',
                'merek' => 'Gigabyte',
                'foto' => 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Performa gaming terkini dengan RTX 4060 dan processor Intel Gen 12. Ideal untuk ray tracing dan DLSS 3.'
            ],

            // Laptop Kantor
            [
                'nama_produk' => 'Lenovo IdeaPad 3 - Ryzen 5 5500U',
                'harga' => 5200000,
                'stok' => 2,
                'kategori' => 'Laptop Kantor',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'Radeon Graphics',
                'cpu' => 'Ryzen 5 5500U',
                'merek' => 'Lenovo',
                'foto' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Performa kencang 6-Core Ryzen 5 untuk multitasking kantor, programming, dan sekolah. Kondisi mulus, baterai normal.'
            ],
            [
                'nama_produk' => 'HP Pavilion 14 - Core i3 Gen11',
                'harga' => 3200000,
                'stok' => 4,
                'kategori' => 'Laptop Kantor',
                'ram' => 4,
                'storage' => 256,
                'vga' => 'Intel UHD Graphics',
                'cpu' => 'Core i3-1125G4',
                'merek' => 'HP',
                'foto' => 'https://images.unsplash.com/photo-1589561253898-768105ca91a8?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop entry-level dengan build quality handal khas HP. Cocok untuk Zoom meeting, mengetik tugas, dan browsing.'
            ],
            [
                'nama_produk' => 'MSI Modern 14 - Ryzen 5 7530U',
                'harga' => 5900000,
                'stok' => 2,
                'kategori' => 'Laptop Kantor',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'AMD Radeon Graphics',
                'cpu' => 'Ryzen 5 7530U',
                'merek' => 'MSI',
                'foto' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop kerja super tipis dengan layar bersertifikasi IPS level. Nyaman digunakan berjam-jam untuk ketik dokumen.'
            ],
            [
                'nama_produk' => 'Dell Vostro 3400 - Core i3 Gen11',
                'harga' => 3500000,
                'stok' => 4,
                'kategori' => 'Laptop Kantor',
                'ram' => 4,
                'storage' => 256,
                'vga' => 'Intel UHD Graphics',
                'cpu' => 'Core i3-1115G4',
                'merek' => 'Dell',
                'foto' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Durabilitas tinggi Dell Vostro, didesain khusus untuk keandalan bisnis kantoran dan administrasi.'
            ],
            [
                'nama_produk' => 'Dell Inspiron 15 - Ryzen 7 5700U',
                'harga' => 6800000,
                'stok' => 2,
                'kategori' => 'Laptop Kantor',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'AMD Radeon Graphics',
                'cpu' => 'Ryzen 7 5700U',
                'merek' => 'Dell',
                'foto' => 'https://images.unsplash.com/photo-1504707748692-419802cf939d?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop dengan RAM 16GB bawaan dan processor 8-Core Ryzen 7. Sangat mulus untuk membuka puluhan tab browser dan Excel berat.'
            ],

            // Creator & Premium Laptops
            [
                'nama_produk' => 'Apple MacBook Pro M2 2022',
                'harga' => 14500000,
                'stok' => 1,
                'kategori' => 'Laptop Kreator',
                'ram' => 8,
                'storage' => 512,
                'vga' => 'Apple M2 10-Core GPU',
                'cpu' => 'Apple M2',
                'merek' => 'Apple',
                'foto' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'MacBook Pro bertenaga M2 dengan sistem pendingin aktif (kipas). Sangat andal untuk edit video 4K dan rendering audio.'
            ],
            [
                'nama_produk' => 'ASUS VivoBook Pro 15 - RTX 3050',
                'harga' => 7900000,
                'stok' => 2,
                'kategori' => 'Laptop Kreator',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'NVIDIA GeForce RTX 3050 4GB',
                'cpu' => 'Ryzen 5 5600H',
                'merek' => 'ASUS',
                'foto' => 'https://images.unsplash.com/photo-1496181130204-755241524eab?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop creator berbiaya terjangkau. Layar luas 15" IPS, ditenagai kartu grafis RTX untuk GPU-accelerated editing.'
            ],
            [
                'nama_produk' => 'MSI Prestige 14 Evo - Core i7 Gen12',
                'harga' => 9900000,
                'stok' => 1,
                'kategori' => 'Laptop Kreator',
                'ram' => 16,
                'storage' => 1024,
                'vga' => 'Intel Iris Xe Graphics',
                'cpu' => 'Core i7-1280P',
                'merek' => 'MSI',
                'foto' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop ultra premium bersertifikasi Intel Evo. Performa kencang Core i7 generasi ke-12, SSD lega 1TB NVMe PCIe Gen4.'
            ],

            // Budget Laptops
            [
                'nama_produk' => 'HP 14s - Celeron N4500',
                'harga' => 2600000,
                'stok' => 3,
                'kategori' => 'Laptop Harian',
                'ram' => 4,
                'storage' => 256,
                'vga' => 'Intel UHD Graphics',
                'cpu' => 'Celeron N4500',
                'merek' => 'HP',
                'foto' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop murah berkualitas tinggi untuk sekolah, belajar online, input kasir toko, dan administrasi ringan.'
            ],
            [
                'nama_produk' => 'Lenovo D330 2-in-1 - Celeron N4020',
                'harga' => 2300000,
                'stok' => 4,
                'kategori' => 'Laptop Harian',
                'ram' => 4,
                'storage' => 128,
                'vga' => 'Intel HD Graphics',
                'cpu' => 'Celeron N4020',
                'merek' => 'Lenovo',
                'foto' => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop hybrid serbaguna yang layarnya bisa dilepas menjadi tablet. Sangat fleksibel untuk presentasi.'
            ],
            [
                'nama_produk' => 'Axioo MyBook 14F - Intel Dual Core',
                'harga' => 2100000,
                'stok' => 3,
                'kategori' => 'Laptop Harian',
                'ram' => 4,
                'storage' => 128,
                'vga' => 'Intel HD Graphics',
                'cpu' => 'Intel Celeron N4020',
                'merek' => 'Axioo',
                'foto' => 'https://images.unsplash.com/photo-1580522151917-c5e4db526e94?auto=format&fit=crop&w=600&q=80',
                'deskripsi' => 'Laptop buatan lokal dengan layar HD 14 inci. Termurah dan pas untuk pengetikan dokumen dasar.'
            ],
        ];

        foreach ($produkList as $produk) {
            DB::table('produk')->insert(array_merge($produk, ['created_at' => now(), 'updated_at' => now()]));
        }
        $this->command->info("✅ " . count($produkList) . " Produk Contoh berhasil ditambahkan.");    }
}
