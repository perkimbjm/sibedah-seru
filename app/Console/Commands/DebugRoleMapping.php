<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoleMappingService;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DebugRoleMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:role-mapping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug role mapping system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== ROLE MAPPING DEBUG ===\n");

        // Get debug info from service
        $debugInfo = RoleMappingService::getDebugInfo();

        $this->info("Environment: " . $debugInfo['environment']);
        $this->info("Auto Detection Enabled: " . ($debugInfo['auto_detection_enabled'] ? 'YES' : 'NO'));
        $this->info("Source Table: " . $debugInfo['source_table']);
        $this->info("Cache Key: " . $debugInfo['cache_key']);

        $this->info("\n=== CURRENT MAPPING ===");
        foreach ($debugInfo['current_mapping'] as $roleId => $roleName) {
            $this->line("Role ID {$roleId} -> '{$roleName}'");
        }

        $this->info("\n=== SPATIE ROLES ===");
        foreach ($debugInfo['spatie_roles'] as $id => $name) {
            $this->line("Spatie Role ID {$id}: '{$name}'");
        }

        $this->info("\n=== VALIDATION ===");
        foreach ($debugInfo['validation'] as $roleId => $validation) {
            $status = $validation['exists_in_spatie'] ? '✓' : '✗';
            $this->line("{$status} Role ID {$roleId} ('{$validation['role_name']}') - Exists in Spatie: " .
                       ($validation['exists_in_spatie'] ? 'YES' : 'NO'));
        }

        // Test dengan beberapa user
        $this->info("\n=== USER ROLE TEST ===");
        $users = User::limit(5)->get();
        foreach ($users as $user) {
            $spatieRoles = $user->getRoleNames()->implode(', ');
            $this->line("User {$user->id} ({$user->email}): role_id={$user->role_id}, Spatie roles: [{$spatieRoles}]");
        }
    }
}
