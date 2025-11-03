@extends('layouts.app')

@section('title', 'My Courses')

@section('content')
    <div class="container py-5">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-0">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>My Courses
                        </h1>
                        <p class="text-muted">Track your learning progress and continue your courses</p>
                    </div>
                    <div>
                        <a href="{{ route('courses.search') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>Find More Courses
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
                        <p class="text-muted mb-0">Total Courses</p>
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
                        <p class="text-muted mb-0">Completed</p>
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
                        <p class="text-muted mb-0">In Progress</p>
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
                        <p class="text-muted mb-0">Avg Progress</p>
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
                                    placeholder="Search your courses..." value="{{ request('search') }}" id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>In Progress
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="sort" id="sortFilter">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First
                                </option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First
                                </option>
                                <option value="progress_high" {{ request('sort') == 'progress_high' ? 'selected' : '' }}>
                                    Progress High</option>
                                <option value="progress_low" {{ request('sort') == 'progress_low' ? 'selected' : '' }}>
                                    Progress Low</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Course Name
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
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
                                            <i class="fas fa-play me-1"></i>Continue Learning
                                        </a>
                                    </div>
                                </div>
                                <div class="course-status-badge">
                                    @if ($enrollment->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">In Progress</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="course-category mb-2">
                                    <span class="badge bg-light text-dark">
                                        {{ $enrollment->course->category->localized_name ?? 'Uncategorized' }}
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
                                            {{ $enrollment->course->instructor->name ?? 'Unknown Instructor' }}
                                        @endif
                                    </small>
                                </div>

                                <div class="course-progress mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Progress</small>
                                        <small class="text-muted">{{ $enrollment->progress_percentage }}%</small>
                                    </div>
                                    <div class="progress progress-h-8">
                                        <div class="progress-bar bg-primary"
                                            style="width: {{ $enrollment->progress_percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $enrollment->completed_lectures }}/{{ $enrollment->total_lectures }} lectures
                                        completed
                                    </small>
                                </div>

                                <div class="course-meta">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">Duration</small>
                                            <small class="fw-bold">{{ $enrollment->course->duration ?? 'N/A' }}</small>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Level</small>
                                            <small
                                                class="fw-bold">{{ ucfirst($enrollment->course->level ?? 'Beginner') }}</small>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">Enrolled</small>
                                            <small class="fw-bold">{{ $enrollment->created_at->format('M d') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('courses.show', $enrollment->course->slug ?? $enrollment->course->id) }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    <a href="{{ route('courses.learn', $enrollment->course->id) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-play me-1"></i>Continue
                                    </a>
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
                    <h3 class="text-muted mb-3">No Courses Found</h3>
                    <p class="text-muted mb-4">
                        @if (request('search') || request('status'))
                            No courses match your current filters. Try adjusting your search criteria.
                        @else
                            You haven't enrolled in any courses yet. Start your learning journey today!
                        @endif
                    </p>
                    <a href="{{ route('courses.search') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-search me-2"></i>Browse Courses
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

