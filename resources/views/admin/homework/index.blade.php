@extends('admin.layout')

@section('title', 'Homework Management')

@push('styles')
    <style>
        .homework-card {
            transition: transform 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .homework-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .homework-stats {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .homework-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin-right: 0.25rem;
        }

        .status-badge {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .status-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Homework Management</h1>
                        <p class="text-muted">Create and manage homework assignments for your courses</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fa fa-upload me-2"></i>Import
                        </button>
                        <div class="dropdown d-inline me-2">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fa fa-download me-2"></i>Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.homework.export', ['format' => 'csv']) }}">
                                        <i class="fa fa-file-csv me-2"></i>CSV
                                    </a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.homework.export', ['format' => 'excel']) }}">
                                        <i class="fa fa-file-excel me-2"></i>Excel
                                    </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.homework.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Homework
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
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-book text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Homework</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_homework'] }}</h4>
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
                            <h6 class="text-muted mb-0">Published</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['published_homework'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-edit text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Draft</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['draft_homework'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-upload text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Submissions</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_submissions'] }}</h4>
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
                            <h6 class="text-muted mb-0">Pending Grading</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['pending_grading'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-soft">
                            <i class="fa fa-exclamation-triangle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Overdue</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['overdue_homework'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.homework.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search homework..."
                                    id="searchInput" value="{{ request('search') }}">
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
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Published
                                </option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.homework.index') }}" class="btn btn-outline-secondary">
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
                </div>
            </div>
        </div>

        <!-- Homework Grid/List -->
        <div id="homeworkContainer">
            <!-- List View -->
            <div id="listViewContainer">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>Name</th>
                                        <th>Course</th>
                                        <th>Instructor</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Submissions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($homework as $hw)
                                        <tr class="homework-row"
                                            data-search="{{ strtolower($hw->name . ' ' . $hw->course->name . ' ' . $hw->instructor->name) }}"
                                            data-course="{{ $hw->course_id }}"
                                            data-instructor="{{ $hw->instructor_id }}"
                                            data-status="{{ $hw->is_published ? 'published' : 'draft' }}">
                                            <td>
                                                <input type="checkbox" class="homework-checkbox"
                                                    value="{{ $hw->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fa fa-book text-primary fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $hw->name }}</h6>
                                                        @if ($hw->description)
                                                            <small
                                                                class="text-muted">{{ Str::limit($hw->description, 50) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $hw->course->name }}</span>
                                            </td>
                                            <td>{{ $hw->instructor->name }}</td>
                                            <td>
                                                <div>{{ $hw->due_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $hw->due_date->format('h:i A') }}</small>
                                                @if ($hw->due_date->isPast())
                                                    <div class="text-danger small">{{ $hw->due_date->diffForHumans() }}
                                                    </div>
                                                @else
                                                    <div class="text-success small">{{ $hw->due_date->diffForHumans() }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge status-badge bg-{{ $hw->is_published ? 'success' : 'secondary' }}"
                                                    onclick="toggleStatus({{ $hw->id }})">
                                                    {{ $hw->is_published ? 'Published' : 'Draft' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="me-2">{{ $hw->submitted_assignments }}/{{ $hw->total_assignments ?: 'N/A' }}</span>
                                                    @if ($hw->total_assignments > 0)
                                                        <div class="progress flex-grow-1" style="height: 6px;">
                                                            <div class="progress-bar"
                                                                style="width: {{ ($hw->submitted_assignments / $hw->total_assignments) * 100 }}%">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if ($hw->graded_assignments > 0)
                                                    <small class="text-muted">{{ $hw->graded_assignments }} graded</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="homework-actions">
                                                    <a href="{{ route('admin.homework.edit', $hw) }}"
                                                        class="btn btn-outline-primary btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.homework.submissions', $hw) }}"
                                                        class="btn btn-outline-info btn-sm" title="Submissions">
                                                        <i class="fa fa-upload"></i>
                                                    </a>
                                                    <a href="{{ route('admin.homework.analytics', $hw) }}"
                                                        class="btn btn-outline-success btn-sm" title="Analytics">
                                                        <i class="fa fa-chart-bar"></i>
                                                    </a>
                                                    <button onclick="duplicateHomework({{ $hw->id }})"
                                                        class="btn btn-outline-secondary btn-sm" title="Duplicate">
                                                        <i class="fa fa-copy"></i>
                                                    </button>
                                                    <button onclick="deleteHomework({{ $hw->id }})"
                                                        class="btn btn-outline-danger btn-sm" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fa fa-book fa-3x mb-3"></i>
                                                    <h5>No homework found</h5>
                                                    <p>Create your first homework assignment to get started</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="bulkDelete()">
                                        <i class="fa fa-trash me-1"></i>Delete Selected
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="fa fa-cog me-1"></i>Bulk Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="bulkUpdateStatus(true)">Publish Selected</a></li>
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="bulkUpdateStatus(false)">Unpublish Selected</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    {{ $homework->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridViewContainer" style="display: none;">
            <div class="row g-4">
                @forelse($homework as $hw)
                    <div class="col-lg-4 col-md-6">
                        <div class="card homework-card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span
                                    class="badge bg-{{ $hw->is_published ? 'success' : 'secondary' }}">{{ $hw->is_published ? 'Published' : 'Draft' }}</span>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.homework.edit', $hw) }}"><i
                                                    class="fa fa-edit me-2"></i>Edit</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin.homework.submissions', $hw) }}"><i
                                                    class="fa fa-upload me-2"></i>Submissions</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('admin.homework.analytics', $hw) }}"><i
                                                    class="fa fa-chart-bar me-2"></i>Analytics</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#"
                                                onclick="deleteHomework({{ $hw->id }})"><i
                                                    class="fa fa-trash me-2"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $hw->name }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($hw->description, 100) }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary">{{ $hw->course->name }}</span>
                                    <span class="badge bg-info">{{ $hw->instructor->name }}</span>
                                </div>

                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="fw-bold">
                                            {{ $hw->submitted_assignments }}/{{ $hw->total_assignments ?: 'N/A' }}</div>
                                        <small class="text-muted">Submissions</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="fw-bold">{{ $hw->max_score }}</div>
                                        <small class="text-muted">Max Score</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="fw-bold">{{ $hw->weight_percentage }}%</div>
                                        <small class="text-muted">Weight</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Due: {{ $hw->due_date->format('M d, Y') }}</small>
                                    <div>
                                        <a href="{{ route('admin.homework.edit', $hw) }}"
                                            class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="{{ route('admin.homework.submissions', $hw) }}"
                                            class="btn btn-sm btn-outline-info">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-4">
                            <div class="text-muted">
                                <i class="fa fa-book fa-3x mb-3"></i>
                                <h5>No homework found</h5>
                                <p>Create your first homework assignment to get started</p>
                                <a href="{{ route('admin.homework.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus me-2"></i>Create Homework
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Homework</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.homework.import') }}" method="POST" enctype="multipart/form-data">
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
                                <li>Required columns: name, course_id, instructor_id</li>
                                <li>Optional columns: description, due_date, max_score, weight_percentage, is_published</li>
                                <li>is_published values: true, false</li>
                                <li>due_date format: YYYY-MM-DD HH:MM:SS</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.homework.export', ['format' => 'csv']) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-download me-1"></i>Download Sample CSV
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import Homework</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Select all functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.homework-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

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

            document.getElementById('courseFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('instructorFilter').addEventListener('change', function() {
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

            // Toggle status
            function toggleStatus(homeworkId) {
                if (confirm('Are you sure you want to toggle the status of this homework?')) {
                    fetch(`/admin/homework/${homeworkId}/toggle-status`, {
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

            // Duplicate homework
            function duplicateHomework(homeworkId) {
                if (confirm('Are you sure you want to duplicate this homework?')) {
                    fetch(`/admin/homework/${homeworkId}/duplicate`, {
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
                            alert('Error duplicating homework');
                        });
                }
            }

            // Delete homework
            function deleteHomework(homeworkId) {
                if (confirm('Are you sure you want to delete this homework? This action cannot be undone.')) {
                    fetch(`/admin/homework/${homeworkId}`, {
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
                            alert('Error deleting homework');
                        });
                }
            }

            // Bulk delete
            function bulkDelete() {
                const selectedHomework = Array.from(document.querySelectorAll('.homework-checkbox:checked')).map(cb => cb
                    .value);

                if (selectedHomework.length === 0) {
                    alert('Please select at least one homework to delete.');
                    return;
                }

                if (confirm(
                        `Are you sure you want to delete ${selectedHomework.length} homework assignment(s)? This action cannot be undone.`
                    )) {
                    fetch('/admin/homework/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                selected_homework: selectedHomework
                            })
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
                            alert('Error deleting homework');
                        });
                }
            }

            // Bulk update status
            function bulkUpdateStatus(status) {
                const selectedHomework = Array.from(document.querySelectorAll('.homework-checkbox:checked')).map(cb => cb
                    .value);

                if (selectedHomework.length === 0) {
                    alert('Please select at least one homework to update.');
                    return;
                }

                const statusText = status ? 'publish' : 'unpublish';
                if (confirm(`Are you sure you want to ${statusText} ${selectedHomework.length} homework assignment(s)?`)) {
                    fetch('/admin/homework/bulk-update-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                selected_homework: selectedHomework,
                                status: status
                            })
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
        </script>
    @endpush
@endsection
