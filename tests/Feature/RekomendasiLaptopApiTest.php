<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RekomendasiLaptopApiTest extends TestCase
{
    use RefreshDatabase;

    private $softwareId;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat produk dengan berbagai macam spesifikasi
        // Asus ROG (Gaming, RAM 16, Storage 512, CPU Ryzen 7, VGA RTX 3060)
        Produk::create([
            'nama_produk' => 'Asus ROG Strix',
            'harga' => 20000000,
            'stok' => 5,
            'kategori' => 'Laptop Gaming',
            'ram' => 16,
            'storage' => 512,
            'cpu' => 'Ryzen 7',
            'vga' => 'RTX 3060',
        ]);

        // Acer Aspire (Office/School, RAM 4, Storage 256, CPU Core i3, VGA Intel UHD)
        Produk::create([
            'nama_produk' => 'Acer Aspire 3',
            'harga' => 6000000,
            'stok' => 3,
            'kategori' => 'Laptop Pelajar / Mahasiswa',
            'ram' => 4,
            'storage' => 256,
            'cpu' => 'Core i3',
            'vga' => 'Intel UHD',
        ]);

        // Buat software
        $this->softwareId = DB::table('software')->insertGetId([
            'nama' => 'Adobe Premiere Pro',
            'kategori' => 'Editing',
        ]);

        // Buat requirement untuk Adobe Premiere (RAM min 16, Storage min 512, CPU 3, VGA 3)
        DB::table('requirement_software')->insert([
            'software_id' => $this->softwareId,
            'ram_min' => 16,
            'storage_min' => 512,
            'cpu_min' => '3', // High tier CPU
            'vga_min' => '3', // High tier VGA
        ]);
    }

    public function test_user_can_get_laptop_recommendation_based_on_software()
    {
        $response = $this->postJson('/api/rekomendasi/laptop', [
            'software_ids' => [$this->softwareId],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id_produk',
                        'nama_produk',
                        'harga',
                        'is_perfect',
                    ]
                ]
            ]);

        // Laptop Asus ROG Strix harusnya berada di posisi pertama karena speknya memadai (is_perfect: true)
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('Asus ROG Strix', $data[0]['nama_produk']);
        $this->assertTrue($data[0]['is_perfect']);
    }

    public function test_user_can_filter_recommendation_by_budget()
    {
        $response = $this->postJson('/api/rekomendasi/laptop', [
            'budget_max' => 8000000,
        ]);

        $response->assertStatus(200);

        // Hanya laptop Acer Aspire 3 yang muncul karena harganya di bawah 8 juta
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Acer Aspire 3', $data[0]['nama_produk']);
    }
}
