<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip captcha validation for non-register requests
        if (!$request->is('register') && !$request->is('api/register')) {
            return $next($request);
        }

        // Skip captcha validation in development environment
        if (app()->environment('local', 'development')) {
            return $next($request);
        }

        $captcha = $request->input('captcha');

        if (!$captcha) {
            return back()->withErrors(['captcha' => 'Captcha harus diisi.'])->withInput();
        }

        // Validate that captcha is a number
        if (!is_numeric($captcha)) {
            return back()->withErrors(['captcha' => 'Captcha harus berupa angka.'])->withInput();
        }

        return $next($request);
    }
}
