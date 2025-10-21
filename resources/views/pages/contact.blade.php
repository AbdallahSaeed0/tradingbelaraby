@extends('layouts.app')

@section('title', 'Contact Us - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    @php
        $contactSettings = \App\Models\ContactSettings::getActive();
    @endphp

    <!-- Contact Banner -->
    <section class="contact-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="contact-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="contact-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">Contact Us</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="contact-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">Home</a> &nbsp;|&nbsp;
                    Contact Us
                </span>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="contact-info-section py-5 bg-white">
        <div class="container">
            <div class="text-center mb-4">
                <span class="text-orange small fw-bold"><i class="fa fa-paper-plane me-1"></i>Keep in Touch</span>
                <h2 class="fw-bold mt-2 mb-4">Get In Touch</h2>
            </div>
            <div class="row justify-content-center g-4">
                <div class="col-md-4">
                    <div class="contact-box text-center p-4 rounded-4 shadow-sm bg-light-blue h-100">
                        <div class="contact-icon-box mb-3 mx-auto bg-white text-orange"><i class="fa fa-phone fa-2x"></i>
                        </div>
                        <div class="fw-bold mb-1">{{ $contactSettings->phone ?? '9123456789' }}</div>
                        <div class="text-muted small">Phone Support</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-box text-center p-4 rounded-4 shadow-sm bg-light-peach h-100">
                        <div class="contact-icon-box mb-3 mx-auto bg-white text-orange"><i class="fa fa-envelope fa-2x"></i>
                        </div>
                        <div class="fw-bold mb-1">{{ $contactSettings->email ?? 'info@example.com' }}</div>
                        <div class="text-muted small">Email Address</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contact-box text-center p-4 rounded-4 shadow-sm bg-light-blue h-100">
                        <div class="contact-icon-box mb-3 mx-auto bg-white text-orange"><i
                                class="fa fa-map-marker-alt fa-2x"></i></div>
                        <div class="fw-bold mb-1">
                            {{ $contactSettings->address ?? 'Company 12345 South Main Street Anywhere' }}</div>
                        <div class="text-muted small">Office Address</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="contact-map-section">
        <div class="container-fluid px-0">
            @if ($contactSettings && $contactSettings->map_embed_url)
                <iframe src="{{ $contactSettings->map_embed_url }}" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            @else
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537363159047!3d-37.8162797420217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d43f1f6e0b1%3A0x5045675218ce6e0!2sMelbourne%20VIC%2C%20Australia!5e0!3m2!1sen!2sus!4v1611816611234!5m2!1sen!2sus"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            @endif
        </div>
    </section>

    <!-- Inquiry Form Section -->
    <section class="contact-form-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h2 class="fw-bold text-center mb-4">Student Inquiry Form</h2>
                    <form id="contactForm" class="contact-form">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="name" class="form-control rounded-3" placeholder="Name"
                                        required>
                                    <span class="input-group-text bg-white"><i class="fa fa-user"></i></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control rounded-3" placeholder="Email"
                                        required>
                                    <span class="input-group-text bg-white"><i class="fa fa-envelope"></i></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="phone" class="form-control rounded-3" placeholder="Phone">
                                    <span class="input-group-text bg-white"><i class="fa fa-phone"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" name="subject" class="form-control rounded-3" placeholder="Subject"
                                    required>
                                <span class="input-group-text bg-white"><i class="fa fa-tag"></i></span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <textarea name="message" class="form-control rounded-3" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-orange px-5 py-2 rounded-3 fw-bold" id="submitBtn">
                                <span class="btn-text">Make An Request <i class="fa fa-arrow-right ms-2"></i></span>
                                <span class="btn-loading d-none-initially">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Sending...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Contact form submission
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $('#submitBtn');
                const btnText = submitBtn.find('.btn-text');
                const btnLoading = submitBtn.find('.btn-loading');

                // Show loading state
                submitBtn.prop('disabled', true);
                btnText.hide();
                btnLoading.show();

                $.ajax({
                    url: '{{ route('contact.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message ||
                                'Your message has been sent successfully!');
                            $('#contactForm')[0].reset();
                        } else {
                            toastr.error(response.message ||
                                'An error occurred while sending your message.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while sending your message.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join('\n');
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        // Hide loading state
                        submitBtn.prop('disabled', false);
                        btnText.show();
                        btnLoading.hide();
                    }
                });
            });
        });
    </script>
@endpush

