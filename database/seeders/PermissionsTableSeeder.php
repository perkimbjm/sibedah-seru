<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {
        $permissions = [
            ['id' => 1, 'name' => 'user_management_access'],
            ['id' => 2, 'name' => 'permission_create'],
            ['id' => 3, 'name' => 'permission_edit'],
            ['id' => 4, 'name' => 'permission_show'],
            ['id' => 5, 'name' => 'permission_delete'],
            ['id' => 6, 'name' => 'permission_access'],
            ['id' => 7, 'name' => 'role_create'],
            ['id' => 8, 'name' => 'role_edit'],
            ['id' => 9, 'name' => 'role_show'],
            ['id' => 10, 'name' => 'role_delete'],
            ['id' => 11, 'name' => 'role_access'],
            ['id' => 12, 'name' => 'user_create'],
            ['id' => 13, 'name' => 'user_edit'],
            ['id' => 14, 'name' => 'user_show'],
            ['id' => 15, 'name' => 'user_delete'],
            ['id' => 16, 'name' => 'user_access'],
            ['id' => 17, 'name' => 'audit_log_create'],
            ['id' => 18, 'name' => 'audit_log_edit'],
            ['id' => 19, 'name' => 'audit_log_show'],
            ['id' => 20, 'name' => 'audit_log_delete'],
            ['id' => 21, 'name' => 'audit_log_access'],
            ['id' => 22, 'name' => 'data_access'],
            ['id' => 23, 'name' => 'house_create'],
            ['id' => 24, 'name' => 'house_edit'],
            ['id' => 25, 'name' => 'house_show'],
            ['id' => 26, 'name' => 'house_delete'],
            ['id' => 27, 'name' => 'house_access'],
            ['id' => 28, 'name' => 'renovated_house_photo_create'],
            ['id' => 29, 'name' => 'renovated_house_photo_edit'],
            ['id' => 30, 'name' => 'renovated_house_photo_show'],
            ['id' => 31, 'name' => 'renovated_house_photo_delete'],
            ['id' => 32, 'name' => 'renovated_house_photo_access'],
            ['id' => 33, 'name' => 'document_create'],
            ['id' => 34, 'name' => 'document_edit'],
            ['id' => 35, 'name' => 'document_show'],
            ['id' => 36, 'name' => 'document_delete'],
            ['id' => 37, 'name' => 'document_access'],
            ['id' => 38, 'name' => 'rtlh_create'],
            ['id' => 39, 'name' => 'rtlh_edit'],
            ['id' => 40, 'name' => 'rtlh_show'],
            ['id' => 41, 'name' => 'rtlh_delete'],
            ['id' => 42, 'name' => 'rtlh_access'],
            ['id' => 43, 'name' => 'house_photo_create'],
            ['id' => 44, 'name' => 'house_photo_edit'],
            ['id' => 45, 'name' => 'house_photo_show'],
            ['id' => 46, 'name' => 'house_photo_delete'],
            ['id' => 47, 'name' => 'house_photo_access'],
            ['id' => 48, 'name' => 'content_management_access'],
            ['id' => 49, 'name' => 'download_create'],
            ['id' => 50, 'name' => 'download_edit'],
            ['id' => 51, 'name' => 'download_show'],
            ['id' => 52, 'name' => 'download_delete'],
            ['id' => 53, 'name' => 'download_access'],
            ['id' => 54, 'name' => 'faq_create'],
            ['id' => 55, 'name' => 'faq_edit'],
            ['id' => 56, 'name' => 'faq_show'],
            ['id' => 57, 'name' => 'faq_delete'],
            ['id' => 58, 'name' => 'faq_access'],
            ['id' => 59, 'name' => 'link_create'],
            ['id' => 60, 'name' => 'link_edit'],
            ['id' => 61, 'name' => 'link_show'],
            ['id' => 62, 'name' => 'link_delete'],
            ['id' => 63, 'name' => 'link_access'],
            ['id' => 64, 'name' => 'review_create'],
            ['id' => 65, 'name' => 'review_edit'],
            ['id' => 66, 'name' => 'review_show'],
            ['id' => 67, 'name' => 'review_delete'],
            ['id' => 68, 'name' => 'review_access'],
            ['id' => 69, 'name' => 'profile_password_edit']        
            ];

        DB::table('permissions')->insert($permissions);
    }
}
