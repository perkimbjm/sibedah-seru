<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SanitizeInput::class,
            \App\Http\Middleware\ValidateCaptcha::class,
            \App\Http\Middleware\IpBlocking::class,
            \App\Http\Middleware\AuthGates::class,
            // 'verify.api.token' => \App\Http\Middleware\VerifyApiToken::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Register middleware aliases
        $middleware->alias([
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'sanitize.input' => \App\Http\Middleware\SanitizeInput::class,
            'validate.captcha' => \App\Http\Middleware\ValidateCaptcha::class,
            'ip.blocking' => \App\Http\Middleware\IpBlocking::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
