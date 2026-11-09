@extends('layouts.app')

@section('title', custom_trans('Complete your profile', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-520">
        <h2 class="text-center mb-4">{{ custom_trans('Complete your profile', 'front') }}</h2>
        <p class="text-center text-muted mb-4">
            @if($needsEmail && $needsPhone)
                {{ custom_trans('You signed in with a social account. Please enter your email and phone number to complete your profile.', 'front') }}
            @elseif($needsEmail)
                {{ custom_trans('You signed in with X. To complete your account, please enter your email address.', 'front') }}
            @else
                {{ custom_trans('Please add your phone number to complete your profile.', 'front') }}
            @endif
        </p>

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.x.complete-profile.save') }}" class="card p-4 shadow-sm">
            @csrf

            @if($needsEmail)
                <div class="mb-3">
                    <label for="email" class="form-label">{{ custom_trans('Email address', 'front') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" {{ $needsEmail ? 'required' : '' }} autofocus
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="{{ custom_trans('e.g. name@example.com', 'front') }}"
                        autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            @if($needsPhone)
                @include('partials.intl-phone-input', [
                    'label' => custom_trans('Mobile number', 'front'),
                    'defaultCountry' => $defaultCountry ?? 'EG',
                    'autofocus' => !$needsEmail,
                ])
            @endif

            <button type="submit" class="btn btn-primary w-100">{{ custom_trans('Save and continue', 'front') }}</button>
        </form>
    </div>
@endsection
