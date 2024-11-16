<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">

    <head>
        <x-head></x-head>
    </head>
    <body class="h-full font-sans antialiased dark:bg-black dark:text-white/50">

<div class="min-h-full">
 <x-navbar></x-navbar>
  <x-header>{{ $title }}</x-header>
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      {{ $slot}}
    </div>
  </main>
</div>

    </body>
</html>
