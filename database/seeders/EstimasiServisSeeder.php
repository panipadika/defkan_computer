<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstimasiServis;

class EstimasiServisSeeder extends Seeder
{
    public function run(): void
    {
        $list = [
            ['jenis_kerusakan' => 'Layar Rusak / Pecah',              'harga_estimasi' => 500000, 'estimasi_durasi' => '3-5 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Keyboard Rusak',                    'harga_estimasi' => 250000, 'estimasi_durasi' => '1-2 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Baterai Tidak Mengisi / Boros',     'harga_estimasi' => 350000, 'estimasi_durasi' => '1-3 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Laptop Tidak Menyala',              'harga_estimasi' => 300000, 'estimasi_durasi' => '2-4 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Overheating / Panas Berlebih',      'harga_estimasi' => 150000, 'estimasi_durasi' => '1-2 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Harddisk / SSD Rusak',              'harga_estimasi' => 200000, 'estimasi_durasi' => '1-3 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'RAM Bermasalah',                    'harga_estimasi' => 100000, 'estimasi_durasi' => '1 Hari Kerja',   'is_aktif' => 1],
            ['jenis_kerusakan' => 'Engsel Laptop Rusak',               'harga_estimasi' => 300000, 'estimasi_durasi' => '2-3 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Port Charger Rusak',                'harga_estimasi' => 200000, 'estimasi_durasi' => '1-2 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Instal Ulang / Upgrade Windows',    'harga_estimasi' => 100000, 'estimasi_durasi' => '1 Hari Kerja',   'is_aktif' => 1],
            ['jenis_kerusakan' => 'Layar Berkedip / Bergaris',         'harga_estimasi' => 400000, 'estimasi_durasi' => '2-4 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Speaker Tidak Berbunyi',            'harga_estimasi' => 150000, 'estimasi_durasi' => '1-2 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'WiFi / Bluetooth Tidak Terdeteksi', 'harga_estimasi' => 200000, 'estimasi_durasi' => '1-2 Hari Kerja', 'is_aktif' => 1],
            ['jenis_kerusakan' => 'Touchpad Tidak Berfungsi',          'harga_estimasi' => 180000, 'estimasi_durasi' => '1-2 Hari Kerja', 'is_aktif' => 1],
        ];

        $inserted = 0;
        foreach ($list as $item) {
            if (!EstimasiServis::where('jenis_kerusakan', $item['jenis_kerusakan'])->exists()) {
                EstimasiServis::create($item);
                $inserted++;
            }
        }
        $this->command->info("✅ {$inserted} Data Estimasi Servis berhasil ditambahkan.");
        }
}
