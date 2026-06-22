<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\Keranjang;
use App\Models\Pesanan;
use App\Models\LayananEkspedisi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PesananApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;
    private $product;
    private $ekspedisi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Pengguna::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => Pengguna::ROLE_USER,
        ]);

        $this->admin = Pengguna::create([
            'nama' => 'Admin Defkan',
            'email' => 'admin@defkan.com',
            'role' => Pengguna::ROLE_ADMIN,
        ]);

        $this->product = Produk::create([
            'nama_produk' => 'Asus ROG',
            'harga' => 15000000,
            'stok' => 5,
            'kategori' => 'Laptop Gaming',
        ]);

        $this->ekspedisi = LayananEkspedisi::create([
            'nama_layanan' => 'JNE Reguler',
            'kode_layanan' => 'JNE_REG',
            'biaya_ongkir' => 20000,
            'is_aktif' => true,
        ]);
    }

    public function test_user_can_checkout_directly()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/pesanan', [
                'id_layanan_ekspedisi' => $this->ekspedisi->id_layanan_ekspedisi,
                'alamat_pengiriman' => 'Jl. Merdeka No. 10',
                'dari_keranjang' => false,
                'items' => [
                    [
                        'id_produk' => $this->product->id_produk,
                        'jumlah' => 1,
                    ]
                ]
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.',
            ]);

        $this->assertDatabaseHas('pesanan', [
            'id_pengguna' => $this->user->id_pengguna,
            'alamat_pengiriman' => 'Jl. Merdeka No. 10',
            'status' => 'pending',
        ]);

        // Stok produk harus berkurang
        $this->product->refresh();
        $this->assertEquals(4, $this->product->stok);
    }

    public function test_user_can_checkout_from_cart()
    {
        // Masukkan ke keranjang dulu
        $cart = Keranjang::create([
            'pengguna_id' => $this->user->id_pengguna,
            'id_produk' => $this->product->id_produk,
            'jumlah' => 2,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/pesanan', [
                'id_layanan_ekspedisi' => $this->ekspedisi->id_layanan_ekspedisi,
                'alamat_pengiriman' => 'Jl. Merdeka No. 10',
                'dari_keranjang' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        // Keranjang belanja harus kosong setelah checkout
        $this->assertDatabaseMissing('keranjang', [
            'id' => $cart->id,
        ]);

        // Stok produk harus berkurang
        $this->product->refresh();
        $this->assertEquals(3, $this->product->stok);
    }

    public function test_user_can_upload_payment_proof()
    {
        Storage::fake('public');

        $pesanan = Pesanan::create([
            'id_pengguna' => $this->user->id_pengguna,
            'id_layanan_ekspedisi' => $this->ekspedisi->id_layanan_ekspedisi,
            'total_harga' => 15020000,
            'status' => 'pending',
            'alamat_pengiriman' => 'Jl. Merdeka No. 10',
        ]);

        $bukti = UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/pesanan/{$pesanan->id_pesanan}/bayar", [
                'metode_pembayaran' => 'SeaBank',
                'bukti_pembayaran' => $bukti,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diunggah. Pesanan sedang diproses.',
            ]);

        $pesanan->refresh();
        $this->assertEquals('SeaBank', $pesanan->metode_pembayaran);
        $this->assertNotNull($pesanan->bukti_pembayaran);
        Storage::disk('public')->assertExists($pesanan->bukti_pembayaran);
    }

    public function test_admin_can_view_all_orders()
    {
        Pesanan::create([
            'id_pengguna' => $this->user->id_pengguna,
            'total_harga' => 15000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/pesanan');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id_pesanan',
                            'total_harga',
                            'status',
                        ]
                    ]
                ]
            ]);
    }

    public function test_admin_can_update_order_status()
    {
        $pesanan = Pesanan::create([
            'id_pengguna' => $this->user->id_pengguna,
            'total_harga' => 15000000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/pesanan/{$pesanan->id_pesanan}/status", [
                'status' => 'diproses',
                'keterangan' => 'Pembayaran valid',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $pesanan->refresh();
        $this->assertEquals('diproses', $pesanan->status);
    }
}
