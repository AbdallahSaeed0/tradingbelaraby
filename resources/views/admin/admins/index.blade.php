@extends('admin.layout')

@section('title', 'Admins Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Admins Management</h1>
                        <p class="text-muted">Manage system administrators, instructors, and employees</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Add Admin
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
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Admins</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_admins'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft">
                            <i class="fa fa-user-shield text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">System Admins</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['system_admins'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-chalkboard-teacher text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Instructors</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['instructors'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-user-check text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Active Users</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['active_admins'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.admins.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search admins..."
                                    id="searchInput" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="type" id="typeFilter">
                                <option value="">All Types</option>
                                @foreach ($adminTypes as $adminType)
                                    <option value="{{ $adminType->name }}"
                                        {{ request('type') == $adminType->name ? 'selected' : '' }}>
                                        {{ $adminType->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort" id="sortFilter">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A
                                </option>
                                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email A-Z
                                </option>
                                <option value="email_desc" {{ request('sort') == 'email_desc' ? 'selected' : '' }}>Email
                                    Z-A</option>
                            </select>
                        </div>
                        <div class="col-md-2">
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

        <!-- Admins Table -->
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
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input admin-checkbox" type="checkbox" value="{{ $admin->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $admins->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fa fa-user-circle text-primary fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $admin->name }}</h6>
                                                <small class="text-muted">ID: {{ $admin->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $admin->type == 'admin' ? 'danger' : ($admin->type == 'instructor' ? 'warning' : 'info') }}">
                                            {{ ucfirst($admin->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $admin->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                            class="badge status-badge bg-{{ $admin->is_active ? 'success' : 'secondary' }}"
                                            style="cursor: pointer;"
                                            onclick="showStatusModal({{ $admin->id }}, '{{ $admin->is_active ? 'active' : 'inactive' }}', [
                                                { value: 'active', label: 'Active' },
                                                { value: 'inactive', label: 'Inactive' }
                                            ], '{{ route('admin.admins.update_status', $admin->id) }}')">
                                            {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $admin->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $admin->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="admin-actions">
                                            <a href="{{ route('admin.admins.show', $admin) }}"
                                                class="btn btn-outline-info btn-sm" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.admins.edit', $admin) }}"
                                                class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button onclick="deleteAdmin({{ $admin->id }})"
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
                                            <i class="fa fa-users fa-3x mb-3"></i>
                                            <h5>No admins found</h5>
                                            <p>Create your first admin to get started</p>
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
                        <small class="text-muted">
                            Showing {{ $admins->count() }} of {{ $admins->total() }} admins
                        </small>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            {{ $admins->links() }}
                        </div>
                    </div>
                </div>
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
                    tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }

                fetch(`{{ route('admin.admins.index') }}?${params.toString()}`, {
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
                        const newUrl = `{{ route('admin.admins.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
                        }
                    });
            }

            // Prevent form submission - use AJAX instead
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performAjaxSearch();
            });

            // Dropdown filters - use AJAX
            document.getElementById('typeFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('statusFilter').addEventListener('change', function() {
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
                    document.getElementById('typeFilter').value = '';
                    document.getElementById('statusFilter').value = '';
                    document.getElementById('sortFilter').value = 'latest';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.admins.index') }}');

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
            function toggleStatus(adminId) {
                if (confirm('Are you sure you want to toggle the status of this admin?')) {
                    fetch(`/admin/admins/${adminId}/active`, {
                            method: 'PUT',
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

            // Delete admin
            function deleteAdmin(adminId) {
                if (confirm('Are you sure you want to delete this admin? This action cannot be undone.')) {
                    fetch(`/admin/admins/${adminId}`, {
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
                            alert('Error deleting admin');
                        });
                }
            }
        </script>
        @include('admin.partials.status-modal')
    @endpush
@endsection

