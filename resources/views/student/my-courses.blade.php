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
                                    <img src="{{ $enrollment->course->image_url }}" alt="{{ $enrollment->course->name }}"
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
                                        {{ $enrollment->course->category->name ?? 'Uncategorized' }}
                                    </span>
                                </div>

                                <h5 class="card-title course-title">
                                    <a href="{{ route('courses.show', $enrollment->course->slug ?? $enrollment->course->id) }}"
                                        class="text-decoration-none">
                                        {{ $enrollment->course->name }}
                                    </a>
                                </h5>

                                <p class="card-text course-description">
                                    {{ Str::limit($enrollment->course->description, 100) }}
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
    </div>

    <style>
        .stat-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .course-card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .course-thumbnail {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .course-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .course-card:hover .course-thumbnail img {
            transform: scale(1.05);
        }

        .course-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
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

        .course-status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .course-title a {
            color: #333;
            text-decoration: none;
        }

        .course-title a:hover {
            color: #007bff;
        }

        .course-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
        }

        .course-meta {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        .empty-state {
            max-width: 400px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }

            .course-card {
                margin-bottom: 1rem;
            }

            .course-thumbnail {
                height: 150px;
            }
        }
    </style>

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
