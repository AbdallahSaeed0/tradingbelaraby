@extends('layouts.app')

@section('title', custom_trans('Set New Password', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-520">
        <h2 class="text-center mb-4">{{ custom_trans('Set New Password', 'front') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <p class="text-center mb-4 text-muted">{{ custom_trans('Enter your new password below.', 'front') }}</p>

        <form method="POST" action="{{ route('password.reset.attempt') }}" class="card p-4 shadow-sm">
            @csrf

            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="otp" value="{{ $otp }}">

            <div class="mb-3">
                <label for="password" class="form-label">{{ custom_trans('New Password', 'front') }}</label>
                <input type="password" name="password" id="password" required
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ custom_trans('New Password', 'front') }}">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">{{ custom_trans('Confirm Password', 'front') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="form-control"
                    placeholder="{{ custom_trans('Confirm Password', 'front') }}">
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ custom_trans('Reset Password', 'front') }}</button>
        </form>

        <p class="text-center mt-3">
            <a href="{{ route('password.forgot.form') }}">{{ custom_trans('Start over', 'front') }}</a>
        </p>
        <p class="text-center mt-2">
            <a href="{{ route('login') }}">{{ custom_trans('Back to login', 'front') }}</a>
        </p>
    </div>
@endsection
