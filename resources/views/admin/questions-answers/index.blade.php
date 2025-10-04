@extends('admin.layout')

@section('title', 'Q&A Management')

@push('styles')
    <style>
        .qa-stat-card {
            border: 1px solid #dee2e6;
            transition: box-shadow 0.2s, transform 0.2s;
            border-radius: 0.5rem;
        }

        .qa-stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .qa-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .qa-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .qa-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            margin-right: 0.25rem;
        }

        .qa-table th,
        .qa-table td {
            vertical-align: middle;
            font-size: 15px;
        }

        .qa-table th {
            background: #f8f9fa;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        .qa-question-title {
            font-weight: 600;
            font-size: 1rem;
            color: #212529;
        }

        .qa-question-snippet {
            color: #6c757d;
            font-size: 0.95rem;
        }

        .qa-badge-priority-urgent {
            background: #dc3545;
            color: #fff;
        }

        .qa-badge-priority-high {
            background: #ffc107;
            color: #212529;
        }

        .qa-badge-priority-normal {
            background: #17a2b8;
            color: #fff;
        }

        .qa-badge-priority-low {
            background: #6c757d;
            color: #fff;
        }

        .qa-badge-status-pending {
            background: #ffc107;
            color: #212529;
        }

        .qa-badge-status-answered {
            background: #28a745;
            color: #fff;
        }

        .qa-badge-status-closed {
            background: #6c757d;
            color: #fff;
        }

        .qa-badge-status-flagged {
            background: #dc3545;
            color: #fff;
        }

        .qa-badge-type {
            background: #f1f3f4;
            color: #333;
        }

        .qa-table .dropdown-menu {
            min-width: 160px;
        }

        .qa-table .dropdown-item i {
            width: 18px;
        }

        .qa-table .dropdown-item {
            font-size: 14px;
        }

        .qa-table .badge {
            font-size: 12px;
        }

        .qa-table .user-info {
            font-size: 13px;
        }

        .qa-table .question-info {
            min-width: 220px;
        }

        .qa-table .qa-actions {
            min-width: 120px;
        }

        .qa-table .qa-badge {
            margin-right: 2px;
        }

        .qa-table .qa-badge:last-child {
            margin-right: 0;
        }

        .qa-table .qa-badge-type {
            margin-top: 2px;
        }

        .qa-table .qa-badge-status-pending {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.5);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }
    </style>
@endpush

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
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-primary text-white">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Total Questions</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-warning text-dark">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Pending</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-success text-white">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Answered</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['answered'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card qa-stat-card d-flex flex-row align-items-center p-3">
                    <div class="qa-stat-icon bg-danger text-white">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Urgent</h6>
                        <h4 class="fw-bold mb-0">{{ $stats['urgent'] }}</h4>
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
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                    placeholder="Search questions, answers, users...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">All Status</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="course_id">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="question_type">
                                <option value="">All Types</option>
                                @foreach ($questionTypes as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('question_type') == $value ? 'selected' : '' }}>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="priority">
                                <option value="">All Priorities</option>
                                @foreach ($priorities as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ request('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-4" id="bulkActions" style="display: none;">
            <div class="col-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-warning" id="selectedCount">0</span>
                                {{ __('questions selected') }}
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                                    <i class="fas fa-trash me-1"></i>{{ __('Delete Selected') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="clearSelection">
                                    <i class="fas fa-times me-1"></i>{{ __('Clear Selection') }}
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
                    <table class="table table-hover qa-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:32px"><input type="checkbox" id="selectAll"
                                        onchange="toggleSelectAll()"></th>
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
                                    <td><input type="checkbox" class="question-checkbox" value="{{ $question->id }}"
                                            onchange="updateSelection()"></td>
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
                                                class="text-muted">{{ $question->created_at->format('M d, Y') }}</small><br>
                                            <small class="text-muted">{{ $question->created_at->format('g:i A') }}</small>
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
                @if ($questions->hasPages())
                    <div class="d-flex justify-content-center p-3">
                        {{ $questions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change
            document.querySelector('select[name="status"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.querySelector('select[name="course_id"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.querySelector('select[name="question_type"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.querySelector('select[name="priority"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            // Search with debounce
            let searchTimeout;
            document.querySelector('input[name="search"]').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
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
                        alert('{{ __('Please select questions to delete.') }}');
                        return;
                    }

                    if (confirm(
                            '{{ __('Are you sure you want to delete the selected questions? This action cannot be undone.') }}'
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
