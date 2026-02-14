@extends('layouts.app')

@section('title', 'Profile')

@section('content')

<div class="container">
    <h3 class="fw-bold mb-4">Profile Saya</h3>

    @if(request('tab') == 'password')

        {{-- PASSWORD ONLY --}}
        <div class="card shadow-sm">
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

    @else

        {{-- PROFILE ONLY --}}
        <div class="card shadow-sm">
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

    @endif

</div>

@endsection
