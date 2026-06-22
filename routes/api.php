<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ServisController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\ChatApiController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UlasanAdminController;
use App\Http\Controllers\Admin\ComplaintAdminController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\RekomendasiLaptopController;
use App\Http\Controllers\User\UlasanController;
use App\Http\Controllers\User\ComplaintController;
use App\Http\Controllers\Shared\EkspedisiController;
use App\Http\Controllers\Shared\SoftwareController;

// Endpoint auth dengan rate limiting ketat untuk mencegah brute force & spam
Route::post('/auth/register', [AuthController::class, 'register'])
    ->middleware('throttle:10,1');                                   // maks 10x per menit per IP

Route::post('/auth/login', [AuthController::class, 'login'])
    ->middleware('throttle:login');                                  // maks 5x per menit per IP

Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:forgot-password');                        // maks 3x per 10 menit per IP

Route::post('/auth/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:5,10');                                   // maks 5x per 10 menit per IP

Route::get('/produk/export', [ProdukController::class, 'export'])->middleware(['auth:sanctum', 'admin']);
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);

Route::get('/ekspedisi', [EkspedisiController::class, 'index']);
Route::get('/software', [SoftwareController::class, 'index']);
Route::post('/rekomendasi/laptop', [RekomendasiLaptopController::class, 'recommend']);

Route::get('/servis/estimasi', [ServisController::class, 'estimasi']);
Route::get('/servis/track/{kode_servis}', [ServisController::class, 'tracking']);

// Endpoint publik ulasan beranda (tidak perlu login)
Route::get('/ulasan/publik', [UlasanController::class, 'publik']);
Route::get('/produk/{id}/ulasan', [UlasanController::class, 'produkReviews']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/servis', [ServisController::class, 'store']);
    Route::get('/servis-saya', [ServisController::class, 'myServices']);
    Route::post('/servis/{id}/bayar', [ServisController::class, 'uploadBuktiPembayaran']);

    Route::get('/me', [GoogleAuthController::class, 'me']);
    Route::put('/profil', [AuthController::class, 'updateProfile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/chat/rooms/{userId}', [ChatApiController::class, 'listRooms']);
    Route::get('/chat/messages/{roomId}', [ChatApiController::class, 'getMessages']);
    Route::post('/chat/messages', [ChatApiController::class, 'sendMessage']);
    Route::post('/chat/rooms', [ChatApiController::class, 'createRoom']);
    Route::patch('/chat/messages/{roomId}/read', [ChatApiController::class, 'markAsRead']);

    Route::get('/keranjang', [KeranjangController::class, 'index']);
    Route::post('/keranjang', [KeranjangController::class, 'store']);
    Route::patch('/keranjang/{id}', [KeranjangController::class, 'update']);
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy']);
    Route::delete('/keranjang', [KeranjangController::class, 'clear']);

    Route::get('/pesanan-saya', [PesananController::class, 'myOrders']);
    Route::get('/pesanan/export', [PesananController::class, 'export'])->middleware('admin');
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);
    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::post('/pesanan/{id}/bayar', [PesananController::class, 'uploadBuktiPembayaran']);

    // ── Ulasan (Review) ──────────────────────────────────────────────────────
    Route::post('/ulasan', [UlasanController::class, 'store']);
    Route::get('/ulasan-saya', [UlasanController::class, 'myReviews']);

    // ── Complaint ────────────────────────────────────────────────────────────
    Route::post('/complaint', [ComplaintController::class, 'store']);
    Route::get('/complaint-saya', [ComplaintController::class, 'myComplaints']);
    Route::get('/complaint/{id}', [ComplaintController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::post('/produk', [ProdukController::class, 'storeWithUpload']);
        Route::put('/produk/{id}', [ProdukController::class, 'update']);
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);

        Route::get('/servis/export', [ServisController::class, 'export']);
        Route::get('/servis', [ServisController::class, 'index']);
        Route::patch('/servis/{id}/status', [ServisController::class, 'updateStatus']);

        Route::get('/pesanan', [PesananController::class, 'index']);
        Route::patch('/pesanan/{id}/status', [PesananController::class, 'updateStatus']);

        Route::get('/admin/stats', [AdminController::class, 'stats']);
        Route::get('/admin/dashboard-stats', [AdminController::class, 'dashboardStats']);
        Route::get('/admin/produk-stats', [AdminController::class, 'produkStats']);
        Route::get('/admin/pesanan-terbaru', [AdminController::class, 'pesananTerbaru']);
        Route::get('/admin/servis-terbaru', [AdminController::class, 'servisTerbaru']);

        Route::get('/admin/pelanggan', [AdminController::class, 'pelanggan']);
        Route::get('/admin/pendapatan', [AdminController::class, 'pendapatan']);
        Route::get('/admin/pendapatan/export', [AdminController::class, 'pendapatanExport']);
        Route::get('/admin/sidebar-counts', [AdminController::class, 'sidebarCounts']);

        Route::get('/admin/chat/rooms', [ChatApiController::class, 'adminListRooms']);
        Route::post('/admin/chat/reply', [ChatApiController::class, 'adminReply']);
        Route::delete('/admin/chat/rooms/{id}', [ChatApiController::class, 'deleteRoom']);

        // ── Admin: Ulasan ────────────────────────────────────────────────────
        Route::get('/admin/ulasan', [UlasanAdminController::class, 'index']);
        Route::delete('/admin/ulasan/{id}', [UlasanAdminController::class, 'destroy']);
        Route::patch('/admin/ulasan/{id}/visibility', [UlasanAdminController::class, 'toggleVisibility']);

        // ── Admin: Complaint ─────────────────────────────────────────────────
        Route::get('/admin/complaint', [ComplaintAdminController::class, 'index']);
        Route::get('/admin/complaint/{id}', [ComplaintAdminController::class, 'show']);
        Route::post('/admin/complaint/{id}/respond', [ComplaintAdminController::class, 'respond']);
    });
});
