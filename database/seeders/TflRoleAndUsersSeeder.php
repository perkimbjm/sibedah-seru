<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class TflRoleAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Give permissions to User, aduan_access for only owned, aduan_create, aduan_show (only his owned)


        // Buat role "tfl"
        $tflRole = Role::firstOrCreate(['name' => 'tfl', 'guard_name' => 'web']);

        // Assign permissions yang sesuai untuk role tfl
        // Berdasarkan role User yang sudah ada, kita berikan permissions yang sama
        $tflRole->givePermissionTo([
            'aduan_access',
            'aduan_create',
            'aduan_show',
            'data_access',
            'house_show',
            'rtlh_show',
            'document_show',
            'download_show',
            'faq_show',
            'link_show',
            'review_show'
        ]);

        // Buat 25 user dengan role tfl
        $users = [];

        for ($i = 1; $i <= 25; $i++) {
            $userNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
            $password = "sanggam_{$userNumber}";

            $users[] = [
                'name' => "TFL {$userNumber}",
                'email' => "tfl_{$userNumber}@sibedahseru.web.id",
                'password' => bcrypt($password),
                'email_verified_at' => now(),
                'role_id' => $tflRole->id,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insert users ke database
        DB::table('users')->insert($users);

        // Assign role tfl ke semua user yang baru dibuat
        $tflUsers = User::where('email', 'like', 'tfl_%@sibedahseru.web.id')->get();
        foreach ($tflUsers as $user) {
            $user->assignRole('tfl');
        }

        $this->command->info('Role "tfl" dan 25 user berhasil dibuat!');
        $this->command->info('Username: tfl_01 sampai tfl_25');
        $this->command->info('Password: sanggam_01 sampai sanggam_25');
    }
}
