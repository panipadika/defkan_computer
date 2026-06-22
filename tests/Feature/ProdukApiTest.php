<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProdukApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_anyone_can_list_products()
    {
        Produk::create([
            'nama_produk' => 'Asus ROG',
            'harga' => 15000000,
            'stok' => 10,
            'kategori' => 'Laptop Gaming',
        ]);

        $response = $this->getJson('/api/produk');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id_produk',
                            'nama_produk',
                            'harga',
                            'stok',
                        ]
                    ]
                ]
            ]);
    }

    public function test_anyone_can_view_single_product()
    {
        $product = Produk::create([
            'nama_produk' => 'Acer Swift 3',
            'harga' => 9000000,
            'stok' => 5,
            'kategori' => 'Laptop Tipis & Ringan',
        ]);

        $response = $this->getJson('/api/produk/' . $product->id_produk);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'nama_produk' => 'Acer Swift 3',
                ]
            ]);
    }

    public function test_admin_can_create_product()
    {
        Storage::fake('public');

        $admin = Pengguna::create([
            'nama' => 'Admin Defkan',
            'email' => 'admin@defkan.com',
            'role' => Pengguna::ROLE_ADMIN,
        ]);

        $foto = UploadedFile::fake()->create('laptop.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/produk', [
                'nama_produk' => 'Lenovo Legion 5',
                'harga' => 18000000,
                'stok' => 7,
                'kategori' => 'Laptop Gaming',
                'merek' => 'Lenovo',
                'ram' => 16,
                'storage' => 512,
                'vga' => 'RTX 3060',
                'cpu' => 'Ryzen 7',
                'foto' => $foto,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan',
            ]);

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Lenovo Legion 5',
            'merek' => 'Lenovo',
            'ram' => 16,
        ]);

        $produk = Produk::where('nama_produk', 'Lenovo Legion 5')->first();
        Storage::disk('public')->assertExists($produk->foto);
    }

    public function test_non_admin_cannot_create_product()
    {
        $user = Pengguna::create([
            'nama' => 'User Biasa',
            'email' => 'user@defkan.com',
            'role' => Pengguna::ROLE_USER,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/produk', [
                'nama_produk' => 'Lenovo Legion 5',
                'harga' => 18000000,
                'stok' => 7,
                'kategori' => 'Laptop Gaming',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Akses ditolak. Fitur ini hanya untuk Admin.',
            ]);
    }

    public function test_admin_can_update_product()
    {
        $admin = Pengguna::create([
            'nama' => 'Admin Defkan',
            'email' => 'admin@defkan.com',
            'role' => Pengguna::ROLE_ADMIN,
        ]);

        $product = Produk::create([
            'nama_produk' => 'HP Pavilion',
            'harga' => 12000000,
            'stok' => 3,
            'kategori' => 'Laptop Kerja',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/produk/' . $product->id_produk, [
                'nama_produk' => 'HP Pavilion Edit',
                'harga' => 13000000,
                'stok' => 5,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Produk berhasil diperbarui',
            ]);

        $this->assertDatabaseHas('produk', [
            'id_produk' => $product->id_produk,
            'nama_produk' => 'HP Pavilion Edit',
            'harga' => 13000000,
            'stok' => 5,
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $admin = Pengguna::create([
            'nama' => 'Admin Defkan',
            'email' => 'admin@defkan.com',
            'role' => Pengguna::ROLE_ADMIN,
        ]);

        $product = Produk::create([
            'nama_produk' => 'HP Pavilion',
            'harga' => 12000000,
            'stok' => 3,
            'kategori' => 'Laptop Kerja',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/produk/' . $product->id_produk);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Produk berhasil dihapus',
            ]);

        $this->assertSoftDeleted('produk', [
            'id_produk' => $product->id_produk,
        ]);
    }
}
