<!DOCTYPE html>
<html>
<head>
    @include('components.frontend.meta')
    
    <title inertia>{{ config('app.name', 'SIBEDAH SERU') }}</title>

    @stack('before-style')

    @include('components.frontend.style')

    @stack('after-style')

</head>
<body class="antialiased overflow-x-hidden">
    <noscript>You need to enable JavaScript to run this app.</noscript>


    @yield('header')


    @yield('content')

    @yield('faq')
        
    @yield('footer')

    @stack('before-script')

        @include('components.frontend.script')
    
    @stack('after-script')

    
</body>
</html>