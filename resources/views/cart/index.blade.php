@extends('layouts.app')

@section('title', 'Shopping Cart - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('shopping_cart', 'front') }}</h1>
                    <p class="lead mb-0">{{ custom_trans('cart_description', 'front') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart Content -->
    <section class="cart-content py-5">
        <div class="container">
            @if ($cartItems->count() > 0)
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="cart-items">
                            @foreach ($cartItems as $item)
                                <div class="cart-item bg-white rounded-3 shadow-sm p-4 mb-3"
                                    data-course-id="{{ $item->course->id }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <img src="{{ $item->course->image_url }}" alt="{{ $item->course->name }}"
                                                class="img-fluid rounded img-h-120">
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="fw-bold mb-2">{{ $item->course->name }}</h5>
                                            <p class="text-muted mb-2">{{ Str::limit($item->course->description, 100) }}</p>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-user text-muted me-2"></i>
                                                <small
                                                    class="text-muted">{{ $item->course->instructor->name ?? 'Not Found' }}</small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clock text-muted me-2"></i>
                                                <small
                                                    class="text-muted">{{ $item->course->duration ?? 'Not Found' }}</small>
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-users text-muted me-2"></i>
                                                <small class="text-muted">{{ $item->course->enrolled_students ?? 0 }}
                                                    students</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <div class="course-price mb-2">
                                                @if ($item->course->price > 0)
                                                    <span
                                                        class="price-display text-primary">₹{{ number_format($item->course->price, 2) }}</span>
                                                    @if ($item->course->original_price > $item->course->price)
                                                        <div class="text-decoration-line-through text-muted">
                                                            ₹{{ number_format($item->course->original_price, 2) }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="free-course-badge">{{ custom_trans('free', 'front') }}</span>
                                                @endif
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger remove-cart-btn"
                                                data-course-id="{{ $item->course->id }}">
                                                <i class="fas fa-trash me-1"></i>{{ custom_trans('remove', 'front') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="col-lg-4">
                        <div class="total-section mb-4">
                            <h4 class="fw-bold mb-3">{{ custom_trans('order_summary', 'front') }}</h4>

                            <!-- Subtotal -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ custom_trans('subtotal', 'front') }}</span>
                                <span class="fw-bold">₹{{ number_format($total, 2) }}</span>
                            </div>

                            <!-- Discount -->
                            <div class="d-flex justify-content-between mb-2 discount-row d-none">
                                <span>{{ custom_trans('discount', 'front') }}</span>
                                <span class="fw-bold discount-applied">-₹0.00</span>
                            </div>

                            <hr class="my-3">

                            <!-- Total -->
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">{{ custom_trans('total', 'front') }}</span>
                                <span class="fw-bold total-amount">₹{{ number_format($total, 2) }}</span>
                            </div>

                            <!-- Checkout Button -->
                            <a href="{{ route('checkout.index') }}" class="btn checkout-btn w-100">
                                <i class="fas fa-credit-card me-2"></i>
                                {{ custom_trans('proceed_to_checkout', 'front') }}
                            </a>
                        </div>

                        <!-- Coupon Section -->
                        <div class="coupon-section">
                            <h5 class="fw-bold mb-3">{{ custom_trans('apply_coupon', 'front') }}</h5>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="couponCode"
                                    placeholder="{{ custom_trans('enter_coupon_code', 'front') }}">
                                <button class="btn btn-outline-primary" type="button" id="applyCoupon">
                                    {{ custom_trans('apply', 'front') }}
                                </button>
                            </div>
                            <small class="text-muted">{{ custom_trans('coupon_description', 'front') }}</small>
                        </div>
                    </div>
                </div>
            @else
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="empty-cart py-5">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                            <h3 class="fw-bold mb-3">{{ custom_trans('empty_cart', 'front') }}</h3>
                            <p class="text-muted mb-4">{{ custom_trans('empty_cart_message', 'front') }}</p>
                            <a href="{{ route('categories.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>{{ custom_trans('browse_courses', 'front') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Remove from cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            const removeButtons = document.querySelectorAll('.remove-cart-btn');
            const applyCouponBtn = document.getElementById('applyCoupon');

            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('{{ custom_trans('remove_from_cart_confirm', 'front') }}')) {
                        const courseId = this.dataset.courseId;

                        fetch(`/cart/${courseId}/remove`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove the cart item from the page
                                    this.closest('.cart-item').remove();

                                    // Check if no more items
                                    if (document.querySelectorAll('.cart-item').length === 0) {
                                        location.reload(); // Reload to show empty state
                                    } else {
                                        // Update totals
                                        updateCartTotals();
                                    }

                                    // Update cart count in header
                                    updateCartCount();
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                toastr.error('An error occurred. Please try again.');
                            });
                    }
                });
            });

            // Apply coupon functionality
            if (applyCouponBtn) {
                applyCouponBtn.addEventListener('click', function() {
                    const couponCode = document.getElementById('couponCode').value.trim();

                    if (!couponCode) {
                        toastr.warning('Please enter a coupon code');
                        return;
                    }

                    // Disable button and show loading
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Applying...';

                    // Send AJAX request to apply coupon
                    fetch('/cart/apply-coupon', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                coupon_code: couponCode
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message || 'Coupon applied successfully!');
                                updateCartTotals(data.discount, data.total);
                            } else {
                                toastr.error(data.message || 'Invalid coupon code');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred. Please try again.');
                        })
                        .finally(() => {
                            this.disabled = false;
                            this.innerHTML = '{{ custom_trans('apply', 'front') }}';
                        });
                });
            }

            // Function to update cart totals
            function updateCartTotals(discount = 0, total = null) {
                const subtotalElement = document.querySelector('.total-section .d-flex:first-child .fw-bold');
                const discountElement = document.querySelector('.discount-applied');
                const totalElement = document.querySelector('.total-amount');
                const discountRow = document.querySelector('.discount-row');

                if (discount > 0) {
                    discountRow.style.display = 'flex !important';
                    discountElement.textContent = `-₹${discount.toFixed(2)}`;
                    totalElement.textContent = `₹${total.toFixed(2)}`;
                } else {
                    discountRow.style.display = 'none !important';
                    totalElement.textContent = subtotalElement.textContent;
                }
            }

            // Function to update cart count in header
            function updateCartCount() {
                const cartBadge = document.querySelector('.user-actions .fa-shopping-cart').parentElement
                    .querySelector('.badge');
                if (cartBadge) {
                    const currentCount = parseInt(cartBadge.textContent) || 0;
                    const newCount = Math.max(0, currentCount - 1);
                    if (newCount === 0) {
                        cartBadge.remove();
                    } else {
                        cartBadge.textContent = newCount;
                    }
                }
            }
        });
    </script>
@endpush

