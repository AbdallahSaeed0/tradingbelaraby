@extends('admin.layout')

@section('title', 'Quiz Management')

@push('styles')
    <style>
        .quiz-card {
            transition: transform 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .quiz-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .quiz-stats {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .question-type-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .difficulty-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .quiz-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin-right: 0.25rem;
        }

        .quiz-thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.375rem;
        }

        .status-badge {
            transition: all 0.2s ease;
        }

        .status-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
                        <h1 class="h3 mb-0">Quiz Management</h1>
                        <p class="text-muted">Create and manage quizzes for your courses</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fa fa-upload me-2"></i>Import Questions
                        </button>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Quiz
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
                            <i class="fa fa-question-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Quizzes</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_quizzes'] }}</h4>
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
                            <h6 class="text-muted mb-0">Active Quizzes</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['active_quizzes'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-list text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Questions</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_questions'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-soft">
                            <i class="fa fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Attempts</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_attempts'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.quizzes.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search quizzes..."
                                    value="{{ request('search') }}" id="searchInput">
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
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Published</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="gridView">
                                    <i class="fa fa-th"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm active" id="listView">
                                    <i class="fa fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quiz Grid/List -->
        <div id="quizContainer">
            <!-- List View -->
            <div class="card" id="listViewContainer">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quizzes</h5>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-danger me-2 d-none-initially" id="bulkDelete">
                            <i class="fa fa-trash me-1"></i>Delete Selected
                        </button>
                        <div class="dropdown me-2 d-none-initially" id="bulkStatusDropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fa fa-toggle-on me-1"></i>Change Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('published')">
                                        <i class="fa fa-check me-2"></i>Publish Selected
                                    </a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('draft')">
                                        <i class="fa fa-clock me-2"></i>Set as Draft
                                    </a></li>
                            </ul>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fa fa-download me-1"></i>Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.quizzes.export_list', ['format' => 'csv']) }}">
                                        <i class="fa fa-file-csv me-2"></i>CSV
                                    </a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.quizzes.export_list', ['format' => 'excel']) }}">
                                        <i class="fa fa-file-excel me-2"></i>Excel
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Quiz</th>
                                    <th>Course</th>
                                    <th>Questions</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizzes as $quiz)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input row-checkbox" type="checkbox"
                                                    value="{{ $quiz->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $quiz->name }}</h6>
                                                <small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                                <div class="quiz-stats mt-1">
                                                    <span class="me-3"><i
                                                            class="fa fa-clock me-1"></i>{{ $quiz->formatted_time_limit }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $quiz->course->name }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $quiz->questions->count() }}</span>
                                            <small class="text-muted d-block">questions</small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $quiz->is_published ? 'success' : 'warning' }} status-badge"
                                                data-quiz-id="{{ $quiz->id }}" class="cursor-pointer">
                                                {{ $quiz->is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $quiz->created_at->format('M d, Y') }}</span>
                                            <small
                                                class="text-muted d-block">{{ $quiz->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="quiz-actions">
                                                <a href="{{ route('admin.quizzes.show', $quiz) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.quizzes.analytics', $quiz) }}"
                                                    class="btn btn-sm btn-outline-info" title="Analytics">
                                                    <i class="fa fa-chart-bar"></i>
                                                </a>
                                                <div class="dropdown d-inline">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.quizzes.duplicate', $quiz) }}"><i
                                                                    class="fa fa-copy me-2"></i>Duplicate</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.quizzes.export', $quiz) }}"><i
                                                                    class="fa fa-download me-2"></i>Export</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('admin.quizzes.destroy', $quiz) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                                    <i class="fa fa-trash me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-question-circle fa-3x mb-3"></i>
                                                <h5>No Quizzes Found</h5>
                                                <p>Create your first quiz to get started.</p>
                                                <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                                                    <i class="fa fa-plus me-2"></i>Create Quiz
                                                </a>
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
                            @if ($quizzes->count() > 0)
                                Showing {{ $quizzes->firstItem() }} to {{ $quizzes->lastItem() }} of
                                {{ $quizzes->total() }} entries
                            @else
                                No entries found
                            @endif
                        </div>
                        @if ($quizzes->hasPages())
                            <nav>
                                {{ $quizzes->links('pagination::bootstrap-4') }}
                            </nav>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Grid View -->
            <div class="card d-none-initially" id="gridViewContainer">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quizzes Grid</h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-3">Showing {{ $quizzes->firstItem() }} to {{ $quizzes->lastItem() }}
                            of {{ $quizzes->total() }} entries</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @forelse($quizzes as $quiz)
                            <div class="col-lg-4 col-md-6">
                                <div class="card quiz-card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span
                                            class="badge bg-{{ $quiz->is_published ? 'success' : 'warning' }}">{{ $quiz->is_published ? 'Published' : 'Draft' }}</span>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.quizzes.show', $quiz) }}"><i
                                                            class="fa fa-eye me-2"></i>View</a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.quizzes.edit', $quiz) }}"><i
                                                            class="fa fa-edit me-2"></i>Edit</a></li>
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.quizzes.duplicate', $quiz) }}"><i
                                                            class="fa fa-copy me-2"></i>Duplicate</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.quizzes.destroy', $quiz) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                            <i class="fa fa-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $quiz->name }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($quiz->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="badge bg-primary">{{ $quiz->course->name }}</span>
                                            <span class="badge bg-info">{{ $quiz->formatted_time_limit }}</span>
                                        </div>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $quiz->questions->count() }}</div>
                                                <small class="text-muted">Questions</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $quiz->attempts->count() }}</div>
                                                <small class="text-muted">Attempts</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $quiz->passing_score }}%</div>
                                                <small class="text-muted">Pass Score</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $quiz->created_at->format('M d, Y') }}</small>
                                            <div>
                                                <a href="{{ route('admin.quizzes.show', $quiz) }}"
                                                    class="btn btn-sm btn-outline-primary">View</a>
                                                <a href="{{ route('admin.quizzes.edit', $quiz) }}"
                                                    class="btn btn-sm btn-outline-secondary">Edit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fa fa-question-circle fa-3x mb-3"></i>
                                        <h5>No Quizzes Found</h5>
                                        <p>Create your first quiz to get started.</p>
                                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
                                            <i class="fa fa-plus me-2"></i>Create Quiz
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            @if ($quizzes->count() > 0)
                                Showing {{ $quizzes->firstItem() }} to {{ $quizzes->lastItem() }} of
                                {{ $quizzes->total() }} entries
                            @else
                                No entries found
                            @endif
                        </div>
                        @if ($quizzes->hasPages())
                            <nav>
                                {{ $quizzes->links('pagination::bootstrap-4') }}
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Quizzes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.quizzes.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="importFile" name="file"
                                accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">Supported formats: CSV, Excel (XLSX, XLS). Max size: 2MB</div>
                        </div>
                        <div class="alert alert-info">
                            <h6>CSV Format Requirements:</h6>
                            <ul class="mb-0">
                                <li>Required columns: name, course_id</li>
                                <li>Optional columns: description, time_limit_minutes, passing_score, is_published</li>
                                <li>is_published values: true, false</li>
                                <li>passing_score should be numeric (0-100)</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.quizzes.export_list', ['format' => 'csv']) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-download me-1"></i>Download Sample CSV
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import Quizzes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // View toggle
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

            document.getElementById('courseFilter').addEventListener('change', function() {
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

            // Select all functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDelete');

            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleBulkActions();
            });

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                    selectAllCheckbox.checked = checkedCount === rowCheckboxes.length;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount <
                        rowCheckboxes.length;
                    toggleBulkActions();
                });
            });

            // Bulk delete functionality
            bulkDeleteBtn.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(checkbox => checkbox.value);

                if (selectedIds.length === 0) {
                    alert('Please select at least one quiz');
                    return;
                }

                if (confirm('Are you sure you want to delete the selected quiz(zes)?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.quizzes.bulk_delete') }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'quiz_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            });

            function toggleBulkActions() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                bulkDeleteBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
                document.getElementById('bulkStatusDropdown').style.display = checkedCount > 0 ? 'inline-block' :
                    'none';
            }

            // Clear filters functionality is handled by the Clear button in the form

            // Status badge click handler
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('status-badge')) {
                    const quizId = e.target.dataset.quizId;
                    toggleQuizStatus(quizId, e.target);
                }
            });

            // Filter functionality
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            document.getElementById('courseFilter').addEventListener('change', function() {
                applyFilters();
            });

            document.getElementById('statusFilter').addEventListener('change', function() {
                applyFilters();
            });

            function applyFilters() {
                const search = document.getElementById('searchInput').value;
                const course = document.getElementById('courseFilter').value;
                const status = document.getElementById('statusFilter').value;

                const params = new URLSearchParams();
                if (search) params.append('search', search);
                if (course) params.append('course', course);
                if (status) params.append('status', status);

                window.location.href = '{{ route('admin.quizzes.index') }}?' + params.toString();
            }

            function toggleQuizStatus(quizId, badgeElement) {
                fetch(`{{ route('admin.quizzes.toggle_status', ':id') }}`.replace(':id', quizId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.is_published) {
                                badgeElement.textContent = 'Published';
                                badgeElement.className = 'badge bg-success status-badge';
                            } else {
                                badgeElement.textContent = 'Draft';
                                badgeElement.className = 'badge bg-warning status-badge';
                            }
                            badgeElement.dataset.quizId = quizId;
                            badgeElement.style.cursor = 'pointer';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating quiz status');
                    });
            }

            function bulkUpdateStatus(status) {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(checkbox => checkbox.value);

                if (selectedIds.length === 0) {
                    alert('Please select at least one quiz');
                    return;
                }

                if (confirm(`Are you sure you want to ${status} the selected quiz(zes)?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.quizzes.bulk_update_status') }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = status;
                    form.appendChild(statusInput);

                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'quiz_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                }
            }
        });
    </script>
@endpush
