<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        
        <x-meta></x-meta>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preload" href="/img/hero_c.webp" as="image" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        @inertia
    </body>
</html>
