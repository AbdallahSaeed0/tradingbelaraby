@extends('admin.layout')

@section('title', 'Coupon Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Coupon Management</h1>
                        <p class="text-muted">Manage discount coupons</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Add New Coupon
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.coupons.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search coupons..."
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="is_active">
                                <option value="">All Status</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="discount_type">
                                <option value="">All Types</option>
                                <option value="percentage" {{ request('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="per_page">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fa fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Coupons Table -->
        <div class="card">
            <div class="card-body">
                @if($coupons->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Discount</th>
                                    <th>Scope</th>
                                    <th>Valid Period</th>
                                    <th>Usage</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupons as $coupon)
                                    <tr>
                                        <td>
                                            <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                                        </td>
                                        <td>
                                            <strong>{{ $coupon->name }}</strong>
                                            @if($coupon->description)
                                                <br><small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->discount_type === 'percentage')
                                                <span class="badge bg-info">{{ $coupon->discount_value }}%</span>
                                            @else
                                                <span class="badge bg-success">{{ $coupon->discount_value }} SAR</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($coupon->scope === 'all_courses')
                                                <span class="badge bg-primary">All Courses</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $coupon->course->name ?? 'N/A' }}</span>
                                            @endif
                                            <br>
                                            @if($coupon->user_scope === 'all_users')
                                                <small class="text-muted">All Users</small>
                                            @else
                                                <small class="text-muted">{{ $coupon->user->name ?? 'N/A' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                {{ $coupon->start_date->format('Y-m-d') }}<br>
                                                to {{ $coupon->end_date->format('Y-m-d') }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $coupon->used_count }}
                                                @if($coupon->usage_limit)
                                                    / {{ $coupon->usage_limit }}
                                                @else
                                                    / ∞
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            @if($coupon->is_active && $coupon->isValid())
                                                <span class="badge bg-success">Active</span>
                                            @elseif($coupon->is_active)
                                                <span class="badge bg-warning">Scheduled</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.coupons.show', $coupon) }}" 
                                                    class="btn btn-sm btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $coupons->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No coupons found.</p>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Your First Coupon
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

