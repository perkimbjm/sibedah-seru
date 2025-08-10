<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsulanPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for usulan
        $usulanPermissions = [
            'usulan_access',
            'usulan_create',
            'usulan_show',
            'usulan_edit',
            'usulan_delete',
            'usulan_verify',
        ];

        $verifikasiPermissions = [
            'verifikasi_access',
            'verifikasi_create',
            'verifikasi_show',
            'verifikasi_edit',
            'verifikasi_delete',
        ];

        $notificationPermissions = [
            'notification_access',
        ];

        // Create all permissions
        foreach ($usulanPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($verifikasiPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        foreach ($notificationPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $tflRole = Role::where('name', 'tfl')->first();
        $userRole = Role::where('name', 'user')->first();

        if ($adminRole) {
            // Admin gets all permissions
            $adminRole->givePermissionTo([
                ...$usulanPermissions,
                ...$verifikasiPermissions,
                ...$notificationPermissions,
            ]);
        }

        if ($tflRole) {
            // TFL gets usulan and verifikasi permissions
            $tflRole->givePermissionTo([
                'usulan_access',
                'usulan_show',
                'usulan_verify',
                'verifikasi_access',
                'verifikasi_create',
                'verifikasi_show',
                'verifikasi_edit',
                'notification_access',
            ]);
        }

        if ($userRole) {
            // User gets basic usulan permissions
            $userRole->givePermissionTo([
                'usulan_access',
                'usulan_create',
                'usulan_show',
                'usulan_edit',
                'usulan_delete',
                'notification_access',
            ]);
        }

        $this->command->info('Usulan permissions seeded successfully!');
    }
}
