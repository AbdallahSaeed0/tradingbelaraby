@extends('layouts.app')

@section('title', 'All Courses')

@section('content')
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar with filters -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('courses.index') }}" id="filterForm">
                            <!-- Category Filter -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Categories</label>
                                @foreach ($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="categories[]"
                                            value="{{ $category->id }}" id="category_{{ $category->id }}"
                                            {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
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
                                    <label class="form-check-label" for="price_free">Free</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="price" value="paid"
                                        id="price_paid" {{ request('price') == 'paid' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="price_paid">Paid</label>
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
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-2"></i>Sort By
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">Latest</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}">Most Popular</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}">Highest Rated</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}">Price: Low to
                                        High</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}">Price: High to
                                        Low</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Courses Grid -->
                @if ($courses->count() > 0)
                    <div class="row g-4">
                        @foreach ($courses as $course)
                            <div class="col-lg-4 col-md-6">
                                <div class="course-card h-100 shadow-sm">
                                    <div class="course-img-wrap">
                                        <div class="course-image">
                                            @if ($course->image)
                                                <img src="{{ $course->image_url }}" alt="{{ $course->name }}"
                                                    class="img-fluid">
                                            @else
                                                <div class="course-placeholder">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </div>
                                            @endif

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
                                                    class="fas fa-folder me-1"></i>{{ $course->category->name ?? 'Uncategorized' }}
                                            </span>
                                        </div>

                                        <h5 class="course-title mb-2">
                                            <a href="{{ route('courses.show', $course) }}"
                                                class="text-decoration-none text-dark">
                                                {{ Str::limit($course->name, 50) }}
                                            </a>
                                        </h5>

                                        <p class="course-description text-muted small mb-3">
                                            {{ Str::limit($course->description, 80) }}
                                        </p>

                                        <div class="course-meta d-flex justify-content-between align-items-center">
                                            <div class="course-instructor">
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $course->instructor->name ?? 'Unknown Instructor' }}
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
                                                        <span class="text-success fw-bold">Free</span>
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

@push('styles')
    <style>
        .course-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .course-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-card:hover .course-image img {
            transform: scale(1.05);
        }

        .course-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 3rem;
        }

        .course-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .course-card:hover .course-overlay {
            opacity: 1;
        }

        .course-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .course-title a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .course-title a:hover {
            color: #f15a29;
        }

        .course-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .course-instructor {
            font-size: 0.85rem;
        }

        .course-rating {
            font-size: 0.85rem;
        }

        .course-footer {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        .course-price {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .course-enrollments {
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .course-image {
                height: 160px;
            }

            .course-content {
                padding: 1rem;
            }

            .course-title {
                font-size: 1rem;
            }

            .course-description {
                font-size: 0.85rem;
            }
        }

        /* Instructor Cover Image Styles */
        .course-img-wrap {
            position: relative;
        }

        .instructor-cover {
            height: 80px;
            overflow: hidden;
            border-radius: 8px;
        }

        .course-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-card:hover .course-img {
            transform: scale(1.02);
        }
    </style>
@endpush
