<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Services\RoleMappingService;
use Spatie\Permission\Models\Role;

class SyncRolesSeeder extends Seeder
{
    public function run()
    {
        $this->info('Starting role synchronization using dynamic mapping...');

        // Get dynamic mapping from service
        $roleMapping = RoleMappingService::getMapping();

        $this->info('Current role mapping:');
        foreach ($roleMapping as $id => $name) {
            $this->line("  {$id} => {$name}");
        }

        // Ambil semua user yang punya role_id
        $users = User::whereNotNull('role_id')->get();
        $this->info("Found {$users->count()} users with role_id");

        $synced = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $roleName = $roleMapping[$user->role_id] ?? null;

            if ($roleName && Role::where('name', $roleName)->exists()) {
                // Remove all existing roles
                $user->syncRoles([]);

                // Assign role berdasarkan dynamic mapping
                $user->assignRole($roleName);

                echo "✅ User {$user->name} (ID: {$user->id}) assigned to role: {$roleName}\n";
                $synced++;
            } else {
                echo "⚠️  User {$user->name} (ID: {$user->id}) - role_id {$user->role_id} not mapped\n";
                $skipped++;
            }
        }

        $this->info("Synchronization completed: {$synced} synced, {$skipped} skipped");
    }

    private function info($message)
    {
        echo "\033[32m{$message}\033[0m\n";
    }

    private function line($message)
    {
        echo "{$message}\n";
    }
}
