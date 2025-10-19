@extends('admin.layout')

@section('title', 'Course Management')

@push('styles')
    <style>
        .course-thumbnail {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 0.375rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .course-stats {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin-right: 0.25rem;
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
                        <h1 class="h3 mb-0">Course Management</h1>
                        <p class="text-muted">Manage courses, sections, and lectures</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Add New Course
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-book text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Courses</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_courses'] }}</h4>
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
                            <h6 class="text-muted mb-0">Published</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['published_courses'] }}</h4>
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
                            <h6 class="text-muted mb-0">Draft</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['draft_courses'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-soft">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Students</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_students'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.courses.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search courses..."
                                    value="{{ request('search') }}" id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published
                                </option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="category" id="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
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
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Toggle and Actions -->
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
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('archived')">
                                        <i class="fa fa-archive me-2"></i>Archive Selected
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-success me-2" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="fa fa-upload me-1"></i>Import
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fa fa-download me-1"></i>Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.courses.export', ['format' => 'csv']) }}">
                                        <i class="fa fa-file-csv me-2"></i>CSV
                                    </a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.courses.export', ['format' => 'excel']) }}">
                                        <i class="fa fa-file-excel me-2"></i>Excel
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="card" id="listViewContainer">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Courses List</h5>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of
                        {{ $courses->total() }} entries</span>
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
                                <th>Course</th>
                                <th>Category</th>
                                <th>Instructor</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox"
                                                value="{{ $course->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($course->image)
                                                <img src="{{ $course->image_url }}" alt="Course"
                                                    class="course-thumbnail me-3">
                                            @else
                                                <div
                                                    class="course-thumbnail me-3 bg-primary text-white d-flex align-items-center justify-content-center fs-12px">
                                                    {{ strtoupper(substr($course->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $course->name }}</h6>
                                                <small
                                                    class="text-muted">{{ Str::limit($course->description, 60) }}</small>
                                                <div class="course-stats mt-1">
                                                    <span class="me-3">
                                                        <i class="fa fa-play-circle me-1"></i>{{ $course->total_lessons }}
                                                        Lectures
                                                    </span>
                                                    <span>
                                                        <i
                                                            class="fa fa-clock me-1"></i>{{ $course->duration ?? 'Not Found' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($course->category)
                                            <span class="badge bg-primary">{{ $course->category->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Not Found</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($course->instructors->count() > 0)
                                            @foreach ($course->instructors as $index => $instructor)
                                                @if ($index < 2)
                                                    <div class="d-flex align-items-center {{ $index > 0 ? 'mt-1' : '' }}">
                                                        @if ($instructor->avatar)
                                                            <img src="{{ asset('storage/' . $instructor->avatar) }}"
                                                                class="rounded-circle me-2" width="24"
                                                                height="24">
                                                        @else
                                                            <div
                                                                class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2 w-24 h-24 fs-10px">
                                                                {{ strtoupper(substr($instructor->name, 0, 2)) }}
                                                            </div>
                                                        @endif
                                                        <span class="small">{{ $instructor->name }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if ($course->instructors->count() > 2)
                                                <small class="text-muted">+{{ $course->instructors->count() - 2 }}
                                                    more</small>
                                            @endif
                                        @else
                                            <span class="text-muted">No instructor</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="fw-bold">{{ $course->enrollments_count ?? $course->enrollments->count() }}</span>
                                        <small class="text-muted d-block">enrolled</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'published' => 'bg-success',
                                                'draft' => 'bg-warning',
                                                'archived' => 'bg-secondary',
                                            ];
                                            $statusClass = $statusClasses[$course->status] ?? 'bg-secondary';
                                        @endphp
                                        <span
                                            class="badge {{ $statusClass }} status-badge">{{ ucfirst($course->status) }}</span>
                                    </td>
                                    <td>
                                        <span>{{ $course->created_at->format('M d, Y') }}</span>
                                        <small
                                            class="text-muted d-block">{{ $course->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.courses.show', $course) }}"
                                                class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.edit', $course) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.analytics', $course) }}"
                                                class="btn btn-sm btn-outline-info" title="Analytics">
                                                <i class="fa fa-chart-bar"></i>
                                            </a>
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fa fa-copy me-2"></i>Duplicate</a></li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('admin.courses.enrollments', $course) }}"><i
                                                                class="fa fa-users me-2"></i>Enrollments</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.courses.destroy', $course) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Are you sure you want to delete this course?')">
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
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-book fa-3x mb-3"></i>
                                            <h5>No courses found</h5>
                                            <p>Start by creating your first course.</p>
                                            <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus me-2"></i>Create Course
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
                        Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }}
                        entries
                    </div>
                    {{ $courses->links() }}
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div class="card" id="gridViewContainer" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Courses Grid</h5>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of
                        {{ $courses->total() }} entries</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @forelse($courses as $course)
                        <div class="col-lg-4 col-md-6">
                            <div class="card course-card h-100">
                                <div class="position-relative">
                                    @if ($course->image)
                                        <img src="{{ $course->image_url }}" class="card-img-top"
                                            alt="{{ $course->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-primary text-white d-flex align-items-center justify-content-center"
                                            style="height: 200px;">
                                            <i class="fa fa-book fa-3x"></i>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 end-0 m-2">
                                        @php
                                            $statusClasses = [
                                                'published' => 'bg-success',
                                                'draft' => 'bg-warning',
                                                'archived' => 'bg-secondary',
                                            ];
                                            $statusClass = $statusClasses[$course->status] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($course->status) }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">{{ $course->name }}</h6>
                                    <p class="card-text text-muted small">{{ Str::limit($course->description, 100) }}</p>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted">
                                            <i class="fa fa-user me-1"></i>
                                            @if ($course->instructors->count() > 0)
                                                {{ $course->instructors->pluck('name')->take(2)->join(', ') }}
                                                @if ($course->instructors->count() > 2)
                                                    <span>+{{ $course->instructors->count() - 2 }}</span>
                                                @endif
                                            @else
                                                No instructor
                                            @endif
                                        </small>
                                        <small class="text-muted">
                                            <i class="fa fa-users me-1"></i>{{ $course->enrollments->count() }} students
                                        </small>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary">{{ number_format($course->price, 2) }}
                                            SAR</span>
                                        <small class="text-muted">{{ $course->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox"
                                                value="{{ $course->id }}">
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.courses.show', $course) }}"
                                                class="btn btn-outline-primary" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.edit', $course) }}"
                                                class="btn btn-outline-secondary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.analytics', $course) }}"
                                                class="btn btn-outline-info" title="Analytics">
                                                <i class="fa fa-chart-bar"></i>
                                            </a>
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
                                    <h5>No courses found</h5>
                                    <p>Start by creating your first course.</p>
                                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus me-2"></i>Create Course
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
                        Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }}
                        entries
                    </div>
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Courses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.courses.import') }}" method="POST" enctype="multipart/form-data">
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
                                <li>Required columns: name, instructor_id, category_id</li>
                                <li>Optional columns: description, price, status</li>
                                <li>Status values: published, draft, archived</li>
                                <li>Price should be numeric</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.courses.export', ['format' => 'csv']) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-download me-1"></i>Download Sample CSV
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import Courses</button>
                    </div>
                </form>
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

            // Select all checkbox functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDelete');
            const bulkStatusDropdown = document.getElementById('bulkStatusDropdown');

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

            function toggleBulkActions() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                bulkDeleteBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
                bulkStatusDropdown.style.display = checkedCount > 0 ? 'inline-block' : 'none';
            }

            // Bulk delete functionality
            bulkDeleteBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete the selected courses?')) {
                    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                        .map(checkbox => checkbox.value);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.courses.bulk_delete') }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    selectedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'course_ids[]';
                        input.value = id;
                        form.appendChild(input);
                    });

                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });

            // Auto-submit form on filter change
            document.getElementById('statusFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('categoryFilter').addEventListener('change', function() {
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
        });

        // Bulk update status function
        function bulkUpdateStatus(status) {
            if (confirm(`Are you sure you want to update the selected courses to ${status}?`)) {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(checkbox => checkbox.value);

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.courses.bulk_update_status') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'course_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = status;

                form.appendChild(csrfToken);
                form.appendChild(statusInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush
