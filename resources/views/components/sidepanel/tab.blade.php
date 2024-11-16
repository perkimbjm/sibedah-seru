@props(['link'])

<button class="sidepanel-tab" data-tab="{{ $link }}">
    {{ $slot }}
</button>