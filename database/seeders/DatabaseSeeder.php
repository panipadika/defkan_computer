<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        
        $this->call([
            EstimasiServisSeeder::class,
            LayananEkspedisiSeeder::class,
            SoftwareSeeder::class,
            ProdukSeeder::class,
        ]);

        $this->seedMockData();
    }

    private function seedAdmin(): void
    {
        $adminEmail = 'admin@defkancomputer.com';
        if (!Pengguna::where('email', $adminEmail)->exists()) {
            Pengguna::create([
                'nama'      => 'Admin Defkan Computer',
                'email'     => $adminEmail,
                'password'  => Hash::make('admin123'),
                'google_id' => 'admin_manual_' . time(),
                'role'      => Pengguna::ROLE_ADMIN,
                'no_hp'     => '08123456789',
            ]);
            $this->command->info("✅ Admin berhasil dibuat: {$adminEmail}");
        } else {
            $this->command->warn("⚠️  Admin sudah ada, skip.");
        }
    }

    private function seedMockData(): void
    {
        // 1. Create a customer user if not exists
        $userEmail = 'pelanggan@defkancomputer.com';
        $user = Pengguna::where('email', $userEmail)->first();
        if (!$user) {
            $user = Pengguna::create([
                'nama'      => 'Budi Pelanggan',
                'email'     => $userEmail,
                'password'  => Hash::make('user123'),
                'google_id' => 'user_manual_' . time(),
                'role'      => Pengguna::ROLE_USER,
                'no_hp'     => '08987654321',
            ]);
        }

        // Get some products
        $product1 = \App\Models\Produk::first();
        $product2 = \App\Models\Produk::skip(1)->first();
        $product3 = \App\Models\Produk::skip(2)->first();

        if (!$product1 || !$product2) {
            return;
        }

        // 2. Create a completed order
        $pesanan = \App\Models\Pesanan::where('id_pengguna', $user->id_pengguna)->first();
        if (!$pesanan) {
            $pesanan = \App\Models\Pesanan::create([
                'id_pengguna' => $user->id_pengguna,
                'total_harga' => $product1->harga + $product2->harga,
                'status' => 'selesai',
                'alamat_pengiriman' => 'Jl. Merdeka No. 45, Bandung',
                'metode_pembayaran' => 'QRIS',
                'waktu_pembayaran' => now(),
            ]);

            // Add detail_pesanan
            DB::table('detail_pesanan')->insert([
                [
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $product1->id_produk,
                    'jumlah' => 1,
                    'harga' => $product1->harga,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $product2->id_produk,
                    'jumlah' => 1,
                    'harga' => $product2->harga,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // 3. Create a completed service
        $servis = \App\Models\Servis::where('pengguna_id', $user->id_pengguna)->first();
        if (!$servis) {
            $servis = \App\Models\Servis::create([
                'pengguna_id' => $user->id_pengguna,
                'kode_servis' => 'SRV-' . strtoupper(bin2hex(random_bytes(4))),
                'merek_laptop' => 'ASUS',
                'nama_perangkat' => 'ROG Strix G15',
                'jenis_kerusakan' => 'Layar Rusak / Pecah',
                'deskripsi' => 'Layar kedap kedip hitam total setelah jatuh.',
                'keterangan' => 'Ganti panel LCD baru selesai.',
                'estimasi_biaya' => 500000,
                'total_biaya' => 500000,
                'status' => 'diambil',
                'tanggal_masuk' => now()->subDays(5),
                'metode_pembayaran' => 'Virtual Account',
                'status_pembayaran' => 'lunas',
                'waktu_pembayaran' => now()->subDays(1),
            ]);
        }

        // 4. Create some default reviews
        if (\App\Models\Ulasan::count() === 0) {
            // Review for Product 1 (from our order)
            \App\Models\Ulasan::create([
                'pengguna_id' => $user->id_pengguna,
                'tipe' => 'produk',
                'id_pesanan' => $pesanan->id_pesanan,
                'id_produk' => $product1->id_produk,
                'rating' => 5,
                'komentar' => 'Sangat mantap laptopnya! Kondisinya masih mulus dan berfungsi sangat baik.',
                'is_visible' => true,
            ]);

            // Review for Product 3 (general review from another user)
            if ($product3) {
                // Create another user
                $anotherUser = Pengguna::create([
                    'nama'      => 'Siti Rahma',
                    'email'     => 'siti@gmail.com',
                    'password'  => Hash::make('user123'),
                    'google_id' => 'user_manual_siti',
                    'role'      => Pengguna::ROLE_USER,
                    'no_hp'     => '08122334455',
                ]);

                // Create order for Siti
                $sitiPesanan = \App\Models\Pesanan::create([
                    'id_pengguna' => $anotherUser->id_pengguna,
                    'total_harga' => $product3->harga,
                    'status' => 'selesai',
                ]);

                DB::table('detail_pesanan')->insert([
                    'id_pesanan' => $sitiPesanan->id_pesanan,
                    'id_produk' => $product3->id_produk,
                    'jumlah' => 1,
                    'harga' => $product3->harga,
                ]);

                \App\Models\Ulasan::create([
                    'pengguna_id' => $anotherUser->id_pengguna,
                    'tipe' => 'produk',
                    'id_pesanan' => $sitiPesanan->id_pesanan,
                    'id_produk' => $product3->id_produk,
                    'rating' => 4,
                    'komentar' => 'Pengiriman cepat, produk original dan admin ramah. Terima kasih!',
                    'is_visible' => true,
                ]);
            }
        }

        // 5. Create some default complaints
        if (\App\Models\Complaint::count() === 0) {
            \App\Models\Complaint::create([
                'pengguna_id' => $user->id_pengguna,
                'tipe' => 'pesanan',
                'id_referensi' => $pesanan->id_pesanan,
                'judul' => 'Charger laptop tidak berfungsi',
                'deskripsi' => 'Halo min, charger bawaan laptop Asus Vivobook yang saya beli tidak mau mengisi daya sama sekali saat dicolok. Mohon bantuannya.',
                'status' => 'menunggu',
            ]);
        }
        $this->command->info("✅ Mock data (ulasan & complaint) berhasil ditambahkan.");
    }
}
