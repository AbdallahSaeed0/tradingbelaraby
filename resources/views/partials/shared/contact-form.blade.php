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
