@extends('layouts.main')
@php
    $menuName = 'Download';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card">
    <div class="card-header">
        Detail Download
    </div>
    <div class="card-body">
        <h5 class="card-title">{{ $download->title }}</h5>
        <p class="card-text"><strong>Nomor / Tahun:</strong> {{ $download->year }}</p>
        <p class="card-text"><strong>Deskripsi:</strong> {{ $download->description }}</p>
        <p class="card-text"><strong>File URL:</strong> <a href="{{ $download->file_url }}" target="_blank">{{ $download->file_url }}</a></p>

        <h6>Preview PDF:</h6>
        <iframe src="{{ $download->file_url }}" width="100%" height="400px"></iframe>

        <div class="mt-3">
            <a href="{{ route('app.downloads.edit', $download->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('app.downloads.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>

@endsection