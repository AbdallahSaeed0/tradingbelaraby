<!DOCTYPE html>
<html lang="{{ $language }}" dir="{{ $language === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ custom_trans('title', 'front') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if ($language === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
    @endif</head>

<body>
    <div class="coming-soon-container">
        @php
            $mainContentSettings = \App\Models\MainContentSettings::getActive();
        @endphp
        <!-- Left Section - Brand Panel -->
        <div class="left-section" role="banner" aria-label="{{ custom_trans('title', 'front') }}">
            <div class="logo" aria-hidden="true">
                <img src="{{ $mainContentSettings ? $mainContentSettings->logo_url : asset('images/default-logo.svg', 'front') }}"
                    alt="{{ $mainContentSettings ? $mainContentSettings->logo_alt_text : 'Site Logo' }}"
                    class="logo-image">
            </div>
            <h1 class="coming-soon-title">{{ custom_trans('title', 'front') }}</h1>
            <p class="coming-soon-subtitle">{{ custom_trans('subtitle', 'front') }}</p>
        </div>

        <!-- Right Section - Form -->
        <div class="right-section">
            <h2 class="form-title">{{ custom_trans('sign_with_us', 'front') }}</h2>

            <div id="alert-container" role="alert" aria-live="polite"></div>

            <form id="subscriptionForm" role="form" aria-label="{{ custom_trans('sign_with_us', 'front') }}">
                @csrf
                <input type="hidden" name="language" value="{{ $language }}">

                <div class="form-group">
                    <label for="name" class="form-label">{{ custom_trans('name', 'front') }} *</label>
                    <input type="text" class="form-control" id="name" name="name" required
                        aria-describedby="name-help" placeholder="{{ custom_trans('name', 'front') }}">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">{{ custom_trans('phone', 'front') }} *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required
                        aria-describedby="phone-help" placeholder="{{ custom_trans('phone', 'front') }}"
                        pattern="[0-9+\-\s\(\)]+" inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9+\-\s\(\)]/g, '')">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">{{ custom_trans('email', 'front') }} *</label>
                    <input type="email" class="form-control" id="email" name="email" required
                        aria-describedby="email-help" placeholder="{{ custom_trans('email', 'front') }}">
                </div>

                <div class="form-group">
                    <label for="whatsapp_number" class="form-label">{{ custom_trans('whatsapp_number', 'front') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fab fa-whatsapp text-success"></i>
                        </span>
                        <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number"
                            placeholder="{{ custom_trans('whatsapp_number', 'front') }}" pattern="^\+?[1-9]\d{1,14}$"
                            inputmode="tel" maxlength="20" autocomplete="tel" data-format="international">
                        <div class="invalid-feedback" id="whatsapp-error"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">{{ custom_trans('country', 'front') }} *</label>
                    <input type="text" class="form-control" id="country" name="country" required
                        aria-describedby="country-help" placeholder="{{ custom_trans('country', 'front') }}">
                </div>

                <div class="form-group">
                    <label for="years_of_experience" class="form-label">{{ custom_trans('years_of_experience', 'front') }}
                        *</label>
                    <select class="form-select" id="years_of_experience" name="years_of_experience" required
                        aria-describedby="experience-help">
                        <option value="">{{ custom_trans('select_experience', 'front') }}</option>
                        <option value="10">10 {{ custom_trans('years', 'front') }}</option>
                        <option value="20">20 {{ custom_trans('years', 'front') }}</option>
                        <option value="30">30 {{ custom_trans('years', 'front') }}</option>
                        <option value="40">40 {{ custom_trans('years', 'front') }}</option>
                        <option value="50">50 {{ custom_trans('years', 'front') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">{{ custom_trans('notes', 'front') }}</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4"
                        placeholder="{{ custom_trans('notes_placeholder', 'front') }}" aria-describedby="notes-help"></textarea>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" aria-describedby="submit-help">
                    {{ custom_trans('submit', 'front') }}
                    <i class="fas fa-spinner fa-spin loading-spinner" id="loadingSpinner" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // WhatsApp Number Input Enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const whatsappInput = document.getElementById('whatsapp_number');
            const whatsappError = document.getElementById('whatsapp-error');

            // Common country codes for auto-completion
            const countryCodes = [
                '+1', '+44', '+33', '+49', '+39', '+34', '+31', '+32', '+41', '+46',
                '+47', '+45', '+358', '+46', '+47', '+45', '+358', '+46', '+47', '+45',
                '+20', '+27', '+234', '+254', '+234', '+254', '+234', '+254', '+234', '+254',
                '+91', '+86', '+81', '+82', '+65', '+60', '+66', '+84', '+62', '+63',
                '+971', '+966', '+974', '+973', '+965', '+968', '+973', '+965', '+968', '+973'
            ];

            // Format phone number as user types
            whatsappInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove all non-digits

                // Auto-add + if it starts with a number
                if (value.length > 0 && !value.startsWith('+')) {
                    value = '+' + value;
                }

                // Format the number
                if (value.length > 1) {
                    // Remove the + for formatting
                    let number = value.substring(1);

                    // Format based on length
                    if (number.length <= 3) {
                        value = '+' + number;
                    } else if (number.length <= 6) {
                        value = '+' + number.substring(0, 3) + ' ' + number.substring(3);
                    } else if (number.length <= 10) {
                        value = '+' + number.substring(0, 3) + ' ' + number.substring(3, 6) + ' ' + number
                            .substring(6);
                    } else {
                        value = '+' + number.substring(0, 3) + ' ' + number.substring(3, 6) + ' ' + number
                            .substring(6, 10) + ' ' + number.substring(10);
                    }
                }

                e.target.value = value;
                validateWhatsAppNumber();
            });



            // Validate WhatsApp number
            function validateWhatsAppNumber() {
                const value = whatsappInput.value.replace(/\D/g, '');
                const isValid = /^[1-9]\d{6,14}$/.test(value);

                if (value.length > 0) {
                    if (!isValid) {
                        whatsappInput.classList.add('is-invalid');
                        whatsappError.textContent = 'Please enter a valid WhatsApp number with country code';
                        return false;
                    } else {
                        whatsappInput.classList.remove('is-invalid');
                        whatsappInput.classList.add('is-valid');
                        whatsappError.textContent = '';
                        return true;
                    }
                } else {
                    whatsappInput.classList.remove('is-invalid', 'is-valid');
                    whatsappError.textContent = '';
                    return true; // Empty is valid since it's optional
                }
            }

            // Validate on blur
            whatsappInput.addEventListener('blur', validateWhatsAppNumber);

            // Add keyboard shortcuts
            whatsappInput.addEventListener('keydown', function(e) {
                // Allow: backspace, delete, tab, escape, enter, and navigation keys
                if ([8, 9, 27, 13, 46, 37, 38, 39, 40].indexOf(e.keyCode) !== -1 ||
                    // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true)) {
                    return;
                }

                // Allow numbers and +
                if ((e.keyCode >= 48 && e.keyCode <= 57) || e.keyCode === 187) {
                    return;
                }

                e.preventDefault();
            });
        });

        document.getElementById('subscriptionForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const alertContainer = document.getElementById('alert-container');
            const whatsappInput = document.getElementById('whatsapp_number');

            // Validate WhatsApp number before submission
            const whatsappValue = whatsappInput.value.replace(/\D/g, '');
            if (whatsappValue.length > 0 && !/^[1-9]\d{6,14}$/.test(whatsappValue)) {
                whatsappInput.classList.add('is-invalid');
                document.getElementById('whatsapp-error').textContent =
                    'Please enter a valid WhatsApp number with country code';
                whatsappInput.focus();
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            loadingSpinner.style.display = 'inline-block';

            // Clear previous alerts
            alertContainer.innerHTML = '';

            const formData = new FormData(this);

            // Clean WhatsApp number before sending (remove spaces and formatting)
            if (formData.get('whatsapp_number')) {
                formData.set('whatsapp_number', formData.get('whatsapp_number').replace(/\s/g, ''));
            }

            try {
                const response = await fetch('{{ route('coming-soon.subscribe', 'front') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
                            ${data.message}
                        </div>
                    `;
                    this.reset();
                } else {
                    let errorMessage = data.message || '{{ custom_trans('coming_soon.subscription_error', 'front') }}';

                    if (data.errors) {
                        const errors = Object.values(data.errors).flat();
                        errorMessage = errors.join('<br>');
                    }

                    alertContainer.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2" aria-hidden="true"></i>
                            ${errorMessage}
                        </div>
                    `;
                }
            } catch (error) {
                alertContainer.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2" aria-hidden="true"></i>
                            {{ custom_trans('subscription_error', 'front') }}
                        </div>
                    `;
            } finally {
                // Hide loading state
                submitBtn.disabled = false;
                loadingSpinner.style.display = 'none';
            }
        });
    </script>
</body>

</html>

