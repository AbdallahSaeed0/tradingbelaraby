<!-- FAQ Section -->
@php
    $faq = \App\Models\FAQ::active()->first();
    $faqs = \App\Models\FAQ::active()->ordered()->get();
@endphp

@if ($faq && $faqs->count() > 0)
    <section class="faq-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold mb-3">{{ $faq->getDisplayTitle() }}</h2>
                        <p class="text-muted">
                            {{ $faq->getDisplayDescription() }}
                        </p>
                    </div>
                    <div class="accordion" id="faqAccordion">
                        @foreach ($faqs as $index => $faqItem)
                            <div class="accordion-item border-0 shadow-sm mb-3 rounded">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button collapsed fw-bold" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                        aria-expanded="false" aria-controls="collapse{{ $index }}">
                                        {{ $faqItem->getDisplayQuestion() }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {{ $faqItem->getDisplayAnswer() }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Trader Registration Form Section -->
<section class="contact-form-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-white mb-2">{{ __('Trader Registration') }}</h2>
                    <p class="text-white-50">
                        {{ __('Join our trading community! Fill out the form below to register as a trader.') }}
                    </p>
                </div>
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <form id="traderForm">
                            @csrf
                            <div class="row g-2">
                                <!-- Basic Information -->
                                <div class="col-md-4">
                                    <label for="name" class="form-label small fw-bold">{{ __('Full Name') }}
                                        *</label>
                                    <input type="text" class="form-control form-control-sm" id="name"
                                        name="name" placeholder="{{ __('Full name') }}" required>
                                </div>

                                <div class="col-md-2">
                                    <label for="sex" class="form-label small fw-bold">{{ __('Gender') }}
                                        *</label>
                                    <select class="form-select form-select-sm" id="sex" name="sex" required>
                                        <option value="">{{ __('Select') }}</option>
                                        <option value="male">{{ __('Male') }}</option>
                                        <option value="female">{{ __('Female') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="birthdate" class="form-label small fw-bold">{{ __('Birth Date') }}
                                        *</label>
                                    <input type="date" class="form-control form-control-sm" id="birthdate"
                                        name="birthdate" required>
                                </div>

                                <div class="col-md-3">
                                    <label for="email" class="form-label small fw-bold">{{ __('Email') }}
                                        *</label>
                                    <input type="email" class="form-control form-control-sm" id="email"
                                        name="email" placeholder="{{ __('Email address') }}" required>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-md-4">
                                    <label for="phone_number"
                                        class="form-label small fw-bold">{{ __('Phone') }}</label>
                                    <input type="tel" class="form-control form-control-sm" id="phone_number"
                                        name="phone_number" placeholder="{{ __('Phone number') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="whatsapp_number"
                                        class="form-label small fw-bold">{{ __('WhatsApp') }}</label>
                                    <input type="tel" class="form-control form-control-sm" id="whatsapp_number"
                                        name="whatsapp_number" placeholder="{{ __('WhatsApp number') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="trading_community"
                                        class="form-label small fw-bold">{{ __('Trading Community') }}</label>
                                    <input type="text" class="form-control form-control-sm" id="trading_community"
                                        name="trading_community" placeholder="{{ __('Community name') }}">
                                </div>

                                <!-- Professional Links -->
                                <div class="col-md-6">
                                    <label for="linkedin" class="form-label small fw-bold">{{ __('LinkedIn') }}</label>
                                    <input type="url" class="form-control form-control-sm" id="linkedin"
                                        name="linkedin" placeholder="{{ __('LinkedIn profile URL') }}">
                                </div>

                                <div class="col-md-6">
                                    <label for="website"
                                        class="form-label small fw-bold">{{ __('Website') }}</label>
                                    <input type="url" class="form-control form-control-sm" id="website"
                                        name="website" placeholder="{{ __('Website URL') }}">
                                </div>

                                <!-- Languages -->
                                <div class="col-md-6">
                                    <label for="first_language"
                                        class="form-label small fw-bold">{{ __('Primary Language') }} *</label>
                                    <input type="text" class="form-control form-control-sm" id="first_language"
                                        name="first_language" placeholder="{{ __('Primary language') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="second_language"
                                        class="form-label small fw-bold">{{ __('Secondary Language') }}</label>
                                    <input type="text" class="form-control form-control-sm" id="second_language"
                                        name="second_language" placeholder="{{ __('Secondary language') }}">
                                </div>

                                <!-- Experience & Certificates -->
                                <div class="col-12">
                                    <label for="certificates"
                                        class="form-label small fw-bold">{{ __('Certificates') }}</label>
                                    <textarea class="form-control form-control-sm" id="certificates" name="certificates" rows="2"
                                        placeholder="{{ __('List your certificates and qualifications') }}"></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="trading_experience"
                                        class="form-label small fw-bold">{{ __('Trading Experience') }}</label>
                                    <textarea class="form-control form-control-sm" id="trading_experience" name="trading_experience" rows="2"
                                        placeholder="{{ __('Describe your trading experience') }}"></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="training_experience"
                                        class="form-label small fw-bold">{{ __('Training Experience') }}</label>
                                    <textarea class="form-control form-control-sm" id="training_experience" name="training_experience" rows="2"
                                        placeholder="{{ __('Describe your training/teaching experience') }}"></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="available_appointments"
                                        class="form-label small fw-bold">{{ __('Available Appointments') }}</label>
                                    <textarea class="form-control form-control-sm" id="available_appointments" name="available_appointments"
                                        rows="1" placeholder="{{ __('When are you available for meetings?') }}"></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="comments"
                                        class="form-label small fw-bold">{{ __('Comments/Questions') }}</label>
                                    <textarea class="form-control form-control-sm" id="comments" name="comments" rows="2"
                                        placeholder="{{ __('Any additional information or questions') }}"></textarea>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg px-4 py-2 fw-bold">
                                        <i class="fas fa-user-plus me-2"></i>{{ __('Register as Trader') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Instructor Section -->
@include('partials.courses.instructor-section')

@push('scripts')
    <script>
        $(document).ready(function() {
            // Trader Registration Form Submission
            $('#traderForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('Registering...') }}');

                $.ajax({
                    url: '{{ route('traders.store') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            toastr.success(response.message ||
                                '{{ __('Thank you for your registration! We will contact you soon.') }}'
                            );
                            // Reset form
                            $('#traderForm')[0].reset();
                        } else {
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    toastr.error(response.errors[key][0]);
                                });
                            } else {
                                toastr.error(response.message ||
                                    '{{ __('An error occurred during registration.') }}');
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                '{{ __('An error occurred while processing your registration. Please try again.') }}'
                            );
                        }
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endpush
