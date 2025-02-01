@extends('layouts.main')

@php
    $menuName = str_replace('.', ' ', Route::currentRouteName());
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp


@section('styles')
<style>
    .fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 9999;
    }
</style>
@endsection


@section('content')
<div class="container position-relative w-100 border-spacing-1" style="height: 85vh;">
    <button onclick="toggleFullscreen()" class="btn btn-primary position-absolute" style="top: 10px; right: 10px; z-index: 1000;">
        <i class="fas fa-expand"></i>
    </button>
    <iframe
        id="atlas-iframe"
        src="https://app.atlas.co/embeds/s8ro0s5eNOP7UF5PcWcq"
        class="position-absolute w-100 h-100"
        style="top: 0; left: 0; border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"
        loading="lazy"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen>
    </iframe>
</div>

<script>
function toggleFullscreen() {
    const iframe = document.getElementById('atlas-iframe');
    if (!iframe.classList.contains('fullscreen')) {
        iframe.classList.add('fullscreen');
    } else {
        iframe.classList.remove('fullscreen');
    }
}

// Menambahkan event listener untuk tombol ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const iframe = document.getElementById('atlas-iframe');
        if (iframe.classList.contains('fullscreen')) {
            iframe.classList.remove('fullscreen');
        }
    }
});
</script>
@endsection
