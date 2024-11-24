<?php

namespace App\Providers;

use App\Models\User;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Vite;
use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\ServiceProvider;

class JetstreamServiceProvider extends ServiceProvider
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
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Vite::prefetch(concurrency: 3);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            // Cek jika user tidak ada atau password tidak cocok
            if (!$user || !Hash::check($request->password, $user->password)) {
                return null; // Mengembalikan null untuk menandakan gagal autentikasi
            }

            return $user; // Mengembalikan objek user jika autentikasi berhasil
        });
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::permissions([
            'create',
            'read',
            'update',
            'delete',
        ]);
    }
}
