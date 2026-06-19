@extends('admin.layout')

@section('title', 'Admin Type Details')

@section('content')
    <div class="container-fluid py-4 admin-detail-page">
        @include('admin.partials.detail-page-header', [
            'title' => $adminType->display_name,
            'subtitle' => 'Admin Type · ' . $adminType->slug,
            'backUrl' => route('admin.admin-types.index'),
            'backLabel' => 'Admin Types',
            'primaryUrl' => $adminType->isAdminType() ? null : route('admin.admin-types.edit', $adminType),
            'primaryLabel' => 'Edit Type',
        ])

        <div class="row admin-detail-main-row">
            <!-- Admin Type Information -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4" id="detail-section-info">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Type Information</h5>
                    </div>
                    <div class="card-body admin-detail-grid">
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Type Name</strong>
                                <span class="admin-detail-value">{{ $adminType->display_name }}</span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Slug</strong>
                                <span class="admin-detail-value"><code>{{ $adminType->slug }}</code></span>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Status</strong>
                                <span class="admin-detail-value">
                                    @if ($adminType->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Sort Order</strong>
                                <span class="admin-detail-value">{{ $adminType->sort_order }}</span>
                            </div>
                        </div>

                        @if ($adminType->description)
                            <div class="admin-detail-field">
                                <strong>Description</strong>
                                <span class="admin-detail-value">{{ $adminType->description }}</span>
                            </div>
                        @endif

                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Created</strong>
                                <span class="admin-detail-value">{{ $adminType->created_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Last Updated</strong>
                                <span class="admin-detail-value">{{ $adminType->updated_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-shield-alt me-2"></i>Permissions</h5>
                    </div>
                    <div class="card-body">
                        @if ($adminType->isAdminType())
                            <div class="text-center py-4">
                                <i class="fa fa-shield-alt fa-3x text-success mb-3"></i>
                                <h6 class="text-success">System Admin Type</h6>
                                <p class="text-muted">This admin type has <strong>ALL PERMISSIONS</strong> by default and
                                    cannot be modified.</p>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i>
                                    <strong>Note:</strong> The admin type is a special system type that automatically grants
                                    all permissions to administrators assigned to it.
                                </div>
                            </div>
                        @elseif ($adminType->permissions && count($adminType->permissions) > 0)
                            <div class="row">
                                <!-- Admin Management -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary"><i class="fa fa-user-shield me-2"></i>Admin Management</h6>
                                    <ul class="list-unstyled">
                                        @foreach (['manage_admins', 'manage_users'] as $permission)
                                            @if (in_array($permission, $adminType->permissions))
                                                <li><i
                                                        class="fa fa-check text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $permission)) }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Course Management -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary"><i class="fa fa-graduation-cap me-2"></i>Course Management</h6>
                                    <ul class="list-unstyled">
                                        @foreach (['manage_courses', 'manage_own_courses', 'manage_categories', 'manage_enrollments'] as $permission)
                                            @if (in_array($permission, $adminType->permissions))
                                                <li><i
                                                        class="fa fa-check text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $permission)) }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Content Management -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary"><i class="fa fa-edit me-2"></i>Content Management</h6>
                                    <ul class="list-unstyled">
                                        @foreach (['manage_quizzes', 'manage_own_quizzes', 'manage_homework', 'manage_own_homework', 'manage_live_classes', 'manage_own_live_classes', 'manage_blogs'] as $permission)
                                            @if (in_array($permission, $adminType->permissions))
                                                <li><i
                                                        class="fa fa-check text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $permission)) }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Support & Analytics -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary"><i class="fa fa-headset me-2"></i>Support & Analytics</h6>
                                    <ul class="list-unstyled">
                                        @foreach (['manage_questions_answers', 'manage_own_questions_answers', 'view_analytics', 'view_own_analytics'] as $permission)
                                            @if (in_array($permission, $adminType->permissions))
                                                <li><i
                                                        class="fa fa-check text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $permission)) }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- System Management -->
                                <div class="col-md-6 mb-3">
                                    <h6 class="text-primary"><i class="fa fa-cog me-2"></i>System Management</h6>
                                    <ul class="list-unstyled">
                                        @foreach (['manage_translations', 'manage_languages', 'export_data', 'import_data'] as $permission)
                                            @if (in_array($permission, $adminType->permissions))
                                                <li><i
                                                        class="fa fa-check text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $permission)) }}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-shield-alt fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No Permissions Assigned</h6>
                                <p class="text-muted">This admin type has no specific permissions assigned.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Stats Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Total Admins</span>
                            <span class="badge bg-primary">{{ $adminType->admins->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Active Admins</span>
                            <span
                                class="badge bg-success">{{ $adminType->admins->where('is_active', true)->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Inactive Admins</span>
                            <span
                                class="badge bg-secondary">{{ $adminType->admins->where('is_active', false)->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fa fa-bolt me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if ($adminType->isAdminType())
                                <button class="btn btn-primary btn-sm" disabled title="Admin type cannot be edited">
                                    <i class="fa fa-edit me-2"></i>Edit Type
                                </button>
                                <button class="btn btn-danger btn-sm" disabled title="Admin type cannot be deleted">
                                    <i class="fa fa-trash me-2"></i>Delete Type
                                </button>
                            @else
                                <a href="{{ route('admin.admin-types.edit', $adminType) }}"
                                    class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit me-2"></i>Edit Type
                                </a>
                                @if ($adminType->admins->count() == 0)
                                    <form action="{{ route('admin.admin-types.destroy', $adminType) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this admin type?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                            <i class="fa fa-trash me-2"></i>Delete Type
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-danger btn-sm" disabled
                                        title="Cannot delete - has associated admins">
                                        <i class="fa fa-trash me-2"></i>Delete Type
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Associated Admins -->
        @if ($adminType->admins->count() > 0)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-users me-2"></i>Associated Administrators</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adminType->admins as $admin)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($admin->avatar)
                                                    <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Avatar"
                                                        class="rounded-circle me-2" width="32" height="32">
                                                @else
                                                    <div
                                                        class="bg-light rounded-circle me-2 d-flex align-items-center justify-content-center w-32 h-32">
                                                        <i class="fa fa-user text-muted"></i>
                                                    </div>
                                                @endif
                                                <span>{{ $admin->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if ($admin->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.admins.show', $admin) }}"
                                                    class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.admins.edit', $admin) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
