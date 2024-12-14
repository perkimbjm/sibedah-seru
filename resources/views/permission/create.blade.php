@extends('layouts.main')
@php
    $menuName = 'Tambah Hak Akses';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card">
    <div class="card-header">
        Tambah Data
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("app.permissions.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">Jenis Hak Akses</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block text-sm">Jenis hak akses berakhiran _access, _create, _show, _edit, _delete</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    SImpan
                </button>
            </div>
        </form>
    </div>
</div>



@endsection
