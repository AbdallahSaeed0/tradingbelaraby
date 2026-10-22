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
                    <div class="admin-list-header-actions">
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>{{ $errors->first() }}
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
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-lg">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                    placeholder="Search students or courses..." id="searchInput"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
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
                        <div class="col-6 col-md-3 col-lg-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 col-lg-2">
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
                        <div class="col-6 col-md-3 col-lg-2">
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
                        <div class="col-12 col-lg-auto">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary text-nowrap">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary text-nowrap" id="clearFiltersBtn">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enrollments Table -->
        <div class="card">
            <div class="card-body">
                <!-- Bulk actions: shown while rows are selected -->
                <div class="enrollment-bulk-bar d-none" id="bulkActionsBar">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-check-square text-primary"></i>
                        <span class="fw-semibold"><span id="bulkSelectedCount">0</span> selected</span>
                        <button type="button" class="btn btn-link btn-sm p-0 ms-1" id="bulkClearSelection">Clear</button>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <select class="form-select form-select-sm w-auto" id="bulkStatusSelect" aria-label="New status">
                            <option value="">Change status to…</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button type="button" class="btn btn-sm btn-primary" id="bulkApplyStatus" disabled>
                            <i class="fa fa-check me-1"></i>Apply
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn">
                            <i class="fa fa-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>

                <div class="table-responsive d-none d-lg-block admin-table-no-mobile-cards">
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
                                            <div class="user-avatar me-3">
                                                <i class="fa fa-user"></i>
                                                @if ($enrollment->user->avatar)
                                                    <img src="{{ asset('storage/' . $enrollment->user->avatar) }}"
                                                        alt="{{ $enrollment->user->name }}" onerror="this.remove()">
                                                @endif
                                            </div>
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
                                        <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                            class="enrollment-progress d-block text-reset text-decoration-none" title="View progress details">
                                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                                <span class="fw-semibold small">{{ $enrollment->live_percent }}%</span>
                                                <small class="text-muted text-nowrap">{{ $enrollment->live_completed }}/{{ $enrollment->live_total }} lectures</small>
                                            </div>
                                            <div class="progress" role="progressbar" aria-valuenow="{{ $enrollment->live_percent }}"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar {{ $enrollment->live_percent >= 100 ? 'bg-success' : '' }}"
                                                    style="width: {{ $enrollment->live_percent }}%"></div>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'active' => 'bg-primary',
                                                'enrolled' => 'bg-primary',
                                                'pending' => 'bg-warning text-dark',
                                                'completed' => 'bg-success',
                                                'cancelled' => 'bg-danger',
                                            ];
                                            $statusClass = $statusClasses[$enrollment->status] ?? 'bg-secondary';
                                        @endphp
                                        <span
                                            class="badge {{ $statusClass }} status-badge"
                                            style="cursor: pointer;"
                                            onclick="showStatusModal({{ $enrollment->id }}, '{{ $enrollment->status }}', [
                                                { value: 'active', label: 'Active' },
                                                { value: 'pending', label: 'Pending' },
                                                { value: 'completed', label: 'Completed' },
                                                { value: 'cancelled', label: 'Cancelled' }
                                            ], '{{ route('admin.enrollments.update_status', $enrollment->id) }}')">{{ ucfirst($enrollment->status) }}</span>
                                    </td>
                                    <td>
                                        @if ($enrollment->live_last_activity)
                                            <div>{{ $enrollment->live_last_activity->format('M d, Y') }}</div>
                                            <small
                                                class="text-muted">{{ $enrollment->live_last_activity->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">No activity</span>
                                        @endif
                                    </td>
                                    <td class="table-icon-actions-cell">
                                        <div class="table-icon-actions">
                                            <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                                class="btn btn-sm btn-outline-success" title="View Progress">
                                                <i class="fa fa-chart-bar"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.enrollments', $enrollment->course) }}"
                                                class="btn btn-sm btn-outline-info" title="View Course Enrollments">
                                                <i class="fa fa-users"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.show', $enrollment->course) }}"
                                                class="btn btn-sm btn-outline-primary" title="View Course">
                                                <i class="fa fa-book"></i>
                                            </a>
                                            <a href="mailto:{{ $enrollment->user->email }}"
                                                class="btn btn-sm btn-outline-warning" title="Email Student">
                                                <i class="fa fa-envelope"></i>
                                            </a>
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

                <div class="d-lg-none mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllMobile">
                        <label class="form-check-label small" for="selectAllMobile">Select all on this page</label>
                    </div>
                </div>
                <div class="admin-mobile-list d-lg-none" id="mobileEnrollmentsList">
                    @foreach ($enrollments as $enrollment)
                        @include('admin.enrollments.partials.mobile-enrollment-card', ['enrollment' => $enrollment])
                    @endforeach
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
            const mobileList = document.getElementById('mobileEnrollmentsList');
            const paginationContainer = document.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');

            // AJAX search function
            function performAjaxSearch() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                // Show loading state
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }
                if (mobileList) {
                    mobileList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
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
                        const newMobileList = tempDiv.querySelector('#mobileEnrollmentsList');
                        const newPagination = tempDiv.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');

                        if (newTableBody && tableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML;
                        }

                        if (newMobileList && mobileList) {
                            mobileList.innerHTML = newMobileList.innerHTML;
                        }

                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        // New rows come in unchecked
                        syncSelectAll();

                        // Update URL without reload
                        const newUrl = `{{ route('admin.enrollments.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
                        }
                        if (mobileList) {
                            mobileList.innerHTML = '<div class="text-center py-4 text-danger">Error loading data. Please try again.</div>';
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

            // Select all: each master checkbox controls the row checkboxes in its own list.
            // Delegated on document so it keeps working after AJAX replaces the rows.
            const selectAllScopes = {
                selectAll: '.table tbody .enrollment-checkbox',
                selectAllMobile: '#mobileEnrollmentsList .enrollment-checkbox',
            };

            function syncSelectAll() {
                Object.entries(selectAllScopes).forEach(([masterId, rowSelector]) => {
                    const master = document.getElementById(masterId);
                    if (!master) {
                        return;
                    }
                    const rows = document.querySelectorAll(rowSelector);
                    const checked = Array.from(rows).filter(cb => cb.checked).length;
                    master.checked = rows.length > 0 && checked === rows.length;
                    master.indeterminate = checked > 0 && checked < rows.length;
                });
                updateBulkBar();
            }

            // Bulk actions bar
            const bulkBar = document.getElementById('bulkActionsBar');
            const bulkStatusSelect = document.getElementById('bulkStatusSelect');
            const bulkApplyStatus = document.getElementById('bulkApplyStatus');

            function selectedEnrollmentIds() {
                // Desktop table and mobile cards hold the same rows; dedupe
                const ids = Array.from(document.querySelectorAll('.enrollment-checkbox:checked')).map(cb => cb.value);
                return [...new Set(ids)];
            }

            function updateBulkBar() {
                const count = selectedEnrollmentIds().length;
                document.getElementById('bulkSelectedCount').textContent = count;
                bulkBar.classList.toggle('d-none', count === 0);
                bulkApplyStatus.disabled = !bulkStatusSelect.value;
            }

            bulkStatusSelect.addEventListener('change', updateBulkBar);

            document.getElementById('bulkClearSelection').addEventListener('click', function() {
                document.querySelectorAll('.enrollment-checkbox, #selectAll, #selectAllMobile').forEach(cb => {
                    cb.checked = false;
                });
                syncSelectAll();
            });

            // Put the selected ids (and extra fields) into the confirm modal's form, then open it
            function confirmBulk(options, extraFields) {
                const form = document.getElementById('actionConfirmForm');
                form.querySelectorAll('.bulk-field').forEach(el => el.remove());
                const fields = Object.assign({}, extraFields);
                selectedEnrollmentIds().forEach((id, i) => {
                    fields['ids[' + i + ']'] = id;
                });
                Object.entries(fields).forEach(([name, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    input.value = value;
                    input.className = 'bulk-field';
                    form.appendChild(input);
                });
                showActionConfirmModal(options);
            }

            bulkApplyStatus.addEventListener('click', function() {
                const status = bulkStatusSelect.value;
                const label = bulkStatusSelect.options[bulkStatusSelect.selectedIndex].text;
                const count = selectedEnrollmentIds().length;
                if (!status || count === 0) {
                    return;
                }
                confirmBulk({
                    title: 'Change Status',
                    message: 'Set ' + count + ' selected ' + (count === 1 ? 'enrollment' : 'enrollments') + ' to "' + label + '"?',
                    warning: status === 'cancelled' ? 'Cancelled enrollments lose access to the course.' : null,
                    action: '{{ route('admin.enrollments.bulk_update_status') }}',
                    method: 'POST',
                    headerClass: 'bg-primary text-white',
                    btnClass: 'btn-primary',
                    btnHtml: '<i class="fa fa-check me-1"></i>Update ' + count
                }, { status: status });
            });

            document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
                const count = selectedEnrollmentIds().length;
                if (count === 0) {
                    return;
                }
                confirmBulk({
                    title: 'Delete Enrollments',
                    message: 'Delete ' + count + ' selected ' + (count === 1 ? 'enrollment' : 'enrollments') + '? The students will lose access to these courses.',
                    warning: 'This cannot be undone.',
                    action: '{{ route('admin.enrollments.bulk_delete') }}',
                    method: 'POST',
                    headerClass: 'bg-danger text-white',
                    btnClass: 'btn-danger',
                    btnHtml: '<i class="fa fa-trash me-1"></i>Delete ' + count
                });
            });

            document.addEventListener('change', function(e) {
                const target = e.target;
                if (selectAllScopes[target.id]) {
                    document.querySelectorAll(selectAllScopes[target.id]).forEach(cb => {
                        cb.checked = target.checked;
                    });
                    syncSelectAll();
                } else if (target.classList.contains('enrollment-checkbox')) {
                    syncSelectAll();
                }
            });

            // Refresh stats
            function refreshStats() {
                location.reload();
            }
        </script>
        @include('admin.partials.status-modal')
        @include('admin.partials.confirm-modal')
    @endpush
@endsection

