@extends('layouts.main')

@php
    $menuName = 'Foto RTLH';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container">
    <h4>Foto Rumah: {{ $rtlh->name }}</h4>
    <div class="m-3">
        <a href="{{ route('app.rutilahu.create', $rtlh) }}" class="mb-3 btn btn-primary">Tambah Foto</a>
        <a href="{{ route('app.rtlh.index') }}" class="mb-3 btn btn-secondary">Kembali</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Thumbnail</th>
                <th>Path</th>
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
                        <td><img src="{{ asset('storage/' . $photo->photo_url) }}" alt="Thumbnail" style="width: 300px;"></td>
                        <td>{{ $photo->photo_url }}</td>
                        <td>{{ $photo->description }}</td>
                        <td>
                            <a href="{{ route('app.rutilahu.edit', [$rtlh, $photo]) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('app.rutilahu.destroy', [$rtlh, $photo]) }}" method="POST" style="display:inline;">
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
