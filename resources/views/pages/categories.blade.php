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
                                class="text-decoration-none">{{ custom_trans('home', 'front') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('categories') }}"
                                class="text-decoration-none">{{ custom_trans('categories', 'front') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ \App\Helpers\TranslationHelper::getLocalizedContent($selectedCategory->name, $selectedCategory->name_ar) }}
                        </li>
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
                    {{ \App\Helpers\TranslationHelper::getLocalizedContent($selectedCategory->name, $selectedCategory->name_ar) }}
                @else
                    {{ custom_trans('explore_categories', 'front') }}
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
                            <i class="fas fa-graduation-cap me-2 mb-2 fs-2rem"></i>
                            <span
                                class="fw-semibold">{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}</span>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-muted">{{ custom_trans('no_categories_found', 'front') }}</p>
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
                            {{ \App\Helpers\TranslationHelper::getLocalizedContent($selectedCategory->name, $selectedCategory->name_ar) }}
                            {{ custom_trans('courses', 'front') }}
                        @else
                            {{ custom_trans('featured_courses', 'front') }}
                        @endif
                    </h2>
                    <div class="d-flex gap-2">
                        <select class="form-select sort-dropdown me-2 max-w-120">
                            <option>{{ custom_trans('sort', 'front') }}</option>
                            <option value="newest">{{ custom_trans('newest', 'front') }}</option>
                            <option value="popular">{{ custom_trans('popular', 'front') }}</option>
                        </select>
                        <select class="form-select limit-dropdown max-w-120">
                            <option>{{ custom_trans('limit', 'front') }}</option>
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
                        <h5 class="fw-bold mb-3">{{ custom_trans('categories', 'front') }}</h5>
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('categories') }}"
                                    class="filter-link {{ !$selectedCategory ? 'fw-bold text-primary' : '' }}">{{ custom_trans('all_categories', 'front') }}</a>
                            </li>
                            @foreach ($categories as $category)
                                <li><a href="{{ route('categories') }}?category={{ $category->slug }}"
                                        class="filter-link {{ $selectedCategory && $selectedCategory->id == $category->id ? 'fw-bold text-primary' : '' }}">{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                    </a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="filter-section p-3 rounded-3 shadow-sm bg-white">
                        <h5 class="fw-bold mb-3">{{ custom_trans('price', 'front') }}</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="priceFree">
                            <label class="form-check-label" for="priceFree">{{ custom_trans('free', 'front') }}</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="pricePaid">
                            <label class="form-check-label" for="pricePaid">{{ custom_trans('paid', 'front') }}</label>
                        </div>
                    </div>
                </div>
                <!-- Courses List/Grid -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-end align-items-center mb-3">
                        <div class="view-toggle-group" role="group" aria-label="{{ custom_trans('View', 'front') }}">
                            <button class="btn" id="listViewBtn" type="button" title="{{ custom_trans('list', 'front') }}">
                                <i class="fas fa-list"></i>
                                <span class="d-none d-sm-inline">{{ custom_trans('list', 'front') }}</span>
                            </button>
                            <button class="btn active" id="gridViewBtn" type="button" title="{{ custom_trans('grid', 'front') }}">
                                <i class="fas fa-th"></i>
                                <span class="d-none d-sm-inline">{{ custom_trans('grid', 'front') }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="row g-4 row-cols-md-2 row-cols-lg-3" id="coursesList">
                        @forelse($courses as $course)
                            <div class="col-12 course-card-col">
                                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                                    <div class="position-relative" style="height: 180px; overflow: hidden;">
                                        <a href="{{ route('courses.show', $course->id) }}" class="d-block h-100 text-decoration-none">
                                            <img src="{{ $course->image_url }}" class="card-img-top"
                                                alt="{{ $course->localized_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </a>
                                        @if ($course->is_featured)
                                            <span class="badge bg-success position-absolute top-0 start-0 m-2" style="z-index: 2; font-size: 0.75rem; padding: 6px 12px; font-weight: 600; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);">
                                                {{ custom_trans('featured', 'front') }}
                                            </span>
                                        @endif
                                        <div class="position-absolute top-0 end-0 m-2 d-flex flex-column align-items-end gap-1" style="z-index: 3; max-width: 45%;">
                                            @if ($course->price > 0)
                                                @if ($course->original_price > $course->price)
                                                    <span class="badge bg-secondary text-white text-decoration-line-through" style="font-size: 0.65rem; padding: 3px 6px; opacity: 0.9; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);">
                                                        {{ number_format($course->original_price, 2) }} SAR
                                                    </span>
                                                @endif
                                                <span class="badge bg-info text-white fw-bold" style="font-size: 0.7rem; padding: 4px 8px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);">
                                                    {{ number_format($course->price, 2) }} SAR
                                                </span>
                                            @else
                                                <span class="badge bg-success" style="font-size: 0.7rem; padding: 4px 8px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);">{{ custom_trans('free', 'front') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column" style="padding: 1rem;">
                                        <!-- Course Title - Full Width -->
                                        <h5 class="card-title course-title fw-bold mb-2" style="font-size: 1rem; line-height: 1.3; min-height: 2.6em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <a href="{{ route('courses.show', $course->id) }}"
                                                class="text-decoration-none text-dark">
                                                {{ $course->localized_name }}
                                            </a>
                                        </h5>
                                        
                                        <!-- Rating and Wishlist - Separate Line -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= ($course->average_rating ?? 0))
                                                        <i class="fas fa-star text-warning" style="font-size: 0.8rem;"></i>
                                                    @else
                                                        <i class="far fa-star text-warning" style="font-size: 0.8rem;"></i>
                                                    @endif
                                                @endfor
                                                <small class="ms-1 text-muted fw-semibold" style="font-size: 0.8rem;">{{ number_format($course->average_rating ?? 0, 1) }}</small>
                                            </div>
                                            @auth
                                                <button class="btn btn-outline-danger btn-sm wishlist-btn"
                                                    data-course-id="{{ $course->id }}"
                                                    data-in-wishlist="{{ auth()->user()->hasInWishlist($course) ? 'true' : 'false' }}"
                                                    style="padding: 4px 6px; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border: 1px solid #dc3545;">
                                                    <i class="fas fa-heart {{ auth()->user()->hasInWishlist($course) ? 'text-danger' : '' }}" style="font-size: 0.75rem;"></i>
                                                </button>
                                            @endauth
                                        </div>

                                        <!-- Course Meta - Only Lessons Count -->
                                        <div class="course-meta d-flex align-items-center mb-2" style="padding-top: 0.5rem; border-top: 1px solid #e9ecef;">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-book text-muted me-2" style="font-size: 0.8rem;"></i>
                                                <small class="text-muted fw-semibold" style="font-size: 0.85rem;">
                                                    {{ $course->total_lessons_count ?? $course->publishedLectures()->count() ?? 0 }} 
                                                    {{ custom_trans('lessons', 'front') }}
                                                </small>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center" style="gap: 0.5rem; margin-top: auto; padding-top: 0.5rem;">
                                            <a href="{{ route('courses.show', $course->id) }}"
                                                class="btn btn-primary btn-sm" style="flex: 1; font-size: 0.75rem; padding: 0.4rem 0.6rem; border-radius: 6px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ custom_trans('view_details', 'front') }}
                                            </a>
                                            @auth
                                                @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                                    <a href="{{ route('courses.learn', $course->id) }}"
                                                        class="btn btn-success btn-sm" style="flex: 1; font-size: 0.75rem; padding: 0.4rem 0.6rem; border-radius: 6px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        <i class="fas fa-graduation-cap me-1" style="font-size: 0.7rem;"></i>
                                                        <span>{{ custom_trans('go_to_course', 'front') }}</span>
                                                    </a>
                                                @else
                                                    <button class="btn btn-success btn-sm category-enroll-btn"
                                                        data-course-id="{{ $course->id }}"
                                                        data-enroll-type="{{ $course->price > 0 ? 'paid' : 'free' }}"
                                                        style="flex: 1; font-size: 0.75rem; padding: 0.4rem 0.6rem; border-radius: 6px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        <i class="fas fa-graduation-cap me-1" style="font-size: 0.7rem;"></i>
                                                        <span>{{ custom_trans('enroll_now', 'front') }}</span>
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-success btn-sm" style="flex: 1; font-size: 0.75rem; padding: 0.4rem 0.6rem; border-radius: 6px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <i class="fas fa-graduation-cap me-1" style="font-size: 0.7rem;"></i>
                                                    <span>{{ custom_trans('enroll_now', 'front') }}</span>
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <p class="text-muted">{{ custom_trans('no_courses_found', 'front') }}</p>
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
                    fetch(`/wishlist/toggle/${courseId}`, {
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
