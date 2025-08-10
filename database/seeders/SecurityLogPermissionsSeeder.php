<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SecurityLogPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'security_log_access',
            'security_log_show',
            'security_log_statistics',
            'security_log_clear',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $assignToRoles = [];

        // Admin
        if ($admin = Role::where('name', 'admin')->first()) {
            $assignToRoles[] = $admin;
        }

        // Super Admin (variasi penamaan)
        $superAdmin = Role::where('name', 'super admin')->first();
        if (!$superAdmin) {
            $superAdmin = Role::where('name', 'Super Admin')->first();
        }
        if ($superAdmin) {
            $assignToRoles[] = $superAdmin;
        }

        foreach ($assignToRoles as $role) {
            $role->givePermissionTo($permissions);
        }
    }
}
