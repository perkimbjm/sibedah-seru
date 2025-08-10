<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AduanPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menambahkan permissions untuk modul aduan
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permissions untuk aduan
        $aduanPermissions = [
            'aduan_access',    // Akses ke halaman aduan
            'aduan_create',    // Membuat aduan baru
            'aduan_show',      // Melihat detail aduan
            'aduan_edit',      // Mengedit/menanggapi aduan
            'aduan_delete',    // Menghapus aduan
            'dashboard_aduan_stats_access', // Akses ke statistik aduan di dashboard
        ];

        // Insert permissions ke database
        foreach ($aduanPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions ke role yang sudah ada
        $this->assignPermissionsToRoles();

        $this->command->info('Aduan permissions berhasil ditambahkan!');
    }

    /**
     * Assign permissions ke role yang sesuai
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin - dapat semua permissions
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo([
                'aduan_access',
                'aduan_create',
                'aduan_show',
                'aduan_edit',
                'aduan_delete',
                'dashboard_aduan_stats_access'
            ]);
        }

        // Admin - dapat semua permissions aduan
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'aduan_access',
                'aduan_create',
                'aduan_show',
                'aduan_edit',
                'aduan_delete',
                'dashboard_aduan_stats_access'
            ]);
        }

        // Pimpinan - dapat akses dan lihat aduan termasuk statistik
        $pimpinanRole = Role::where('name', 'Pimpinan')->first();
        if ($pimpinanRole) {
            $pimpinanRole->givePermissionTo([
                'aduan_access',
                'aduan_show',
                'dashboard_aduan_stats_access'
            ]);
        }

        // User - hanya dapat membuat dan melihat aduan miliknya sendiri, tidak dapat melihat statistik dashboard
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $userRole->givePermissionTo([
                'aduan_access',
                'aduan_create',
                'aduan_show'
            ]);
        }

        $this->command->info('Permissions aduan berhasil diassign ke role!');
    }
}
