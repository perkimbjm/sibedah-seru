<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\TflRoleAndUsersSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UsersTableSeeder::class,
            PermissionsTableSeeder::class,
            RolesAndPermissionsSeeder::class,
            AduanPermissionsSeeder::class,
            TflRoleAndUsersSeeder::class
        ]);
    }
}
