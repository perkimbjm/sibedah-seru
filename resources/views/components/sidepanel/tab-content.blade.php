@props(['tab'])

<div class="sidepanel-tab-content" data-tab-content="{{ $tab }}">
    {{ $slot }}
</div>