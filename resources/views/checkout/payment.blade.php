@extends('layouts.app')

@section('title', 'Payment - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('payment', 'front') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('complete_your_payment', 'front') }}</p>
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
                        <h4 class="fw-bold mb-4">{{ custom_trans('credit_card_payment', 'front') }}</h4>

                        <div class="payment-form">
                            <div id="payment-error" class="alert alert-danger d-none"></div>
                            <div id="payment-loading" class="alert alert-info">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                {{ custom_trans('loading_payment_form', 'front') }}
                            </div>

                            <div id="payment-buttons"></div>

                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    {{ custom_trans('secure_payment_message', 'front') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h4 class="fw-bold mb-3">{{ custom_trans('order_summary', 'front') }}</h4>

                        <!-- Order Information -->
                        <div class="mb-3">
                            <p class="mb-1"><strong>{{ custom_trans('order_number', 'front') }}:</strong></p>
                            <p class="mb-0">{{ $order->order_number }}</p>
                        </div>

                        <!-- Course Items -->
                        <div class="mb-4">
                            @foreach ($order->orderItems as $item)
                                <div class="course-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">{{ $item->course->localized_name }}</h6>
                                            <small
                                                class="text-muted">{{ $item->course->instructor->name ?? custom_trans('Unknown Instructor', 'front') }}</small>
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
                            <span>{{ custom_trans('subtotal', 'front') }}</span>
                            <span class="fw-bold">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">{{ custom_trans('total', 'front') }}</span>
                            <span class="fw-bold total-amount">₹{{ number_format($order->total, 2) }}</span>
                        </div>

                        <!-- Back to Checkout -->
                        <a href="{{ route('checkout.index') }}" class="btn btn-outline-light w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            {{ custom_trans('back_to_checkout', 'front') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ config('cybersource.base_url') }}/uc/v1/assets/{{ config('cybersource.client_version') }}/UnifiedCheckout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const sessionJWT = @json($captureContext);
            const completeUrl = '{{ route('checkout.payment.complete', $order->id) }}';
            const successUrl = '{{ route('checkout.success', $order->id) }}';
            const csrfToken = '{{ csrf_token() }}';

            const loadingEl = document.getElementById('payment-loading');
            const errorEl = document.getElementById('payment-error');

            function showError(message) {
                errorEl.textContent = message;
                errorEl.classList.remove('d-none');
                loadingEl.classList.add('d-none');
            }

            let client, checkout;
            try {
                client = await VAS.UnifiedCheckout(sessionJWT);
                checkout = await client.createCheckout();

                loadingEl.classList.add('d-none');

                const result = await checkout.mount('#payment-buttons');

                loadingEl.classList.remove('d-none');
                loadingEl.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ custom_trans('confirming_payment', 'front') }}';

                const response = await fetch(completeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ result }),
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect || successUrl;
                } else {
                    showError(data.message || '{{ custom_trans('payment_failed_message', 'front') }}');
                }
            } catch (error) {
                console.error('Unified Checkout error:', error);
                showError(error?.message || '{{ custom_trans('payment_load_error', 'front') }}');
            } finally {
                if (checkout) checkout.destroy();
                if (client) client.destroy();
            }
        });
    </script>
@endpush

