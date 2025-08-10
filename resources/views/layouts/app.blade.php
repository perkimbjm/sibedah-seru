<!DOCTYPE html>
<html>
<head>
    @include('components.frontend.meta')

    <title inertia>{{ config('app.name', 'SIBEDAH SERU') }}</title>

    @stack('before-style')

    @include('components.frontend.style')

    @stack('after-style')

    <link rel="preconnect" href="https://fonts.bunny.net">

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js'])

</head>
<body class="overflow-x-hidden antialiased">
    <noscript>You need to enable JavaScript to run this app.</noscript>


    @yield('header')
    @inertia

    @yield('content')

    @yield('faq')

    @yield('footer')

    @stack('before-script')

    @include('components.frontend.script')

    @stack('after-script')

</body>
</html>
