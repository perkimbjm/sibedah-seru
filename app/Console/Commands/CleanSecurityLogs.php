<?php

namespace App\Console\Commands;

use App\Models\SecurityLog;
use Illuminate\Console\Command;

class CleanSecurityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:clean-logs {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old security logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = SecurityLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Successfully deleted {$deletedCount} security logs older than {$days} days.");

        return Command::SUCCESS;
    }
}
