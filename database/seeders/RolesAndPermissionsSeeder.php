<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
            Permission::create(['name' => 'user_management_access']);
            Permission::create(['name' => 'permission_create']);
            Permission::create(['name' => 'permission_edit']);
            Permission::create(['name' => 'permission_show']);
            Permission::create(['name' => 'permission_delete']);
            Permission::create(['name' => 'permission_access']);
            Permission::create(['name' => 'role_create']);
            Permission::create(['name' => 'role_edit']);
            Permission::create(['name' => 'role_show']);
            Permission::create(['name' => 'role_delete']);
            Permission::create(['name' => 'role_access']);
            Permission::create(['name' => 'user_create']);
            Permission::create(['name' => 'user_edit']);
            Permission::create(['name' => 'user_show']);
            Permission::create(['name' => 'user_delete']);
            Permission::create(['name' => 'user_access']);
            Permission::create(['name' => 'audit_log_create']);
            Permission::create(['name' => 'audit_log_edit']);
            Permission::create(['name' => 'audit_log_show']);
            Permission::create(['name' => 'audit_log_delete']);
            Permission::create(['name' => 'audit_log_access']);
            Permission::create(['name' => 'data_access']);
            Permission::create(['name' => 'house_create']);
            Permission::create(['name' => 'house_edit']);
            Permission::create(['name' => 'house_show']);
            Permission::create(['name' => 'house_delete']);
            Permission::create(['name' => 'house_access']);
            Permission::create(['name' => 'renovated_house_photo_create']);
            Permission::create(['name' => 'renovated_house_photo_edit']);
            Permission::create(['name' => 'renovated_house_photo_show']);
            Permission::create(['name' => 'renovated_house_photo_delete']);
            Permission::create(['name' => 'renovated_house_photo_access']);
            Permission::create(['name' => 'document_create']);
            Permission::create(['name' => 'document_edit']);
            Permission::create(['name' => 'document_show']);
            Permission::create(['name' => 'document_delete']);
            Permission::create(['name' => 'document_access']);
            Permission::create(['name' => 'rtlh_create']);
            Permission::create(['name' => 'rtlh_edit']);
            Permission::create(['name' => 'rtlh_show']);
            Permission::create(['name' => 'rtlh_delete']);
            Permission::create(['name' => 'rtlh_access']);
            Permission::create(['name' => 'house_photo_create']);
            Permission::create(['name' => 'house_photo_edit']);
            Permission::create(['name' => 'house_photo_show']);
            Permission::create(['name' => 'house_photo_delete']);
            Permission::create(['name' => 'house_photo_access']);
            Permission::create(['name' => 'content_management_access']);
            Permission::create(['name' => 'download_create']);
            Permission::create(['name' => 'download_edit']);
            Permission::create(['name' => 'download_show']);
            Permission::create(['name' => 'download_delete']);
            Permission::create(['name' => 'download_access']);
            Permission::create(['name' => 'faq_create']);
            Permission::create(['name' => 'faq_edit']);
            Permission::create(['name' => 'faq_show']);
            Permission::create(['name' => 'faq_delete']);
            Permission::create(['name' => 'faq_access']);
            Permission::create(['name' => 'link_create']);
            Permission::create(['name' => 'link_edit']);
            Permission::create(['name' => 'link_show']);
            Permission::create(['name' => 'link_delete']);
            Permission::create(['name' => 'link_access']);
            Permission::create(['name' => 'review_create']);
            Permission::create(['name' => 'review_edit']);
            Permission::create(['name' => 'review_show']);
            Permission::create(['name' => 'review_delete']);
            Permission::create(['name' => 'review_access']);
            Permission::create(['name' => 'profile_password_edit']);

            // create roles and assign permissions
            $roleSuperAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
            $roleSuperAdmin->givePermissionTo(Permission::all());

            // Tambahkan role lainnya
            Role::create(['name' => 'Admin', 'guard_name' => 'web']);
            Role::create(['name' => 'User', 'guard_name' => 'web']);
            Role::create(['name' => 'Pimpinan', 'guard_name' => 'web']);


            $superAdminUser = User::find(1); // Ganti dengan ID pengguna yang sesuai
            if ($superAdminUser) {
                $superAdminUser->assignRole('Super Admin');
            }
    }
}
