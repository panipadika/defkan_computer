<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_is_accessible()
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_reset_password_page_is_accessible()
    {
        $response = $this->get('/reset-password/sample-token');
        $response->assertStatus(200);
    }

    public function test_forgot_password_sends_reset_email_for_valid_user()
    {
        Notification::fake();

        /** @var \App\Models\Pengguna $user */
        $user = Pengguna::create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => Pengguna::ROLE_USER,
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Link reset password telah dikirim ke email Anda.',
            ]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_forgot_password_fails_for_invalid_email()
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
                'message' => 'Email tidak terdaftar di sistem kami.',
            ]);
    }

    public function test_reset_password_fails_for_invalid_token()
    {
        /** @var \App\Models\Pengguna $user */
        $user = Pengguna::create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => Pengguna::ROLE_USER,
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Token reset password tidak valid atau sudah kedaluwarsa.',
            ]);
    }

    public function test_reset_password_updates_password_successfully()
    {
        /** @var \App\Models\Pengguna $user */
        $user = Pengguna::create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => Pengguna::ROLE_USER,
        ]);

        /** @var \Illuminate\Auth\Passwords\PasswordBroker $broker */
        $broker = Password::broker();
        $token = $broker->createToken($user);

        $response = $this->postJson('/api/auth/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Password Anda berhasil diperbarui. Silakan login kembali.',
            ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}
