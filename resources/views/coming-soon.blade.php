<!DOCTYPE html>
<html lang="{{ $language }}" dir="{{ $language === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ custom_trans('title') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @if ($language === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
    @endif
    <style>
        :root {
            /* Brand Colors */
            --brand-orange: #f15a29;
            --brand-green: #17506b;
            --brand-orange-light: #ff7a5a;
            --brand-green-light: #4b5563;

            /* Neutral Colors */
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;

            /* Semantic Colors */
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;

            /* Gradients */
            --bg-gradient: linear-gradient(135deg, var(--brand-orange) 0%, var(--brand-green) 100%);
            --card-gradient: linear-gradient(135deg, var(--brand-orange-light) 0%, var(--brand-green-light) 100%);
            --button-gradient: linear-gradient(135deg, var(--brand-orange) 0%, var(--brand-green) 100%);

            /* Spacing */
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --spacing-3xl: 4rem;

            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
            --radius-2xl: 1.5rem;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);

            /* Transitions */
            --transition-fast: 150ms ease-in-out;
            --transition-normal: 250ms ease-in-out;
            --transition-slow: 350ms ease-in-out;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: {{ $language === 'ar' ? "'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" : "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" }};
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            line-height: 1.6;
            color: var(--gray-800);
        }

        .coming-soon-container {
            background: var(--white);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 650px;
            position: relative;
        }

        /* Left Section - Brand Panel */
        .left-section {
            background: var(--card-gradient);
            color: var(--white);
            padding: var(--spacing-3xl) var(--spacing-2xl);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .logo {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-2xl);
            font-size: 3rem;
            font-weight: 600;
            color: var(--white);
            transition: var(--transition-normal);
            position: relative;
            z-index: 1;
        }

        .logo:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.2);
        }

        .coming-soon-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: var(--spacing-lg);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
            line-height: 1.2;
        }

        .coming-soon-subtitle {
            font-size: 1.125rem;
            opacity: 0.95;
            line-height: 1.7;
            max-width: 300px;
            position: relative;
            z-index: 1;
            font-weight: 400;
        }

        /* Right Section - Form */
        .right-section {
            padding: var(--spacing-3xl) var(--spacing-2xl);
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .form-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: var(--spacing-2xl);
            text-align: center;
            line-height: 1.3;
        }

        .form-group {
            margin-bottom: var(--spacing-lg);
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: var(--spacing-sm);
            display: block;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .form-control,
        .form-select {
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: var(--spacing-md) var(--spacing-lg);
            font-size: 1rem;
            transition: var(--transition-normal);
            width: 100%;
            background: var(--white);
            color: var(--gray-800);
            font-family: inherit;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand-orange);
            box-shadow: 0 0 0 3px rgba(246, 87, 47, 0.1);
            outline: none;
            background: var(--white);
        }

        .form-control:hover,
        .form-select:hover {
            border-color: var(--gray-300);
        }

        .form-control::placeholder {
            color: var(--gray-400);
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: {{ $language === 'ar' ? 'left 0.75rem center' : 'right 0.75rem center' }};
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-{{ $language === 'ar' ? 'left' : 'right' }}: 2.5rem;
        }

        .btn-submit {
            background: var(--button-gradient);
            border: none;
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg) var(--spacing-2xl);
            color: var(--white);
            font-weight: 700;
            font-size: 1.125rem;
            width: 100%;
            cursor: pointer;
            transition: var(--transition-normal);
            margin-top: var(--spacing-xl);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition-slow);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-submit:disabled::before {
            display: none;
        }

        .alert {
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            border: none;
            padding: var(--spacing-lg);
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
            border-left: 4px solid var(--error);
        }

        .loading-spinner {
            display: none;
            margin-{{ $language === 'ar' ? 'left' : 'right' }}: var(--spacing-sm);
        }

        /* RTL Specific Styles */
        [dir="rtl"] .form-control,
        [dir="rtl"] .form-select {
            text-align: right;
        }

        [dir="rtl"] .form-select {
            background-position: left 0.75rem center;
            padding-left: 2.5rem;
            padding-right: var(--spacing-lg);
        }

        [dir="rtl"] .loading-spinner {
            margin-right: 0;
            margin-left: var(--spacing-sm);
        }

        [dir="rtl"] .alert {
            border-left: none;
            border-right: 4px solid;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: var(--spacing-md);
            }

            .coming-soon-container {
                grid-template-columns: 1fr;
                max-width: 500px;
                min-height: auto;
            }

            .left-section {
                padding: var(--spacing-2xl) var(--spacing-lg);
                order: 1;
            }

            .right-section {
                padding: var(--spacing-2xl) var(--spacing-lg);
                order: 2;
            }

            .coming-soon-title {
                font-size: 2rem;
            }

            .logo {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }

            .form-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .coming-soon-container {
                border-radius: var(--radius-xl);
            }

            .left-section,
            .right-section {
                padding: var(--spacing-xl) var(--spacing-md);
            }

            .coming-soon-title {
                font-size: 1.75rem;
            }

            .form-title {
                font-size: 1.25rem;
            }

            .btn-submit {
                padding: var(--spacing-md) var(--spacing-lg);
                font-size: 1rem;
            }
        }

        /* Accessibility Improvements */
        .form-control:focus-visible,
        .form-select:focus-visible,
        .btn-submit:focus-visible {
            outline: 2px solid var(--brand-orange);
            outline-offset: 2px;
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {

            .form-control,
            .form-select {
                border-width: 3px;
            }

            .btn-submit {
                border: 2px solid var(--white);
            }
        }

        /* Number input improvements */
        input[type="tel"] {
            -webkit-appearance: none;
            -moz-appearance: textfield;
        }

        input[type="tel"]::-webkit-outer-spin-button,
        input[type="tel"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }

            .btn-submit::before {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="coming-soon-container">
        <!-- Left Section - Brand Panel -->
        <div class="left-section" role="banner" aria-label="{{ custom_trans('title') }}">
            <div class="logo" aria-hidden="true">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1 class="coming-soon-title">{{ custom_trans('title') }}</h1>
            <p class="coming-soon-subtitle">{{ custom_trans('subtitle') }}</p>
        </div>

        <!-- Right Section - Form -->
        <div class="right-section">
            <h2 class="form-title">{{ custom_trans('sign_with_us') }}</h2>

            <div id="alert-container" role="alert" aria-live="polite"></div>

            <form id="subscriptionForm" role="form" aria-label="{{ custom_trans('sign_with_us') }}">
                @csrf
                <input type="hidden" name="language" value="{{ $language }}">

                <div class="form-group">
                    <label for="name" class="form-label">{{ custom_trans('name') }} *</label>
                    <input type="text" class="form-control" id="name" name="name" required
                        aria-describedby="name-help" placeholder="{{ custom_trans('name') }}">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">{{ custom_trans('phone') }} *</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required
                        aria-describedby="phone-help" placeholder="{{ custom_trans('phone') }}"
                        pattern="[0-9+\-\s\(\)]+" inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9+\-\s\(\)]/g, '')">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">{{ custom_trans('email') }} *</label>
                    <input type="email" class="form-control" id="email" name="email" required
                        aria-describedby="email-help" placeholder="{{ custom_trans('email') }}">
                </div>

                <div class="form-group">
                    <label for="whatsapp_number" class="form-label">{{ custom_trans('whatsapp_number') }}</label>
                    <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number"
                        placeholder="{{ custom_trans('whatsapp_number') }}" pattern="[0-9+\-\s\(\)]+"
                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9+\-\s\(\)]/g, '')">
                </div>

                <div class="form-group">
                    <label for="country" class="form-label">{{ custom_trans('country') }} *</label>
                    <input type="text" class="form-control" id="country" name="country" required
                        aria-describedby="country-help" placeholder="{{ custom_trans('country') }}">
                </div>

                <div class="form-group">
                    <label for="years_of_experience" class="form-label">{{ custom_trans('years_of_experience') }}
                        *</label>
                    <select class="form-select" id="years_of_experience" name="years_of_experience" required
                        aria-describedby="experience-help">
                        <option value="">{{ custom_trans('select_experience') }}</option>
                        <option value="10">10 {{ custom_trans('years') }}</option>
                        <option value="20">20 {{ custom_trans('years') }}</option>
                        <option value="30">30 {{ custom_trans('years') }}</option>
                        <option value="40">40 {{ custom_trans('years') }}</option>
                        <option value="50">50 {{ custom_trans('years') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label">{{ custom_trans('notes') }}</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4"
                        placeholder="{{ custom_trans('notes_placeholder') }}" aria-describedby="notes-help"></textarea>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" aria-describedby="submit-help">
                    {{ custom_trans('submit') }}
                    <i class="fas fa-spinner fa-spin loading-spinner" id="loadingSpinner" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('subscriptionForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const alertContainer = document.getElementById('alert-container');

            // Show loading state
            submitBtn.disabled = true;
            loadingSpinner.style.display = 'inline-block';

            // Clear previous alerts
            alertContainer.innerHTML = '';

            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route('coming-soon.subscribe') }}', {
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
                    let errorMessage = data.message || '{{ __('coming_soon.subscription_error') }}';

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
                            {{ custom_trans('subscription_error') }}
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
