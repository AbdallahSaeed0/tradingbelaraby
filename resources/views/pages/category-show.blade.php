@extends('layouts.app')

@section('title', (isset($category) ? \App\Helpers\TranslationHelper::getLocalizedContent($category->name,
    $category->name_ar) : 'Category') . ' - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Category Banner -->
    <section class="category-banner position-relative d-flex align-items-center justify-content-center">
        @if ($category->image)
            <img src="{{ asset('storage/' . $category->image) }}"
                alt="{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}"
                class="category-banner-bg position-absolute w-100 h-100 top-0 start-0">
        @else
            <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png"
                alt="Banner" class="category-banner-bg position-absolute w-100 h-100 top-0 start-0">
        @endif
        <div class="category-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-3 fw-bold text-white mb-3">
                {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="category-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">Home</a>
                    &nbsp;|&nbsp;
                    {{ custom_trans('category_detail', 'front') }}
                </span>
            </div>
            @php
                $localizedDescription = \App\Helpers\TranslationHelper::getLocalizedContent(
                    $category->description,
                    $category->description_ar,
                );
            @endphp
            @if ($localizedDescription)
                <p class="text-white lead mb-0">{{ $localizedDescription }}</p>
            @endif
        </div>
    </section>

    <!-- Breadcrumb -->
    <section class="py-3 bg-light">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none">{{ custom_trans('home', 'front') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}"
                            class="text-decoration-none">{{ custom_trans('categories', 'front') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Category Courses Section -->
    <section class="category-courses-section py-5 bg-white">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <div class="filter-section p-3 rounded-3 shadow-sm bg-white mb-4">
                        <h5 class="fw-bold mb-3">{{ custom_trans('filters', 'front') }}</h5>

                        <!-- Price Filter -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">{{ custom_trans('price', 'front') }}</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input price-filter" type="checkbox" id="priceFree" value="free">
                                <label class="form-check-label" for="priceFree">{{ custom_trans('free', 'front') }}</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input price-filter" type="checkbox" id="pricePaid" value="paid">
                                <label class="form-check-label" for="pricePaid">{{ custom_trans('paid', 'front') }}</label>
                            </div>
                        </div>

                        <!-- Rating Filter -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-2">{{ custom_trans('rating', 'front') }}</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" id="rating5" value="5">
                                <label class="form-check-label" for="rating5">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    (5 {{ custom_trans('stars', 'front') }})
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" id="rating4" value="4">
                                <label class="form-check-label" for="rating4">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    & up (4+ {{ custom_trans('stars', 'front') }})
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input rating-filter" type="checkbox" id="rating3" value="3">
                                <label class="form-check-label" for="rating3">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    <i class="far fa-star text-warning"></i>
                                    & up (3+ {{ custom_trans('stars', 'front') }})
                                </label>
                            </div>
                        </div>

                        <!-- Level filter removed as requested -->

                        <!-- Clear Filters -->
                        <button class="btn btn-outline-secondary btn-sm w-100" id="clearFilters">
                            {{ custom_trans('clear_filters', 'front') }}
                        </button>
                    </div>

                    <!-- Category stats removed as requested -->
                </div>

                <!-- Courses List/Grid -->
                <div class="col-lg-9">
                    <!-- Header with Sort Options -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">
                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                {{ custom_trans('courses', 'front') }}</h4>
                            <p class="text-muted mb-0">{{ $courses->total() }} {{ custom_trans('courses_found', 'front') }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <select class="form-select sort-dropdown max-w-150">
                                <option value="newest">{{ custom_trans('newest', 'front') }}</option>
                                <option value="popular">{{ custom_trans('popular', 'front') }}</option>
                                <option value="rating">{{ custom_trans('highest_rated', 'front') }}</option>
                                <option value="price_low">{{ custom_trans('price_low_to_high', 'front') }}</option>
                                <option value="price_high">{{ custom_trans('price_high_to_low', 'front') }}</option>
                            </select>
                            <div class="btn-group" role="group" aria-label="View toggle">
                                <button class="btn btn-outline-primary active" id="listViewBtn" type="button">
                                    <i class="fas fa-list me-1"></i>
                                </button>
                                <button class="btn btn-outline-primary" id="gridViewBtn" type="button">
                                    <i class="fas fa-th me-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Grid -->
                    <div class="row g-4" id="coursesList">
                        @forelse($courses as $course)
                            <div class="col-12 course-card-col">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="position-relative">
                                        <img src="{{ $course->image_url }}" class="card-img-top img-h-200"
                                            alt="{{ $course->localized_name }}">
                                        @if ($course->is_featured)
                                            <span class="badge bg-success position-absolute top-0 start-0 m-2">
                                                {{ custom_trans('featured', 'front') }}
                                            </span>
                                        @endif
                                        <div class="position-absolute top-0 end-0 m-2">
                                            @if ($course->price > 0)
                                                <span
                                                    class="badge bg-info text-white">{{ number_format($course->price, 2) }}
                                                    SAR</span>
                                                @if ($course->original_price > $course->price)
                                                    <small class="text-decoration-line-through text-muted d-block">
                                                        {{ number_format($course->original_price, 2) }} SAR
                                                    </small>
                                                @endif
                                            @else
                                                <span class="badge bg-success">{{ custom_trans('free', 'front') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title course-title fw-bold mb-0 flex-grow-1">
                                                <a href="{{ route('courses.show', $course->id) }}"
                                                    class="text-decoration-none text-dark">
                                                    {{ $course->localized_name }}
                                                </a>
                                            </h5>
                                            <div class="d-flex align-items-center gap-2 ms-2">
                                                @auth
                                                    <button class="btn btn-outline-danger btn-sm wishlist-btn"
                                                        data-course-id="{{ $course->id }}"
                                                        data-in-wishlist="{{ auth()->user()->hasInWishlist($course) ? 'true' : 'false' }}">
                                                        <i
                                                            class="fas fa-heart {{ auth()->user()->hasInWishlist($course) ? 'text-danger' : '' }}"></i>
                                                    </button>
                                                @endauth
                                                <div class="d-flex align-items-center">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $course->average_rating)
                                                            <i class="fas fa-star text-warning small"></i>
                                                        @else
                                                            <i class="far fa-star text-warning small"></i>
                                                        @endif
                                                    @endfor
                                                    <small
                                                        class="ms-1 text-muted">{{ number_format($course->average_rating, 1) }}</small>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="card-text text-muted mb-3 flex-grow-1">
                                            {{ Str::limit($course->localized_description, 100) }}
                                        </p>

                                        <!-- Course Meta -->
                                        <div class="course-meta d-flex align-items-center mb-3">
                                            <div class="d-flex align-items-center me-3">
                                                <i class="fas fa-user text-muted me-1"></i>
                                                <small class="text-muted">
                                                    @if ($course->instructors && $course->instructors->count() > 0)
                                                        {{ $course->instructors->pluck('name')->take(2)->join(', ') }}
                                                        @if ($course->instructors->count() > 2)
                                                            +{{ $course->instructors->count() - 2 }}
                                                        @endif
                                                    @else
                                                        {{ $course->instructor->name ?? 'Instructor' }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center me-3">
                                                <i class="fas fa-clock text-muted me-1"></i>
                                                <small class="text-muted">{{ $course->duration ?? 'N/A' }}</small>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users text-muted me-1"></i>
                                                <small class="text-muted">{{ $course->enrolled_students ?? 0 }}
                                                    {{ custom_trans('students', 'front') }}</small>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="{{ route('courses.show', $course->id) }}"
                                                class="btn btn-primary btn-sm">
                                                {{ custom_trans('view_details', 'front') }}
                                            </a>
                                            @auth
                                                @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                                    <a href="{{ route('courses.learn', $course->id) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        {{ custom_trans('go_to_course', 'front') }}
                                                    </a>
                                                @else
                                                    <button class="btn btn-success btn-sm enroll-btn"
                                                        data-course-id="{{ $course->id }}">
                                                        <i class="fas fa-graduation-cap me-1"></i>
                                                        {{ custom_trans('enroll_now', 'front') }}
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-graduation-cap me-1"></i>
                                                    {{ custom_trans('enroll_now', 'front') }}
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                    <h4 class="fw-bold text-muted mb-2">{{ custom_trans('no_courses_found', 'front') }}</h4>
                                    <p class="text-muted">{{ custom_trans('no_courses_in_category', 'front') }}</p>
                                    <a href="{{ route('categories.index') }}" class="btn btn-primary">
                                        {{ custom_trans('browse_all_categories', 'front') }}
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($courses->hasPages())
                        <div class="row mt-4">
                            <div class="col-12">
                                <nav aria-label="Courses pagination">
                                    {{ $courses->links() }}
                                </nav>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/categories.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/categories.css') }}">
    @endpush
@endif

@push('scripts')
    <script>
        // View toggle functionality
        const listBtn = document.getElementById('listViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');
        const coursesList = document.getElementById('coursesList');

        listBtn?.addEventListener('click', () => {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');

            // Update course card columns for list view
            const courseCards = document.querySelectorAll('.course-card-col');
            courseCards.forEach(card => {
                card.className = 'col-12 course-card-col';
                card.classList.remove('d-none'); // Ensure course is visible
            });
        });

        gridBtn?.addEventListener('click', () => {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');

            // Update course card columns for grid view
            const courseCards = document.querySelectorAll('.course-card-col');
            courseCards.forEach(card => {
                card.className = 'col-12 col-md-6 col-lg-4 course-card-col';
                card.classList.remove('d-none'); // Ensure course is visible
            });
        });

        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filters = document.querySelectorAll('.price-filter, .rating-filter');
            const clearBtn = document.getElementById('clearFilters');

            // Ensure all courses are visible on page load
            const courseCards = document.querySelectorAll('.course-card-col');
            courseCards.forEach(card => {
                card.classList.remove('d-none');
            });

            filters.forEach(filter => {
                filter.addEventListener('change', applyFilters);
            });

            clearBtn?.addEventListener('click', clearAllFilters);
        });

        function applyFilters() {
            // Get all active filters
            const activeFilters = {
                price: Array.from(document.querySelectorAll('.price-filter:checked')).map(cb => cb.value),
                rating: Array.from(document.querySelectorAll('.rating-filter:checked')).map(cb => cb.value)
            };

            // Apply filters to course cards
            const courseCards = document.querySelectorAll('.course-card-col');

            courseCards.forEach(card => {
                let show = true;

                // Apply price filter
                if (activeFilters.price.length > 0) {
                    const priceElement = card.querySelector('.position-absolute .badge');
                    if (priceElement) {
                        const price = priceElement.textContent.trim();
                        const isFree = price === '{{ custom_trans('free', 'front') }}';
                        const isPaid = price !== '{{ custom_trans('free', 'front') }}';

                        if (activeFilters.price.includes('free') && !isFree) show = false;
                        if (activeFilters.price.includes('paid') && !isPaid) show = false;
                    }
                }

                // Apply rating filter
                if (activeFilters.rating.length > 0) {
                    const ratingElement = card.querySelector('.d-flex.align-items-center small');
                    if (ratingElement) {
                        const rating = parseFloat(ratingElement.textContent);
                        const hasValidRating = activeFilters.rating.some(r => rating >= parseInt(r));
                        if (!hasValidRating) show = false;
                    }
                }

                // Level filter removed as requested

                if (show) {
                    card.classList.remove('d-none');
                } else {
                    card.classList.add('d-none');
                }
            });
        }

        function clearAllFilters() {
            document.querySelectorAll('.price-filter, .rating-filter').forEach(cb => {
                cb.checked = false;
            });

            document.querySelectorAll('.course-card-col').forEach(card => {
                card.classList.remove('d-none');
            });
        }

        // Wishlist functionality
        document.addEventListener('DOMContentLoaded', function() {
            const wishlistButtons = document.querySelectorAll('.wishlist-btn');

            wishlistButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const inWishlist = this.dataset.inWishlist === 'true';
                    const icon = this.querySelector('i');

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        toastr.error('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    // Send AJAX request
                    fetch(`/wishlist/${courseId}/toggle`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                if (response.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                // Update button state
                                this.dataset.inWishlist = data.inWishlist;
                                if (data.inWishlist) {
                                    icon.classList.add('text-danger');
                                    toastr.success(data.message || 'Course added to wishlist!');
                                } else {
                                    icon.classList.remove('text-danger');
                                    toastr.info(data.message ||
                                        'Course removed from wishlist!');
                                }
                            } else if (data) {
                                toastr.error(data.message || 'An error occurred');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred. Please try again.');
                        });
                });
            });

            // Enrollment functionality
            const enrollButtons = document.querySelectorAll('.enroll-btn');

            enrollButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const originalText = this.innerHTML;

                    // Disable button and show loading
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enrolling...';

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        toastr.error('CSRF token not found. Please refresh the page.');
                        this.disabled = false;
                        this.innerHTML = originalText;
                        return;
                    }

                    // Send AJAX request to enroll
                    fetch(`/courses/${courseId}/enroll`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => {
                            if (!response.ok) {
                                if (response.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                // Show enrolled state first
                                this.className = 'btn btn-success btn-sm';
                                this.innerHTML = '<i class="fas fa-check me-1"></i>Enrolled';
                                this.disabled = true;

                                // Show success message
                                toastr.success(data.message ||
                                    'Successfully enrolled in course!');

                                // After 2 seconds, replace with "Go to Course" link
                                setTimeout(() => {
                                    const goToCourseLink = document.createElement('a');
                                    goToCourseLink.href = `/courses/${courseId}/learn`;
                                    goToCourseLink.className = 'btn btn-success btn-sm';
                                    goToCourseLink.innerHTML =
                                        '<i class="fas fa-graduation-cap me-1"></i>{{ custom_trans('go_to_course', 'front') }}';

                                    this.parentNode.replaceChild(goToCourseLink, this);
                                }, 2000);
                            } else if (data) {
                                toastr.error(data.message || 'An error occurred');
                                this.disabled = false;
                                this.innerHTML = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred. Please try again.');
                            this.disabled = false;
                            this.innerHTML = originalText;
                        });
                });
            });
        });
    </script>
@endpush
