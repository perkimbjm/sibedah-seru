<x-layout>
<x-slot:title>{{ $title ?? 'SIBEDAH-SERU' }}</x-slot:title>
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
  <h1 class="text-3xl font-bold tracking-tight text-gray-900">Galeri</h1>
</div>

<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach ($galleries as $gallery)
    <article class="bg-white p-4 rounded-lg shadow-md max-w-screen-md border-b border-gray-300 mt-4">
        <a href="/gallery/{{ $gallery['slug'] }}" class="hover:underline">
          <h2 class="text-lg font-semibold text-gray-800 hover:text-blue-800">{{ $gallery['title'] }}</h2>
        </a>
        <p class="text-base text-gray-500 mt-2">Tanggal Posting: {{ $gallery->created_at->diffForHumans() }}</p>
        <img src="{{ $gallery['image'] }}" alt="{{ $gallery['alt'] }}" class="w-full h-48 object-cover mt-4 rounded-md">
        <p class="text-sm text-gray-600 my-4 font-light">{{ Str::limit($gallery['description'], 50) }}</p>
        
        <a href="/gallery/{{ $gallery['slug'] }}" class="text-blue-500 hover:text-blue-800 mt-4 inline-block">Read More &raquo;</a>
    </article>

    @endforeach
    </div>
</div>
</x-layout>
