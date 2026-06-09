<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LayananEkspedisi;

class LayananEkspedisiSeeder extends Seeder
{
    public function run(): void
    {
        $list = [
            ['nama_layanan' => 'JNE Reguler',            'kode_layanan' => 'JNE-REG',  'biaya_ongkir' => 15000, 'is_aktif' => 1],
            ['nama_layanan' => 'JNE YES (1 Hari)',        'kode_layanan' => 'JNE-YES',  'biaya_ongkir' => 35000, 'is_aktif' => 1],
            ['nama_layanan' => 'J&T Express',             'kode_layanan' => 'JNT',      'biaya_ongkir' => 12000, 'is_aktif' => 1],
            ['nama_layanan' => 'SiCepat Halu',            'kode_layanan' => 'SICEPAT',  'biaya_ongkir' => 10000, 'is_aktif' => 1],
            ['nama_layanan' => 'AnterAja',                'kode_layanan' => 'ANTERAJA', 'biaya_ongkir' => 11000, 'is_aktif' => 1],
            ['nama_layanan' => 'Ambil di Toko (Gratis)',  'kode_layanan' => 'PICKUP',   'biaya_ongkir' => 0,     'is_aktif' => 1],
        ];

        $inserted = 0;
        foreach ($list as $item) {
            if (!LayananEkspedisi::where('kode_layanan', $item['kode_layanan'])->exists()) {
                LayananEkspedisi::create($item);
                $inserted++;
            }
        }
        $this->command->info("✅ {$inserted} Layanan Ekspedisi berhasil ditambahkan.");
        }
}
