@extends('layouts.main')
@php
    $menuName = 'Edit Data Pengguna';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card">
    <div class="card-header">
        Edit Data
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("app.users.update", [$user->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">Nama</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="email">Email</label>
                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                @if($errors->has('email'))
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                @endif
            </div>
            
            <div class="form-group">
                <label for="phone">Nomor telepon</label>
                <input class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}">
                @if($errors->has('phone'))
                    <span class="text-danger">{{ $errors->first('phone') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="password">Password (Kosongkan jika tidak ingin diubah)</label>
                <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password">
                @if($errors->has('password'))
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label class="required" for="roles">Role</label>
                <select class="form-control select2 {{ $errors->has('role_id') ? 'is-invalid' : '' }}" name="role_id" id="roles" required>
                    @foreach($roles as $id => $role)
                        <option value="{{ $id }}" {{ (old('role_id') ?? $user->role_id) == $id ? 'selected' : '' }}>{{ $role }}</option>
                    @endforeach
                </select>
                @if($errors->has('role_id'))
                    <span class="text-danger">{{ $errors->first('role_id') }}</span>
                @endif
            </div>

            <div class="form-group">
    <label>Verifikasi Email</label>
    <div>
        @if($user->email_verified_at)
            <label>
                <input type="radio" name="email_verified_at" value="keep" checked>
                Sudah Terverifikasi
            </label>
        @else
            <label>
                <input type="radio" name="email_verified_at" value="now">
                Sekarang
            </label>
            <label>
                <input type="radio" name="email_verified_at" value="unverify" checked>
                Biarkan Tidak Terverifikasi
            </label>
        @endif
    </div>
</div>


            <div class="form-group">
                <label for="profile_photo_path">Foto Profil</label>
                @if($user->profile_photo_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto Profil" width="150" height="150" class="img-thumbnail">
                    </div>
                @endif
                <input class="form-control {{ $errors->has('profile_photo_path') ? 'is-invalid' : '' }}" type="file" name="profile_photo_path" id="profile_photo_path" accept=".jpeg,.jpg,.png,.webp">
                @if($errors->has('profile_photo_path'))
                    <span class="text-danger">{{ $errors->first('profile_photo_path') }}</span>
                @endif
            </div>

            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    Simpan
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-info">Batal</a>
            </div>
        </form>
    </div>
</div>

@endsection
