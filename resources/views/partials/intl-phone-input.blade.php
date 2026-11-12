{{--
    Phone field with a separate country dial-code picker (intl-tel-input).
    The visible input only holds the local number; a hidden input carrying the field's
    name holds the full international number (e.g. +201001234567) sent to the server.
    Safe to include several times on one page (each field needs its own $id).

    Params (all optional):
      $name            Form field name (default "phone")
      $id              Visible input id (default = $name)
      $value           Initial value (default old($name))
      $label           Label text; omit for no label
      $labelClass      Label classes (default "form-label")
      $wrapperClass    Wrapper classes (default "mb-3")
      $inputClass      Extra classes for the input (e.g. "form-control-sm")
      $placeholder     Placeholder; by default the country's example number is shown
      $required        Whether the field is required (default true)
      $defaultCountry  ISO code to preselect (e.g. "EG"). When omitted, the visitor's
                       country is looked up in the background (see /visitor-country)
      $countrySelectId Optional id of a <select> of ISO country codes to keep in sync
      $autofocus       Optional bool
--}}
@php
    $name = $name ?? 'phone';
    $id = $id ?? $name;
    $value = $value ?? old($name);
    $required = $required ?? true;
    $wrapperClass = $wrapperClass ?? 'mb-3';
    $labelClass = $labelClass ?? 'form-label';
    $inputClass = $inputClass ?? '';
@endphp
<div class="{{ $wrapperClass }}">
    @if (!empty($label))
        <label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }}</label>
    @endif
    <div dir="ltr">
        {{-- Submits as-is until the picker loads; then the hidden input below takes over the name --}}
        <input type="tel" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" {{ $required ? 'required' : '' }}
            {{ !empty($autofocus) ? 'autofocus' : '' }}
            @if (!empty($placeholder)) placeholder="{{ $placeholder }}" @endif
            class="form-control {{ $inputClass }} @error($name) is-invalid @enderror"
            inputmode="tel" maxlength="20" autocomplete="tel">
    </div>
    <input type="hidden" data-name="{{ $name }}" id="{{ $id }}_full" value="{{ $value }}">
    <div class="invalid-feedback d-block" id="{{ $id }}-error">@error($name){{ $message }}@enderror</div>
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
    <script>
        (function () {
            var STORE_KEY = 'tb_visitor_country';
            var lookup = null;

            // Load the picker library without blocking the page; fields wait in a queue until it is ready
            var state = 'loading', queue = [];
            var lib = document.createElement('script');
            lib.src = 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js';
            lib.async = true;
            lib.onload = function () { state = 'ready'; queue.splice(0).forEach(init); };
            lib.onerror = function () { state = 'failed'; queue.length = 0; };
            document.head.appendChild(lib);
            window.tbIntlPhone = function (o) {
                if (state === 'ready') init(o);
                else if (state === 'loading') queue.push(o);
                // failed: the plain input keeps its name and submits what was typed
            };

            // Visitor's country (ISO, uppercase) from our server, cached in the browser for a day
            function visitorCountry() {
                if (lookup) return lookup;
                lookup = fetch(@json(route('visitor-country')), { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (d) {
                        var cc = d && /^[A-Z]{2}$/.test(d.country) ? d.country : null;
                        if (cc) {
                            try { localStorage.setItem(STORE_KEY, JSON.stringify({ cc: cc, t: Date.now() })); } catch (e) {}
                        }
                        return cc;
                    })
                    .catch(function () { return null; });
                return lookup;
            }

            function storedCountry() {
                try {
                    var s = JSON.parse(localStorage.getItem(STORE_KEY) || 'null');
                    return s && s.cc && Date.now() - s.t < 864e5 ? s.cc : null;
                } catch (e) { return null; }
            }

            function init(o) {
                var input = document.getElementById(o.id);
                var hidden = document.getElementById(o.id + '_full');
                var errorBox = document.getElementById(o.id + '-error');
                if (!input || !hidden || typeof window.intlTelInput !== 'function') return;

                // From now on the hidden input carries the full international number
                hidden.setAttribute('name', hidden.getAttribute('data-name'));
                input.removeAttribute('name');

                var countrySelect = o.countrySelectId ? document.getElementById(o.countrySelectId) : null;
                var known = (countrySelect && countrySelect.value) || o.defaultCountry || storedCountry();
                var initial = known || 'EG';

                var iti = window.intlTelInput(input, {
                    initialCountry: String(initial).toLowerCase(),
                    preferredCountries: ['eg', 'sa', 'ae', 'kw', 'qa', 'bh', 'om', 'jo'],
                    separateDialCode: true,
                    nationalMode: true,
                    autoPlaceholder: input.getAttribute('placeholder') ? 'off' : 'aggressive',
                    utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js'
                });

                // Country unknown yet: ask the server, unless the visitor already chose or typed
                var userTouched = false;
                if (!known) {
                    visitorCountry().then(function (cc) {
                        if (cc && !userTouched && !input.value.trim()) iti.setCountry(cc.toLowerCase());
                    });
                }

                function sync() {
                    hidden.value = input.value.trim() ? iti.getNumber() : '';
                }

                function clearError() {
                    input.classList.remove('is-invalid');
                    if (errorBox) errorBox.textContent = '';
                }

                input.addEventListener('input', function () {
                    userTouched = true;
                    var cleaned = this.value.replace(/[^\d+]/g, '');
                    if (cleaned !== this.value) this.value = cleaned;
                    clearError();
                    sync();
                });
                input.addEventListener('open:countrydropdown', function () { userTouched = true; });

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
                        userTouched = true;
                        syncingFromSelect = true;
                        iti.setCountry(this.value.toLowerCase());
                        syncingFromSelect = false;
                    });
                }

                // Capture phase: runs before the page's own submit handlers, so an invalid number stops them
                if (input.form) {
                    input.form.addEventListener('submit', function (e) {
                        sync();
                        var empty = !input.value.trim();
                        if (empty && !o.required) return;
                        var ok = !empty && (window.intlTelInputUtils ? iti.isValidNumber() : /^\+[1-9]\d{7,14}$/.test(hidden.value));
                        if (!ok) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            input.classList.add('is-invalid');
                            if (errorBox) errorBox.textContent = o.invalidMsg;
                            input.focus();
                        }
                    }, true);
                    // Forms reset by script (e.g. after an AJAX submit) also clear the number
                    input.form.addEventListener('reset', function () {
                        setTimeout(function () { clearError(); sync(); }, 0);
                    });
                }

                sync();
            }
        })();
    </script>
@endonce
<script>
    window.tbIntlPhone && window.tbIntlPhone({
        id: @json($id),
        required: @json((bool) $required),
        defaultCountry: @json($defaultCountry ?? null),
        countrySelectId: @json($countrySelectId ?? null),
        invalidMsg: @json(custom_trans('Please enter a valid phone number for the selected country.', 'front'))
    });
</script>
