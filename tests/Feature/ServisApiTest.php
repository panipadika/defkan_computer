<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Models\Servis;
use App\Models\EstimasiServis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServisApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $admin;
    private $estimasi;

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

        $this->estimasi = EstimasiServis::create([
            'jenis_kerusakan' => 'Ganti Keyboard',
            'harga_estimasi' => 350000,
            'estimasi_durasi' => '1-2 Hari Kerja',
            'is_aktif' => true,
        ]);
    }

    public function test_user_can_submit_service_request()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/servis', [
                'jenis_kerusakan' => 'Ganti Keyboard',
                'deskripsi' => 'Tombol A dan S tidak berfungsi',
                'merek_laptop' => 'Asus VivoBook',
                'no_wa' => '081234567890',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('servis', [
            'pengguna_id' => $this->user->id_pengguna,
            'jenis_kerusakan' => 'Ganti Keyboard',
            'merek_laptop' => 'Asus VivoBook',
            'status' => 'menunggu',
        ]);
    }

    public function test_anyone_can_track_service_via_code()
    {
        $servis = Servis::create([
            'pengguna_id' => $this->user->id_pengguna,
            'kode_servis' => 'SRV-TEST12345',
            'merek_laptop' => 'Acer Swift',
            'jenis_kerusakan' => 'Install Ulang OS',
            'status' => 'diperiksa',
            'no_wa' => '081234567890',
        ]);

        $response = $this->getJson('/api/servis/track/SRV-TEST12345');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'kode_servis' => 'SRV-TEST12345',
                    'status' => 'diperiksa',
                ]
            ]);
    }

    public function test_user_can_upload_service_payment_proof()
    {
        Storage::fake('public');

        $servis = Servis::create([
            'pengguna_id' => $this->user->id_pengguna,
            'kode_servis' => 'SRV-PAY123',
            'merek_laptop' => 'Acer Swift',
            'jenis_kerusakan' => 'Ganti Keyboard',
            'status' => 'selesai',
            'total_biaya' => 350000,
            'no_wa' => '081234567890',
        ]);

        $bukti = UploadedFile::fake()->create('bukti.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/servis/{$servis->id}/bayar", [
                'metode_pembayaran' => 'DANA',
                'bukti_pembayaran' => $bukti,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Bukti pembayaran servis berhasil diunggah.',
            ]);

        $servis->refresh();
        $this->assertEquals('DANA', $servis->metode_pembayaran);
        $this->assertEquals('dibayar', $servis->status_pembayaran);
        $this->assertNotNull($servis->bukti_pembayaran);
        Storage::disk('public')->assertExists($servis->bukti_pembayaran);
    }

    public function test_admin_can_view_all_services()
    {
        Servis::create([
            'pengguna_id' => $this->user->id_pengguna,
            'kode_servis' => 'SRV-TEST1',
            'merek_laptop' => 'Asus',
            'jenis_kerusakan' => 'Keyboard',
            'no_wa' => '081234567890',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/servis');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);
    }

    public function test_admin_can_update_service_status()
    {
        $servis = Servis::create([
            'pengguna_id' => $this->user->id_pengguna,
            'kode_servis' => 'SRV-TEST2',
            'merek_laptop' => 'Asus',
            'jenis_kerusakan' => 'Keyboard',
            'no_wa' => '081234567890',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/servis/{$servis->id}/status", [
                'status' => 'dikerjakan',
                'keterangan' => 'Sedang diganti keyboard baru',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $servis->refresh();
        $this->assertEquals('dikerjakan', $servis->status);
        $this->assertEquals('Sedang diganti keyboard baru', $servis->keterangan);
    }
}
