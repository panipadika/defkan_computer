<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Rate limiter umum untuk semua API endpoint
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter ketat untuk endpoint login:
        // Maks 5 percobaan per menit per IP — mencegah brute force password
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Terlalu banyak percobaan login. Silakan tunggu 1 menit sebelum mencoba lagi.',
                    ], 429);
                });
        });

        // Rate limiter ketat untuk endpoint lupa password:
        // Maks 3 percobaan per 10 menit per IP — mencegah spam email reset
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinutes(10, 3)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Terlalu banyak permintaan reset password. Silakan tunggu 10 menit sebelum mencoba lagi.',
                    ], 429);
                });
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
