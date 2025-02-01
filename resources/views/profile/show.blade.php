@extends('layouts.main')

@php
    $menuName = str_replace('.', ' ', Route::currentRouteName());
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));

    use Laravel\Fortify\Features as FortifyFeatures;

@endphp

@section('content')
<div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
    @if (FortifyFeatures::enabled(FortifyFeatures::updateProfileInformation()))
        @include('profile.partials.update-profile-information-form')
        @include('components.section-border')
    @endif

    @if (FortifyFeatures::enabled(FortifyFeatures::updatePasswords()))
        @include('profile.partials.update-password-form')
        @include('components.section-border')
    @endif

    {{-- @if (FortifyFeatures::enabled(FortifyFeatures::twoFactorAuthentication()))
        @include('profile.partials.two-factor-authentication', [
            'requiresConfirmation' => $confirmsTwoFactorAuthentication,
        ])
        @include('components.section-border')
    @endif --}}

    @include('profile.partials.logout-other-browser-sessions-form', [
        'sessions' => $sessions,
    ])

    @include('components.section-border')
    @include('profile.partials.delete-user-form')

</div>
@endsection

