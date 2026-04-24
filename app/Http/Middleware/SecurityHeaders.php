<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // CORS Headers for cross-origin requests
        $response->headers->set('Access-Control-Allow-Origin', 'http://sibedahseru.balangankab.go.id');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Content Security Policy - Different for development and production
        if (app()->environment('local', 'development')) {
            // Development CSP - Completely permissive for development
            $csp = "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; " .
                   "script-src * 'unsafe-inline' 'unsafe-eval' data: blob:; " .
                   "style-src * 'unsafe-inline' data: blob:; " .
                   "font-src * data: blob:; " .
                   "img-src * data: blob:; " .
                   "connect-src * data: blob:; " .
                   "frame-src *; " .
                   "object-src *; " .
                   "base-uri *; " .
                   "form-action *;";
        } else {
            // Production CSP - More restrictive but still allows necessary resources
            $csp = "default-src 'self' http://sibedahseru.balangankab.go.id; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://sibedahseru.balangankab.go.id https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com https://unpkg.com https://api.mapbox.com https://api.tiles.mapbox.com https://cdn.datatables.net https://stackpath.bootstrapcdn.com https://cdn.rawgit.com; " .
                   "style-src 'self' 'unsafe-inline' http://sibedahseru.balangankab.go.id https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://api.mapbox.com https://api.tiles.mapbox.com https://cdn.datatables.net https://stackpath.bootstrapcdn.com; " .
                   "font-src 'self' http://sibedahseru.balangankab.go.id https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.bunny.net; " .
                   "img-src 'self' data: https: http://sibedahseru.balangankab.go.id; " .
                   "connect-src 'self' https: http://sibedahseru.balangankab.go.id; " .
                   "frame-src 'self' https: https://app.atlas.co http://sibedahseru.balangankab.go.id; " .
                   "manifest-src 'self' http://sibedahseru.balangankab.go.id; " .
                   "object-src 'none'; " .
                   "base-uri 'self' http://sibedahseru.balangankab.go.id; " .
                   "form-action 'self' http://sibedahseru.balangankab.go.id;";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
