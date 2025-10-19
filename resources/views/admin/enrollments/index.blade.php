@extends('admin.layout')

@section('title', 'All Enrollments Management')

@push('styles')
    <style>
        .enrollment-card {
            transition: transform 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .enrollment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .progress-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.875rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .stat-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
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

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .enrollment-actions .btn {
            margin: 0 2px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .enrollment-actions .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">All Enrollments Management</h1>
                        <p class="text-muted">Monitor and manage student enrollments across all courses</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.enrollments.export') }}" class="btn btn-outline-success me-2">
                            <i class="fa fa-download me-2"></i>Export All
                        </a>
                        <button class="btn btn-outline-primary" onclick="refreshStats()">
                            <i class="fa fa-refresh me-2"></i>Refresh Stats
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_enrollments'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft">
                            <i class="fa fa-check-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Completed</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['completed_enrollments'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-clock text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">In Progress</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['in_progress_enrollments'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Avg Progress</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['average_progress'] }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-soft">
                            <i class="fa fa-times-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Cancelled</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['cancelled_enrollments'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-graduation-cap text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Active Courses</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['active_courses'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.enrollments.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                    placeholder="Search students or courses..." id="searchInput"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="course" id="courseFilter">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="enrolled" {{ request('status') == 'enrolled' ? 'selected' : '' }}>Enrolled
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="progress" id="progressFilter">
                                <option value="">All Progress</option>
                                <option value="0-25" {{ request('progress') == '0-25' ? 'selected' : '' }}>0-25%
                                </option>
                                <option value="26-50" {{ request('progress') == '26-50' ? 'selected' : '' }}>26-50%
                                </option>
                                <option value="51-75" {{ request('progress') == '51-75' ? 'selected' : '' }}>51-75%
                                </option>
                                <option value="76-100" {{ request('progress') == '76-100' ? 'selected' : '' }}>76-100%
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort" id="sortFilter">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="progress_high" {{ request('sort') == 'progress_high' ? 'selected' : '' }}>
                                    Progress High</option>
                                <option value="progress_low" {{ request('sort') == 'progress_low' ? 'selected' : '' }}>
                                    Progress Low</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Student Name
                                </option>
                                <option value="course" {{ request('sort') == 'course' ? 'selected' : '' }}>Course Name
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-refresh me-1"></i>Clear Filters
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enrollments Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Enrollment Date</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Last Activity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($enrollment->user->avatar)
                                                <img src="{{ asset('storage/' . $enrollment->user->avatar) }}"
                                                    alt="{{ $enrollment->user->name }}" class="user-avatar me-3">
                                            @else
                                                <div
                                                    class="user-avatar me-3 bg-primary text-white d-flex align-items-center justify-content-center">
                                                    {{ strtoupper(substr($enrollment->user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $enrollment->user->name }}</h6>
                                                <small class="text-muted">{{ $enrollment->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ $enrollment->course->name }}</h6>
                                            <small class="text-muted">
                                                @if ($enrollment->course->instructors && $enrollment->course->instructors->count() > 0)
                                                    {{ $enrollment->course->instructors->pluck('name')->take(2)->join(', ') }}
                                                    @if ($enrollment->course->instructors->count() > 2)
                                                        +{{ $enrollment->course->instructors->count() - 2 }}
                                                    @endif
                                                @else
                                                    {{ $enrollment->course->instructor->name ?? 'N/A' }}
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $enrollment->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $enrollment->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress-circle bg-light me-2"
                                                style="background: conic-gradient(#007bff {{ $enrollment->progress_percentage }}%, #e9ecef 0deg);">
                                                <span
                                                    class="bg-white rounded-circle d-flex align-items-center justify-content-center w-50 h-50">
                                                    {{ round($enrollment->progress_percentage) }}%
                                                </span>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted">{{ $enrollment->completed_lectures }}/{{ $enrollment->total_lectures }}
                                                    lectures</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'enrolled' => 'bg-primary',
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                            ];
                                            $statusClass = $statusClasses[$enrollment->status] ?? 'bg-secondary';
                                        @endphp
                                        <span
                                            class="badge {{ $statusClass }} status-badge">{{ ucfirst($enrollment->status) }}</span>
                                    </td>
                                    <td>
                                        @if ($enrollment->last_activity)
                                            <div>{{ $enrollment->last_activity->format('M d, Y') }}</div>
                                            <small
                                                class="text-muted">{{ $enrollment->last_activity->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">No activity</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="enrollment-actions">
                                            <a href="{{ route('admin.courses.enrollments', $enrollment->course) }}"
                                                class="btn btn-outline-info btn-sm" title="View Course Enrollments">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.show', $enrollment->course) }}"
                                                class="btn btn-outline-primary btn-sm" title="View Course">
                                                <i class="fa fa-book"></i>
                                            </a>
                                            <button onclick="sendMessage({{ $enrollment->user_id }})"
                                                class="btn btn-outline-warning btn-sm" title="Send Message">
                                                <i class="fa fa-envelope"></i>
                                            </button>
                                            <button onclick="viewProgress({{ $enrollment->id }})"
                                                class="btn btn-outline-success btn-sm" title="View Progress">
                                                <i class="fa fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-users fa-3x mb-3"></i>
                                            <h5>No enrollments found</h5>
                                            <p>No students have enrolled in any courses yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Showing {{ $enrollments->count() }} of {{ $enrollments->total() }} enrollments
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            {{ $enrollments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-submit form on filter change
            document.getElementById('courseFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('statusFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('progressFilter').addEventListener('change', function() {
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

            // Refresh stats
            function refreshStats() {
                location.reload();
            }

            // Send message to student
            function sendMessage(userId) {
                // TODO: Implement messaging functionality
                alert('Messaging functionality will be implemented soon!');
            }

            // View detailed progress
            function viewProgress(enrollmentId) {
                // TODO: Implement detailed progress view
                alert('Detailed progress view will be implemented soon!');
            }
        </script>
    @endpush
@endsection
