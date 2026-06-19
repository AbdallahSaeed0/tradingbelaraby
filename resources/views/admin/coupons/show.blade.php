@extends('admin.layout')

@section('title', 'View Coupon')

@section('content')
    <div class="container-fluid py-4 admin-detail-page">
        @include('admin.partials.detail-page-header', [
            'title' => $coupon->name,
            'subtitle' => 'Coupon Details · ' . $coupon->code,
            'backUrl' => route('admin.coupons.index'),
            'backLabel' => 'Coupons',
            'primaryUrl' => route('admin.coupons.edit', $coupon),
            'primaryLabel' => 'Edit Coupon',
        ])

        <div class="row admin-detail-main-row">
            <div class="col-lg-8">
                <!-- Coupon Information -->
                <div class="card mb-4" id="detail-section-info">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Coupon Information</h5>
                    </div>
                    <div class="card-body admin-detail-grid">
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Coupon Code</strong>
                                <span class="admin-detail-value"><code class="bg-light px-3 py-2 rounded d-inline-block">{{ $coupon->code }}</code></span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Name</strong>
                                <span class="admin-detail-value">{{ $coupon->name }}</span>
                            </div>
                        </div>

                        @if ($coupon->description)
                            <div class="admin-detail-field">
                                <strong>Description</strong>
                                <span class="admin-detail-value">{{ $coupon->description }}</span>
                            </div>
                        @endif

                        <div class="row mb-0">
                            <div class="col-md-4 admin-detail-field">
                                <strong>Discount Type</strong>
                                <span class="admin-detail-value">
                                    @if ($coupon->discount_type === 'percentage')
                                        <span class="badge bg-info">Percentage</span>
                                    @else
                                        <span class="badge bg-success">Fixed Amount</span>
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-4 admin-detail-field">
                                <strong>Discount Value</strong>
                                <span class="admin-detail-value fs-5 text-primary">
                                    @if ($coupon->discount_type === 'percentage')
                                        {{ $coupon->discount_value }}%
                                    @else
                                        {{ $coupon->discount_value }} SAR
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-4 admin-detail-field">
                                <strong>Status</strong>
                                <span class="admin-detail-value">
                                    @if ($coupon->is_active && $coupon->isValid())
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($coupon->is_active)
                                        <span class="badge bg-warning text-dark">Scheduled</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Course Scope</strong>
                                <span class="admin-detail-value">
                                    @if ($coupon->scope === 'all_courses')
                                        <span class="badge bg-primary">All Courses</span>
                                    @else
                                        {{ $coupon->course->name ?? 'N/A' }}
                                    @endif
                                </span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>User Scope</strong>
                                <span class="admin-detail-value">
                                    @if ($coupon->user_scope === 'all_users')
                                        <span class="badge bg-primary">All Users</span>
                                    @else
                                        {{ $coupon->user->name ?? 'N/A' }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-4 admin-detail-field">
                                <strong>Start Date</strong>
                                <span class="admin-detail-value">{{ $coupon->start_date->format('Y-m-d') }}</span>
                            </div>
                            <div class="col-md-4 admin-detail-field">
                                <strong>End Date</strong>
                                <span class="admin-detail-value">{{ $coupon->end_date->format('Y-m-d') }}</span>
                            </div>
                            <div class="col-md-4 admin-detail-field">
                                <strong>Created</strong>
                                <span class="admin-detail-value">{{ $coupon->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage History -->
                <div class="card mb-4" id="detail-section-usage">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-history me-2"></i>Usage History ({{ $coupon->usages->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if ($coupon->usages->count() > 0)
                            <div class="table-responsive d-none d-lg-block admin-table-no-mobile-cards">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Order</th>
                                            <th>Used At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($coupon->usages as $usage)
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
                            <div class="admin-mobile-list d-lg-none">
                                @foreach ($coupon->usages as $usage)
                                    @include('admin.partials.mobile-data-card', [
                                        'heroUrl' => route('admin.orders.show', $usage->order_id),
                                        'iconClass' => 'fa-shopping-cart',
                                        'title' => $usage->user->name ?? 'Unknown user',
                                        'subtitle' => $usage->order->order_number ?? 'Order',
                                        'stats' => [
                                            ['icon' => 'fa-clock', 'text' => $usage->used_at->format('M d, Y H:i')],
                                        ],
                                        'actionsHtml' => '<a href="' . route('admin.orders.show', $usage->order_id) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>',
                                    ])
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No usage history yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 admin-detail-sidebar">
                <!-- Usage Statistics -->
                <div class="card mb-4" id="detail-section-stats">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Usage Statistics</h5>
                    </div>
                    <div class="card-body admin-detail-grid">
                        <div class="admin-detail-field">
                            <strong>Global Usage</strong>
                            <div class="progress mt-2">
                                @php
                                    $liveUsed = $coupon->liveUsedCount();
                                    $usagePercentage = $coupon->usage_limit ? ($liveUsed / $coupon->usage_limit) * 100 : 0;
                                @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ min($usagePercentage, 100) }}%">
                                    {{ $liveUsed }}
                                    @if ($coupon->usage_limit)
                                        / {{ $coupon->usage_limit }}
                                    @endif
                                </div>
                            </div>
                            @if (!$coupon->usage_limit)
                                <small class="text-muted">Unlimited</small>
                            @endif
                        </div>

                        <div class="admin-detail-field">
                            <strong>Per User Limit</strong>
                            <span class="admin-detail-value">{{ $coupon->per_user_limit }}</span>
                        </div>

                        <div class="admin-detail-field">
                            <strong>Remaining Uses</strong>
                            <span class="admin-detail-value fs-4 text-success">
                                @if ($coupon->usage_limit)
                                    {{ max(0, $coupon->usage_limit - $liveUsed) }}
                                @else
                                    ∞
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card admin-form-inline-actions" id="detail-section-actions">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-cog me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fa fa-edit me-2"></i>Edit Coupon
                        </a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this coupon?');"
                            data-settings-mobile-toolbar="skip">
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
