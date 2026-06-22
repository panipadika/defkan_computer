<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully()
    {
        $response = $this->postJson('/api/auth/register', [
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'no_hp' => '081234567890',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Registrasi berhasil',
            ]);

        $this->assertDatabaseHas('pengguna', [
            'email' => 'budi@example.com',
            'nama' => 'Budi Santoso',
            'role' => 'user',
        ]);
    }

    public function test_user_registration_fails_due_to_validation()
    {
        // Email tidak valid dan password konfirmasi tidak cocok
        $response = $this->postJson('/api/auth/register', [
            'nama' => 'Budi Santoso',
            'email' => 'email-tidak-valid',
            'password' => 'password123',
            'password_confirmation' => 'password_beda',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = Pengguna::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role' => Pengguna::ROLE_USER,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Login berhasil',
            ])
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'token_type',
                    'user',
                ],
            ]);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = Pengguna::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role' => Pengguna::ROLE_USER,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'password_salah',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Email atau password salah',
            ]);
    }

    public function test_authenticated_user_can_update_profile()
    {
        $user = Pengguna::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role' => Pengguna::ROLE_USER,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/profil', [
                'nama' => 'Budi Baru',
                'no_hp' => '08999999999',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui',
            ]);

        $this->assertDatabaseHas('pengguna', [
            'id_pengguna' => $user->id_pengguna,
            'nama' => 'Budi Baru',
            'no_hp' => '08999999999',
        ]);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = Pengguna::create([
            'nama' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role' => Pengguna::ROLE_USER,
        ]);

        // Login first to generate token
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'budi@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.access_token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logout berhasil',
            ]);
    }
}
