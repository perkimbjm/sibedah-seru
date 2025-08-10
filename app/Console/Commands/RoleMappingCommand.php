<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoleMappingService;

class RoleMappingCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'role:mapping
                            {action : Action to perform (show|refresh|validate|debug)}
                            {--clear-cache : Clear mapping cache}';

    /**
     * The console command description.
     */
    protected $description = 'Manage dynamic role mapping between role_id and Spatie Permission roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        if ($this->option('clear-cache')) {
            RoleMappingService::refresh();
            $this->info('Role mapping cache cleared.');
        }

        switch ($action) {
            case 'show':
                $this->showMapping();
                break;
            case 'refresh':
                $this->refreshMapping();
                break;
            case 'validate':
                $this->validateMapping();
                break;
            case 'debug':
                $this->debugMapping();
                break;
            default:
                $this->error('Invalid action. Use: show, refresh, validate, or debug');
        }
    }

    private function showMapping()
    {
        $mapping = RoleMappingService::getMapping();

        $this->info('Current Role Mapping:');
        $this->table(
            ['role_id', 'Spatie Role Name'],
            collect($mapping)->map(fn($name, $id) => [$id, $name])
        );
    }

    private function refreshMapping()
    {
        $this->info('Refreshing role mapping...');
        $mapping = RoleMappingService::refresh();

        $this->info('Mapping refreshed successfully!');
        $this->showMapping();
    }

    private function validateMapping()
    {
        $validation = RoleMappingService::validateMapping();

        $this->info('Role Mapping Validation:');
        $rows = [];

        foreach ($validation as $roleId => $data) {
            $status = $data['exists_in_spatie'] ? '✅ Valid' : '❌ Missing';
            $rows[] = [$roleId, $data['role_name'], $status];
        }

        $this->table(['role_id', 'Role Name', 'Status'], $rows);

        $invalid = collect($validation)->filter(fn($data) => !$data['exists_in_spatie']);

        if ($invalid->isNotEmpty()) {
            $this->warn('Some roles are not found in Spatie Permission system!');
            $this->comment('Run: php artisan db:seed --class=PermissionSeeder to create missing roles');
        } else {
            $this->info('All roles are valid!');
        }
    }

    private function debugMapping()
    {
        $debug = RoleMappingService::getDebugInfo();

        $this->info('Debug Information:');
        $this->line('Environment: ' . $debug['environment']);
        $this->line('Auto-detection: ' . ($debug['auto_detection_enabled'] ? 'Enabled' : 'Disabled'));
        $this->line('Source table: ' . $debug['source_table']);
        $this->line('Cache key: ' . $debug['cache_key']);

        $this->newLine();
        $this->info('Current Mapping:');
        foreach ($debug['current_mapping'] as $id => $name) {
            $this->line("  {$id} => {$name}");
        }

        $this->newLine();
        $this->info('Available Spatie Roles:');
        foreach ($debug['spatie_roles'] as $id => $name) {
            $this->line("  {$id} => {$name}");
        }
    }
}
