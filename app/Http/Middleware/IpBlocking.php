<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SecurityLog;
use Symfony\Component\HttpFoundation\Response;

class IpBlocking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Check if IP is blocked for login attempts
        if ($request->is('login') && SecurityLog::isIpBlocked($ip, 'login_failed')) {
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
            SecurityLog::logEvent('register_blocked', [
                'email' => $request->input('email'),
                'status' => 'blocked',
                'reason' => 'Too many failed registration attempts'
            ]);

            return back()->withErrors([
                'email' => 'IP Anda telah diblokir karena terlalu banyak percobaan pendaftaran gagal. Silakan coba lagi dalam 1 jam.',
            ]);
        }

        return $next($request);
    }
}
