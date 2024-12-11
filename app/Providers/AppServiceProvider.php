<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Route::middleware('web')
        ->group(function () {
            Route::middleware([
                'permission' => PermissionMiddleware::class,
                'role' => RoleMiddleware::class,
            ]);
        });

        Paginator::useBootstrap();
    }
}
