@extends('layouts.app')

@section('title', custom_trans('payment', 'front') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <section class="nbe-pay py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h1 class="fw-bold mb-2">{{ custom_trans('payment', 'front') }}</h1>
                <p class="text-muted mb-0">{{ custom_trans('complete_your_payment', 'front') }}</p>
            </div>

            <div class="row g-4">
                <!-- Card Form -->
                <div class="col-lg-8">
                    <div class="nbe-card">
                        <div class="nbe-card-header">
                            <div class="d-flex align-items-center gap-3">
                                <span class="nbe-icon"><i class="fas fa-credit-card"></i></span>
                                <div>
                                    <h5 class="fw-bold mb-0">{{ custom_trans('credit_card_payment', 'front') }}</h5>
                                    <small class="text-muted">{{ custom_trans('nbe_cards_accepted', 'front') }}</small>
                                </div>
                            </div>
                            <div class="nbe-brands">
                                <i class="fab fa-cc-visa"></i>
                                <i class="fab fa-cc-mastercard"></i>
                            </div>
                        </div>

                        <div id="payment-error" class="alert alert-danger d-none m-3 mb-0"></div>

                        <div id="payment-loading" class="nbe-loading">
                            <div class="spinner-border text-success" role="status"></div>
                            <span>{{ custom_trans('loading_payment_form', 'front') }}</span>
                        </div>

                        <div id="embed-target" class="nbe-embed"></div>

                        <div class="nbe-trust">
                            <span><i class="fas fa-lock"></i> {{ custom_trans('nbe_ssl_encrypted', 'front') }}</span>
                            <span><i class="fas fa-shield-alt"></i> {{ custom_trans('nbe_3ds_protected', 'front') }}</span>
                            <span><i class="fas fa-university"></i> {{ custom_trans('nbe_processed_by_bank', 'front') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="nbe-card p-4">
                        <h5 class="fw-bold mb-3">{{ custom_trans('order_summary', 'front') }}</h5>

                        <div class="d-flex justify-content-between small mb-3">
                            <span class="text-muted">{{ custom_trans('order_number', 'front') }}</span>
                            <span class="fw-semibold" dir="ltr">{{ $order->order_number }}</span>
                        </div>

                        <ul class="list-unstyled nbe-items mb-3">
                            @foreach ($order->orderItems as $item)
                                <li class="d-flex justify-content-between gap-2">
                                    <span>{{ $item->bundle?->localized_name ?? $item->course?->localized_name }}</span>
                                    <span class="text-nowrap" dir="ltr">{{ number_format($item->price, 2) }} SAR</span>
                                </li>
                            @endforeach
                        </ul>

                        @if ($order->discount_amount > 0)
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">{{ custom_trans('discount', 'front') }}</span>
                                <span class="text-success" dir="ltr">-{{ number_format($order->discount_amount, 2) }} SAR</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center nbe-total">
                            <span class="fw-bold">{{ custom_trans('total', 'front') }}</span>
                            <span class="fw-bold fs-5" dir="ltr">{{ number_format($order->total, 2) }} SAR</span>
                        </div>

                        <div class="nbe-charge">
                            <div class="d-flex justify-content-between">
                                <span>{{ custom_trans('nbe_amount_charged', 'front') }}</span>
                                <span class="fw-bold" dir="ltr">{{ number_format((float) $chargeAmount, 2) }} {{ $chargeCurrency }}</span>
                            </div>
                            <small class="text-muted d-block mt-1">{{ custom_trans('nbe_currency_note', 'front') }}</small>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="btn btn-outline-secondary w-100 mt-3">
                            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} me-2"></i>
                            {{ custom_trans('back_to_checkout', 'front') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .nbe-pay { background: #f6f8fa; }
        .nbe-card { background: #fff; border: 1px solid #e6e9ee; border-radius: 14px; box-shadow: 0 4px 18px rgba(16, 24, 40, .05); overflow: hidden; }
        .nbe-card-header { display: flex; justify-content: space-between; align-items: center; padding: 18px 22px; border-bottom: 1px solid #eef0f3; }
        .nbe-icon { width: 44px; height: 44px; border-radius: 10px; background: #e8f5ee; color: #198754; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .nbe-brands i { font-size: 2rem; color: #1a1f71; margin-inline-start: 6px; }
        .nbe-brands .fa-cc-mastercard { color: #eb001b; }
        .nbe-loading { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 60px 20px; color: #6c757d; }
        .nbe-embed { padding: 8px 12px; min-height: 40px; }
        .nbe-trust { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 22px; padding: 14px 18px; background: #fafbfc; border-top: 1px solid #eef0f3; font-size: .85rem; color: #6c757d; }
        .nbe-trust i { color: #198754; margin-inline-end: 4px; }
        .nbe-items li { padding: 8px 0; border-bottom: 1px dashed #e6e9ee; font-size: .92rem; }
        .nbe-total { padding: 12px 0; }
        .nbe-charge { background: #e8f5ee; border-radius: 10px; padding: 12px 14px; font-size: .92rem; }
    </style>
@endpush

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

                // Hide the spinner once the gateway has injected its form.
                const target = document.getElementById('embed-target');
                new MutationObserver(function (_, observer) {
                    document.getElementById('payment-loading').classList.add('d-none');
                    observer.disconnect();
                }).observe(target, { childList: true, subtree: true });
            } catch (error) {
                nbeErrorCallback(error);
            }
        });
    </script>
@endpush
