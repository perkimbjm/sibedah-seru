@extends('layouts.main')

@php
    $menuName = 'Penerima Bantuan Bedah Rumah';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container">
    <h3>Foto Rumah: {{ $house->name }}</h3>
    <div class="m-3">
        <a href="{{ route('app.gallery.create', $house) }}" class="mb-3 btn btn-primary">Tambah Foto</a>
        <a href="{{ route('app.houses.index') }}" class="mb-3 btn btn-secondary">Kembali</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Thumbnail</th>
                <th>Path</th>
                <th>Progres</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if($photos->isEmpty())
                <tr>
                    <td class="text-3xl font-bold text-center" colspan="5">Ooops belum ada data foto</td>
                </tr>
            @else
                @foreach ($photos as $photo)
                    <tr>
                        <td><img src="{{ asset($photo->photo_url) }}" alt="Thumbnail" style="width: 300px;"></td>
                        <td>{{ $photo->photo_url }}</td>
                        <td>{{ $photo->progres }}%</td>
                        <td>{{ $photo->description }}</td>
                        <td>
                            <a href="{{ route('app.gallery.edit', [$house, $photo]) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('app.gallery.destroy', [$house, $photo]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus foto ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection
