{{--
    Quick sign-up modal for guests on a course page.
    Any link with [data-quick-signup] opens it instead of going to the full /register page
    (the link still works without JS). Submitting creates the account, enrolls in the
    course when it is free, and goes straight to the "you're enrolled" page.
    Needs: $course
--}}
@php
    $quickSignupGoogle = app(\App\Services\SocialLoginSettingsService::class)->isGoogleEnabled();
    $quickSignupTitle = $course->requiresPayment()
        ? custom_trans('Create your account to continue', 'front')
        : custom_trans('Join for free in seconds', 'front');
@endphp

<div class="modal fade" id="quickSignupModal" tabindex="-1" aria-labelledby="quickSignupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0 align-items-start">
                <div class="flex-grow-1 min-w-0">
                    <h5 class="modal-title fw-bold" id="quickSignupModalLabel">{{ $quickSignupTitle }}</h5>
                    <div class="text-muted small mt-1">{{ $course->localized_name }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ custom_trans('Close', 'front') }}"></button>
            </div>
            <div class="modal-body pt-3">
                @if ($quickSignupGoogle)
                    @include('partials.auth.google-button', ['enrollCourseId' => $course->id])
                    <div class="text-center text-muted small my-3">{{ custom_trans('or', 'front') }}</div>
                @endif

                <form id="quickSignupForm" action="{{ route('register.attempt') }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="enroll" value="{{ $course->id }}">
                    <input type="hidden" name="form_start_time" value="{{ time() }}">
                    <div style="position: absolute; width: 1px; height: 1px; overflow: hidden; clip-path: inset(50%); white-space: nowrap;" aria-hidden="true">{{-- clipped, not pushed off-screen: left -9999px made RTL pages scroll sideways --}}
                        <label for="qs_website">Website (leave blank)</label>
                        <input type="text" name="website" id="qs_website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label for="qs_name" class="form-label">{{ custom_trans('Name', 'front') }}</label>
                        <input type="text" name="name" id="qs_name" class="form-control" required autocomplete="name">
                        <div class="invalid-feedback" data-error-for="name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="qs_email" class="form-label">{{ custom_trans('Email address', 'front') }}</label>
                        <input type="email" name="email" id="qs_email" class="form-control" required autocomplete="email" inputmode="email">
                        <div class="invalid-feedback" data-error-for="email"></div>
                    </div>
                    <div class="mb-3">
                        <label for="qs_password" class="form-label">{{ custom_trans('Password', 'front') }}</label>
                        <input type="password" name="password" id="qs_password" class="form-control" required minlength="6" autocomplete="new-password">
                        <div class="invalid-feedback" data-error-for="password"></div>
                    </div>

                    <div class="alert alert-danger small d-none" data-error-general></div>

                    <button type="submit" class="btn btn-orange w-100 fw-bold" data-submit>
                        <i class="fas fa-graduation-cap me-2"></i>{{ $course->requiresPayment() ? custom_trans('Create account', 'front') : custom_trans('Join now', 'front') }}
                    </button>
                </form>

                <p class="text-center small mt-3 mb-0">
                    {{ custom_trans('Already have an account?', 'front') }}
                    <a href="{{ route('login', ['enroll' => $course->id]) }}" class="fw-bold">{{ custom_trans('Sign in', 'front') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const modalEl = document.getElementById('quickSignupModal');
        const form = document.getElementById('quickSignupForm');
        if (!modalEl || !form) return;

        const openedAt = Date.now();
        const submitBtn = form.querySelector('[data-submit]');
        const submitHtml = submitBtn.innerHTML;
        const generalError = form.querySelector('[data-error-general]');
        const genericMsg = @json(custom_trans('An unexpected error occurred. Please try again later.', 'front'));
        const waitMsg = @json(custom_trans('Please take your time filling the form.', 'front'));

        // Open the modal from any guest "Enroll" link (fallback: the link goes to /register)
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-quick-signup]');
            if (!trigger || typeof bootstrap === 'undefined') return;
            e.preventDefault();
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
        modalEl.addEventListener('shown.bs.modal', function () {
            // Desktop only: on phones the keyboard would pop up and hide the Google button
            if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
                document.getElementById('qs_name').focus();
            }
        });

        function clearErrors() {
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('[data-error-for]').forEach(el => { el.textContent = ''; });
            generalError.classList.add('d-none');
            generalError.textContent = '';
        }

        function showErrors(errors) {
            let shown = false;
            Object.keys(errors || {}).forEach(function (field) {
                const input = form.querySelector('[name="' + field + '"]');
                const box = form.querySelector('[data-error-for="' + field + '"]');
                const msg = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                if (input && box) {
                    input.classList.add('is-invalid');
                    box.textContent = msg;
                    shown = true;
                } else {
                    generalError.textContent = msg;
                    generalError.classList.remove('d-none');
                    shown = true;
                }
            });
            if (!shown) {
                generalError.textContent = genericMsg;
                generalError.classList.remove('d-none');
            }
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            clearErrors();

            if (!form.checkValidity()) {
                form.querySelectorAll('input[required]').forEach(function (input) {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                        const box = form.querySelector('[data-error-for="' + input.name + '"]');
                        if (box) box.textContent = input.validationMessage;
                    }
                });
                return;
            }

            if (Date.now() - openedAt < 3000) {
                showErrors({ general: waitMsg });
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>' + submitHtml;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                });
                const data = await response.json().catch(() => ({}));

                if (response.ok && data.success && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }

                showErrors(data.errors || (data.message ? { general: data.message } : null));
            } catch (err) {
                showErrors(null);
            }

            submitBtn.disabled = false;
            submitBtn.innerHTML = submitHtml;
        });
    })();
</script>
@endpush
