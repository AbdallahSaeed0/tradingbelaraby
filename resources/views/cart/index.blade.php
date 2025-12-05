@extends('layouts.app')

@section('title', 'Shopping Cart - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="cart-hero-section py-5 position-relative overflow-hidden">
        <div class="cart-hero-bg position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <div class="cart-hero-icon mb-3">
                        <i class="fas fa-shopping-cart fa-3x text-white"></i>
                    </div>
                    <h1 class="fw-bold mb-3 text-white display-4">{{ custom_trans('shopping_cart', 'front') }}</h1>
                    <p class="lead mb-0 text-white-50">{{ custom_trans('cart_description', 'front') }}</p>
                    @if ($cartItems->count() > 0)
                        <div class="mt-3">
                            <span class="badge bg-white text-primary fs-6 px-4 py-2 rounded-pill shadow">
                                <i class="fas fa-shopping-cart me-2"></i>{{ $cartItems->count() }} 
                                {{ $cartItems->count() === 1 ? custom_trans('item', 'front') : custom_trans('items', 'front') }}
                            </span>
                        </div>
                    @endif
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
                    <div class="col-lg-8 mb-4 mb-lg-0">
                        <div class="cart-items-header bg-white rounded-3 shadow-sm p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-bold">
                                    <i class="fas fa-list-ul me-2 text-primary"></i>{{ custom_trans('cart_items', 'front') }}
                                </h5>
                                <span class="badge bg-primary rounded-pill">{{ $cartItems->count() }}</span>
                            </div>
                        </div>
                        <div class="cart-items">
                            @foreach ($cartItems as $item)
                                <div class="cart-item bg-white rounded-3 shadow-sm p-4 mb-3 border border-light position-relative overflow-hidden cart-item-animate"
                                    data-item-id="{{ $item->isBundle() ? $item->bundle_id : $item->course_id }}"
                                    data-item-type="{{ $item->isBundle() ? 'bundle' : 'course' }}">
                                    <div class="cart-item-ribbon position-absolute top-0 end-0"></div>
                                    <div class="row align-items-center">
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="cart-item-image position-relative">
                                                @if($item->isBundle())
                                                    <img src="{{ $item->bundle->image_url }}" alt="{{ $item->bundle->name }}"
                                                        class="img-fluid rounded shadow-sm img-h-120 w-100" style="object-fit: cover;">
                                                    @if ($item->bundle->original_price > $item->bundle->price && $item->bundle->price > 0)
                                                        <div class="position-absolute top-0 start-0 m-2">
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-tag me-1"></i>{{ custom_trans('sale', 'front') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @else
                                                    <img src="{{ $item->course->image_url }}" alt="{{ $item->course->name }}"
                                                        class="img-fluid rounded shadow-sm img-h-120 w-100" style="object-fit: cover;">
                                                    @if ($item->course->original_price > $item->course->price && $item->course->price > 0)
                                                        <div class="position-absolute top-0 start-0 m-2">
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-tag me-1"></i>{{ custom_trans('sale', 'front') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            @if($item->isBundle())
                                                <a href="{{ route('bundles.show', $item->bundle->slug) }}" class="text-decoration-none">
                                                    <h5 class="fw-bold mb-2 text-dark hover-primary">
                                                        <i class="fa fa-box me-1"></i>{{ $item->bundle->name }}
                                                    </h5>
                                                </a>
                                                <p class="text-muted mb-2 small">{{ Str::limit($item->bundle->description, 100) }}</p>
                                                <div class="course-meta">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="me-3">
                                                            <i class="fas fa-book text-primary me-1"></i>
                                                            <small class="text-muted">
                                                                {{ $item->bundle->courses->count() }} {{ custom_trans('courses', 'front') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <a href="{{ route('courses.show', $item->course->id) }}" class="text-decoration-none">
                                                    <h5 class="fw-bold mb-2 text-dark hover-primary">{{ $item->course->name }}</h5>
                                                </a>
                                                <p class="text-muted mb-2 small">{{ Str::limit($item->course->description, 100) }}</p>
                                                <div class="course-meta">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <div class="me-3">
                                                            <i class="fas fa-user text-primary me-1"></i>
                                                            <small class="text-muted">
                                                                {{ $item->course->instructor->name ?? custom_trans('Unknown Instructor', 'front') }}
                                                            </small>
                                                        </div>
                                                        <div>
                                                            <i class="fas fa-star text-warning me-1"></i>
                                                            <small class="text-muted">{{ number_format($item->course->average_rating ?? 0, 1) }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <span class="me-3">{{ $item->course->duration ?? custom_trans('Not Found', 'front') }}</span>
                                                        <i class="fas fa-users me-1"></i>
                                                        <span>{{ $item->course->enrolled_students ?? 0 }} {{ custom_trans('students', 'front') }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-md-end">
                                            <div class="course-price mb-3">
                                                @php
                                                    $price = $item->getPrice();
                                                    $originalPrice = $item->isBundle() ? ($item->bundle->original_price ?? 0) : ($item->course->original_price ?? 0);
                                                @endphp
                                                @if ($price > 0)
                                                    <div class="price-display fs-4 fw-bold text-primary mb-1">
                                                        SAR {{ number_format($price, 2) }}
                                                    </div>
                                                    @if ($originalPrice > $price)
                                                        <div class="text-decoration-line-through text-muted small">
                                                            SAR {{ number_format($originalPrice, 2) }}
                                                        </div>
                                                        <div class="text-success small fw-semibold">
                                                            <i class="fas fa-arrow-down me-1"></i>
                                                            {{ round((($originalPrice - $price) / $originalPrice) * 100) }}% {{ custom_trans('off', 'front') }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="badge bg-success fs-6 px-3 py-2">
                                                        <i class="fas fa-gift me-1"></i>{{ custom_trans('free', 'front') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger remove-cart-btn w-100"
                                                data-item-id="{{ $item->isBundle() ? $item->bundle_id : $item->course_id }}"
                                                data-item-type="{{ $item->isBundle() ? 'bundle' : 'course' }}">
                                                <i class="fas fa-trash me-1"></i>{{ custom_trans('remove', 'front') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Continue Shopping -->
                        <div class="text-center mt-4">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>{{ custom_trans('continue_shopping', 'front') }}
                            </a>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="col-lg-4">
                        <div class="cart-summary-sticky">
                            <div class="total-section bg-white rounded-3 shadow-sm p-4 mb-4 border border-light">
                                <div class="summary-header text-center mb-4 pb-3 border-bottom">
                                    <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                                    <h4 class="fw-bold mb-0">{{ custom_trans('order_summary', 'front') }}</h4>
                                </div>

                                <!-- Items Count -->
                                <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                    <span class="text-muted">
                                        <i class="fas fa-shopping-cart me-2"></i>{{ custom_trans('items', 'front') }}
                                    </span>
                                    <span class="fw-semibold">{{ $cartItems->count() }}</span>
                                </div>

                                <!-- Subtotal -->
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">{{ custom_trans('subtotal', 'front') }}</span>
                                    <span class="fw-bold">SAR {{ number_format($total, 2) }}</span>
                                </div>

                                <!-- Discount -->
                                <div class="d-flex justify-content-between mb-3 discount-row d-none">
                                    <span class="text-success">
                                        <i class="fas fa-tag me-1"></i>{{ custom_trans('discount', 'front') }}
                                    </span>
                                    <span class="fw-bold text-success discount-applied">-SAR 0.00</span>
                                </div>

                                <hr class="my-3">

                                <!-- Total -->
                                <div class="d-flex justify-content-between mb-4 p-3 bg-primary bg-opacity-10 rounded">
                                    <span class="fw-bold fs-5">{{ custom_trans('total', 'front') }}</span>
                                    <span class="fw-bold fs-4 text-primary total-amount">SAR {{ number_format($total, 2) }}</span>
                                </div>

                                <!-- Checkout Button -->
                                <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg w-100 mb-3 shadow-sm checkout-btn-animated">
                                    <i class="fas fa-lock me-2"></i>
                                    {{ custom_trans('proceed_to_checkout', 'front') }}
                                </a>
                                
                                <!-- Security Badge -->
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1 text-success"></i>
                                        {{ custom_trans('secure_checkout', 'front') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Coupon Section -->
                            <div class="coupon-section bg-white rounded-3 shadow-sm p-4 border border-light">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-ticket-alt fa-2x text-warning me-3"></i>
                                    <h5 class="fw-bold mb-0">{{ custom_trans('apply_coupon', 'front') }}</h5>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control form-control-lg" id="couponCode"
                                        placeholder="{{ custom_trans('enter_coupon_code', 'front') }}">
                                    <button class="btn btn-warning fw-semibold" type="button" id="applyCoupon">
                                        <i class="fas fa-check me-1"></i>{{ custom_trans('apply', 'front') }}
                                    </button>
                                </div>
                                <small class="text-muted d-flex align-items-start">
                                    <i class="fas fa-info-circle me-2 mt-1"></i>
                                    <span>{{ custom_trans('coupon_description', 'front') }}</span>
                                </small>
                            </div>
                            
                            <!-- Trust Badges -->
                            <div class="trust-badges mt-4 text-center">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="trust-badge p-2 bg-white rounded shadow-sm">
                                            <i class="fas fa-certificate text-success mb-1"></i>
                                            <small class="d-block text-muted">{{ custom_trans('certified', 'front') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="trust-badge p-2 bg-white rounded shadow-sm">
                                            <i class="fas fa-infinity text-primary mb-1"></i>
                                            <small class="d-block text-muted">{{ custom_trans('lifetime_access', 'front') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="trust-badge p-2 bg-white rounded shadow-sm">
                                            <i class="fas fa-mobile-alt text-info mb-1"></i>
                                            <small class="d-block text-muted">{{ custom_trans('mobile_access', 'front') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="empty-cart bg-white rounded-3 shadow-sm p-5 border border-light">
                            <div class="empty-cart-icon mb-4">
                                <i class="fas fa-shopping-cart fa-5x text-muted opacity-50"></i>
                            </div>
                            <h3 class="fw-bold mb-3">{{ custom_trans('empty_cart', 'front') }}</h3>
                            <p class="text-muted mb-4 lead">{{ custom_trans('empty_cart_message', 'front') }}</p>
                            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                                <a href="{{ route('categories.index') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-search me-2"></i>{{ custom_trans('browse_courses', 'front') }}
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-home me-2"></i>{{ custom_trans('go_home', 'front') }}
                                </a>
                            </div>
                            
                            <!-- Popular Categories -->
                            <div class="mt-5 pt-4 border-top">
                                <h5 class="fw-bold mb-3">{{ custom_trans('popular_categories', 'front') }}</h5>
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    @php
                                        $popularCategories = \App\Models\Category::withCount('courses')->orderBy('courses_count', 'desc')->take(5)->get();
                                    @endphp
                                    @foreach($popularCategories as $category)
                                        <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-outline-secondary">
                                            {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* Cart Hero Section */
        .cart-hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 300px;
        }
        
        .cart-hero-bg {
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            opacity: 0.3;
        }
        
        .cart-hero-icon {
            animation: cartBounce 2s ease-in-out infinite;
        }
        
        @keyframes cartBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Cart Items */
        .cart-item-animate {
            transition: all 0.3s ease;
            animation: slideInUp 0.5s ease;
        }
        
        .cart-item-animate:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cart-item-ribbon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            transform: rotate(45deg) translate(50%, -50%);
        }
        
        .cart-item-image img {
            transition: transform 0.3s ease;
        }
        
        .cart-item-animate:hover .cart-item-image img {
            transform: scale(1.05);
        }
        
        .hover-primary:hover {
            color: var(--bs-primary) !important;
        }
        
        /* Cart Summary */
        .cart-summary-sticky {
            position: sticky;
            top: 100px;
        }
        
        .checkout-btn-animated {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .checkout-btn-animated:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .checkout-btn-animated:hover:before {
            width: 300px;
            height: 300px;
        }
        
        .checkout-btn-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2) !important;
        }
        
        /* Trust Badges */
        .trust-badge {
            transition: all 0.3s ease;
        }
        
        .trust-badge:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        }
        
        .trust-badge i {
            font-size: 1.5rem;
        }
        
        /* Empty Cart */
        .empty-cart-icon {
            animation: emptyCartFloat 3s ease-in-out infinite;
        }
        
        @keyframes emptyCartFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .cart-summary-sticky {
                position: relative;
                top: 0;
            }
        }
        
        /* Background */
        .cart-content {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Remove from cart functionality
        document.addEventListener('DOMContentLoaded', function() {
            const removeButtons = document.querySelectorAll('.remove-cart-btn');
            const applyCouponBtn = document.getElementById('applyCoupon');

            removeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('{{ custom_trans('remove_from_cart_confirm', 'front') }}')) {
                        const itemId = this.dataset.itemId;
                        const itemType = this.dataset.itemType;
                        const url = itemType === 'bundle' 
                            ? `/cart/remove-bundle/${itemId}` 
                            : `/cart/remove/${itemId}`;

                        fetch(url, {
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
                                        location.reload(); // Reload to update totals
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

