@extends('layouts.app')

@section('title', 'Payment - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@push('styles')
    <style>
        .payment-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .order-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            position: sticky;
            top: 20px;
        }

        .payment-form {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card-input {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .card-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-pay {
            background: #28a745;
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-pay:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .course-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }

        .price-display {
            font-size: 1.2rem;
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('payment') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('complete_your_payment') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Content -->
    <section class="payment-content py-5">
        <div class="container">
            <div class="row">
                <!-- Payment Form -->
                <div class="col-lg-8">
                    <div class="payment-section">
                        <h4 class="fw-bold mb-4">{{ custom_trans('credit_card_payment') }}</h4>

                        <div class="payment-form">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ custom_trans('payment_placeholder_message') }}
                            </div>

                            <form id="paymentForm">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">{{ custom_trans('card_number') }} *</label>
                                    <input type="text" class="form-control card-input" id="card_number"
                                        placeholder="1234 5678 9012 3456" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expiry_date" class="form-label">{{ custom_trans('expiry_date') }}
                                            *</label>
                                        <input type="text" class="form-control card-input" id="expiry_date"
                                            placeholder="MM/YY" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">{{ custom_trans('cvv') }} *</label>
                                        <input type="text" class="form-control card-input" id="cvv"
                                            placeholder="123" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="card_holder" class="form-label">{{ custom_trans('card_holder_name') }}
                                        *</label>
                                    <input type="text" class="form-control card-input" id="card_holder"
                                        placeholder="{{ custom_trans('card_holder_placeholder') }}" required>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-pay">
                                        <i class="fas fa-lock me-2"></i>
                                        {{ custom_trans('pay_now') }} ₹{{ number_format($order->total, 2) }}
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    {{ custom_trans('secure_payment_message') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h4 class="fw-bold mb-3">{{ custom_trans('order_summary') }}</h4>

                        <!-- Order Information -->
                        <div class="mb-3">
                            <p class="mb-1"><strong>{{ custom_trans('order_number') }}:</strong></p>
                            <p class="mb-0">{{ $order->order_number }}</p>
                        </div>

                        <!-- Course Items -->
                        <div class="mb-4">
                            @foreach ($order->orderItems as $item)
                                <div class="course-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $item->course->name }}</h6>
                                            <small
                                                class="text-muted">{{ $item->course->instructor->name ?? 'Unknown Instructor' }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="price-display">₹{{ number_format($item->price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr class="my-3">

                        <!-- Totals -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ custom_trans('subtotal') }}</span>
                            <span class="fw-bold">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">{{ custom_trans('total') }}</span>
                            <span class="fw-bold total-amount">₹{{ number_format($order->total, 2) }}</span>
                        </div>

                        <!-- Back to Checkout -->
                        <a href="{{ route('checkout.index') }}" class="btn btn-outline-light w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            {{ custom_trans('back_to_checkout') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentForm = document.getElementById('paymentForm');

            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                // Simulate payment processing
                setTimeout(() => {
                    // For now, just redirect to success page
                    // In a real implementation, you would integrate with a payment gateway
                    window.location.href = '{{ route('checkout.success', $order->id) }}';
                }, 2000);
            });

            // Format card number input
            const cardNumber = document.getElementById('card_number');
            cardNumber.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });

            // Format expiry date input
            const expiryDate = document.getElementById('expiry_date');
            expiryDate.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });

            // Format CVV input
            const cvv = document.getElementById('cvv');
            cvv.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                e.target.value = value.substring(0, 4);
            });
        });
    </script>
@endpush
