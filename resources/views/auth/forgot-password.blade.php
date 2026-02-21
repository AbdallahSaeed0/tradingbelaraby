@extends('layouts.app')

@section('title', custom_trans('Forgot Password', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-520">
        <h2 class="text-center mb-4">{{ custom_trans("I don't remember my password", 'front') }}</h2>

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <p class="text-center mb-4 text-muted">{{ custom_trans('Enter your email and we will send you a 6-digit OTP to reset your password.', 'front') }}</p>

        <form method="POST" action="{{ route('password.forgot.send') }}" class="card p-4 shadow-sm">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">{{ custom_trans('Email address', 'front') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="{{ custom_trans('Email address', 'front') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ custom_trans('Send OTP', 'front') }}</button>
        </form>

        <p class="text-center mt-3">
            <a href="{{ route('login') }}">{{ custom_trans('Back to login', 'front') }}</a>
        </p>
    </div>
@endsection
