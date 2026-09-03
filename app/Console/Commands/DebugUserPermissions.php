<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class DebugUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:user-permissions {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug user permissions for aduan_create';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email {$email} tidak ditemukan!");
            return;
        }

        $this->info("=== DEBUG USER PERMISSIONS ===");
        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Role ID: {$user->role_id}");

        // Check direct permissions
        $this->info("\n=== DIRECT PERMISSIONS ===");
        $directPermissions = $user->getDirectPermissions();
        if ($directPermissions->count() > 0) {
            foreach ($directPermissions as $permission) {
                $this->line("- {$permission->name}");
            }
        } else {
            $this->line("No direct permissions");
        }

        // Check role permissions
        $this->info("\n=== ROLE PERMISSIONS ===");
        $roles = $user->getRoleNames();
        if ($roles->count() > 0) {
            foreach ($roles as $roleName) {
                $this->line("Role: {$roleName}");
                $role = $user->roles()->where('name', $roleName)->first();
                if ($role) {
                    foreach ($role->permissions as $permission) {
                        $this->line("  - {$permission->name}");
                    }
                }
            }
        } else {
            $this->line("No roles assigned");
        }

        // Check all permissions user has
        $this->info("\n=== ALL USER PERMISSIONS ===");
        $allPermissions = $user->getAllPermissions();
        foreach ($allPermissions as $permission) {
            $this->line("- {$permission->name}");
        }

        // Specific check for aduan_create
        $this->info("\n=== ADUAN_CREATE CHECK ===");
        $hasPermission = $user->hasPermissionTo('aduan_create');
        $this->line("Has aduan_create permission: " . ($hasPermission ? 'YES' : 'NO'));

        // Check if permission exists in database
        $aduanCreatePermission = Permission::where('name', 'aduan_create')->first();
        if ($aduanCreatePermission) {
            $this->line("aduan_create permission exists in database: YES");
        } else {
            $this->error("aduan_create permission NOT found in database!");
        }

        // Check Gate
        $this->info("\n=== GATE CHECK ===");
        try {
            $gateResult = Gate::forUser($user)->allows('aduan_create');
            $this->line("Gate allows aduan_create: " . ($gateResult ? 'YES' : 'NO'));
        } catch (\Exception $e) {
            $this->error("Gate check failed: " . $e->getMessage());
        }
    }
}
