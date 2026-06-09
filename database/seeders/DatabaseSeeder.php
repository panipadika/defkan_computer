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
}
