{{--
    Phone field with a separate country dial-code picker (intl-tel-input).
    The visible input only holds the local number; a hidden input named "phone"
    carries the full international number (e.g. +201001234567) to the server.

    Params:
      $label           Label text
      $defaultCountry  ISO code to preselect (e.g. "EG"), usually from VisitorCountryService
      $countrySelectId Optional id of a <select> of ISO country codes to keep in sync
      $autofocus       Optional bool
--}}
@php
    $phoneInvalidMsg = custom_trans('Please enter a valid phone number for the selected country.', 'front');
@endphp
<div class="mb-3">
    <label for="phone" class="form-label">{{ $label }}</label>
    <div dir="ltr">
        <input type="tel" id="phone" value="{{ old('phone') }}" required {{ !empty($autofocus) ? 'autofocus' : '' }}
            class="form-control @error('phone') is-invalid @enderror"
            inputmode="tel" maxlength="20" autocomplete="tel">
    </div>
    <input type="hidden" name="phone" id="phone_full" value="{{ old('phone') }}">
    <div class="invalid-feedback d-block" id="phone-error">@error('phone'){{ $message }}@enderror</div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css">
        <style>
            .iti { width: 100%; }
            .iti .form-control { direction: ltr; text-align: left; }
            .iti__country-list { text-align: left; }
            /* Bootstrap's invalid icon would sit on top of the dial code */
            .iti .form-control.is-invalid { background-image: none; }
        </style>
    @endpush
@endonce
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script>
    (function () {
        var input = document.getElementById('phone');
        var hidden = document.getElementById('phone_full');
        var errorBox = document.getElementById('phone-error');
        var countrySelect = @json($countrySelectId ?? null) ? document.getElementById(@json($countrySelectId ?? null)) : null;
        var invalidMsg = @json($phoneInvalidMsg);
        if (!input || typeof window.intlTelInput !== 'function') {
            // Library failed to load: submit whatever was typed
            if (input) {
                input.setAttribute('name', 'phone');
                if (hidden) hidden.remove();
            }
            return;
        }

        // An already chosen country (e.g. after a validation error) wins over the IP guess
        var initial = (countrySelect && countrySelect.value) || @json($defaultCountry ?? 'EG');

        var iti = window.intlTelInput(input, {
            initialCountry: String(initial).toLowerCase(),
            preferredCountries: ['eg', 'sa', 'ae', 'kw', 'qa', 'bh', 'om', 'jo'],
            separateDialCode: true,
            nationalMode: true,
            autoPlaceholder: 'aggressive',
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js'
        });

        function sync() {
            hidden.value = input.value.trim() ? iti.getNumber() : '';
        }

        function clearError() {
            input.classList.remove('is-invalid');
            errorBox.textContent = '';
        }

        input.addEventListener('input', function () {
            var cleaned = this.value.replace(/[^\d+]/g, '');
            if (cleaned !== this.value) this.value = cleaned;
            clearError();
            sync();
        });

        // Keep the country dropdown and the dial code in step with each other
        var syncingFromSelect = false;
        input.addEventListener('countrychange', function () {
            sync();
            var iso = (iti.getSelectedCountryData().iso2 || '').toUpperCase();
            if (countrySelect && !syncingFromSelect && iso && countrySelect.value !== iso
                && countrySelect.querySelector('option[value="' + iso + '"]')) {
                countrySelect.value = iso;
                countrySelect.dispatchEvent(new Event('change'));
            }
        });
        if (countrySelect) {
            if (!countrySelect.value && countrySelect.querySelector('option[value="' + String(initial).toUpperCase() + '"]')) {
                countrySelect.value = String(initial).toUpperCase();
            }
            countrySelect.addEventListener('change', function () {
                if (!this.value) return;
                syncingFromSelect = true;
                iti.setCountry(this.value.toLowerCase());
                syncingFromSelect = false;
            });
        }

        // Registered before other submit handlers on the page, so an invalid number stops them all
        input.form.addEventListener('submit', function (e) {
            sync();
            var ok = window.intlTelInputUtils ? iti.isValidNumber() : /^\+[1-9]\d{7,14}$/.test(hidden.value);
            if (!input.value.trim() || !ok) {
                e.preventDefault();
                e.stopImmediatePropagation();
                input.classList.add('is-invalid');
                errorBox.textContent = invalidMsg;
                input.focus();
            }
        });

        sync();
    })();
</script>
