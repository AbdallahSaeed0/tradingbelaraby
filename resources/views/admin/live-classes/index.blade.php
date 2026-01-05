@extends('admin.layout')

@section('title', 'Live Classes Management')

@section('content')
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
                                <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Toggle Buttons -->
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-outline-primary btn-sm" id="gridView">
                <i class="fa fa-th"></i>
            </button>
            <button class="btn btn-outline-primary btn-sm active" id="listView">
                <i class="fa fa-list"></i>
            </button>
        </div>

        <!-- Live Classes Grid/List -->
        <div id="liveClassContainer">
            <!-- List View -->
            <div id="listViewContainer">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll">
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
                                                <input type="checkbox" class="class-checkbox"
                                                    value="{{ $liveClass->id }}">
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
            // Select all functionality
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.class-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
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

