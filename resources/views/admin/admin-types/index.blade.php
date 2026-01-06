@extends('admin.layout')

@section('title', 'Admin Types')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Admin Types</h1>
                        <p class="text-muted">Manage administrator types and their permissions</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.admin-types.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Add New Type
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
                            <i class="fa fa-user-tag text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">
                                {{ request()->hasAny(['search', 'status', 'permission']) ? 'Filtered' : 'Total' }} Types
                            </h6>
                            <h4 class="fw-bold mb-0">{{ $adminTypes->total() }}</h4>
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
                            <h6 class="text-muted mb-0">Active Types</h6>
                            <h4 class="fw-bold mb-0">{{ $adminTypes->where('is_active', true)->count() }}</h4>
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
                            <h6 class="text-muted mb-0">Total Admins</h6>
                            <h4 class="fw-bold mb-0">{{ $adminTypes->sum('admins_count') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-pause-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Inactive Types</h6>
                            <h4 class="fw-bold mb-0">{{ $adminTypes->where('is_active', false)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div
            class="card shadow-sm mb-4 {{ request()->filled('search') || request()->filled('status') || request()->filled('permission') ? 'border-primary' : '' }}">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fa fa-filter me-2"></i>Filters
                    @if (request()->filled('search') || request()->filled('status') || request()->filled('permission'))
                        <span
                            class="badge bg-primary ms-2">{{ collect([request('search'), request('status'), request('permission')])->filter()->count() }}
                            active</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.admin-types.index') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="search" class="form-label">
                                Search
                                @if (request()->filled('search'))
                                    <span class="text-primary">({{ request('search') }})</span>
                                @endif
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" name="search"
                                    value="{{ request('search') }}" placeholder="Search by name, description...">
                                <span class="input-group-text search-indicator d-none">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">
                                Status
                                @if (request()->filled('status'))
                                    <span class="text-primary">({{ ucfirst(request('status')) }})</span>
                                @endif
                            </label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="permission" class="form-label">
                                Permission
                                @if (request()->filled('permission'))
                                    <span
                                        class="text-primary">({{ $availablePermissions[request('permission')] ?? request('permission') }})</span>
                                @endif
                            </label>
                            <select class="form-select" id="permissionFilter" name="permission">
                                <option value="">All Permissions</option>
                                @foreach ($availablePermissions as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ request('permission') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                    <i class="fa fa-times me-2"></i>Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admin Types Table -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-list me-2"></i>Admin Types List</h5>
                <div class="text-muted d-flex align-items-center">
                    <small id="results-count">{{ $adminTypes->total() }} result{{ $adminTypes->total() !== 1 ? 's' : '' }}
                        found</small>
                    <div id="loading-indicator" class="ms-2 d-none">
                        <i class="fa fa-spinner fa-spin text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($adminTypes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Type Name</th>
                                    <th>Description</th>
                                    <th>Permissions</th>
                                    <th>Admins Count</th>
                                    <th>Status</th>
                                    <th>Sort Order</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adminTypes as $type)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <div class="avatar-title bg-light rounded-circle">
                                                        <i class="fa fa-user-tag text-primary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        {{ $type->display_name }}
                                                        @if ($type->isAdminType())
                                                            <span class="badge bg-warning text-dark ms-2">System</span>
                                                        @endif
                                                    </h6>
                                                    <small class="text-muted">{{ $type->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($type->description)
                                                <span class="text-muted">{{ Str::limit($type->description, 50) }}</span>
                                            @else
                                                <span class="text-muted">No description</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($type->isAdminType())
                                                <span class="badge bg-success">All Permissions</span>
                                            @elseif ($type->permissions && count($type->permissions) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach (array_slice($type->permissions, 0, 3) as $permission)
                                                        <span class="badge bg-primary">{{ $permission }}</span>
                                                    @endforeach
                                                    @if (count($type->permissions) > 3)
                                                        <span
                                                            class="badge bg-secondary">+{{ count($type->permissions) - 3 }}
                                                            more</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No permissions</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $type->admins_count ?? 0 }} admins</span>
                                        </td>
                                        <td>
                                            @if ($type->isAdminType())
                                                <span class="badge bg-success">Active</span>
                                                <small class="d-block text-muted">System Type</small>
                                            @else
                                                <span
                                                    class="badge {{ $type->is_active ? 'bg-success' : 'bg-danger' }} status-badge"
                                                    style="cursor: pointer;"
                                                    onclick="showStatusModal({{ $type->id }}, '{{ $type->is_active ? 'active' : 'inactive' }}', [
                                                        { value: 'active', label: 'Active' },
                                                        { value: 'inactive', label: 'Inactive' }
                                                    ], '{{ route('admin.admin-types.update_status', $type->id) }}')">
                                                    {{ $type->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $type->sort_order }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $type->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.admin-types.show', $type) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if ($type->isAdminType())
                                                    <button class="btn btn-sm btn-outline-secondary" disabled
                                                        title="Admin type cannot be edited">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" disabled
                                                        title="Admin type cannot be deleted">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ route('admin.admin-types.edit', $type) }}"
                                                        class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @if ($type->admins_count == 0)
                                                        <form action="{{ route('admin.admin-types.destroy', $type) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this admin type?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-danger" disabled
                                                            title="Cannot delete - has associated admins">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-user-tag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Admin Types Found</h5>
                        <p class="text-muted">Get started by creating your first admin type.</p>
                        <a href="{{ route('admin.admin-types.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create First Type
                        </a>
                    </div>
                @endif

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
                        @if ($adminTypes->hasPages())
                            <div class="d-flex justify-content-end">
                                {{ $adminTypes->links() }}
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
        // Change per page function
        function changePerPage(value) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {

            // Toggle status functionality
            function attachToggleListeners() {
                const statusToggles = document.querySelectorAll('.status-toggle');
                statusToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                    const adminTypeId = this.dataset.id;
                    const isCurrentlyActive = this.classList.contains('active');

                    // Show loading state
                    this.classList.add('loading');

                    fetch(`/admin/admin-types/${adminTypeId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the toggle state
                                if (data.is_active) {
                                    this.classList.remove('inactive');
                                    this.classList.add('active');
                                    this.querySelector('.status-text').textContent = 'Active';
                                } else {
                                    this.classList.remove('active');
                                    this.classList.add('inactive');
                                    this.querySelector('.status-text').textContent = 'Inactive';
                                }

                                // Show success message
                                showToast('Success', data.message, 'success');
                            } else {
                                showToast('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            showToast('Error', 'An error occurred while updating the status.',
                                'error');
                            console.error('Error:', error);
                        })
                        .finally(() => {
                            // Remove loading state
                            this.classList.remove('loading');
                        });
                    });
                });
            }
            attachToggleListeners();
        });

        function showToast(title, message, type) {
            // Create toast element
            const toast = document.createElement('div');
            toast.className =
                `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}:</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

            // Add to toast container
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }

            toastContainer.appendChild(toast);

            // Show toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove toast after it's hidden
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }
    </script>
    @include('admin.partials.status-modal')
@endpush

