@extends('admin.layout')

@section('title', 'Users')

@push('styles')
    <style>
        .user-card {
            transition: transform 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
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
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="role" id="roleFilter">
                                <option value="">All Roles</option>
                                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                                <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="sort" id="sortFilter">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>Email A-Z</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Users List</h5>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries</span>
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
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
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
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $user->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="User" class="user-avatar me-3">
                                            @else
                                                <div class="user-avatar me-3 bg-primary text-white d-flex align-items-center justify-content-center">
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
                                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} status-badge">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
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
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <div class="dropdown d-inline">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="fa fa-toggle-on me-2"></i>{{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this user?')">
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
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                    </div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change
            document.getElementById('statusFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('roleFilter').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });

            document.getElementById('sortFilter').addEventListener('change', function() {
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

            selectAllCheckbox.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                    selectAllCheckbox.checked = checkedCount === rowCheckboxes.length;
                    selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
                });
            });
        });
    </script>
@endpush
