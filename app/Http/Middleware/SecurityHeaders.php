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
            $csp = "default-src 'self' https://sibedahseru.web.id; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://sibedahseru.web.id https://cdn.jsdelivr.net https://code.jquery.com https://cdnjs.cloudflare.com https://unpkg.com https://api.mapbox.com https://api.tiles.mapbox.com https://cdn.datatables.net https://stackpath.bootstrapcdn.com https://cdn.rawgit.com; " .
                   "style-src 'self' 'unsafe-inline' https://sibedahseru.web.id https://fonts.googleapis.com https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://api.mapbox.com https://api.tiles.mapbox.com https://cdn.datatables.net https://stackpath.bootstrapcdn.com; " .
                   "font-src 'self' https://sibedahseru.web.id https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.bunny.net; " .
                   "img-src 'self' data: https: https://sibedahseru.web.id; " .
                   "connect-src 'self' https: https://sibedahseru.web.id; " .
                   "frame-src 'self' https: https://app.atlas.co https://sibedahseru.web.id; " .
                   "manifest-src 'self' https://sibedahseru.web.id; " .
                   "object-src 'none'; " .
                   "base-uri 'self' https://sibedahseru.web.id; " .
                   "form-action 'self' https://sibedahseru.web.id;";
        }

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
