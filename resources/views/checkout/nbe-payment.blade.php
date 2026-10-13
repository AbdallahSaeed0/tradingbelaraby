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

                            <div id="embed-target"></div>

                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-link" onclick="Checkout.showPaymentPage();">
                                    {{ custom_trans('open_secure_payment_page', 'front') }}
                                </button>
                            </div>

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

                        <div class="mb-3">
                            <p class="mb-1"><strong>{{ custom_trans('order_number', 'front') }}:</strong></p>
                            <p class="mb-0">{{ $order->order_number }}</p>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">{{ custom_trans('total', 'front') }}</span>
                            <span class="fw-bold total-amount">{{ number_format($order->total, 2) }} SAR</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <small class="text-muted">{{ custom_trans('charged_amount', 'front') }}</small>
                            <small class="text-muted">{{ $chargeAmount }} {{ $chargeCurrency }}</small>
                        </div>

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
    <script>
        function nbeShowError(message) {
            const errorEl = document.getElementById('payment-error');
            errorEl.textContent = message;
            errorEl.classList.remove('d-none');
            document.getElementById('payment-loading').classList.add('d-none');
        }

        // Referenced by name from the checkout.min.js data-* attributes below.
        function nbeErrorCallback(error) {
            console.error('NBE checkout error:', error);
            nbeShowError(@json(custom_trans('payment_load_error', 'front')));
        }

        function nbeCancelCallback() {
            window.location.href = @json(route('nbe.cancel', $order->id));
        }
    </script>
    <script src="{{ $scriptUrl }}"
            data-error="nbeErrorCallback"
            data-cancel="nbeCancelCallback"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                Checkout.configure({
                    session: { id: @json($sessionId) }
                });

                Checkout.showEmbeddedPage('#embed-target');
                document.getElementById('payment-loading').classList.add('d-none');
            } catch (error) {
                nbeErrorCallback(error);
            }
        });
    </script>
@endpush
