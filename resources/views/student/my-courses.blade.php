@extends('layouts.app')

@section('title', custom_trans('My Courses', 'front'))

@section('content')
    <div class="container py-5">
        <!-- Page Header -->
        <div class="row mb-4">
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
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-primary-soft mb-3">
                            <i class="fas fa-book text-primary fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-1">{{ $stats['total_courses'] }}</h3>
                        <p class="text-muted mb-0">{{ custom_trans('Total Courses', 'front') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-success-soft mb-3">
                            <i class="fas fa-check-circle text-success fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-1">{{ $stats['completed_courses'] }}</h3>
                        <p class="text-muted mb-0">{{ custom_trans('Completed', 'front') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-warning-soft mb-3">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-1">{{ $stats['in_progress_courses'] }}</h3>
                        <p class="text-muted mb-0">{{ custom_trans('In Progress', 'front') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="stat-icon bg-info-soft mb-3">
                            <i class="fas fa-chart-line text-info fa-2x"></i>
                        </div>
                        <h3 class="fw-bold text-info mb-1">{{ $stats['average_progress'] }}%</h3>
                        <p class="text-muted mb-0">{{ custom_trans('Avg Progress', 'front') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
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
                    <div class="col-lg-4 col-md-6">
                        <div class="card course-card h-100">
                            <div class="course-thumbnail">
                                @if ($enrollment->course->image)
                                    <img src="{{ $enrollment->course->image_url }}" alt="{{ $enrollment->course->localized_name }}"
                                        class="card-img-top">
                                @else
                                    <div class="course-placeholder">
                                        <i class="fas fa-graduation-cap fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="course-overlay">
                                    <div class="course-actions">
                                        <a href="{{ route('courses.learn', $enrollment->course->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-play me-1"></i>{{ custom_trans('Continue Learning', 'front') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="course-status-badge">
                                    @if ($enrollment->status == 'completed')
                                        <span class="badge bg-success">{{ custom_trans('Completed', 'front') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ custom_trans('In Progress', 'front') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="course-category mb-2">
                                    <span class="badge bg-light text-dark">
                                        {{ $enrollment->course->category->localized_name ?? custom_trans('Uncategorized', 'front') }}
                                    </span>
                                </div>

                                <h5 class="card-title course-title">
                                    <a href="{{ route('courses.show', $enrollment->course->slug ?? $enrollment->course->id) }}"
                                        class="text-decoration-none">
                                        {{ $enrollment->course->localized_name }}
                                    </a>
                                </h5>

                                <p class="card-text course-description">
                                    {{ Str::limit($enrollment->course->localized_description, 100) }}
                                </p>

                                <div class="course-instructor mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        @if ($enrollment->course->instructors && $enrollment->course->instructors->count() > 0)
                                            {{ $enrollment->course->instructors->pluck('name')->take(2)->join(', ') }}
                                            @if ($enrollment->course->instructors->count() > 2)
                                                +{{ $enrollment->course->instructors->count() - 2 }}
                                            @endif
                                        @else
                                            {{ $enrollment->course->instructor->name ?? custom_trans('Unknown Instructor', 'front') }}
                                        @endif
                                    </small>
                                </div>

                                <div class="course-progress mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">{{ custom_trans('Progress', 'front') }}</small>
                                        <small class="text-muted">{{ $enrollment->progress_percentage }}%</small>
                                    </div>
                                    <div class="progress progress-h-8">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $enrollment->completed_lectures }}/{{ $enrollment->total_lectures }} {{ custom_trans('lectures completed', 'front') }}
                                    </small>
                                </div>

                                <div class="course-meta">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">{{ custom_trans('Duration', 'front') }}</small>
                                            <small class="fw-bold">{{ $enrollment->course->duration ?? custom_trans('N/A', 'front') }}</small>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">{{ custom_trans('Level', 'front') }}</small>
                                            <small
                                                class="fw-bold">{{ ucfirst($enrollment->course->level ?? custom_trans('Beginner', 'front')) }}</small>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">{{ custom_trans('Enrolled', 'front') }}</small>
                                            <small class="fw-bold">{{ $enrollment->created_at->format('M d') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <a href="{{ route('courses.show', $enrollment->course->slug ?? $enrollment->course->id) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>{{ custom_trans('View Details', 'front') }}
                                    </a>
                                    <div class="d-flex gap-2">
                                        @if ($enrollment->status == 'completed' && $enrollment->course->enable_certificate)
                                            @if ($enrollment->certificate_path)
                                                <a href="{{ route('certificate.download', $enrollment->id) }}"
                                                    class="btn btn-success btn-sm" title="{{ custom_trans('Download Certificate', 'front') }}">
                                                    <i class="fas fa-certificate me-1"></i>{{ custom_trans('Certificate', 'front') }}
                                                </a>
                                            @else
                                                <a href="{{ route('certificate.request', $enrollment->course->id) }}"
                                                    class="btn btn-warning btn-sm" title="{{ custom_trans('Request Certificate', 'front') }}">
                                                    <i class="fas fa-certificate me-1"></i>{{ custom_trans('Get Certificate', 'front') }}
                                                </a>
                                            @endif
                                        @endif
                                        <a href="{{ route('courses.learn', $enrollment->course->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-play me-1"></i>{{ custom_trans('Continue', 'front') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            <div class="text-center py-5">
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
    </div><script>
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

