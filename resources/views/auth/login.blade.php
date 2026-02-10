@extends('layouts.app')

@section('title', custom_trans('Login', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-520">
        <h2 class="text-center mb-4">{{ custom_trans('Login', 'front') }}</h2>

        <form method="POST" action="{{ route('login.attempt') }}" class="card p-4 shadow-sm">
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

            <div class="mb-3">
                <label for="password" class="form-label">{{ custom_trans('Password', 'front') }}</label>
                <input type="password" name="password" id="password" required
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="{{ custom_trans('Password', 'front') }}">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">{{ custom_trans('Remember me', 'front') }}</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ custom_trans('Sign in', 'front') }}</button>
        </form>

        <p class="text-center mt-3">{{ custom_trans("Don't have an account?", 'front') }} <a href="{{ route('register') }}">{{ custom_trans('Sign up', 'front') }}</a></p>
    </div>
@endsection
