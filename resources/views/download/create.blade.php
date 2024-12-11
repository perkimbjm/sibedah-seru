@extends('layouts.main')
@php
    $menuName = 'Download';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card">
    <div class="card-header">
        Tambah data
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("app.downloads.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="title">Jenis Peraturan</label>
                <input class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" type="text" name="title" id="title" value="{{ old('title', '') }}" required>
                @if($errors->has('title'))
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="year">Nomor / Tahun</label>
                <input class="form-control {{ $errors->has('year') ? 'is-invalid' : '' }}" type="text" name="year" id="year" value="{{ old('year','') }}" required>
                @if($errors->has('year'))
                    <span class="text-danger">{{ $errors->first('year') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="title">Deskripsi</label>
                <input class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" type="text" name="description" id="description" value="{{ old('description', '') }}">
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="title">File URL</label>
                <input class="form-control {{ $errors->has('file_url') ? 'is-invalid' : '' }}" type="text" name="file_url" id="file_url" value="{{ old('file_url', '') }}" required>
                @if($errors->has('file_url'))
                    <span class="text-danger">{{ $errors->first('file_url') }}</span>
                @endif
            </div>



            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
