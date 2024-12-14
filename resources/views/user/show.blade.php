@extends('layouts.main')
@php
    $menuName = 'Profil Pengguna';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card">
    <div class="card-header">
        Profil Pengguna
    </div>
    <div class="card-body text-center">
        <div class="profile-header">
            @if($user->profile_photo_path)
                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto Profil" class="img-thumbnail rounded-circle" width="150" height="150">
            @else
                <img src="https://via.placeholder.com/150" alt="Foto Profil" class="img-thumbnail rounded-circle" width="150" height="150">
            @endif
            <h3 class="mt-3">{{ $user->name }}</h3>
            <p class="text-muted">
                <i class="fas fa-envelope"></i> {{ $user->email }}
            </p>
            <p class="text-muted">
                <i class="fas fa-phone"></i> {{ $user->phone ?? 'Belum dicantumkan' }}
            </p>
        </div>

        <div class="profile-details mt-4">
            <h5 class="mb-3">Detail Pengguna</h5>
            <p><strong>Role:</strong> {{ $user->role->name ?? '-' }}</p>
            <p><strong>Email Terverifikasi:</strong> {{ $user->email_verified_at ? 'Ya' : 'Tidak' }}</p>
            <p><strong>Terdaftar Sejak:</strong> {{ $user->created_at->format('d M Y') }}</p>
        </div>

        <div class="mt-4">
            <a href="{{ route('app.users.edit', $user->id) }}" class="btn btn-warning">Edit Profil</a>
            <a href="{{ route('app.users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
</div>

@endsection