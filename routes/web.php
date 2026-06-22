<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('user.home');
});

// Auth Views
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::get('/register', function () { return view('auth.register'); });
Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');
Route::get('/reset-password/{token}', function ($token) { return view('auth.reset-password', ['token' => $token]); })->name('password.reset');

// Fitur Views
Route::get('/produk', function () { return view('user.produk.index'); });
Route::get('/keranjang', function () { return view('user.keranjang.index'); });
Route::get('/checkout', function () { return view('user.checkout.index'); });
Route::get('/pesanan', function () { return view('user.pesanan.index'); });
Route::get('/servis', function () { return view('user.servis.index'); });
Route::get('/servis/track', function () { return view('user.servis.track'); });
Route::get('/rekomendasi', function () { return view('user.rekomendasi.index'); });

// Admin Views — Diproteksi AdminWebGuard (validasi token Sanctum + cek role admin di server)
Route::middleware('admin.web')->prefix('admin')->group(function () {
    Route::get('/',         fn ()      => view('admin.dashboard'));
    Route::get('/produk',   fn ()      => view('admin.produk'));
    Route::get('/produk/tambah', fn () => view('admin.produk-form'));
    Route::get('/produk/edit/{id}', fn ($id) => view('admin.produk-form', compact('id')));
    Route::get('/pesanan',  fn ()      => view('admin.pesanan'));
    Route::get('/servis',   fn ()      => view('admin.servis'));
    Route::get('/chat',     fn ()      => view('admin.chat'));
    Route::get('/pelanggan', fn ()     => view('admin.pelanggan'));
    Route::get('/pendapatan', fn ()    => view('admin.pendapatan'));
    Route::get('/ulasan',   fn ()      => view('admin.ulasan'));
    Route::get('/complaint', fn ()     => view('admin.complaint'));
});

// Google Login (Web redirect flow)
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

// Temporary setup route to migrate and seed production/hosting database
Route::get('/admin-setup-secret', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return 'Database successfully migrated and seeded! Try logging in now with: admin@defkancomputer.com / admin123';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Fallback Route for storage files when symbolic link is not working
Route::get('/storage/{path}', function ($path) {
    $filePath = 'public/' . $path;
    if (!Storage::exists($filePath)) {
        abort(404);
    }
    
    $file = Storage::path($filePath);
    $mimeType = Storage::mimeType($filePath);
    
    return response()->file($file, [
        'Content-Type' => $mimeType,
    ]);
})->where('path', '.*');

