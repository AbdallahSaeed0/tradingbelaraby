@extends('admin.layout')

@section('title', 'Users')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">User Management</h1>
                        <p class="text-muted">Manage system users and their permissions</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Add New User
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
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Users</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_users'] }}</h4>
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
                            <h6 class="text-muted mb-0">Active Users</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['active_users'] }}</h4>
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
                            <h6 class="text-muted mb-0">New This Month</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['new_users_month'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-soft">
                            <i class="fa fa-user-graduate text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Enrolled Students</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['enrolled_students'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search users..."
                                    id="searchInput" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="role" id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student
                                </option>
                                <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>
                                    Instructor</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort" id="sortFilter">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email A-Z
                                </option>
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


        <!-- Users Table -->
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
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Verified</th>
                                <th>Enrollments</th>
                                <th>Joined</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox"
                                                value="{{ $user->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="User"
                                                    class="user-avatar me-3">
                                            @else
                                                <div
                                                    class="user-avatar me-3 bg-primary text-white d-flex align-items-center justify-content-center">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">ID: {{ $user->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($user->role ?? 'student') }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} status-badge"
                                            style="cursor: pointer;"
                                            onclick="showStatusModal({{ $user->id }}, '{{ $user->is_active ? 'active' : 'inactive' }}', [
                                                { value: 'active', label: 'Active' },
                                                { value: 'inactive', label: 'Inactive' }
                                            ], '{{ route('admin.users.update_status', $user->id) }}')">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">
                                                <i class="fa fa-check-circle me-1"></i>Verified
                                            </span>
                                            <small class="text-muted d-block">{{ $user->email_verified_at->format('M d, Y') }}</small>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fa fa-times-circle me-1"></i>Not Verified
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $user->enrollments_count ?? 0 }}</span>
                                        <small class="text-muted d-block">courses</small>
                                    </td>
                                    <td>
                                        <span>{{ $user->created_at->format('M d, Y') }}</span>
                                        <small class="text-muted d-block">{{ $user->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.users.show', $user) }}"
                                                class="btn btn-outline-primary" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                class="btn btn-outline-secondary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($user->email_verified_at)
                                                        <li>
                                                            <button type="button" class="dropdown-item" onclick="unverifyUser({{ $user->id }})">
                                                                <i class="fa fa-times-circle me-2"></i>Unverify Email
                                                            </button>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <button type="button" class="dropdown-item" onclick="verifyUser({{ $user->id }})">
                                                                <i class="fa fa-check-circle me-2"></i>Verify Email
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item" onclick="resendVerification({{ $user->id }})">
                                                                <i class="fa fa-envelope me-2"></i>Resend Verification Email
                                                            </button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.users.toggle-active', $user) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i
                                                                    class="fa fa-toggle-on me-2"></i>{{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.users.destroy', $user) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Are you sure you want to delete this user?')">
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
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-users fa-3x mb-3"></i>
                                            <h5>No users found</h5>
                                            <p>No users match your current filters.</p>
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
                            <div id="bulkActions" style="display: none;">
                                <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn">
                                    <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete Selected', 'admin') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="clearSelection">
                                    <i class="fas fa-times me-1"></i>{{ custom_trans('Clear Selection', 'admin') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end align-items-center">
                            @if ($users->hasPages())
                                <nav aria-label="Users pagination">
                                    <ul class="pagination pagination-sm justify-content-end mb-0">
                                        {{-- Previous Page Link --}}
                                        @if ($users->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fa fa-chevron-left"></i>
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $users->previousPageUrl() }}"
                                                    aria-label="Previous">
                                                    <i class="fa fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        {{-- First Page --}}
                                        @if ($users->currentPage() > 3)
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $users->url(1) }}">1</a>
                                            </li>
                                            @if ($users->currentPage() > 4)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                                            @if ($page == $users->currentPage())
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
                                        @if ($users->currentPage() < $users->lastPage() - 2)
                                            @if ($users->currentPage() < $users->lastPage() - 3)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $users->url($users->lastPage()) }}">{{ $users->lastPage() }}</a>
                                            </li>
                                        @endif

                                        {{-- Next Page Link --}}
                                        @if ($users->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $users->nextPageUrl() }}" aria-label="Next">
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
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Change per page function
        function changePerPage(value) {
            // Use AJAX to update
            const formData = new FormData(document.getElementById('filterForm'));
            formData.set('per_page', value);
            const params = new URLSearchParams(formData);
            performAjaxSearch();
        }

        document.addEventListener('DOMContentLoaded', function() {
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

                fetch(`{{ route('admin.users.index') }}?${params.toString()}`, {
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
                        const newUrl = `{{ route('admin.users.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);

                        // Re-attach event listeners
                        setupCheckboxes();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
                        }
                    });
            }

            // Setup checkboxes function
            function setupCheckboxes() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const rowCheckboxes = document.querySelectorAll('.row-checkbox');

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        rowCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateBulkActions();
                    });
                }

                rowCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                        if (selectAllCheckbox) {
                            selectAllCheckbox.checked = checkedCount === rowCheckboxes.length;
                            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
                        }
                        updateBulkActions();
                    });
                });
                updateBulkActions();
            }

            // Prevent form submission - use AJAX instead
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performAjaxSearch();
            });

            // Auto-trigger filters with AJAX
            document.getElementById('statusFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('roleFilter').addEventListener('change', function() {
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
                    document.getElementById('statusFilter').value = '';
                    document.getElementById('roleFilter').value = '';
                    document.getElementById('sortFilter').value = 'latest';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.users.index') }}');

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

            // Setup checkboxes (called initially and after AJAX updates)
            function setupCheckboxes() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const rowCheckboxes = document.querySelectorAll('.row-checkbox');

                if (selectAllCheckbox) {
                    // Remove old listener and add new one
                    const newSelectAll = selectAllCheckbox.cloneNode(true);
                    selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);

                    newSelectAll.addEventListener('change', function() {
                        rowCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateBulkActions();
                    });
                }

                rowCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const selectAll = document.getElementById('selectAll');
                        const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                        if (selectAll) {
                            selectAll.checked = checkedCount === rowCheckboxes.length;
                            selectAll.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
                        }
                        updateBulkActions();
                    });
                });
                updateBulkActions();
            }
            setupCheckboxes();

            // Update bulk actions visibility
            function updateBulkActions() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                const bulkActions = document.getElementById('bulkActions');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                const selectedCountSpan = document.getElementById('selectedCount');

                if (checkedCount > 0) {
                    bulkActions.style.display = 'block';
                    bulkDeleteBtn.disabled = false;
                    selectedCountSpan.textContent = checkedCount;
                } else {
                    bulkActions.style.display = 'none';
                    bulkDeleteBtn.disabled = true;
                }
            }

            // Clear selection functionality
            const clearSelectionBtn = document.getElementById('clearSelection');
            if (clearSelectionBtn) {
                clearSelectionBtn.addEventListener('click', function() {
                    document.querySelectorAll('.row-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    document.getElementById('selectAll').checked = false;
                    document.getElementById('selectAll').indeterminate = false;
                    updateBulkActions();
                });
            }

            // Bulk delete functionality
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedIds.length === 0) {
                        alert('{{ custom_trans('Please select users to delete.', 'admin') }}');
                        return;
                    }

                    if (confirm(
                            '{{ custom_trans('Are you sure you want to delete the selected users? This action cannot be undone.', 'admin') }}'
                        )) {
                        // Create form and submit
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('admin.users.bulk-delete') }}';

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
                            input.name = 'user_ids[]';
                            input.value = id;
                            form.appendChild(input);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            // Add loading state to pagination links
            document.querySelectorAll('.pagination a').forEach(link => {
                link.addEventListener('click', function() {
                    // Add loading spinner to the clicked link
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                    this.style.pointerEvents = 'none';

                    // Reset after a short delay (in case of slow navigation)
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.style.pointerEvents = 'auto';
                    }, 2000);
                });
            });

            // Add keyboard navigation for pagination
            document.addEventListener('keydown', function(e) {
                if (e.altKey) {
                    switch (e.key) {
                        case 'ArrowLeft':
                            const prevLink = document.querySelector(
                                '.pagination .page-item:not(.disabled) a[aria-label="Previous"]');
                            if (prevLink) {
                                e.preventDefault();
                                window.location.href = prevLink.href;
                            }
                            break;
                        case 'ArrowRight':
                            const nextLink = document.querySelector(
                                '.pagination .page-item:not(.disabled) a[aria-label="Next"]');
                            if (nextLink) {
                                e.preventDefault();
                                window.location.href = nextLink.href;
                            }
                            break;
                    }
                }
            });
        });

        // Verify user email
        function verifyUser(userId) {
            if (!confirm('Are you sure you want to verify this user\'s email?')) {
                return;
            }

            fetch(`{{ url('admin/users') }}/${userId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'User email verified successfully');
                    // Reload the page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message || 'Failed to verify user email');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while verifying the user email');
            });
        }

        // Unverify user email
        function unverifyUser(userId) {
            if (!confirm('Are you sure you want to remove email verification from this user?')) {
                return;
            }

            fetch(`{{ url('admin/users') }}/${userId}/unverify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'User email verification removed successfully');
                    // Reload the page to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message || 'Failed to remove email verification');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while removing email verification');
            });
        }

        // Resend verification email
        function resendVerification(userId) {
            if (!confirm('Are you sure you want to resend the verification email to this user?')) {
                return;
            }

            fetch(`{{ url('admin/users') }}/${userId}/resend-verification`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Verification email sent successfully');
                } else {
                    toastr.error(data.message || 'Failed to send verification email');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while sending the verification email');
            });
        }
    </script>
    @include('admin.partials.status-modal')
@endpush
