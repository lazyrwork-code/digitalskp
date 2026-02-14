@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')

<div class="container" style="max-width:500px">

    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="fw-bold mb-3 text-center">
                Reset Password
            </h4>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>

                    <input type="email"
                           name="email"
                           value="{{ old('email', request()->email) }}"
                           class="form-control @error('email') is-invalid @enderror"
                           required>

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>

                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required>

                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password</label>

                    <input type="password"
                           name="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           required>

                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary w-100">
                    Reset Password
                </button>

            </form>

        </div>
    </div>

</div>

@endsection
