@extends('admin.layout')

@section('title', 'Live Classes Management')

@section('content')
    <style>
        /* Force placeholder color in dark mode - Using brighter color */
        html[data-theme="dark"] input#searchInput::placeholder,
        html[data-theme="dark"] input#searchInput::-webkit-input-placeholder,
        html[data-theme="dark"] input#searchInput::-moz-placeholder,
        html[data-theme="dark"] input#searchInput:-ms-input-placeholder,
        html[data-theme="dark"] input#searchInput:-moz-placeholder,
        html[data-theme="dark"] .input-group input::placeholder,
        html[data-theme="dark"] .input-group input::-webkit-input-placeholder,
        html[data-theme="dark"] .input-group input::-moz-placeholder,
        html[data-theme="dark"] .input-group input:-ms-input-placeholder,
        html[data-theme="dark"] .input-group input:-moz-placeholder,
        html[data-theme="dark"] .input-group .form-control::placeholder,
        html[data-theme="dark"] .input-group .form-control::-webkit-input-placeholder,
        html[data-theme="dark"] .input-group .form-control::-moz-placeholder,
        html[data-theme="dark"] .input-group .form-control:-ms-input-placeholder,
        html[data-theme="dark"] .input-group .form-control:-moz-placeholder {
            color: #d0d3d8 !important;
            opacity: 1 !important;
        }
    </style>
    <script>
        // Direct fix for search input
        (function() {
            function fixSearchInput() {
                const searchInput = document.getElementById('searchInput');
                if (searchInput && document.documentElement.getAttribute('data-theme') === 'dark') {
                    const styleId = 'search-input-ph-fix';
                    if (!document.getElementById(styleId)) {
                        const style = document.createElement('style');
                        style.id = styleId;
                        style.textContent = `
                            #searchInput::placeholder { color: #d0d3d8 !important; opacity: 1 !important; }
                            #searchInput::-webkit-input-placeholder { color: #d0d3d8 !important; opacity: 1 !important; }
                            #searchInput::-moz-placeholder { color: #d0d3d8 !important; opacity: 1 !important; }
                            #searchInput:-ms-input-placeholder { color: #d0d3d8 !important; opacity: 1 !important; }
                        `;
                        document.head.appendChild(style);
                    }
                }
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', fixSearchInput);
            } else {
                fixSearchInput();
            }
            setTimeout(fixSearchInput, 500);
        })();
    </script>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Live Classes Management</h1>
                        <p class="text-muted">Create and manage live classes for your courses</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.live-classes.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Live Class
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-video text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Classes</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_classes'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft">
                            <i class="fa fa-calendar-check text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Scheduled</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['scheduled_classes'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-broadcast-tower text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Live Now</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['live_classes'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Registrations</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_registrations'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.live-classes.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search classes..."
                                    id="searchInput" value="{{ request('search') }}" autocomplete="off">
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
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>
                                    Scheduled
                                </option>
                                <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>Live</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="instructor" id="instructorFilter">
                                <option value="">All Instructors</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}"
                                        {{ request('instructor') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Live Classes Table -->
        <div id="liveClassContainer">
            <!-- List View -->
            <div id="listViewContainer">
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
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Instructor</th>
                                        <th>Scheduled At</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Participants</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($liveClasses as $liveClass)
                                        <tr class="live-class-row"
                                            data-search="{{ strtolower($liveClass->name . ' ' . $liveClass->course->name . ' ' . $liveClass->instructor->name) }}"
                                            data-course="{{ $liveClass->course_id }}"
                                            data-status="{{ $liveClass->status }}"
                                            data-instructor="{{ $liveClass->instructor_id }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input class-checkbox" type="checkbox"
                                                        value="{{ $liveClass->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fa fa-video text-primary fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $liveClass->name }}</h6>
                                                        @if ($liveClass->description)
                                                            <small
                                                                class="text-muted">{{ Str::limit($liveClass->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-light text-dark">{{ $liveClass->course->name }}</span>
                                            </td>
                                            <td>{{ $liveClass->instructor->name }}</td>
                                            <td>
                                                <div>{{ $liveClass->scheduled_at->format('M d, Y') }}</div>
                                                <small
                                                    class="text-muted">{{ $liveClass->scheduled_at->format('h:i A') }}</small>
                                            </td>
                                            <td>{{ $liveClass->formatted_duration }}</td>
                                            <td>
                                                <span
                                                    class="badge status-badge bg-{{ $liveClass->status == 'scheduled' ? 'primary' : ($liveClass->status == 'live' ? 'success' : ($liveClass->status == 'completed' ? 'secondary' : 'danger')) }}"
                                                    style="cursor: pointer;"
                                                    onclick="showStatusModal({{ $liveClass->id }}, '{{ $liveClass->status }}', [
                                                        { value: 'scheduled', label: 'Scheduled' },
                                                        { value: 'live', label: 'Live' },
                                                        { value: 'completed', label: 'Completed' },
                                                        { value: 'cancelled', label: 'Cancelled' }
                                                    ], '{{ route('admin.live-classes.update_status', $liveClass->id) }}')">
                                                    {{ ucfirst($liveClass->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($liveClass->max_participants)
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="me-2">{{ $liveClass->current_participants }}/{{ $liveClass->max_participants }}</span>
                                                        <div class="progress flex-grow-1 progress-h-6">
                                                            <div class="progress-bar"
                                                                style="width: {{ $liveClass->participant_percentage }}%">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span>{{ $liveClass->current_participants }} registered</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="live-class-actions">
                                                    <a href="{{ route('admin.live-classes.edit', $liveClass) }}"
                                                        class="btn btn-outline-primary btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.live-classes.registrations', $liveClass) }}"
                                                        class="btn btn-outline-info btn-sm" title="Registrations">
                                                        <i class="fa fa-users"></i>
                                                    </a>
                                                    <button onclick="duplicateClass({{ $liveClass->id }})"
                                                        class="btn btn-outline-secondary btn-sm" title="Duplicate">
                                                        <i class="fa fa-copy"></i>
                                                    </button>
                                                    <button onclick="deleteClass({{ $liveClass->id }})"
                                                        class="btn btn-outline-danger btn-sm" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fa fa-video fa-3x mb-3"></i>
                                                    <h5>No live classes found</h5>
                                                    <p>Create your first live class to get started</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="row mt-3" id="bulkActionsContainer" style="display: none !important;">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center me-3">
                                        <label class="form-label me-2 mb-0 small">Per page:</label>
                                        <select class="form-select form-select-sm w-auto" id="perPageSelect"
                                            onchange="changePerPage(this.value)">
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
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="bulkDelete()">
                                            <i class="fa fa-trash me-1"></i>Delete Selected
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="fa fa-cog me-1"></i>Bulk Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="bulkUpdateStatus('scheduled')">Mark as Scheduled</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="bulkUpdateStatus('live')">Mark as Live</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="bulkUpdateStatus('completed')">Mark as Completed</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="bulkUpdateStatus('cancelled')">Mark as Cancelled</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    {{ $liveClasses->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Function to toggle bulk actions visibility
            function toggleBulkActions() {
                const checkboxes = document.querySelectorAll('.class-checkbox:checked');
                const bulkActionsContainer = document.getElementById('bulkActionsContainer');
                if (bulkActionsContainer) {
                    if (checkboxes.length > 0) {
                        bulkActionsContainer.style.display = '';
                    } else {
                        bulkActionsContainer.style.display = 'none';
                    }
                }
            }

            // Initialize - hide bulk actions on page load
            toggleBulkActions();

            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.class-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleBulkActions();
                });
            }

            // Individual checkbox change handlers
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('class-checkbox')) {
                    const selectAll = document.getElementById('selectAll');
                    const checkboxes = document.querySelectorAll('.class-checkbox');
                    const checkedBoxes = document.querySelectorAll('.class-checkbox:checked');

                    // Update select all checkbox state
                    if (selectAll) {
                        selectAll.checked = checkboxes.length === checkedBoxes.length;
                        selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < checkboxes.length;
                    }

                    toggleBulkActions();
                }
            });

            // Change per page function
            function changePerPage(value) {
                const url = new URL(window.location);
                url.searchParams.set('per_page', value);
                url.searchParams.delete('page'); // Reset to first page
                // Use AJAX to update
                const formData = new FormData(document.getElementById('filterForm'));
                formData.set('per_page', value);
                const params = new URLSearchParams(formData);
                performAjaxSearch();
            }

            // Initialize variables for AJAX search
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.querySelector('.table tbody');
            const paginationContainer = document.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');
            const tableContainer = document.getElementById('listViewContainer');

            // AJAX search function
            function performAjaxSearch() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                // Show loading state
                if (tableBody) {
                    tableBody.innerHTML =
                        '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }

                fetch(`{{ route('admin.live-classes.index') }}?${params.toString()}`, {
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
                        const newPagination = tempDiv.querySelector(
                            '.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');
                        const newBulkActions = tempDiv.querySelector('.row.mt-3 .col-md-6:first-child');

                        if (newTableBody && tableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML;
                        }

                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        if (newBulkActions) {
                            const bulkActionsContainer = document.querySelector('.row.mt-3 .col-md-6:first-child');
                            if (bulkActionsContainer) {
                                bulkActionsContainer.innerHTML = newBulkActions.innerHTML;
                            }
                        }

                        // Update URL without reload
                        const newUrl = `{{ route('admin.live-classes.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);

                        // Re-attach event listeners for checkboxes
                        const selectAllCheckbox = document.getElementById('selectAll');
                        if (selectAllCheckbox) {
                            // Remove old listener and add new one
                            const newSelectAll = selectAllCheckbox.cloneNode(true);
                            selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);

                            newSelectAll.addEventListener('change', function() {
                                const checkboxes = document.querySelectorAll('.class-checkbox');
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = this.checked;
                                });
                                toggleBulkActions();
                            });
                        }

                        // Re-attach individual checkbox listeners
                        document.querySelectorAll('.class-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const selectAll = document.getElementById('selectAll');
                                const allCheckboxes = document.querySelectorAll('.class-checkbox');
                                const checkedBoxes = document.querySelectorAll('.class-checkbox:checked');

                                if (selectAll) {
                                    selectAll.checked = allCheckboxes.length === checkedBoxes.length;
                                    selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes
                                        .length < allCheckboxes.length;
                                }

                                toggleBulkActions();
                            });
                        });

                        // Hide bulk actions if no checkboxes are selected
                        toggleBulkActions();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML =
                                '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
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

            document.getElementById('instructorFilter').addEventListener('change', function() {
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
                    document.getElementById('instructorFilter').value = '';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.live-classes.index') }}');

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

            // Toggle status
            function toggleStatus(classId) {
                if (confirm('Are you sure you want to toggle the status of this live class?')) {
                    fetch(`/admin/live-classes/${classId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating status');
                        });
                }
            }

            // Duplicate class
            function duplicateClass(classId) {
                if (confirm('Are you sure you want to duplicate this live class?')) {
                    fetch(`/admin/live-classes/${classId}/duplicate`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error duplicating class');
                        });
                }
            }

            // Delete class
            function deleteClass(classId) {
                if (confirm('Are you sure you want to delete this live class? This action cannot be undone.')) {
                    fetch(`/admin/live-classes/${classId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting class');
                        });
                }
            }

            // Bulk delete
            function bulkDelete() {
                const selectedClasses = Array.from(document.querySelectorAll('.class-checkbox:checked')).map(cb => cb.value);

                if (selectedClasses.length === 0) {
                    alert('Please select at least one live class to delete.');
                    return;
                }

                if (confirm(
                        `Are you sure you want to delete ${selectedClasses.length} live class(es)? This action cannot be undone.`
                    )) {
                    fetch('/admin/live-classes/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                selected_classes: selectedClasses
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Uncheck all checkboxes and hide bulk actions
                                document.querySelectorAll('.class-checkbox').forEach(cb => cb.checked = false);
                                const selectAll = document.getElementById('selectAll');
                                if (selectAll) selectAll.checked = false;
                                toggleBulkActions();
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting classes');
                        });
                }
            }

            // Bulk update status
            function bulkUpdateStatus(status) {
                const selectedClasses = Array.from(document.querySelectorAll('.class-checkbox:checked')).map(cb => cb.value);

                if (selectedClasses.length === 0) {
                    alert('Please select at least one live class to update.');
                    return;
                }

                if (confirm(
                        `Are you sure you want to update the status of ${selectedClasses.length} live class(es) to "${status}"?`
                    )) {
                    fetch('/admin/live-classes/bulk-update-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                selected_classes: selectedClasses,
                                status: status
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Uncheck all checkboxes and hide bulk actions
                                document.querySelectorAll('.class-checkbox').forEach(cb => cb.checked = false);
                                const selectAll = document.getElementById('selectAll');
                                if (selectAll) selectAll.checked = false;
                                toggleBulkActions();
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error updating status');
                        });
                }
            }
        </script>
        @include('admin.partials.status-modal')
    @endpush
@endsection
