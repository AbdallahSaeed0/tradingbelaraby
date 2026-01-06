@extends('admin.layout')

@section('title', 'Q&A Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Q&A Management</h1>
                    <p class="text-muted">Moderate, reply, and manage all course questions and answers</p>
                </div>
                <div>
                    <a href="{{ route('admin.questions-answers.analytics') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-chart-bar me-1"></i> Analytics
                    </a>
                    <a href="{{ route('admin.questions-answers.export') }}" class="btn btn-success">
                        <i class="fas fa-download me-1"></i> Export
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>{{ session('success', 'admin') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error', 'admin') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-question-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Questions</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
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
                            <h6 class="text-muted mb-0">Pending</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4>
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
                            <h6 class="text-muted mb-0">Answered</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['answered'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-soft">
                            <i class="fa fa-exclamation-triangle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Urgent</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['urgent'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters/Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.questions-answers.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" id="searchInput" value="{{ request('search') }}"
                                    placeholder="Search questions, answers, users...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="course_id" id="courseFilter">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="question_type" id="questionTypeFilter">
                                <option value="">All Types</option>
                                @foreach ($questionTypes as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('question_type') == $value ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="priority" id="priorityFilter">
                                <option value="">All Priorities</option>
                                @foreach ($priorities as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-4 d-none-initially" id="bulkActions">
            <div class="col-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-warning" id="selectedCount">0</span>
                                {{ custom_trans('questions selected', 'admin') }}
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                                    <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete Selected', 'admin') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="clearSelection">
                                    <i class="fas fa-times me-1"></i>{{ custom_trans('Clear Selection', 'admin') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Questions & Answers</h5>
                <div class="d-flex align-items-center">
                    <label class="me-2 mb-0">Sort by:</label>
                    <select class="form-select form-select-sm w-auto" onchange="updateSort(this.value)">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="priority" {{ request('sort') == 'priority' ? 'selected' : '' }}>Priority</option>
                        <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Most Viewed</option>
                        <option value="votes" {{ request('sort') == 'votes' ? 'selected' : '' }}>Most Voted</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped qa-table">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </div>
                                </th>
                                <th>Question</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Type</th>
                                <th>Views</th>
                                <th>Votes</th>
                                <th>Created</th>
                                <th class="qa-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input question-checkbox" type="checkbox" value="{{ $question->id }}"
                                                onchange="updateSelection()">
                                        </div>
                                    </td>
                                    <td class="question-info">
                                        <div class="qa-question-title mb-1">
                                            <a href="{{ route('admin.questions-answers.show', $question) }}"
                                                class="text-dark">
                                                {{ Str::limit($question->question_title, 50) }}
                                            </a>
                                        </div>
                                        <div class="qa-question-snippet">
                                            {{ Str::limit(strip_tags($question->question_content), 80) }}
                                        </div>
                                        @if ($question->is_anonymous)
                                            <span class="badge bg-secondary qa-badge">Anonymous</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($question->is_anonymous)
                                            <span class="text-muted">Anonymous</span>
                                        @else
                                            <div class="user-info">
                                                <span class="fw-bold">{{ $question->user->name ?? 'Unknown' }}</span><br>
                                                <small class="text-muted">{{ $question->user->email ?? '' }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $question->course->name ?? 'N/A' }}</span>
                                        @if ($question->lecture)
                                            <br><small class="text-muted">{{ $question->lecture->title }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="qa-badge qa-badge-status-{{ $question->status }}">
                                            {{ ucfirst($question->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="qa-badge qa-badge-priority-{{ $question->priority }}">
                                            {{ ucfirst($question->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="qa-badge qa-badge-type">
                                            {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                        </span>
                                    </td>
                                    <td><span class="text-muted">{{ $question->views_count }}</span></td>
                                    <td><span
                                            class="text-muted">{{ $question->helpful_votes }}/{{ $question->total_votes }}</span>
                                    </td>
                                    <td>
                                        @if ($question->created_at)
                                            <small
                                                class="text-muted">{{ $question->created_at->format('M d, Y', 'admin') }}</small><br>
                                            <small class="text-muted">{{ $question->created_at->format('g:i A', 'admin') }}</small>
                                        @else
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                    <td class="qa-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.questions-answers.show', $question) }}">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                                @if ($question->status === 'pending')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.questions-answers.reply', $question) }}">
                                                        <i class="fas fa-reply"></i> Reply
                                                    </a>
                                                @endif
                                                @if ($question->status === 'answered')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.questions-answers.reply', $question) }}">
                                                        <i class="fas fa-edit"></i> Edit Reply
                                                    </a>
                                                @endif
                                                @if (!$question->is_public)
                                                    <form
                                                        action="{{ route('admin.questions-answers.approve', $question) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif
                                                @if ($question->status !== 'closed')
                                                    <form action="{{ route('admin.questions-answers.close', $question) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-lock"></i> Close
                                                        </button>
                                                    </form>
                                                @else
                                                    <form
                                                        action="{{ route('admin.questions-answers.reopen', $question) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-unlock"></i> Reopen
                                                        </button>
                                                    </form>
                                                @endif
                                                <div class="dropdown-divider"></div>
                                                <form action="{{ route('admin.questions-answers.reject', $question) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-flag"></i> Flag/Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                            <h5>No questions found</h5>
                                            <p class="text-muted">No questions match your current filters.</p>
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
                        @if ($questions->hasPages())
                            <div class="d-flex justify-content-end">
                                {{ $questions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

                fetch(`{{ route('admin.questions-answers.index') }}?${params.toString()}`, {
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
                        const newUrl = `{{ route('admin.questions-answers.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);

                        // Re-attach event listeners
                        const selectAllCheckbox = document.getElementById('selectAll');
                        if (selectAllCheckbox) {
                            selectAllCheckbox.onchange = toggleSelectAll;
                        }
                        document.querySelectorAll('.question-checkbox').forEach(checkbox => {
                            checkbox.onchange = updateSelection;
                        });
                        updateSelection();
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
            document.getElementById('statusFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('courseFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('questionTypeFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('priorityFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            // Clear filters button - use AJAX
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Clear all form fields
                    document.getElementById('searchInput').value = '';
                    document.getElementById('statusFilter').value = '';
                    document.getElementById('courseFilter').value = '';
                    document.getElementById('questionTypeFilter').value = '';
                    document.getElementById('priorityFilter').value = '';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.questions-answers.index') }}');

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
        });

        function updateSort(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort', value);
            window.location = url;
        }

        // Select All functionality
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const questionCheckboxes = document.querySelectorAll('.question-checkbox');

            questionCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            updateSelection();
        }

        // Update selection count and bulk actions
        function updateSelection() {
            const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
            const selectedCount = checkedBoxes.length;
            const totalCheckboxes = document.querySelectorAll('.question-checkbox').length;
            const selectAllCheckbox = document.getElementById('selectAll');
            const bulkActions = document.getElementById('bulkActions');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedCountSpan = document.getElementById('selectedCount');

            // Update select all checkbox state
            if (selectedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (selectedCount === totalCheckboxes) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }

            // Show/hide bulk actions
            if (selectedCount > 0) {
                bulkActions.style.display = 'block';
                bulkDeleteBtn.disabled = false;
                selectedCountSpan.textContent = selectedCount;
            } else {
                bulkActions.style.display = 'none';
                bulkDeleteBtn.disabled = true;
            }
        }

        // Clear selection
        document.addEventListener('DOMContentLoaded', function() {
            const clearSelectionBtn = document.getElementById('clearSelection');
            if (clearSelectionBtn) {
                clearSelectionBtn.addEventListener('click', function() {
                    document.querySelectorAll('.question-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    document.getElementById('selectAll').checked = false;
                    document.getElementById('selectAll').indeterminate = false;
                    updateSelection();
                });
            }

            // Bulk delete functionality
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(document.querySelectorAll('.question-checkbox:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedIds.length === 0) {
                        alert('{{ custom_trans('Please select questions to delete.', 'admin') }}');
                        return;
                    }

                    if (confirm(
                            '{{ custom_trans('Are you sure you want to delete the selected questions? This action cannot be undone.', 'admin') }}'
                        )) {
                        // Create form and submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('admin.questions-answers.bulk-delete') }}';

                        // Add CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        // Add method override
                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';
                        form.appendChild(methodField);

                        // Add selected IDs
                        selectedIds.forEach(id => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'question_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        });
    </script>
@endpush

