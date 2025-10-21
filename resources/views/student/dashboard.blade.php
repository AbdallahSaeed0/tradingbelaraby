@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
    <div class="container py-5">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">Welcome back, {{ Auth::user()->name }}! 👋</h2>
                                <p class="mb-0">Continue your learning journey and track your progress</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="{{ route('student.my-courses') }}" class="btn btn-light">
                                    <i class="fas fa-graduation-cap me-2"></i>View My Courses
                                </a>
                            </div>
                        </div>
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

        <div class="row g-4">
            <!-- Recent Courses -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2 text-primary"></i>Recent Courses
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($enrollments->count() > 0)
                            <div class="row g-3">
                                @foreach ($enrollments->take(6) as $enrollment)
                                    <div class="col-md-6">
                                        <div class="course-item d-flex align-items-center p-3 border rounded">
                                            <div class="course-thumbnail me-3">
                                                @if ($enrollment->course->thumbnail)
                                                    <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}"
                                                        alt="{{ $enrollment->course->name }}"
                                                        class="rounded w-60 h-60 img-h-60">
                                                @else
                                                    <div
                                                        class="bg-light rounded d-flex align-items-center justify-content-center w-60 h-60">
                                                        <i class="fas fa-graduation-cap text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="course-info flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('courses.show', $enrollment->course->slug) }}"
                                                        class="text-decoration-none">
                                                        {{ $enrollment->course->name }}
                                                    </a>
                                                </h6>
                                                <div class="progress mb-2 progress-h-6">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: {{ $enrollment->progress_percentage }}%"></div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $enrollment->progress_percentage }}% complete
                                                </small>
                                            </div>
                                            <div class="course-actions">
                                                <a href="{{ route('student.learn-course', $enrollment->course->id) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('student.my-courses') }}" class="btn btn-outline-primary">
                                    View All Courses
                                </a>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No courses enrolled yet</h5>
                                <p class="text-muted">Start your learning journey by enrolling in courses</p>
                                <a href="{{ route('courses.search') }}" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Browse Courses
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2 text-info"></i>Recent Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($recent_activity->count() > 0)
                            <div class="activity-list">
                                @foreach ($recent_activity as $activity)
                                    <div class="activity-item d-flex align-items-start mb-3">
                                        <div class="activity-icon me-3">
                                            <i class="fas fa-graduation-cap text-primary"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-text">
                                                <strong>{{ $activity->course->name }}</strong>
                                            </div>
                                            <small class="text-muted">
                                                {{ $activity->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="fas fa-clock fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No recent activity</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('courses.search') }}" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Find New Courses
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="btn btn-outline-danger">
                                <i class="fas fa-heart me-2"></i>My Wishlist
                            </a>
                            <a href="{{ route('purchase.history') }}" class="btn btn-outline-info">
                                <i class="fas fa-history me-2"></i>Purchase History
                            </a>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-success">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>@endsection

