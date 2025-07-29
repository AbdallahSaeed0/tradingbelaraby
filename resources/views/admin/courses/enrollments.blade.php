@extends('admin.layout')

@section('title', 'Course Enrollments')

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
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Course Enrollments</h1>
                        <p class="text-muted">Manage student enrollments for {{ $course->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.courses.enrollments.export', $course) }}"
                            class="btn btn-outline-success me-2">
                            <i class="fa fa-download me-2"></i>Export Enrollments
                        </a>
                        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Course
                        </a>
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

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Enrollments</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_enrollments'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            <div class="col-md-3">
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
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.courses.enrollments', $course) }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search students..."
                                    value="{{ request('search') }}" id="searchInput">
                            </div>
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
                                <option value="0-25" {{ request('progress') == '0-25' ? 'selected' : '' }}>0-25%</option>
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
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First
                                </option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First
                                </option>
                                <option value="progress_high" {{ request('sort') == 'progress_high' ? 'selected' : '' }}>
                                    Progress High</option>
                                <option value="progress_low" {{ request('sort') == 'progress_low' ? 'selected' : '' }}>
                                    Progress Low</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.courses.enrollments', $course) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="btn-group me-3" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="listView">
                                <i class="fa fa-list me-1"></i>List View
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="gridView">
                                <i class="fa fa-th me-1"></i>Grid View
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3">Showing {{ $enrollments->firstItem() }} to
                            {{ $enrollments->lastItem() }} of {{ $enrollments->total() }} entries</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollments List View -->
        <div class="card" id="listViewContainer">
            <div class="card-header">
                <h5 class="mb-0">Enrollments List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
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
                                        <span>{{ $enrollment->created_at->format('M d, Y') }}</span>
                                        <small
                                            class="text-muted d-block">{{ $enrollment->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress-circle bg-light me-2"
                                                style="background: conic-gradient(#007bff {{ $enrollment->progress_percentage }}%, #e9ecef 0deg);">
                                                <span
                                                    class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    {{ round($enrollment->progress_percentage) }}%
                                                </span>
                                            </div>
                                            <div>
                                                <small
                                                    class="text-muted">{{ $enrollment->lessons_completed }}/{{ $enrollment->total_lessons }}
                                                    lessons</small>
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
                                        @if ($enrollment->last_accessed_at)
                                            <span>{{ $enrollment->last_accessed_at->format('M d, Y') }}</span>
                                            <small
                                                class="text-muted d-block">{{ $enrollment->last_accessed_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">No activity</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="#" class="btn btn-outline-primary" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="#" class="btn btn-outline-info" title="Progress Report">
                                                <i class="fa fa-chart-bar"></i>
                                            </a>
                                            <button class="btn btn-outline-warning" title="Send Message">
                                                <i class="fa fa-envelope"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-users fa-3x mb-3"></i>
                                            <h5>No enrollments found</h5>
                                            <p>No students have enrolled in this course yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $enrollments->firstItem() }} to {{ $enrollments->lastItem() }} of
                        {{ $enrollments->total() }} entries
                    </div>
                    {{ $enrollments->links() }}
                </div>
            </div>
        </div>

        <!-- Enrollments Grid View -->
        <div class="card" id="gridViewContainer" style="display: none;">
            <div class="card-header">
                <h5 class="mb-0">Enrollments Grid</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @forelse($enrollments as $enrollment)
                        <div class="col-lg-4 col-md-6">
                            <div class="card enrollment-card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
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

                                    <div class="text-center mb-3">
                                        <div class="progress-circle mx-auto mb-2"
                                            style="background: conic-gradient(#007bff {{ $enrollment->progress_percentage }}%, #e9ecef 0deg);">
                                            <span
                                                class="bg-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                {{ round($enrollment->progress_percentage) }}%
                                            </span>
                                        </div>
                                        <small
                                            class="text-muted">{{ $enrollment->lessons_completed }}/{{ $enrollment->total_lessons }}
                                            lessons</small>
                                    </div>

                                    <div class="row text-center mb-3">
                                        <div class="col-6">
                                            <small class="text-muted">Enrolled</small>
                                            <div class="fw-bold">{{ $enrollment->created_at->format('M d, Y') }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Status</small>
                                            <div>
                                                @php
                                                    $statusClasses = [
                                                        'enrolled' => 'bg-primary',
                                                        'completed' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                    ];
                                                    $statusClass =
                                                        $statusClasses[$enrollment->status] ?? 'bg-secondary';
                                                @endphp
                                                <span
                                                    class="badge {{ $statusClass }} status-badge">{{ ucfirst($enrollment->status) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="#" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye me-1"></i>Details
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-info">
                                            <i class="fa fa-chart-bar me-1"></i>Progress
                                        </a>
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="fa fa-envelope me-1"></i>Message
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fa fa-users fa-3x mb-3"></i>
                                    <h5>No enrollments found</h5>
                                    <p>No students have enrolled in this course yet.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $enrollments->firstItem() }} to {{ $enrollments->lastItem() }} of
                        {{ $enrollments->total() }} entries
                    </div>
                    {{ $enrollments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // View toggle functionality
            const gridViewBtn = document.getElementById('gridView');
            const listViewBtn = document.getElementById('listView');
            const gridViewContainer = document.getElementById('gridViewContainer');
            const listViewContainer = document.getElementById('listViewContainer');

            gridViewBtn.addEventListener('click', function() {
                gridViewContainer.style.display = 'block';
                listViewContainer.style.display = 'none';
                this.classList.add('active');
                listViewBtn.classList.remove('active');
            });

            listViewBtn.addEventListener('click', function() {
                listViewContainer.style.display = 'block';
                gridViewContainer.style.display = 'none';
                this.classList.add('active');
                gridViewBtn.classList.remove('active');
            });

            // Auto-submit form on filter change
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
        });
    </script>
@endpush
