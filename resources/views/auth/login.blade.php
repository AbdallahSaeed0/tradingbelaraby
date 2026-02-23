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

            <div class="mb-3 form-check d-flex justify-content-between align-items-center">
                <div>
                    <input type="checkbox" name="remember" id="remember" class="form-check-input"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">{{ custom_trans('Remember me', 'front') }}</label>
                </div>
                <a href="{{ route('password.forgot.form') }}">{{ custom_trans("I don't remember my password", 'front') }}</a>
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ custom_trans('Sign in', 'front') }}</button>

            @if($googleLoginEnabled ?? false)
                <div class="mt-3">
                    <a href="{{ route('auth.google') }}" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path fill="#4285F4" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#34A853" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.16 7.09-10.27 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.82l7.97-6.19z"/><path fill="#EA4335" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                        {{ custom_trans('Continue with Google', 'front') }}
                    </a>
                </div>
            @endif
            @if($twitterLoginEnabled ?? false)
                <div class="mt-2">
                    <a href="{{ route('auth.twitter') }}" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        {{ custom_trans('Continue with X', 'front') }}
                    </a>
                </div>
            @endif
        </form>

        <p class="text-center mt-3">{{ custom_trans("Don't have an account?", 'front') }} <a href="{{ route('register') }}">{{ custom_trans('Sign up', 'front') }}</a></p>
    </div>
@endsection
