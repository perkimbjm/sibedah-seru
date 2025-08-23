<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SecurityLog;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IpBlocking
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $path = $request->path();
        $environment = app()->environment();

        // Log all requests to register path for debugging
        if ($path === 'register') {
            Log::info('Register page accessed', [
                'ip' => $ip,
                'environment' => $environment,
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);
        }

        // Check if IP is blocked for login attempts
        if ($request->is('login') && SecurityLog::isIpBlocked($ip, 'login_failed')) {
            Log::warning('IP blocked for login', [
                'ip' => $ip,
                'environment' => $environment,
                'path' => $path
            ]);

            SecurityLog::logEvent('login_blocked', [
                'email' => $request->input('email'),
                'status' => 'blocked',
                'reason' => 'Too many failed login attempts'
            ]);

            return back()->withErrors([
                'email' => 'IP Anda telah diblokir karena terlalu banyak percobaan login gagal. Silakan coba lagi dalam 1 jam.',
            ]);
        }

        // Check if IP is blocked for register attempts
        if ($request->is('register') && SecurityLog::isIpBlocked($ip, 'register_failed')) {
            Log::warning('IP blocked for register', [
                'ip' => $ip,
                'environment' => $environment,
                'path' => $path,
                'failed_attempts' => SecurityLog::getFailedAttempts($ip, 'register_failed', 60)
            ]);

            SecurityLog::logEvent('register_blocked', [
                'email' => $request->input('email'),
                'status' => 'blocked',
                'reason' => 'Too many failed registration attempts'
            ]);

            return back()->withErrors([
                'email' => 'IP Anda telah diblokir karena terlalu banyak percobaan pendaftaran gagal. Silakan coba lagi dalam 1 jam.',
            ]);
        }

        // Log successful access to register
        if ($path === 'register') {
            Log::info('Register page access allowed', [
                'ip' => $ip,
                'environment' => $environment,
                'timestamp' => now()->toISOString()
            ]);
        }

        return $next($request);
    }
}
