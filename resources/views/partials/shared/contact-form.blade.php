<!-- Contact Form Component -->
<div class="contact-form-section">
    <form class="contact-form" method="POST" action="{{ route('contact.submit') }}">
        @csrf
        <div class="row g-3">
            <!-- Name Field -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="form-label fw-bold">Full Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Email Field -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="form-label fw-bold">Email Address *</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Phone Field -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="phone" class="form-label fw-bold">Phone Number</label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Subject Field -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="subject" class="form-label fw-bold">Subject *</label>
                    <select class="form-select @error('subject') is-invalid @enderror" id="subject" name="subject"
                        required>
                        <option value="">Select a subject</option>
                        <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry
                        </option>
                        <option value="course" {{ old('subject') == 'course' ? 'selected' : '' }}>Course Information
                        </option>
                        <option value="technical" {{ old('subject') == 'technical' ? 'selected' : '' }}>Technical
                            Support</option>
                        <option value="billing" {{ old('subject') == 'billing' ? 'selected' : '' }}>Billing Question
                        </option>
                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Message Field -->
            <div class="col-12">
                <div class="form-group">
                    <label for="message" class="form-label fw-bold">Message *</label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5"
                        placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" value="1"
                        {{ old('newsletter') ? 'checked' : '' }}>
                    <label class="form-check-label" for="newsletter">
                        Subscribe to our newsletter for updates and course announcements
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12">
                <button type="submit" class="btn btn-orange btn-lg w-100">
                    <i class="fa fa-paper-plane me-2"></i>Send Message
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .contact-form-section {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-control,
    .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #ff6b35;
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }

    .form-check-input:checked {
        background-color: #ff6b35;
        border-color: #ff6b35;
    }

    .btn-orange {
        background: linear-gradient(45deg, #ff6b35, #f7931e);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-orange:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
    }

    .invalid-feedback {
        font-size: 0.875rem;
        color: #dc3545;
    }
</style>
