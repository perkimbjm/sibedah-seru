<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@sibedahseru.web.id',
                'password' => bcrypt('sanggam123'),
                'email_verified_at' => now(),
                'role_id' => '1',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Perkim',
                'email' => 'perkim@sibedahseru.web.id',
                'password' => bcrypt('sanggam123'),
                'email_verified_at' => now(),
                'role_id' => '2',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Dev',
                'email' => 'webgis.bjm2@gmail.com',
                'password' => bcrypt('sanggam123'),
                'email_verified_at' => now(),
                'role_id' => '3',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'name' => 'Kepala Dinas',
                'email' => 'kadis@sibedahseru.web.id',
                'password' => bcrypt('sanggam123'),
                'email_verified_at' => now(),
                'role_id' => '4',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now()
            ],
            
        ]);
    }
}
