<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\SecurityLog;

class DebugRegisterRoute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:register-route {ip?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug register route issues in production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ip = $this->argument('ip') ?? request()->ip();

        $this->info("Debugging register route for IP: {$ip}");
        $this->info("Environment: " . app()->environment());
        $this->line('');

        // Check if IP is blocked
        $this->info('=== IP Blocking Status ===');
        $loginFailedAttempts = SecurityLog::getFailedAttempts($ip, 'login_failed', 60);
        $registerFailedAttempts = SecurityLog::getFailedAttempts($ip, 'register_failed', 60);

        $this->line("Login failed attempts (last 60 min): {$loginFailedAttempts}");
        $this->line("Register failed attempts (last 60 min): {$registerFailedAttempts}");

        $isLoginBlocked = SecurityLog::isIpBlocked($ip, 'login_failed');
        $isRegisterBlocked = SecurityLog::isIpBlocked($ip, 'register_failed');

        $this->line("Login blocked: " . ($isLoginBlocked ? 'YES' : 'NO'));
        $this->line("Register blocked: " . ($isRegisterBlocked ? 'YES' : 'NO'));
        $this->line('');

        // Check rate limiting
        $this->info('=== Rate Limiting Status ===');
        $this->line("Current rate limit for register: " . (app()->environment('local', 'development') ? '10' : '5') . " per minute");
        $this->line('');

        // Check routes
        $this->info('=== Route Status ===');
        $registerRoute = collect(Route::getRoutes())->first(function ($route) {
            return $route->getName() === 'register';
        });

        if ($registerRoute) {
            $this->line("Register route exists: YES");
            $this->line("Register route URI: " . $registerRoute->uri());
            $this->line("Register route middleware: " . implode(', ', $registerRoute->middleware()));
        } else {
            $this->error("Register route NOT FOUND!");
        }
        $this->line('');

        // Check recent logs
        $this->info('=== Recent Security Logs ===');
        $recentLogs = SecurityLog::where('ip_address', $ip)
            ->whereIn('event_type', ['login_failed', 'register_failed', 'login_blocked', 'register_blocked'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($recentLogs->count() > 0) {
            foreach ($recentLogs as $log) {
                $this->line("{$log->created_at}: {$log->event_type} - {$log->status}");
            }
        } else {
            $this->line("No recent security logs found for this IP");
        }
        $this->line('');

        // Check environment configuration
        $this->info('=== Environment Configuration ===');
        $this->line("APP_ENV: " . config('app.env'));
        $this->line("APP_DEBUG: " . (config('app.debug') ? 'true' : 'false'));
        $this->line("APP_URL: " . config('app.url'));
        $this->line("HTTPS forced: " . (config('app.env') === 'production' ? 'YES' : 'NO'));
        $this->line('');

        $this->info('Debug completed. Check Laravel logs for more details.');

        return Command::SUCCESS;
    }
}
