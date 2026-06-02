@extends('layouts.app')

@section('title', custom_trans('Verify WhatsApp', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-520">
        <div class="card p-4 shadow-sm">
            <div class="text-center mb-4">
                <span style="font-size: 3rem;">📱</span>
                <h2 class="mt-2">{{ custom_trans('Verify Your WhatsApp Number', 'front') }}</h2>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <p class="text-center text-muted mb-4">
                {{ custom_trans('A 6-digit verification code has been sent to your WhatsApp number', 'front') }}
                @if(Auth::user()?->phone)
                    <strong>{{ substr(Auth::user()->phone, 0, -4) . '****' }}</strong>.
                @else
                    .
                @endif
                {{ custom_trans('Please enter it below to confirm your number.', 'front') }}
            </p>

            <form method="POST" action="{{ route('whatsapp.verify.attempt') }}">
                @csrf

                <div class="mb-3">
                    <label for="otp" class="form-label">{{ custom_trans('Verification Code', 'front') }}</label>
                    <input type="text" name="otp" id="otp" value="{{ old('otp') }}" required autofocus
                        maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                        class="form-control form-control-lg text-center @error('otp') is-invalid @enderror"
                        placeholder="000000">
                    @error('otp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    {{ custom_trans('Verify Number', 'front') }}
                </button>
            </form>

            <div class="mt-3 text-center">
                <form method="POST" action="{{ route('whatsapp.verify.resend') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 text-decoration-none">
                        {{ custom_trans('Resend code to WhatsApp', 'front') }}
                    </button>
                </form>
            </div>

            <div class="mt-2 text-center">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ custom_trans('Back to login', 'front') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
@endsection
