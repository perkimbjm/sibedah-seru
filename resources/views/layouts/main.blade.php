<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        
        <x-meta></x-meta>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">


        <!-- Scripts -->

        @vite('resources/js/app.js')


        @if(request()->routeIs('map'))
            @stack('after-style')
        @endif
    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        @yield('header')
        <div id="app" data-page="{{ json_encode($page ?? []) }}">
            <!-- Inertia akan me-render halaman di sini -->
        </div>

        @yield('content')


        @if(request()->routeIs('map'))
            @stack('after-script')
        @endif
    </body>
</html>
