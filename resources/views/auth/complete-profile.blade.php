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
                <div class="mb-3">
                    <label for="phone" class="form-label">{{ custom_trans('Mobile number', 'front') }}</label>
                    <div dir="ltr">
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required {{ $needsEmail ? '' : 'autofocus' }}
                            class="form-control @error('phone') is-invalid @enderror"
                            inputmode="tel"
                            maxlength="20"
                            autocomplete="tel">
                    </div>
                    <div class="invalid-feedback d-block" id="phone-error">@error('phone'){{ $message }}@enderror</div>
                </div>
            @endif

            <button type="submit" class="btn btn-primary w-100">{{ custom_trans('Save and continue', 'front') }}</button>
        </form>
    </div>

    @if($needsPhone)
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">
        <style>
            .iti { width: 100%; }
            .iti .form-control { direction: ltr; text-align: left; }
            .iti__country-list { text-align: left; }
        </style>
    @endpush
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
    <script>
        (function () {
            var input = document.getElementById('phone');
            var errorBox = document.getElementById('phone-error');
            if (!input || typeof window.intlTelInput !== 'function') return;

            var iti = window.intlTelInput(input, {
                initialCountry: @json($defaultCountry ?? 'eg'),
                preferredCountries: ['eg', 'sa', 'ae', 'kw', 'qa', 'bh', 'om', 'jo'],
                separateDialCode: true,
                nationalMode: true,
                autoPlaceholder: 'aggressive',
                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js'
            });

            var invalidMsg = @json(custom_trans('Please enter a valid phone number for the selected country.', 'front'));

            // Digits only in the local part (the dial code is shown separately)
            input.addEventListener('input', function () {
                var cleaned = this.value.replace(/[^\d+]/g, '');
                if (cleaned !== this.value) this.value = cleaned;
                input.classList.remove('is-invalid');
                errorBox.textContent = '';
            });

            input.form.addEventListener('submit', function (e) {
                var number = iti.getNumber();
                var utilsReady = !!window.intlTelInputUtils;
                var ok = utilsReady ? iti.isValidNumber() : /^\+[1-9]\d{7,14}$/.test(number);
                if (!input.value.trim() || !ok) {
                    e.preventDefault();
                    input.classList.add('is-invalid');
                    errorBox.textContent = invalidMsg;
                    input.focus();
                    return;
                }
                // Submit the full international number, e.g. +201001234567
                input.value = number;
            });
        })();
    </script>
    @endif
@endsection
