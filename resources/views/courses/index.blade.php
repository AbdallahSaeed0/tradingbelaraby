@extends('layouts.app')

@section('title', 'All Courses')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/courses-listing.css') }}">
@endpush
@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/courses-listing.css') }}">
    @endpush
@endif

@section('content')
    <div class="courses-page container py-5">
        <div class="row">
            <!-- Sidebar with filters -->
            <div class="col-lg-3 mb-4 courses-page__sidebar">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('courses.index') }}" id="filterForm">
                            <input type="hidden" name="view" value="{{ $viewMode ?? 'grid' }}">
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif
                            <!-- Category Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Categories</label>
                                @foreach ($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]"
                                            value="{{ $category->id }}" id="category_{{ $category->id }}"
                                            {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                            <span class="badge bg-secondary ms-1">{{ $category->courses_count }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Price Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Price</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price" value="free"
                                        id="price_free" {{ request('price') == 'free' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price_free">{{ custom_trans('free', 'front') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price" value="paid"
                                        id="price_paid" {{ request('price') == 'paid' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price_paid">{{ custom_trans('Paid', 'front') }}</label>
                                </div>
                            </div>

                            <!-- Level Filter - Removed as level field doesn't exist in courses table -->
                            {{-- <div class="mb-3">
                            <label class="form-label fw-bold">Level</label>
                            <select name="level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div> --}}

                            <!-- Rating Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Minimum Rating</label>
                                <select name="rating" class="form-select">
                                    <option value="">Any Rating</option>
                                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars
                                    </option>
                                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars
                                    </option>
                                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2+ Stars
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Apply Filters
                            </button>

                            @if (request()->hasAny(['categories', 'price', 'rating']))
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fas fa-times me-2"></i>Clear Filters
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-lg-9">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">All Courses</h1>
                        <p class="text-muted mb-0">{{ $courses->total() }} courses available</p>
                    </div>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <select class="form-select sort-dropdown w-auto" id="coursesSortDropdown" aria-label="{{ custom_trans('Sort By', 'front') }}">
                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>{{ custom_trans('Latest', 'front') }}</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ custom_trans('Most Popular', 'front') }}</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ custom_trans('Highest Rated', 'front') }}</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ custom_trans('Price: Low to High', 'front') }}</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ custom_trans('Price: High to Low', 'front') }}</option>
                        </select>
                        <div class="btn-group" role="group" aria-label="{{ custom_trans('View', 'front') }}">
                            <a id="gridViewBtn" href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="btn btn-outline-primary {{ ($viewMode ?? 'grid') === 'grid' ? 'active' : '' }}" title="{{ custom_trans('grid', 'front') }}">
                                <i class="fas fa-th-large"></i><span class="d-none d-sm-inline ms-1">{{ custom_trans('grid', 'front') }}</span>
                            </a>
                            <a id="listViewBtn" href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="btn btn-outline-primary {{ ($viewMode ?? 'grid') === 'list' ? 'active' : '' }}" title="{{ custom_trans('list', 'front') }}">
                                <i class="fas fa-list"></i><span class="d-none d-sm-inline ms-1">{{ custom_trans('list', 'front') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Courses Grid / List -->
                @if ($courses->count() > 0)
                    @if (($viewMode ?? 'grid') === 'list')
                    <div class="courses-page__list row g-4">
                        @foreach ($courses as $course)
                            <div class="col-12">
                                <article class="course-card course-card--list shadow-sm bg-white rounded-4 overflow-hidden">
                                    <div class="course-card--list__inner d-flex flex-column flex-md-row">
                                        <div class="course-card--list__image-wrap">
                                            <a href="{{ route('courses.show', $course) }}" class="d-block text-decoration-none h-100">
                                                @if ($course->image)
                                                    <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}" class="course-card--list__img">
                                                @else
                                                    <div class="course-placeholder course-card--list__placeholder"><i class="fas fa-graduation-cap"></i></div>
                                                @endif
                                            </a>
                                            @if ($course->is_featured)
                                                <span class="badge bg-warning position-absolute top-0 start-0 m-2"><i class="fas fa-star me-1"></i>{{ custom_trans('Featured', 'front') }}</span>
                                            @endif
                                            <div class="course-card--list__price-badge position-absolute top-0 end-0 m-2">
                                                @if ($course->is_free)
                                                    <span class="badge bg-success">{{ custom_trans('free', 'front') }}</span>
                                                @else
                                                    <span class="badge bg-primary">${{ number_format($course->price, 2) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="course-card--list__body flex-grow-1 d-flex flex-column p-3 p-md-4">
                                            <div class="course-category mb-2">
                                                <span class="badge bg-light text-dark"><i class="fas fa-folder me-1"></i>{{ \App\Helpers\TranslationHelper::getLocalizedContent($course->category->name ?? '', $course->category->name_ar ?? '') ?: 'Uncategorized' }}</span>
                                            </div>
                                            <h5 class="course-card--list__title mb-2">
                                                <a href="{{ route('courses.show', $course) }}" class="text-decoration-none text-dark">{{ Str::limit($course->localized_name, 60) }}</a>
                                            </h5>
                                            <p class="course-card--list__desc text-muted small mb-2 flex-grow-1">{{ Str::limit($course->localized_description, 120) }}</p>
                                            <div class="course-meta d-flex justify-content-between align-items-center mb-2 small text-muted">
                                                <span><i class="fas fa-user me-1"></i>@if ($course->instructors && $course->instructors->count() > 0){{ $course->instructors->pluck('name')->take(2)->join(', ') }}@else{{ $course->instructor->name ?? '—' }}@endif</span>
                                                <span>@if ($course->average_rating > 0)<i class="fas fa-star text-warning"></i> {{ number_format($course->average_rating, 1) }} ({{ $course->ratings_count }})@else—@endif</span>
                                            </div>
                                            <div class="course-card--list__footer d-flex flex-wrap gap-2 align-items-center justify-content-between mt-auto pt-2 border-top">
                                                <span class="text-muted small"><i class="fas fa-users me-1"></i>{{ $course->enrollments_count }} enrolled</span>
                                                <div class="course-card--list__actions d-flex gap-2">
                                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary btn-sm">{{ custom_trans('Show details', 'front') }}</a>
                                                    @auth
                                                        @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                                            <a href="{{ route('courses.learn', $course->id) }}" class="btn btn-success btn-sm">{{ custom_trans('go_to_course', 'front') }}</a>
                                                        @else
                                                            <button type="button" class="btn btn-primary btn-sm enroll-btn" data-course-id="{{ $course->id }}" data-enroll-type="{{ $course->price > 0 ? 'paid' : 'free' }}"><i class="fa fa-graduation-cap me-1"></i>{{ custom_trans('enroll_now', 'front') }}</button>
                                                        @endif
                                                    @else
                                                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">{{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}</a>
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="row g-4">
                        @foreach ($courses as $course)
                            <div class="col-lg-4 col-md-6">
                                <div class="course-card h-100 shadow-sm">
                                    <div class="course-img-wrap">
                                        <div class="course-image position-relative">
                                            <a href="{{ route('courses.show', $course) }}" class="d-block text-decoration-none">
                                                @if ($course->image)
                                                    <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}"
                                                        class="img-fluid">
                                                @else
                                                    <div class="course-placeholder">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </div>
                                                @endif
                                            </a>

                                            @if ($course->is_featured)
                                                <span class="badge bg-warning position-absolute top-0 start-0 m-2">
                                                    <i class="fas fa-star me-1"></i>Featured
                                                </span>
                                            @endif

                                            <div class="course-overlay">
                                                <a href="{{ route('courses.show', $course) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Course
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Instructor Cover Image -->
                                        <div class="instructor-cover mt-2">
                                            <img src="{{ $course->instructor->cover_url ?? 'https://via.placeholder.com/300x100/007bff/ffffff?text=Instructor' }}"
                                                alt="{{ $course->instructor->name ?? 'Instructor' }} Cover"
                                                class="course-img img-fluid rounded">
                                        </div>
                                    </div>

                                    <div class="course-content p-3">
                                        <div class="course-category mb-2">
                                            <span class="badge bg-light text-dark">
                                                <i
                                                    class="fas fa-folder me-1"></i>{{ \App\Helpers\TranslationHelper::getLocalizedContent($course->category->name ?? '', $course->category->name_ar ?? '') ?: 'Uncategorized' }}
                                            </span>
                                        </div>

                                        <h5 class="course-title mb-2">
                                            <a href="{{ route('courses.show', $course) }}"
                                                class="text-decoration-none text-dark">
                                                {{ Str::limit($course->localized_name, 50) }}
                                            </a>
                                        </h5>

                                        <p class="course-description text-muted small mb-3">
                                            {{ Str::limit($course->localized_description, 80) }}
                                        </p>

                                        <div class="course-meta d-flex justify-content-between align-items-center">
                                            <div class="course-instructor">
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    @if ($course->instructors && $course->instructors->count() > 0)
                                                        {{ $course->instructors->pluck('name')->take(2)->join(', ') }}
                                                        @if ($course->instructors->count() > 2)
                                                            +{{ $course->instructors->count() - 2 }}
                                                        @endif
                                                    @else
                                                        {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="course-rating">
                                                @if ($course->average_rating > 0)
                                                    <small class="text-warning">
                                                        <i class="fas fa-star"></i>
                                                        {{ number_format($course->average_rating, 1) }}
                                                        <span class="text-muted">({{ $course->ratings_count }})</span>
                                                    </small>
                                                @else
                                                    <small class="text-muted">No ratings</small>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="course-footer mt-3 pt-3 border-top">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="course-price">
                                                    @if ($course->is_free)
                                                        <span class="text-success fw-bold">{{ custom_trans('free', 'front') }}</span>
                                                    @else
                                                        <span
                                                            class="text-primary fw-bold">${{ number_format($course->price, 2) }}</span>
                                                    @endif
                                                </div>
                                                <div class="course-enrollments">
                                                    <small class="text-muted">
                                                        <i class="fas fa-users me-1"></i>
                                                        {{ $course->enrollments_count }} enrolled
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-5">
                        {{ $courses->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-search fa-3x text-muted"></i>
                        </div>
                        <h4 class="text-muted">No courses found</h4>
                        <p class="text-muted">Try adjusting your filters or search criteria.</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>Clear All Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sortDropdown = document.getElementById('coursesSortDropdown');
    if (sortDropdown) {
        sortDropdown.addEventListener('change', function() {
            var url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            url.searchParams.set('view', '{{ $viewMode ?? "grid" }}');
            window.location.href = url.toString();
        });
    }
});
</script>
@endpush



