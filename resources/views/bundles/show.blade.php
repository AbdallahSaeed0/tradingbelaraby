@extends('layouts.app')

@section('title', ($bundle->localized_name ?? custom_trans('Bundle', 'front')) . ' - ' .
    (\App\Models\MainContentSettings::getActive()?->site_name ?? custom_trans('Site Name', 'front')))

@section('content')
    <!-- Banner Section -->
    <section class="course-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="course-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="course-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative py-5 z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">{{ custom_trans('Bundle Details', 'front') }}</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="course-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home', 'front') }}"
                        class="text-dark text-decoration-none hover-primary">{{ custom_trans('home', 'front') }}</a>
                    &nbsp;|&nbsp;
                    <a href="{{ route('bundles.index') }}"
                        class="text-dark text-decoration-none hover-primary">{{ custom_trans('Bundles', 'front') }}</a>
                    &nbsp;|&nbsp;
                    {{ custom_trans('Details', 'front') }}
                </span>
            </div>
        </div>
    </section>

    <!-- Bundle Detail Section -->
    <section class="bundle-detail-section py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Left Side -->
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4">{{ $bundle->localized_name }}</h2>
                    <img src="{{ $bundle->image_url }}" class="img-fluid rounded-4 mb-4"
                        alt="{{ $bundle->localized_name }}">
                    
                    <!-- Description -->
                    <div class="mb-5">
                        <h4 class="fw-bold mb-3">{{ custom_trans('About This Bundle', 'front') }}</h4>
                        <div class="mb-4" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                            {!! nl2br(e($bundle->localized_description)) !!}
                        </div>
                    </div>

                    <!-- Courses in Bundle -->
                    <div class="mb-5">
                        <h4 class="fw-bold mb-4">{{ custom_trans('Courses Included', 'front') }} ({{ $bundle->courses->count() }})</h4>
                        
                        @foreach($bundle->courses as $course)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}" 
                                                class="img-fluid rounded">
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="mb-2">{{ $course->localized_name }}</h5>
                                            <p class="text-muted mb-2">{{ Str::limit($course->localized_description, 100) }}</p>
                                            <div class="d-flex gap-3 text-muted small">
                                                <span><i class="fa fa-book me-1"></i>{{ $course->total_lessons }} {{ custom_trans('lessons', 'front') }}</span>
                                                <span><i class="fa fa-clock me-1"></i>{{ $course->duration }}</span>
                                                <span><i class="fa fa-users me-1"></i>{{ $course->enrolled_students }} {{ custom_trans('students', 'front') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <div class="fw-bold text-primary">{{ $course->formatted_price }}</div>
                                            <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-primary mt-2">
                                                {{ custom_trans('View Course', 'front') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right Side -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body">
                            <div class="mb-4">
                                <h3 class="text-primary mb-2">{{ $bundle->formatted_price }}</h3>
                                @if($bundle->original_price)
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted text-decoration-line-through">{{ $bundle->formatted_original_price }}</span>
                                        <span style="background: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 0.875rem;">-{{ $bundle->discount_percentage }}%</span>
                                    </div>
                                @endif
                            </div>

                            <div class="alert alert-success mb-4">
                                <i class="fa fa-check-circle me-2"></i>
                                {{ custom_trans('Save', 'front') }} {{ number_format($bundle->savings_amount, 2) }} SAR
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">{{ custom_trans('This bundle includes:', 'front') }}</h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fa fa-check text-success me-2"></i>{{ $bundle->courses->count() }} {{ custom_trans('courses', 'front') }}</li>
                                    <li class="mb-2"><i class="fa fa-check text-success me-2"></i>{{ custom_trans('Lifetime access', 'front') }}</li>
                                    <li class="mb-2"><i class="fa fa-check text-success me-2"></i>{{ custom_trans('Certificate of completion', 'front') }}</li>
                                </ul>
                            </div>

                            @auth
                                <button class="btn btn-primary w-100 fw-bold mb-3 bundle-add-to-cart-btn"
                                    data-bundle-id="{{ $bundle->id }}">
                                    <i class="fas fa-shopping-cart me-2"></i>{{ custom_trans('Add to Cart', 'front') }}
                                </button>
                            @else
                                <a href="{{ route('login', 'front') }}" class="btn btn-primary w-100 fw-bold mb-3">
                                    <i class="fas fa-shopping-cart me-2"></i>{{ custom_trans('Add to Cart', 'front') }}
                                </a>
                            @endauth

                            <div class="text-center">
                                <small class="text-muted">{{ custom_trans('30-day money-back guarantee', 'front') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Bundles -->
    @if($relatedBundles && $relatedBundles->count() > 0)
        <section class="related-bundles-section py-5 bg-light">
            <div class="container">
                <h3 class="fw-bold mb-4">{{ custom_trans('Related Bundles', 'front') }}</h3>
                <div class="row g-4">
                    @foreach($relatedBundles as $relatedBundle)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="position-relative">
                                    <img src="{{ $relatedBundle->image_url }}" class="card-img-top" alt="{{ $relatedBundle->localized_name }}" 
                                        style="height: 200px; object-fit: cover;">
                                    @if($relatedBundle->discount_percentage)
                                        <span class="position-absolute top-0 end-0 m-2 badge bg-danger">
                                            -{{ $relatedBundle->discount_percentage }}%
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $relatedBundle->localized_name }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($relatedBundle->localized_description, 80) }}</p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="text-primary mb-0">{{ $relatedBundle->formatted_price }}</h5>
                                                @if($relatedBundle->original_price)
                                                    <small class="text-muted text-decoration-line-through">
                                                        {{ $relatedBundle->formatted_original_price }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('bundles.show', $relatedBundle->slug) }}" class="btn btn-primary w-100">
                                            {{ custom_trans('View Bundle', 'front') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Add-to-cart success modal --}}
    <div class="modal fade" id="cartSuccessModal" tabindex="-1" aria-labelledby="cartSuccessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="cartSuccessModalLabel">
                        {{ custom_trans('Bundle added to cart', 'front') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        {{ custom_trans('You can continue shopping or proceed to checkout to complete your purchase.', 'front') }}
                    </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ custom_trans('Continue shopping', 'front') }}
                    </button>
                    <a href="{{ route('checkout.index') }}" class="btn btn-orange fw-bold">
                        {{ custom_trans('Go to checkout', 'front') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart button
        const addToCartBtn = document.querySelector('.bundle-add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                const bundleId = this.dataset.bundleId;

                try {
                    const response = await fetch(`/cart/add-bundle/${bundleId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({}),
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show modal
                        const modalElement = document.getElementById('cartSuccessModal');
                        if (modalElement && typeof bootstrap !== 'undefined') {
                            const modal = new bootstrap.Modal(modalElement);
                            modal.show();
                        }
                    } else {
                        alert(data.message || '{{ custom_trans('Unable to add bundle to cart.', 'front') }}');
                    }
                } catch (error) {
                    console.error('Add to cart error:', error);
                    alert('{{ custom_trans('An unexpected error occurred. Please try again later.', 'front') }}');
                }
            });
        }
    });
</script>
@endpush

