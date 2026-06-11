@extends('layouts.app')

@section('title', custom_trans('My Courses', 'front'))

@section('content')
    @php
        $isRtl = \App\Helpers\TranslationHelper::getCurrentLanguage()->direction === 'rtl';
        $levelLabels = [
            'beginner' => custom_trans('Beginner', 'front'),
            'intermediate' => custom_trans('Intermediate', 'front'),
            'advanced' => custom_trans('Advanced', 'front'),
        ];
    @endphp

    @if ($isRtl)
        @push('rtl-styles')
            <link rel="stylesheet" href="{{ asset('css/rtl/pages/my-courses.css') }}">
        @endpush
    @else
        @push('styles')
            <link rel="stylesheet" href="{{ asset('css/pages/my-courses.css') }}">
        @endpush
    @endif

    <div class="container py-5 my-courses-page">
        <!-- Page Header -->
        <div class="row mb-4 my-courses-page__header">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-0">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>{{ custom_trans('My Courses', 'front') }}
                        </h1>
                        <p class="text-muted">{{ custom_trans('Track your learning progress and continue your courses', 'front') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('courses.search') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>{{ custom_trans('Find More Courses', 'front') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="my-courses-stat h-100">
                    <div class="card-body text-center py-4">
                        <div class="my-courses-stat__icon my-courses-stat__icon--primary">
                            <i class="fas fa-book fa-lg"></i>
                        </div>
                        <div class="my-courses-stat__value text-primary">{{ $stats['total_courses'] }}</div>
                        <p class="my-courses-stat__label">{{ custom_trans('Total Courses', 'front') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="my-courses-stat h-100">
                    <div class="card-body text-center py-4">
                        <div class="my-courses-stat__icon my-courses-stat__icon--success">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                        <div class="my-courses-stat__value text-success">{{ $stats['completed_courses'] }}</div>
                        <p class="my-courses-stat__label">{{ custom_trans('Completed', 'front') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="my-courses-stat h-100">
                    <div class="card-body text-center py-4">
                        <div class="my-courses-stat__icon my-courses-stat__icon--warning">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <div class="my-courses-stat__value text-warning">{{ $stats['in_progress_courses'] }}</div>
                        <p class="my-courses-stat__label">{{ custom_trans('In Progress', 'front') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="my-courses-stat h-100">
                    <div class="card-body text-center py-4">
                        <div class="my-courses-stat__icon my-courses-stat__icon--info">
                            <i class="fas fa-chart-line fa-lg"></i>
                        </div>
                        <div class="my-courses-stat__value text-info">{{ $stats['average_progress'] }}%</div>
                        <p class="my-courses-stat__label">{{ custom_trans('Avg Progress', 'front') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="my-courses-filters card mb-4 border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('student.my-courses') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                    placeholder="{{ custom_trans('Search your courses...', 'front') }}" value="{{ request('search') }}" id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">{{ custom_trans('All Status', 'front') }}</option>
                                <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>{{ custom_trans('In Progress', 'front') }}
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ custom_trans('Completed', 'front') }}
                                </option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ custom_trans('Pending Confirmation', 'front') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort" id="sortFilter">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ custom_trans('Latest First', 'front') }}
                                </option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ custom_trans('Oldest First', 'front') }}
                                </option>
                                <option value="progress_high" {{ request('sort') == 'progress_high' ? 'selected' : '' }}>
                                    {{ custom_trans('Progress High', 'front') }}</option>
                                <option value="progress_low" {{ request('sort') == 'progress_low' ? 'selected' : '' }}>
                                    {{ custom_trans('Progress Low', 'front') }}</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ custom_trans('Course Name', 'front') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>{{ custom_trans('Filter', 'front') }}
                                </button>
                                <a href="{{ route('student.my-courses') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Courses Grid -->
        @if ($enrollments->count() > 0)
            <div class="row g-4">
                @foreach ($enrollments as $enrollment)
                    @php
                        $course = $enrollment->course;
                        $progress = (int) ($enrollment->progress_percentage ?? 0);
                        $levelKey = strtolower($course->level ?? 'beginner');
                        $levelLabel = $levelLabels[$levelKey] ?? custom_trans('Beginner', 'front');
                        $statusClass = $enrollment->status === 'pending'
                            ? 'pending'
                            : ($enrollment->status === 'completed' ? 'completed' : 'progress');
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <article class="my-course-card">
                            <div class="my-course-card__media">
                                @if ($course->image)
                                    <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}">
                                @else
                                    <div class="my-course-card__placeholder">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                @endif

                                <span class="my-course-card__status my-course-card__status--{{ $statusClass }}">
                                    @if ($enrollment->status === 'pending')
                                        {{ custom_trans('Pending Confirmation', 'front') }}
                                    @elseif ($enrollment->status === 'completed')
                                        {{ custom_trans('Completed', 'front') }}
                                    @else
                                        {{ custom_trans('In Progress', 'front') }}
                                    @endif
                                </span>

                                @if ($enrollment->grantsAccess())
                                    <div class="my-course-card__overlay">
                                        <a href="{{ route('courses.learn', $course->id) }}"
                                            class="my-course-card__overlay-btn">
                                            <i class="fas fa-play me-1"></i>{{ custom_trans('Continue Learning', 'front') }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="my-course-card__body">
                                <span class="my-course-card__category">
                                    {{ $course->category->localized_name ?? custom_trans('Uncategorized', 'front') }}
                                </span>

                                <h3 class="my-course-card__title">
                                    <a href="{{ route('courses.show', $course) }}">{{ $course->localized_name }}</a>
                                </h3>

                                @if ($course->localized_description)
                                    <p class="my-course-card__desc">
                                        {{ Str::limit($course->localized_description, 100) }}
                                    </p>
                                @endif

                                <div class="my-course-card__instructor">
                                    <i class="fas fa-user"></i>
                                    <span>
                                        @if ($course->instructors && $course->instructors->count() > 0)
                                            {{ $course->instructors->pluck('name')->take(2)->join(', ') }}
                                            @if ($course->instructors->count() > 2)
                                                +{{ $course->instructors->count() - 2 }}
                                            @endif
                                        @else
                                            {{ $course->instructor->name ?? custom_trans('Unknown Instructor', 'front') }}
                                        @endif
                                    </span>
                                </div>

                                <div class="my-course-card__progress">
                                    <div class="my-course-card__progress-head">
                                        <span>{{ custom_trans('Progress', 'front') }}</span>
                                        <span class="my-course-card__progress-pct">{{ $progress }}%</span>
                                    </div>
                                    <div class="my-course-card__progress-track">
                                        <div class="my-course-card__progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <div class="my-course-card__progress-sub">
                                        {{ $enrollment->completed_lectures }}/{{ $enrollment->total_lectures }}
                                        {{ custom_trans('lectures completed', 'front') }}
                                    </div>
                                </div>

                                <div class="my-course-card__stats">
                                    <div class="my-course-card__stat">
                                        <span class="my-course-card__stat-label">{{ custom_trans('Duration', 'front') }}</span>
                                        <span class="my-course-card__stat-value">
                                            <i class="fas fa-clock"></i>{{ $course->duration ?? custom_trans('N/A', 'front') }}
                                        </span>
                                    </div>
                                    <div class="my-course-card__stat">
                                        <span class="my-course-card__stat-label">{{ custom_trans('Level', 'front') }}</span>
                                        <span class="my-course-card__stat-value">
                                            <i class="fas fa-signal"></i>{{ $levelLabel }}
                                        </span>
                                    </div>
                                    <div class="my-course-card__stat">
                                        <span class="my-course-card__stat-label">{{ custom_trans('Enrolled', 'front') }}</span>
                                        <span class="my-course-card__stat-value">
                                            <i class="fas fa-calendar-alt"></i>{{ $enrollment->created_at->translatedFormat('M d') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="my-course-card__footer">
                                <a href="{{ route('courses.show', $course) }}"
                                    class="my-course-card__btn my-course-card__btn--outline">
                                    <i class="fas fa-eye"></i>{{ custom_trans('View Details', 'front') }}
                                </a>
                                <div class="my-course-card__actions">
                                    @if ($enrollment->status === 'completed' && $course->enable_certificate)
                                        @if ($enrollment->certificate_path)
                                            <a href="{{ route('certificate.download', $enrollment->id) }}"
                                                class="my-course-card__btn my-course-card__btn--success"
                                                title="{{ custom_trans('Download Certificate', 'front') }}">
                                                <i class="fas fa-certificate"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('certificate.request', $course->id) }}"
                                                class="my-course-card__btn my-course-card__btn--warning"
                                                title="{{ custom_trans('Request Certificate', 'front') }}">
                                                <i class="fas fa-certificate"></i>
                                            </a>
                                        @endif
                                    @endif
                                    @if ($enrollment->grantsAccess())
                                        <a href="{{ route('courses.learn', $course->id) }}"
                                            class="my-course-card__btn my-course-card__btn--primary">
                                            <i class="fas fa-play"></i>{{ custom_trans('Continue', 'front') }}
                                        </a>
                                    @else
                                        <span class="my-course-card__btn my-course-card__btn--disabled">
                                            <i class="fas fa-clock"></i>{{ custom_trans('Pending Confirmation', 'front') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $enrollments->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5 my-courses-empty">
                <div class="empty-state">
                    <i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">{{ custom_trans('No Courses Found', 'front') }}</h3>
                    <p class="text-muted mb-4">
                        @if (request('search') || request('status'))
                            {{ custom_trans('No courses match your current filters. Try adjusting your search criteria.', 'front') }}
                        @else
                            {{ custom_trans("You haven't enrolled in any courses yet. Start your learning journey today!", 'front') }}
                        @endif
                    </p>
                    <a href="{{ route('courses.search') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>{{ custom_trans('Browse Courses', 'front') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change
            document.getElementById('statusFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('sortFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // Search with debounce
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        });
    </script>
@endsection

