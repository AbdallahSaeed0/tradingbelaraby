@extends('layouts.app')

@section('title', 'Checkout - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('checkout', 'front') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('complete_your_purchase', 'front') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout Content -->
    <section class="checkout-content py-5">
        <div class="container">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row">
                    <!-- Checkout Form -->
                    <div class="col-lg-8">
                        <!-- Billing Information -->
                        <div class="checkout-section">
                            <h4 class="fw-bold mb-4">{{ custom_trans('billing_information', 'front') }}</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">{{ custom_trans('first_name', 'front') }} *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ $user->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">{{ custom_trans('last_name', 'front') }} *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                        value="{{ $user->name }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ custom_trans('email', 'front') }} *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $user->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ custom_trans('phone', 'front') }} *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ custom_trans('address', 'front') }} *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">{{ custom_trans('city', 'front') }} *</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">{{ custom_trans('state', 'front') }} *</label>
                                    <input type="text" class="form-control" id="state" name="state" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="form-label">{{ custom_trans('postal_code', 'front') }} *</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">{{ custom_trans('country', 'front') }} *</label>
                                    <input type="text" class="form-control" id="country" name="country" required>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section">
                            <h4 class="fw-bold mb-4">{{ custom_trans('payment_method', 'front') }}</h4>

                            @php
                                $allFree = $cartItems->every(function ($item) {
                                    return $item->course->price == 0;
                                });
                                $hasPaid = $cartItems->some(function ($item) {
                                    return $item->course->price > 0;
                                });
                            @endphp

                            @if ($allFree)
                                <div class="payment-option selected">
                                    <input type="radio" name="payment_method" value="free" id="free_payment" checked>
                                    <label for="free_payment" class="fw-bold">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        {{ custom_trans('free_enrollment', 'front') }}
                                    </label>
                                    <p class="text-muted mb-0 mt-2">{{ custom_trans('free_enrollment_description', 'front') }}</p>
                                </div>
                            @elseif($hasPaid)
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="visa" id="visa_payment">
                                    <label for="visa_payment" class="fw-bold">
                                        <i class="fab fa-cc-visa me-2"></i>
                                        {{ custom_trans('credit_card', 'front') }}
                                    </label>
                                    <p class="text-muted mb-0 mt-2">{{ custom_trans('credit_card_description', 'front') }}</p>
                                </div>
                            @endif

                            @if ($allFree && $hasPaid)
                                <div class="payment-option">
                                    <input type="radio" name="payment_method" value="free" id="free_payment">
                                    <label for="free_payment" class="fw-bold">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        {{ custom_trans('free_enrollment', 'front') }}
                                    </label>
                                    <p class="text-muted mb-0 mt-2">{{ custom_trans('free_enrollment_description', 'front') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h4 class="fw-bold mb-3">{{ custom_trans('order_summary', 'front') }}</h4>

                            <!-- Course Items -->
                            <div class="mb-4">
                                @foreach ($cartItems as $item)
                                    <div class="course-item">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold mb-1">{{ $item->course->name }}</h6>
                                                <small
                                                    class="text-muted">{{ $item->course->instructor->name ?? 'Unknown Instructor' }}</small>
                                            </div>
                                            <div class="text-end">
                                                @if ($item->course->price > 0)
                                                    <span
                                                        class="price-display">₹{{ number_format($item->course->price, 2) }}</span>
                                                @else
                                                    <span class="free-course-badge">{{ custom_trans('free', 'front') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <hr class="my-3">

                            <!-- Totals -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ custom_trans('subtotal', 'front') }}</span>
                                <span class="fw-bold">₹{{ number_format($subtotal, 2) }}</span>
                            </div>

                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">{{ custom_trans('total', 'front') }}</span>
                                <span class="fw-bold total-amount">₹{{ number_format($total, 2) }}</span>
                            </div>

                            <!-- Checkout Button -->
                            <button type="submit" class="btn btn-checkout w-100">
                                <i class="fas fa-lock me-2"></i>
                                {{ custom_trans('complete_purchase', 'front') }}
                            </button>

                            <div class="text-center mt-3">
                                <small class="text-light">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    {{ custom_trans('secure_checkout', 'front') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment option selection
            const paymentOptions = document.querySelectorAll('.payment-option');

            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    paymentOptions.forEach(opt => opt.classList.remove('selected'));

                    // Add selected class to clicked option
                    this.classList.add('selected');

                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                    }
                });
            });

            // Form validation
            const checkoutForm = document.getElementById('checkoutForm');

            checkoutForm.addEventListener('submit', function(e) {
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

                if (!paymentMethod) {
                    e.preventDefault();
                    toastr.error('Please select a payment method');
                    return;
                }
            });
        });
    </script>
@endpush

