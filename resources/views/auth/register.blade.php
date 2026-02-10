@extends('layouts.app')

@section('title', custom_trans('Register', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <div class="container py-5 max-w-560">
        <h2 class="text-center mb-4">{{ custom_trans('Create an Account', 'front') }}</h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('register.attempt') }}" class="card p-4 shadow-sm" id="registerForm"
            data-check-email-url="{{ route('register.check-email') }}"
            data-email-taken-msg="{{ custom_trans('This email is already registered. Please sign in or use a different email.', 'front') }}">
            @csrf
            <input type="hidden" name="form_start_time" value="{{ time() }}">
            
            {{-- Honeypot field - hidden from users but bots will fill it --}}
            <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                <label for="website">Website (leave blank)</label>
                <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">{{ custom_trans('Name', 'front') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="{{ custom_trans('Name', 'front') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="register_email" class="form-label">{{ custom_trans('Email address', 'front') }}</label>
                <input type="email" name="email" id="register_email" value="{{ old('email') }}" required
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="{{ custom_trans('Email address', 'front') }}">
                <div class="invalid-feedback" id="email-js-error" style="display: none;"></div>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="country" class="form-label">{{ custom_trans('Country', 'front') }}</label>
                <select name="country" id="country" required
                    class="form-select @error('country') is-invalid @enderror">
                    <option value="">{{ custom_trans('Select Country', 'front') }}</option>
                    @include('partials.countries')
                </select>
                @error('country')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">{{ custom_trans('Phone Number', 'front') }}</label>
                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder="{{ custom_trans('Phone Number', 'front') }}">
                @error('phone')
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

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">{{ custom_trans('Confirm Password', 'front') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="form-control"
                    placeholder="{{ custom_trans('Confirm Password', 'front') }}">
            </div>

            <button type="submit" class="btn btn-primary w-100" id="submitBtn">{{ custom_trans('Register', 'front') }}</button>
        </form>

        <p class="text-center mt-3">{{ custom_trans('Already have an account?', 'front') }} <a href="{{ route('login') }}">{{ custom_trans('Sign in', 'front') }}</a></p>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('register_email');
            const emailJsError = document.getElementById('email-js-error');
            const checkEmailUrl = form.dataset.checkEmailUrl;
            const emailTakenMsg = form.dataset.emailTakenMsg;
            let formStartTime = Date.now();

            function showEmailError(msg) {
                emailInput.classList.add('is-invalid');
                emailJsError.textContent = msg;
                emailJsError.style.display = 'block';
            }
            function clearEmailError() {
                emailInput.classList.remove('is-invalid');
                emailJsError.textContent = '';
                emailJsError.style.display = 'none';
            }
            emailInput.addEventListener('input', clearEmailError);
            emailInput.addEventListener('blur', clearEmailError);

            // Update form start time when form is loaded
            const startTimeInput = form.querySelector('input[name="form_start_time"]');
            if (startTimeInput) {
                startTimeInput.value = Math.floor(formStartTime / 1000);
            }

            form.addEventListener('submit', function(e) {
                const timeTaken = (Date.now() - formStartTime) / 1000;
                if (timeTaken < 3) {
                    e.preventDefault();
                    alert('Please take your time filling out the form. Registration forms should take at least a few seconds to complete.');
                    return false;
                }

                // Client-side validation: check email availability before submit (no page refresh)
                e.preventDefault();
                clearEmailError();
                const email = (emailInput.value || '').trim();
                if (!email) {
                    submitBtn.disabled = false;
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>{{ custom_trans("Register", "front") }}...';

                fetch(checkEmailUrl + '?email=' + encodeURIComponent(email), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.available === false) {
                        showEmailError(emailTakenMsg);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '{{ custom_trans("Register", "front") }}';
                        return;
                    }
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>{{ custom_trans("Register", "front") }}...';
                    form.submit();
                })
                .catch(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '{{ custom_trans("Register", "front") }}';
                });
            });
        });
    </script>
    @endpush
@endsection
