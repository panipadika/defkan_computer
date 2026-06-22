<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Produk;
use App\Models\Keranjang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KeranjangApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Pengguna::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => Pengguna::ROLE_USER,
        ]);

        $this->product = Produk::create([
            'nama_produk' => 'Asus ROG',
            'harga' => 15000000,
            'stok' => 5,
            'kategori' => 'Laptop Gaming',
        ]);
    }

    public function test_user_can_add_product_to_cart()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/keranjang', [
                'id_produk' => $this->product->id_produk,
                'jumlah' => 2,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('keranjang', [
            'pengguna_id' => $this->user->id_pengguna,
            'id_produk' => $this->product->id_produk,
            'jumlah' => 2,
        ]);
    }

    public function test_user_cannot_add_product_to_cart_if_stock_insufficient()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/keranjang', [
                'id_produk' => $this->product->id_produk,
                'jumlah' => 10, // stok cuma 5
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_user_can_view_cart()
    {
        Keranjang::create([
            'pengguna_id' => $this->user->id_pengguna,
            'id_produk' => $this->product->id_produk,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/keranjang');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'total_item' => 1,
                'total_harga' => 15000000,
            ]);
    }

    public function test_user_can_update_cart_item_quantity()
    {
        $cart = Keranjang::create([
            'pengguna_id' => $this->user->id_pengguna,
            'id_produk' => $this->product->id_produk,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/keranjang/' . $cart->id, [
                'jumlah' => 3,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('keranjang', [
            'id' => $cart->id,
            'jumlah' => 3,
        ]);
    }

    public function test_user_cannot_update_cart_item_quantity_beyond_stock()
    {
        $cart = Keranjang::create([
            'pengguna_id' => $this->user->id_pengguna,
            'id_produk' => $this->product->id_produk,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->patchJson('/api/keranjang/' . $cart->id, [
                'jumlah' => 10, // stok cuma 5
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_user_can_delete_cart_item()
    {
        $cart = Keranjang::create([
            'pengguna_id' => $this->user->id_pengguna,
            'id_produk' => $this->product->id_produk,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/keranjang/' . $cart->id);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Item berhasil dihapus dari keranjang',
            ]);

        $this->assertDatabaseMissing('keranjang', [
            'id' => $cart->id,
        ]);
    }

    public function test_user_can_clear_cart()
    {
        Keranjang::create([
            'pengguna_id' => $this->user->id_pengguna,
            'id_produk' => $this->product->id_produk,
            'jumlah' => 1,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/keranjang');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Keranjang belanja berhasil dikosongkan',
            ]);

        $this->assertDatabaseMissing('keranjang', [
            'pengguna_id' => $this->user->id_pengguna,
        ]);
    }
}
