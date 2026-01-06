@extends('admin.layout')

@section('title', 'All Enrollments Management')

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
                            <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                <i class="fa fa-refresh me-1"></i>Clear Filters
                            </button>
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
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
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
                                        <div class="form-check">
                                            <input class="form-check-input enrollment-checkbox" type="checkbox" value="{{ $enrollment->id }}">
                                        </div>
                                    </td>
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
                                            class="badge {{ $statusClass }} status-badge"
                                            style="cursor: pointer;"
                                            onclick="showStatusModal({{ $enrollment->id }}, '{{ $enrollment->status }}', [
                                                { value: 'enrolled', label: 'Enrolled' },
                                                { value: 'completed', label: 'Completed' },
                                                { value: 'cancelled', label: 'Cancelled' }
                                            ], '{{ route('admin.enrollments.update_status', $enrollment->id) }}')">{{ ucfirst($enrollment->status) }}</span>
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
                                    <td colspan="8" class="text-center py-4">
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
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center me-3">
                                <label class="form-label me-2 mb-0 small">Per page:</label>
                                <select class="form-select form-select-sm w-auto" id="perPageSelect" onchange="changePerPage(this.value)">
                                    @php
                                        $perPage = (int) request('per_page', 10);
                                    @endphp
                                    <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage === 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage === 100 ? 'selected' : '' }}>100</option>
                                    <option value="500" {{ $perPage === 500 ? 'selected' : '' }}>500</option>
                                    <option value="1000" {{ $perPage === 1000 ? 'selected' : '' }}>1000</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                        @if ($enrollments->hasPages())
                            <nav aria-label="Enrollments pagination">
                                <ul class="pagination pagination-sm justify-content-end mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($enrollments->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fa fa-chevron-left"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $enrollments->previousPageUrl() }}"
                                                aria-label="Previous">
                                                <i class="fa fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- First Page --}}
                                    @if ($enrollments->currentPage() > 3)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $enrollments->url(1) }}">1</a>
                                        </li>
                                        @if ($enrollments->currentPage() > 4)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($enrollments->getUrlRange(max(1, $enrollments->currentPage() - 2), min($enrollments->lastPage(), $enrollments->currentPage() + 2)) as $page => $url)
                                        @if ($page == $enrollments->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Last Page --}}
                                    @if ($enrollments->currentPage() < $enrollments->lastPage() - 2)
                                        @if ($enrollments->currentPage() < $enrollments->lastPage() - 3)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="{{ $enrollments->url($enrollments->lastPage()) }}">{{ $enrollments->lastPage() }}</a>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($enrollments->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $enrollments->nextPageUrl() }}" aria-label="Next">
                                                <i class="fa fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fa fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @else
                            <div class="text-end text-muted small">
                                Page 1 of 1
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
                {{-- Page Info --}}
                @if ($enrollments->hasPages())
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="text-center">
                                <small class="text-muted">
                                    Page {{ $enrollments->currentPage() }} of {{ $enrollments->lastPage() }}
                                    @if ($enrollments->total() > 0)
                                        ({{ number_format($enrollments->total()) }} total enrollments)
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Change per page function
            function changePerPage(value) {
                // Use AJAX to update
                const formData = new FormData(document.getElementById('filterForm'));
                formData.set('per_page', value);
                performAjaxSearch();
            }
            // Initialize variables for AJAX search
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.querySelector('.table tbody');
            const paginationContainer = document.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');

            // AJAX search function
            function performAjaxSearch() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                // Show loading state
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }

                fetch(`{{ route('admin.enrollments.index') }}?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Create a temporary container to parse the HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        // Extract table body
                        const newTableBody = tempDiv.querySelector('.table tbody');
                        const newPagination = tempDiv.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');

                        if (newTableBody && tableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML;
                        }

                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        // Update URL without reload
                        const newUrl = `{{ route('admin.enrollments.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
                        }
                    });
            }

            // Prevent form submission - use AJAX instead
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performAjaxSearch();
            });

            // Dropdown filters - use AJAX
            document.getElementById('courseFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('statusFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('progressFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('sortFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            // Clear filters button - use AJAX
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Clear all form fields
                    document.getElementById('searchInput').value = '';
                    document.getElementById('courseFilter').value = '';
                    document.getElementById('statusFilter').value = '';
                    document.getElementById('progressFilter').value = '';
                    document.getElementById('sortFilter').value = 'latest';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.enrollments.index') }}');

                    // Perform AJAX search with cleared filters
                    performAjaxSearch();
                });
            }

            // Search with debounce - AJAX only
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        performAjaxSearch();
                    }, 500);
                });

                // Prevent form submission on Enter key in search
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(searchTimeout);
                        performAjaxSearch();
                    }
                });
            }

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
        @include('admin.partials.status-modal')
    @endpush
@endsection

