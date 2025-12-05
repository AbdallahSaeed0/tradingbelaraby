@extends('admin.layout')

@section('title', 'View Coupon')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">{{ $coupon->name }}</h1>
                        <p class="text-muted">Coupon Details</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Coupons
                        </a>
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary">
                            <i class="fa fa-edit me-2"></i>Edit Coupon
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Coupon Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Coupon Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Coupon Code:</strong>
                                <p><code class="bg-light px-3 py-2 rounded fs-5">{{ $coupon->code }}</code></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Name:</strong>
                                <p>{{ $coupon->name }}</p>
                            </div>
                        </div>

                        @if($coupon->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p>{{ $coupon->description }}</p>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong>Discount Type:</strong>
                                <p>
                                    @if($coupon->discount_type === 'percentage')
                                        <span class="badge bg-info">Percentage</span>
                                    @else
                                        <span class="badge bg-success">Fixed Amount</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Discount Value:</strong>
                                <p class="fs-4 text-primary">
                                    @if($coupon->discount_type === 'percentage')
                                        {{ $coupon->discount_value }}%
                                    @else
                                        {{ $coupon->discount_value }} SAR
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <p>
                                    @if($coupon->is_active && $coupon->isValid())
                                        <span class="badge bg-success">Active</span>
                                    @elseif($coupon->is_active)
                                        <span class="badge bg-warning">Scheduled</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Course Scope:</strong>
                                <p>
                                    @if($coupon->scope === 'all_courses')
                                        <span class="badge bg-primary">All Courses</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $coupon->course->name ?? 'N/A' }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>User Scope:</strong>
                                <p>
                                    @if($coupon->user_scope === 'all_users')
                                        <span class="badge bg-primary">All Users</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $coupon->user->name ?? 'N/A' }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <strong>Start Date:</strong>
                                <p>{{ $coupon->start_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>End Date:</strong>
                                <p>{{ $coupon->end_date->format('Y-m-d') }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Created:</strong>
                                <p>{{ $coupon->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage History -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-history me-2"></i>Usage History ({{ $coupon->usages->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($coupon->usages->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Order</th>
                                            <th>Used At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($coupon->usages as $usage)
                                            <tr>
                                                <td>
                                                    <strong>{{ $usage->user->name ?? 'N/A' }}</strong>
                                                    <br><small class="text-muted">{{ $usage->user->email ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $usage->order_id) }}" class="text-decoration-none">
                                                        {{ $usage->order->order_number ?? 'N/A' }}
                                                    </a>
                                                </td>
                                                <td>{{ $usage->used_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No usage history yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Usage Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Usage Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Global Usage:</strong>
                            <div class="progress mt-2">
                                @php
                                    $usagePercentage = $coupon->usage_limit ? ($coupon->used_count / $coupon->usage_limit) * 100 : 0;
                                @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ min($usagePercentage, 100) }}%">
                                    {{ $coupon->used_count }}
                                    @if($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </div>
                            </div>
                            @if(!$coupon->usage_limit)
                                <small class="text-muted">Unlimited</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <strong>Per User Limit:</strong>
                            <p>{{ $coupon->per_user_limit }}</p>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <strong>Remaining Uses:</strong>
                            <p class="fs-4 text-success">
                                @if($coupon->usage_limit)
                                    {{ max(0, $coupon->usage_limit - $coupon->used_count) }}
                                @else
                                    ∞
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-cog me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fa fa-edit me-2"></i>Edit Coupon
                        </a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fa fa-trash me-2"></i>Delete Coupon
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

