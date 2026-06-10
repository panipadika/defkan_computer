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

// Admin Views (akses dikontrol oleh frontend JS cek role admin)
Route::get('/admin', function () { return view('admin.dashboard'); });
Route::get('/admin/produk', function () { return view('admin.produk'); });
Route::get('/admin/produk/tambah', function () { return view('admin.produk-form'); });
Route::get('/admin/produk/edit/{id}', function ($id) { return view('admin.produk-form', compact('id')); });
Route::get('/admin/pesanan', function () { return view('admin.pesanan'); });
Route::get('/admin/servis', function () { return view('admin.servis'); });
Route::get('/admin/chat', function () { return view('admin.chat'); });
Route::get('/admin/pelanggan', function () { return view('admin.pelanggan'); });
Route::get('/admin/pendapatan', function () { return view('admin.pendapatan'); });

// Google Login (Web redirect flow)
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

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

