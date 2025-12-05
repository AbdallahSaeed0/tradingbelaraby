@extends('layouts.app')

@section('title', 'Checkout - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="checkout-hero-section py-5 position-relative overflow-hidden">
        <div class="checkout-hero-bg position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Progress Steps -->
                    <div class="checkout-steps mb-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="step-item completed">
                                    <div class="step-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="step-label">{{ custom_trans('cart', 'front') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="step-item active">
                                    <div class="step-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="step-label">{{ custom_trans('checkout', 'front') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="step-item">
                                    <div class="step-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="step-label">{{ custom_trans('complete', 'front') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center text-white">
                        <h1 class="fw-bold mb-3 display-4">{{ custom_trans('checkout', 'front') }}</h1>
                        <p class="lead mb-0">{{ custom_trans('complete_your_purchase', 'front') }}</p>
                    </div>
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
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <!-- Billing Information -->
                        <div class="checkout-section bg-white rounded-3 shadow-sm p-4 mb-4 border border-light">
                            <div class="section-header d-flex align-items-center mb-4 pb-3 border-bottom">
                                <div class="section-icon me-3">
                                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">{{ custom_trans('billing_information', 'front') }}</h4>
                                    <small class="text-muted">{{ custom_trans('enter_billing_details', 'front') }}</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">{{ custom_trans('first_name', 'front') }}
                                        *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name"
                                        value="{{ $user->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">{{ custom_trans('last_name', 'front') }}
                                        *</label>
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
                                    <label for="postal_code" class="form-label">{{ custom_trans('postal_code', 'front') }}
                                        *</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">{{ custom_trans('country', 'front') }}
                                        *</label>
                                    <input type="text" class="form-control" id="country" name="country" required>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="checkout-section bg-white rounded-3 shadow-sm p-4 border border-light">
                            <div class="section-header d-flex align-items-center mb-4 pb-3 border-bottom">
                                <div class="section-icon me-3">
                                    <i class="fas fa-credit-card fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">{{ custom_trans('payment_method', 'front') }}</h4>
                                    <small class="text-muted">{{ custom_trans('select_payment_method', 'front') }}</small>
                                </div>
                            </div>

                            @php
                                $allFree = $cartItems->every(function ($item) {
                                    return $item->getPrice() == 0;
                                });
                                $hasPaid = $cartItems->some(function ($item) {
                                    return $item->getPrice() > 0;
                                });
                            @endphp

                            <div class="payment-options-grid">
                                @if ($allFree)
                                    <div class="payment-option-card selected p-4 border rounded-3 position-relative">
                                        <input type="radio" name="payment_method" value="free" id="free_payment"
                                            checked class="position-absolute top-0 end-0 m-3">
                                        <div class="payment-icon mb-3">
                                            <i class="fas fa-graduation-cap fa-3x text-success"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">{{ custom_trans('free_enrollment', 'front') }}</h5>
                                        <p class="text-muted mb-0 small">
                                            {{ custom_trans('free_enrollment_description', 'front') }}</p>
                                        <div class="selected-badge position-absolute top-0 start-0 m-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                    </div>
                                @elseif($hasPaid)
                                    <div class="payment-option-card p-4 border rounded-3 position-relative">
                                        <input type="radio" name="payment_method" value="visa" id="visa_payment"
                                            class="position-absolute top-0 end-0 m-3">
                                        <div class="payment-icon mb-3">
                                            <i class="fab fa-cc-visa fa-3x text-primary"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">{{ custom_trans('credit_card', 'front') }}</h5>
                                        <p class="text-muted mb-0 small">
                                            {{ custom_trans('credit_card_description', 'front') }}</p>
                                        <div class="selected-badge position-absolute top-0 start-0 m-2 d-none">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div class="payment-logos mt-3 d-flex gap-2">
                                            <i class="fab fa-cc-mastercard fa-2x text-muted"></i>
                                            <i class="fab fa-cc-amex fa-2x text-muted"></i>
                                            <i class="fab fa-cc-discover fa-2x text-muted"></i>
                                        </div>
                                    </div>

                                    <!-- Tabby Payment Option -->
                                    <div class="payment-option-card p-4 border rounded-3 position-relative">
                                        <input type="radio" name="payment_method" value="tabby" id="tabby_payment"
                                            class="position-absolute top-0 end-0 m-3">
                                        <div class="payment-icon mb-3">
                                            <img src="https://cdn.tabby.ai/assets/logo_pink.png" alt="Tabby"
                                                style="height: 40px;">
                                        </div>
                                        <h5 class="fw-bold mb-2">Pay with Tabby</h5>
                                        <p class="text-muted mb-0 small">
                                            Split into 4 payments. No interest.</p>
                                        <div class="selected-badge position-absolute top-0 start-0 m-2 d-none">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                    </div>
                                @endif

                                @if ($allFree && $hasPaid)
                                    <div class="payment-option-card p-4 border rounded-3 position-relative">
                                        <input type="radio" name="payment_method" value="free" id="free_payment"
                                            class="position-absolute top-0 end-0 m-3">
                                        <div class="payment-icon mb-3">
                                            <i class="fas fa-graduation-cap fa-3x text-success"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">{{ custom_trans('free_enrollment', 'front') }}</h5>
                                        <p class="text-muted mb-0 small">
                                            {{ custom_trans('free_enrollment_description', 'front') }}</p>
                                        <div class="selected-badge position-absolute top-0 start-0 m-2 d-none">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary-sticky">
                            <div class="order-summary bg-white rounded-3 shadow-sm p-4 border border-light">
                                <div class="summary-header text-center mb-4 pb-3 border-bottom">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-primary mb-2"></i>
                                    <h4 class="fw-bold mb-0">{{ custom_trans('order_summary', 'front') }}</h4>
                                </div>

                                <!-- Course Items -->
                                <div class="course-items-list mb-4">
                                    @foreach ($cartItems as $item)
                                        <div class="course-item-summary mb-3 p-3 bg-light rounded-3">
                                            <div class="d-flex gap-3">
                                                <div class="course-thumb">
                                                    @if($item->isBundle())
                                                        <img src="{{ $item->bundle->image_url }}"
                                                            alt="{{ $item->bundle->name }}" class="rounded"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <img src="{{ $item->course->image_url }}"
                                                            alt="{{ $item->course->name }}" class="rounded"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    @if($item->isBundle())
                                                        <h6 class="fw-bold mb-1 small">
                                                            <i class="fa fa-box me-1"></i>{{ Str::limit($item->bundle->name, 35) }}
                                                        </h6>
                                                        <small class="text-muted d-block mb-2">
                                                            <i class="fas fa-book me-1"></i>
                                                            {{ $item->bundle->courses->count() }} {{ custom_trans('courses', 'front') }}
                                                        </small>
                                                    @else
                                                        <h6 class="fw-bold mb-1 small">
                                                            {{ Str::limit($item->course->name, 40) }}</h6>
                                                        <small class="text-muted d-block mb-2">
                                                            <i class="fas fa-user me-1"></i>
                                                            {{ $item->course->instructor->name ?? custom_trans('Unknown Instructor', 'front') }}
                                                        </small>
                                                    @endif
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        @php
                                                            $price = $item->getPrice();
                                                        @endphp
                                                        @if ($price > 0)
                                                            <span class="fw-bold text-primary">SAR
                                                                {{ number_format($price, 2) }}</span>
                                                        @else
                                                            <span
                                                                class="badge bg-success">{{ custom_trans('free', 'front') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Coupon Section -->
                                <div class="coupon-section mb-4 p-3 bg-light rounded-3">
                                    @if($coupon)
                                        <div class="alert alert-success mb-2" id="coupon-applied-alert">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fa fa-check-circle me-2"></i>
                                                    <strong>{{ $coupon->code }}</strong> {{ custom_trans('applied', 'front') }}
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-coupon-btn">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="coupon-input-group">
                                            <label class="form-label small fw-bold mb-2">
                                                <i class="fa fa-ticket-alt me-1"></i>{{ custom_trans('Have a coupon code?', 'front') }}
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="coupon_code" 
                                                    placeholder="{{ custom_trans('Enter coupon code', 'front') }}" 
                                                    style="text-transform: uppercase;">
                                                <button type="button" class="btn btn-primary apply-coupon-btn">
                                                    {{ custom_trans('Apply', 'front') }}
                                                </button>
                                            </div>
                                            <div id="coupon-message" class="mt-2 small"></div>
                                        </div>
                                    @endif
                                </div>

                                <hr class="my-3">

                                <!-- Totals -->
                                <div class="totals-section">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="text-muted">{{ custom_trans('subtotal', 'front') }}</span>
                                        <span class="fw-bold subtotal-amount">SAR {{ number_format($subtotal, 2) }}</span>
                                    </div>

                                    @if($discount > 0)
                                        <div class="d-flex justify-content-between mb-3 p-2 bg-success bg-opacity-10 rounded">
                                            <span class="text-success">
                                                <i class="fas fa-tag me-1"></i>{{ custom_trans('Discount', 'front') }}
                                                @if($coupon)
                                                    ({{ $coupon->code }})
                                                @endif
                                            </span>
                                            <span class="fw-semibold text-success discount-amount">-SAR {{ number_format($discount, 2) }}</span>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between mb-3 p-2 bg-light rounded">
                                        <span class="text-muted">
                                            <i class="fas fa-tag me-1"></i>{{ custom_trans('tax', 'front') }}
                                        </span>
                                        <span class="fw-semibold">SAR 0.00</span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-4 p-3 bg-primary bg-opacity-10 rounded">
                                        <span class="fw-bold fs-5">{{ custom_trans('total', 'front') }}</span>
                                        <span class="fw-bold fs-4 text-primary total-amount">SAR
                                            {{ number_format($total, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Checkout Button -->
                                <button type="submit"
                                    class="btn btn-success btn-lg w-100 mb-3 shadow-sm complete-purchase-btn">
                                    <i class="fas fa-lock me-2"></i>
                                    {{ custom_trans('complete_purchase', 'front') }}
                                </button>

                                <div class="text-center mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1 text-success"></i>
                                        {{ custom_trans('secure_checkout', 'front') }}
                                    </small>
                                </div>

                                <!-- Money Back Guarantee -->
                                <div class="guarantee-badge text-center p-3 bg-warning bg-opacity-10 rounded-3">
                                    <i class="fas fa-medal text-warning fa-2x mb-2"></i>
                                    <div class="fw-bold small">{{ custom_trans('30_day_money_back', 'front') }}</div>
                                    <small
                                        class="text-muted">{{ custom_trans('satisfaction_guaranteed', 'front') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Checkout Hero Section */
        .checkout-hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 350px;
        }

        .checkout-hero-bg {
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }

        /* Progress Steps */
        .checkout-steps {
            position: relative;
        }

        .checkout-steps::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 25%;
            right: 25%;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 0;
        }

        .step-item {
            position: relative;
            z-index: 1;
        }

        .step-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }

        .step-item.completed .step-icon {
            background: #28a745;
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
        }

        .step-item.active .step-icon {
            background: white;
            color: #667eea;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .step-label {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Checkout Sections */
        .checkout-section {
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-icon {
            animation: iconFloat 3s ease-in-out infinite;
        }

        @keyframes iconFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Payment Options */
        .payment-options-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .payment-option-card {
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .payment-option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .payment-option-card.selected {
            border-color: #28a745 !important;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.05) 0%, rgba(40, 167, 69, 0.1) 100%);
        }

        .payment-option-card.selected .selected-badge {
            display: block !important;
        }

        .payment-option-card input[type="radio"] {
            cursor: pointer;
        }

        /* Order Summary */
        .order-summary-sticky {
            position: sticky;
            top: 100px;
        }

        .course-item-summary {
            transition: all 0.3s ease;
        }

        .course-item-summary:hover {
            transform: translateX(5px);
            background: #e9ecef !important;
        }

        .complete-purchase-btn {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .complete-purchase-btn:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .complete-purchase-btn:hover:before {
            width: 300px;
            height: 300px;
        }

        .complete-purchase-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(40, 167, 69, 0.3) !important;
        }

        .guarantee-badge {
            animation: badgeShine 3s ease-in-out infinite;
        }

        @keyframes badgeShine {

            0%,
            100% {
                box-shadow: 0 0 0 rgba(255, 193, 7, 0);
            }

            50% {
                box-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
            }
        }

        /* Form Inputs */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .order-summary-sticky {
                position: relative;
                top: 0;
            }
        }

        /* Background */
        .checkout-content {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Payment option selection
            const paymentOptionCards = document.querySelectorAll('.payment-option-card');

            paymentOptionCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove selected class from all cards
                    paymentOptionCards.forEach(c => {
                        c.classList.remove('selected');
                        c.querySelector('.selected-badge')?.classList.add('d-none');
                    });

                    // Add selected class to clicked card
                    this.classList.add('selected');
                    this.querySelector('.selected-badge')?.classList.remove('d-none');

                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                    }
                });
            });

            // Form validation with loading state
            const checkoutForm = document.getElementById('checkoutForm');
            const submitBtn = checkoutForm.querySelector('button[type="submit"]');

            checkoutForm.addEventListener('submit', function(e) {
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

                if (!paymentMethod) {
                    e.preventDefault();
                    toastr.error('{{ custom_trans('please_select_payment_method', 'front') }}');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ custom_trans('processing', 'front') }}';
            });

            // Form field animations
            const formInputs = document.querySelectorAll('.form-control');
            formInputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });

            // Coupon functionality
            const applyCouponBtn = document.querySelector('.apply-coupon-btn');
            const removeCouponBtn = document.querySelector('.remove-coupon-btn');
            const couponCodeInput = document.getElementById('coupon_code');
            const couponMessage = document.getElementById('coupon-message');

            if (applyCouponBtn) {
                applyCouponBtn.addEventListener('click', async function() {
                    const code = couponCodeInput.value.trim().toUpperCase();
                    
                    if (!code) {
                        couponMessage.innerHTML = '<span class="text-danger">Please enter a coupon code</span>';
                        return;
                    }

                    applyCouponBtn.disabled = true;
                    applyCouponBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    try {
                        const response = await fetch('{{ route("checkout.apply-coupon") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                coupon_code: code
                            }),
                        });

                        const data = await response.json();

                        if (data.success) {
                            couponMessage.innerHTML = '<span class="text-success">' + data.message + '</span>';
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            couponMessage.innerHTML = '<span class="text-danger">' + data.message + '</span>';
                            applyCouponBtn.disabled = false;
                            applyCouponBtn.innerHTML = '{{ custom_trans("Apply", "front") }}';
                        }
                    } catch (error) {
                        console.error('Coupon error:', error);
                        couponMessage.innerHTML = '<span class="text-danger">An error occurred. Please try again.</span>';
                        applyCouponBtn.disabled = false;
                        applyCouponBtn.innerHTML = '{{ custom_trans("Apply", "front") }}';
                    }
                });

                // Allow Enter key to apply coupon
                if (couponCodeInput) {
                    couponCodeInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            applyCouponBtn.click();
                        }
                    });
                }
            }

            if (removeCouponBtn) {
                removeCouponBtn.addEventListener('click', async function() {
                    try {
                        const response = await fetch('{{ route("checkout.remove-coupon") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error('Remove coupon error:', error);
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        });
    </script>
@endpush
