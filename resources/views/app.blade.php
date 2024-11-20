<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        
        <x-meta></x-meta>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">


        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        @if(request()->routeIs('map'))
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
            <link href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" rel="stylesheet" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol/dist/L.Control.Locate.min.css" crossorigin="" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css" crossorigin="" />
            <link rel="stylesheet" href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css" crossorigin="" />
            <link rel="stylesheet" href='{{ asset('css/locateControl.css') }}' />
            <link rel="stylesheet" href="{{ asset('css/iconLayers.css') }}" />
            <link rel="stylesheet" href="{{ asset('css/leaflet.pm.css') }}">
            <link rel="stylesheet" href="{{ asset('css/Leaflet.PolylineMeasure.css') }}">
            <link rel="stylesheet" href="{{ asset('css/leaflet.toolbar.css') }}" />
            <link rel="stylesheet" href="{{ asset('css/leaflet-sidepanel.css') }}" />

        @endif
    </head>
    <body class="font-sans antialiased overflow-x-hidden">
        @inertia
    </body>
</html>
