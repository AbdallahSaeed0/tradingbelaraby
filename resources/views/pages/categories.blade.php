@extends('layouts.app')

@section('title', 'Categories - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Breadcrumb -->
    @if ($selectedCategory)
        <section class="py-3 bg-light">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                class="text-decoration-none">{{ custom_trans('home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('categories') }}"
                                class="text-decoration-none">{{ custom_trans('categories') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $selectedCategory->name }}</li>
                    </ol>
                </nav>
            </div>
        </section>
    @endif

    <!-- Category Banner -->
    <section class="category-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png"
            alt="Banner" class="category-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="category-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-3 fw-bold text-white mb-3">
                @if ($selectedCategory)
                    {{ $selectedCategory->name }}
                @else
                    {{ custom_trans('explore_categories') }}
                @endif
            </h1>
            <!-- Course count removed as requested -->
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center g-4">
                @forelse($categories as $category)
                    <div class="col-12 col-md-4 col-lg-3">
                        <a href="{{ route('categories') }}?category={{ $category->slug }}"
                            class="btn {{ $selectedCategory && $selectedCategory->id == $category->id ? 'btn-primary' : 'btn-light' }} w-100 py-4 shadow-sm d-flex flex-column align-items-center justify-content-center subcat-btn text-decoration-none">
                            <i class="fas fa-graduation-cap me-2 mb-2" style="font-size: 2rem;"></i>
                            <span class="fw-semibold">{{ $category->name }}</span>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">{{ custom_trans('no_categories_found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section class="category-courses-section py-4 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4 d-flex flex-wrap align-items-center justify-content-between">
                    <h2 class="fw-bold mb-3 mb-md-0">
                        @if ($selectedCategory)
                            {{ $selectedCategory->name }} {{ custom_trans('courses') }}
                        @else
                            {{ custom_trans('featured_courses') }}
                        @endif
                    </h2>
                    <div class="d-flex gap-2">
                        <select class="form-select sort-dropdown me-2" style="max-width:120px;">
                            <option>{{ custom_trans('sort') }}</option>
                            <option value="newest">{{ custom_trans('newest') }}</option>
                            <option value="popular">{{ custom_trans('popular') }}</option>
                        </select>
                        <select class="form-select limit-dropdown" style="max-width:120px;">
                            <option>{{ custom_trans('limit') }}</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Sidebar Filters -->
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <div class="filter-section p-3 rounded-3 shadow-sm bg-white mb-4">
                        <h5 class="fw-bold mb-3">{{ custom_trans('categories') }}</h5>
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('categories') }}"
                                    class="filter-link {{ !$selectedCategory ? 'fw-bold text-primary' : '' }}">{{ custom_trans('all_categories') }}</a>
                            </li>
                            @foreach ($categories as $category)
                                <li><a href="{{ route('categories') }}?category={{ $category->slug }}"
                                        class="filter-link {{ $selectedCategory && $selectedCategory->id == $category->id ? 'fw-bold text-primary' : '' }}">{{ $category->name }}
                                    </a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="filter-section p-3 rounded-3 shadow-sm bg-white">
                        <h5 class="fw-bold mb-3">{{ custom_trans('price') }}</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="priceFree">
                            <label class="form-check-label" for="priceFree">{{ custom_trans('free') }}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pricePaid">
                            <label class="form-check-label" for="pricePaid">{{ custom_trans('paid') }}</label>
                        </div>
                    </div>
                </div>
                <!-- Courses List/Grid -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-end align-items-center mb-3">
                        <div class="btn-group" role="group" aria-label="View toggle">
                            <button class="btn btn-outline-primary active" id="listViewBtn" type="button">
                                <i class="fas fa-list me-1"></i>
                            </button>
                            <button class="btn btn-outline-primary" id="gridViewBtn" type="button">
                                <i class="fas fa-th me-1"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row g-4" id="coursesList">
                        @forelse($courses as $course)
                            <div class="col-12 course-card-col">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="position-relative">
                                        <img src="{{ $course->image_url }}" class="card-img-top" alt="{{ $course->name }}"
                                            style="height: 200px; object-fit: cover;">
                                        @if ($course->is_featured)
                                            <span class="badge bg-success position-absolute top-0 start-0 m-2">
                                                {{ custom_trans('featured') }}
                                            </span>
                                        @endif
                                        <div class="position-absolute top-0 end-0 m-2">
                                            @if ($course->price > 0)
                                                <span
                                                    class="badge bg-info text-white">{{ number_format($course->price, 2) }}₹</span>
                                                @if ($course->original_price > $course->price)
                                                    <small class="text-decoration-line-through text-muted d-block">
                                                        {{ number_format($course->original_price, 2) }}₹
                                                    </small>
                                                @endif
                                            @else
                                                <span class="badge bg-success">{{ custom_trans('free') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title fw-bold mb-0 flex-grow-1">{{ $course->name }}</h5>
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
                                            {{ Str::limit($course->description, 100) }}
                                        </p>
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('courses.show', $course->id) }}"
                                                class="btn btn-primary btn-sm">
                                                {{ custom_trans('view_details') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p class="text-muted">{{ custom_trans('no_courses_found') }}</p>
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

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/categories.css') }}">
@endpush

@push('scripts')
    <script>
        // Simple toggle between list/grid view
        const listBtn = document.getElementById('listViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');
        const coursesList = document.getElementById('coursesList');

        listBtn?.addEventListener('click', () => {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            coursesList.classList.remove('row-cols-md-2', 'row-cols-lg-3');
            coursesList.classList.add('row-cols-1');
        });

        gridBtn?.addEventListener('click', () => {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            coursesList.classList.remove('row-cols-1');
            coursesList.classList.add('row-cols-md-2', 'row-cols-lg-3');
        });

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

                                // Update wishlist count in header
                                const wishlistCount = document.querySelector(
                                    '.user-actions .fa-heart').parentElement.querySelector(
                                    '.badge');
                                if (wishlistCount) {
                                    // You can update the count here if needed
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
        });
    </script>
@endpush
