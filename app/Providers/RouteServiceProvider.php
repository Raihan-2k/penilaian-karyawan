<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Http\Middleware\RoleMiddleware;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Jalur default berdasarkan role pengguna setelah login.
     *
     * @param  \App\Models\Employee  $user
     * @return string
     */
    public static function redirectToByRole($user): string
    {
        return match ($user->role) {
            'admin'    => '/admin/dashboard',              // Ganti jika kamu pakai /admin/dashboard
            'manager'  => '/dashboard',
            'karyawan' => '/absensi/dashboard',
            'administrator' => '/absensi/dashboard',
            default    => '/',
        };
    }

    /**
     * Daftarkan rute dan alias middleware.
     */
    public function boot(): void
    {
        // Daftarkan middleware aliases khusus (seperti 'role')
        Route::middlewareGroup('web', []);

        Route::middlewareAliases([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Daftarkan rute aplikasi
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        });
    }
}
