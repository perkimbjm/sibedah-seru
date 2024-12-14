@extends('layouts.main')
@php
    $menuName = 'FAQ';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp
@section('content')

<div class="card shadow-sm rounded-md border-0 mx-5">
    <div class="card-header bg-primary text-white text-center">
        <h5 class="mb-0">Detail FAQ</h5>
    </div>
    <div class="card-body p-4">
        <h3 class="card-title font-weight-bold text-lg text-dark">{{ $faq->question }}</h3>
        <p class="card-text mt-3 text-lg"><strong>Jawaban:</strong> {{ $faq->answer }}</p>

        <div class="mt-4 text-center">
            <a href="{{ route('app.faqs.edit', $faq->id) }}" class="btn btn-warning btn-sm px-4 me-2">Edit</a>
            <a href="{{ route('app.faqs.index') }}" class="btn btn-secondary btn-sm px-4">Kembali</a>
        </div>
    </div>
</div>

@endsection
