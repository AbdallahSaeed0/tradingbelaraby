@extends('layouts.app')

@section('title', custom_trans('Verify OTP', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-520">
        <h2 class="text-center mb-4">{{ custom_trans('Verify OTP', 'front') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <p class="text-center mb-4 text-muted">{{ custom_trans('Enter the 6-digit OTP sent to your email.', 'front') }}</p>
        <p class="text-center mb-4 text-muted small">{{ custom_trans('Email:', 'front') }} <strong>{{ $email }}</strong></p>

        <form method="POST" action="{{ route('password.verify-otp.attempt') }}" class="card p-4 shadow-sm">
            @csrf

            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label for="otp" class="form-label">{{ custom_trans('OTP Code (6 digits)', 'front') }}</label>
                <input type="text" name="otp" id="otp" value="{{ old('otp') }}" required autofocus
                    maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                    class="form-control form-control-lg text-center @error('otp') is-invalid @enderror"
                    placeholder="000000">
                @error('otp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ custom_trans('Verify OTP', 'front') }}</button>
        </form>

        <p class="text-center mt-3">
            <form method="POST" action="{{ route('password.forgot.send') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="btn btn-link p-0 text-decoration-none">{{ custom_trans('Request new OTP', 'front') }}</button>
            </form>
        </p>
        <p class="text-center mt-2">
            <a href="{{ route('login') }}">{{ custom_trans('Back to login', 'front') }}</a>
        </p>
    </div>
@endsection
