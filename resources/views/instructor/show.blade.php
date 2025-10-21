@extends('layouts.app')

@section('title', $instructor->name . ' - ' . __('Instructor Profile'))

@section('content')
    <!-- Instructor Profile Header -->
    <section class="instructor-profile-header py-5 bg-gradient-primary position-relative">
        <!-- Instructor Cover Image Background -->
        @if ($instructor->cover)
            <div class="instructor-cover-bg position-absolute w-100 h-100">
                <img src="{{ $instructor->cover_url }}" alt="{{ $instructor->name }} Cover" class="w-100 h-100">
            </div>
        @endif

        <div class="container position-relative instructor-page-container">
            <div class="row align-items-center">
                <div class="col-lg-4 text-center">
                    <div class="instructor-avatar-wrapper mb-4">
                        <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}"
                            class="instructor-avatar rounded-circle shadow-lg instructor-large-avatar">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="instructor-info text-white">
                        <h1 class="display-4 fw-bold mb-3">{{ $instructor->name }}</h1>
                        <p class="lead mb-3">
                            <i class="fas fa-graduation-cap me-2"></i>{{ __('Instructor') }}
                        </p>
                        <div class="instructor-stats d-flex flex-wrap gap-4 mb-4">
                            <div class="stat-item text-center">
                                <h3 class="fw-bold mb-1">{{ $instructor->courses->count() }}</h3>
                                <p class="mb-0">{{ __('Courses') }}</p>
                            </div>
                            <div class="stat-item text-center">
                                <h3 class="fw-bold mb-1">{{ $instructor->courses->sum('enrolled_students') }}</h3>
                                <p class="mb-0">{{ __('Students') }}</p>
                            </div>
                            <div class="stat-item text-center">
                                <h3 class="fw-bold mb-1">
                                    {{ $instructor->courses->avg('average_rating') ? number_format($instructor->courses->avg('average_rating'), 1) : 'N/A' }}
                                </h3>
                                <p class="mb-0">{{ __('Rating') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instructor Courses -->
    <section class="instructor-courses py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="section-title text-center mb-5">
                        <span class="text-warning fw-bold">{{ __('Courses by') }}</span> {{ $instructor->name }}
                    </h2>
                </div>
            </div>

            @if ($instructor->courses->count() > 0)
                <div class="row">
                    @foreach ($instructor->courses as $course)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="course-card h-100 shadow-sm rounded">
                                <div class="course-image-wrapper position-relative">
                                    <img src="{{ $course->image_url }}" alt="{{ $course->name }}"
                                        class="course-image w-100 img-h-200">
                                    @if ($course->is_featured)
                                        <span class="badge bg-warning position-absolute top-0 start-0 m-2">
                                            {{ __('Featured') }}
                                        </span>
                                    @endif
                                    @if ($course->is_free)
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                            {{ __('Free') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="course-content p-3">
                                    <h5 class="course-title fw-bold mb-2">
                                        <a href="{{ route('courses.show', $course) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $course->name }}
                                        </a>
                                    </h5>
                                    <p class="course-description text-muted mb-3">
                                        {{ Str::limit($course->description, 100) }}
                                    </p>
                                    <div class="course-meta d-flex justify-content-between align-items-center mb-3">
                                        <div class="course-rating">
                                            @if ($course->average_rating)
                                                <div class="d-flex align-items-center">
                                                    <div class="text-warning me-2">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i
                                                                class="fas fa-star{{ $i <= $course->average_rating ? '' : '-o' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <small class="text-muted">({{ $course->total_ratings }})</small>
                                                </div>
                                            @else
                                                <small class="text-muted">{{ __('No ratings yet') }}</small>
                                            @endif
                                        </div>
                                        <div class="course-price">
                                            @if ($course->is_free)
                                                <span class="text-success fw-bold">{{ __('Free') }}</span>
                                            @else
                                                <span class="text-primary fw-bold">{{ $course->formatted_price }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="course-stats d-flex justify-content-between text-muted small mb-3">
                                        <span><i class="fas fa-users me-1"></i>{{ $course->enrolled_students }}
                                            {{ __('students') }}</span>
                                        <span><i class="fas fa-clock me-1"></i>{{ $course->duration }}</span>
                                    </div>
                                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary w-100">
                                        {{ __('View Course') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">{{ __('No courses yet') }}</h4>
                        <p class="text-muted">{{ __('This instructor hasn\'t published any courses yet.') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/instructor.css') }}">
@endpush

