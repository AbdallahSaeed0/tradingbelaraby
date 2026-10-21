@extends('admin.layout')

@section('title', 'Order ' . $order->order_number)

@section('content')
    @php
        $statusMeta = [
            'completed' => ['bg-success', 'fa-check-circle', 'Completed', 'text-success'],
            'pending' => ['bg-warning text-dark', 'fa-clock', 'Pending', 'text-warning'],
            'failed' => ['bg-danger', 'fa-times-circle', 'Failed', 'text-danger'],
            'cancelled' => ['bg-secondary', 'fa-ban', 'Cancelled', 'text-secondary'],
            'rejected' => ['bg-danger', 'fa-times-circle', 'Rejected', 'text-danger'],
        ];
        [$orderBadge, $orderIcon, $orderLabel, $orderText] = $statusMeta[$order->status]
            ?? ['bg-secondary', 'fa-circle', ucfirst($order->status), 'text-secondary'];

        $enrollmentBadge = fn ($status) => match ($status) {
            'active', 'completed' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'cancelled', 'rejected' => 'bg-danger',
            default => 'bg-secondary',
        };

        // One row per course; bundles expand into their courses under a bundle header
        $rows = collect();
        foreach ($order->orderItems as $item) {
            if ($item->course) {
                $rows->push(['course' => $item->course, 'bundle' => null, 'price' => $item->price, 'bundleStart' => false]);
            } elseif ($item->bundle) {
                foreach ($item->bundle->courses as $i => $course) {
                    $rows->push([
                        'course' => $course,
                        'bundle' => $item->bundle,
                        'price' => $item->price,
                        'bundleStart' => $i === 0,
                    ]);
                }
            }
        }
        $activeCount = $enrollments->whereIn('status', ['active', 'completed'])->count();
        $transactionRef = $order->transaction_reference;
    @endphp

    <div class="container-fluid py-4 order-detail-page">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div class="d-flex align-items-center gap-3 min-w-0">
                <div class="order-hero__icon"><i class="fa fa-receipt"></i></div>
                <div class="min-w-0">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <h1 class="h4 mb-0 text-break">Order {{ $order->order_number }}</h1>
                        <span class="badge {{ $orderBadge }} order-status-pill">
                            <i class="fa {{ $orderIcon }} me-1"></i>{{ $orderLabel }}
                        </span>
                    </div>
                    <div class="text-muted small mt-1">
                        <i class="fa fa-calendar-alt me-1"></i>Placed {{ $order->created_at->format('M d, Y · h:i A') }}
                        <span class="mx-1">·</span>{{ $order->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i>Back to Orders
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Top row: payment / student / action — equal height -->
        <div class="row g-4 mb-4">
            <!-- Payment -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-file-invoice-dollar me-2"></i>Payment</h5></div>
                    <div class="card-body">
                        <div class="order-amount">SAR {{ number_format($order->total, 2) }}</div>
                        <dl class="order-dl mb-0">
                            <dt>Method</dt>
                            <dd class="fw-semibold">{{ $order->payment_method_label }}</dd>
                            <dt>Reference</dt>
                            <dd>
                                @if ($transactionRef)
                                    <span class="order-ref">
                                        <code>{{ $transactionRef }}</code>
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-2 order-copy"
                                            data-copy="{{ $transactionRef }}" title="Copy reference">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </dd>
                            @if ($order->coupon)
                                <dt>Coupon</dt>
                                <dd><span class="badge bg-info">{{ $order->coupon->code }}</span></dd>
                            @endif
                            <dt>Updated</dt>
                            <dd>{{ $order->updated_at->format('M d, Y · h:i A') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Student -->
            <div class="col-md-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-user me-2"></i>Student</h5></div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="user-avatar">
                                <i class="fa fa-user"></i>
                                @if ($order->user->avatar)
                                    <img src="{{ asset('storage/' . $order->user->avatar) }}" alt="{{ $order->user->name }}"
                                        onerror="this.remove()">
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="fw-semibold text-break">{{ $order->user->name }}</div>
                                <div class="small text-muted">Student #{{ $order->user->id }}</div>
                            </div>
                        </div>
                        <ul class="list-unstyled order-contact mb-3">
                            <li>
                                <i class="fa fa-envelope"></i>
                                <a href="mailto:{{ $order->user->email }}" class="text-break">{{ $order->user->email }}</a>
                            </li>
                            @if ($order->user->phone)
                                <li>
                                    <i class="fa fa-phone"></i>
                                    <a href="tel:{{ $order->user->phone }}" dir="ltr">{{ $order->user->phone }}</a>
                                </li>
                            @endif
                        </ul>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="fa fa-eye me-1"></i>Profile
                            </a>
                            <a href="{{ route('admin.users.enrollments-report', $order->user) }}" class="btn btn-sm btn-outline-info flex-fill">
                                <i class="fa fa-chart-bar me-1"></i>Enrollments
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action (pending) or status summary -->
            <div class="col-md-12 col-xl-4">
                @if ($order->status === 'pending')
                    <div class="card h-100 order-action-card">
                        <div class="card-header"><h5 class="mb-0"><i class="fa fa-hourglass-half me-2 text-warning"></i>Awaiting Confirmation</h5></div>
                        <div class="card-body d-flex flex-column">
                            <p class="small text-muted mb-3">
                                @if ($order->payment_method === 'bank_transfer')
                                    Verify the bank transfer using the payment reference, then confirm to activate the enrollments.
                                @else
                                    Check this {{ $order->payment_method_label }} payment using the payment reference, then confirm to activate the enrollments.
                                @endif
                            </p>
                            <div class="mt-auto d-grid gap-2">
                                <button type="button" class="btn btn-success" id="btnConfirmOrder"
                                    data-action="{{ route('admin.orders.confirm', $order) }}">
                                    <i class="fa fa-check me-1"></i>Confirm & Activate Enrollments
                                </button>
                                <button type="button" class="btn btn-outline-warning" id="btnRejectOrder"
                                    data-action="{{ route('admin.orders.reject', $order) }}">
                                    <i class="fa fa-times me-1"></i>Reject Order
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card h-100">
                        <div class="card-header"><h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Order Status</h5></div>
                        <div class="card-body d-flex align-items-center gap-3">
                            <i class="fa {{ $orderIcon }} fa-3x {{ $orderText }}"></i>
                            <div>
                                <div class="fw-semibold fs-5">{{ $orderLabel }}</div>
                                <div class="small text-muted">
                                    {{ $activeCount }} of {{ $rows->count() }} {{ Str::plural('enrollment', $rows->count()) }} active
                                </div>
                                <div class="small text-muted">Last updated {{ $order->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items (invoice style) -->
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0"><i class="fa fa-shopping-bag me-2"></i>Order Items</h5>
                <span class="small text-muted">
                    {{ $rows->count() }} {{ Str::plural('course', $rows->count()) }}
                    <span class="mx-1">·</span>{{ $activeCount }} active
                </span>
            </div>
            <div class="card-body p-0">
                <div class="order-items-head d-none d-md-flex">
                    <span class="flex-grow-1">Course</span>
                    <span class="order-col-status">Enrollment</span>
                    <span class="order-col-price">Price</span>
                </div>
                @forelse ($rows as $row)
                    @php $enrollment = $enrollments->firstWhere('course_id', $row['course']->id); @endphp
                    @if ($row['bundleStart'])
                        <div class="order-bundle-head">
                            <span class="flex-grow-1">
                                <i class="fa fa-layer-group me-2"></i>Bundle: <strong>{{ $row['bundle']->name }}</strong>
                                <span class="text-muted">({{ $row['bundle']->courses->count() }} courses)</span>
                            </span>
                            <span class="order-col-price fw-semibold">SAR {{ number_format($row['price'], 2) }}</span>
                        </div>
                    @endif
                    <div class="order-item {{ $row['bundle'] ? 'order-item--in-bundle' : '' }}">
                        <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                            <img src="{{ $row['course']->image_url }}" alt="" class="order-item__thumb"
                                onerror="this.onerror=null; this.src='{{ asset('images/default-course.jpg') }}'">
                            <div class="min-w-0">
                                <a href="{{ route('admin.courses.show', $row['course']) }}" class="order-item__title">{{ $row['course']->name }}</a>
                                <div class="order-item__meta">
                                    @if ($enrollment)
                                        <i class="fa fa-calendar-check me-1"></i>Enrolled {{ $enrollment->created_at->format('M d, Y') }}
                                    @else
                                        <i class="fa fa-exclamation-circle me-1"></i>No enrollment record
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="order-col-status">
                            @if ($enrollment)
                                <span class="badge {{ $enrollmentBadge($enrollment->status) }}">{{ ucfirst($enrollment->status) }}</span>
                            @else
                                <span class="badge bg-secondary">None</span>
                            @endif
                        </div>
                        <div class="order-col-price fw-semibold">
                            {{ $row['bundle'] ? '—' : 'SAR ' . number_format($row['price'], 2) }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4 mb-0">This order has no items.</p>
                @endforelse

                <div class="order-totals">
                    <div><span class="text-muted">Subtotal</span><span>SAR {{ number_format($order->subtotal, 2) }}</span></div>
                    @if ($order->discount_amount > 0)
                        <div class="text-success">
                            <span>Discount @if ($order->coupon)<small>({{ $order->coupon->code }})</small>@endif</span>
                            <span>− SAR {{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="order-totals__grand"><span>Total</span><span>SAR {{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Danger zone (slim) -->
        <div class="card order-danger-card">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="small">
                    <span class="fw-semibold text-danger"><i class="fa fa-exclamation-triangle me-1"></i>Danger Zone</span>
                    <span class="text-muted ms-1">Permanently delete this order and remove all linked enrollments for this student.</span>
                    @if ($order->status === 'completed')
                        <strong class="text-danger d-block mt-1">This order is completed — the student will lose course access.</strong>
                    @endif
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm text-nowrap" id="btnDeleteOrder"
                    data-action="{{ route('admin.orders.destroy', $order) }}"
                    data-order="{{ $order->order_number }}"
                    data-completed="{{ $order->status === 'completed' ? '1' : '0' }}">
                    <i class="fa fa-trash me-1"></i>Delete Order & Enrollments
                </button>
            </div>
        </div>
    </div>

    @include('admin.partials.confirm-modal')
@endsection

@push('styles')
    <style>
        .order-detail-page .order-hero__icon {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(13, 110, 253, 0.15);
            color: #0d6efd;
            font-size: 1.3rem;
        }

        .order-detail-page .order-status-pill {
            font-size: 0.8rem;
            padding: 0.4em 0.75em;
        }

        .order-detail-page .order-amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 1rem;
        }

        .order-detail-page .order-dl {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 0.6rem 1rem;
            align-items: center;
        }

        .order-detail-page .order-dl dt {
            font-weight: 400;
            color: var(--admin-text-muted, #6c757d);
            margin: 0;
        }

        .order-detail-page .order-dl dd {
            margin: 0;
            text-align: end;
            min-width: 0;
        }

        .order-detail-page .order-ref {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
        }

        .order-detail-page .order-ref code {
            word-break: break-all;
        }

        .order-detail-page .order-contact li {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.4rem;
            min-width: 0;
        }

        .order-detail-page .order-contact i {
            width: 16px;
            color: var(--admin-text-muted, #6c757d);
        }

        .order-detail-page .order-contact a {
            color: inherit;
            text-decoration: none;
        }

        .order-detail-page .order-contact a:hover {
            text-decoration: underline;
        }

        /* Items table-like rows */
        .order-detail-page .order-col-status {
            width: 130px;
            flex-shrink: 0;
            text-align: center;
        }

        .order-detail-page .order-col-price {
            width: 130px;
            flex-shrink: 0;
            text-align: end;
            white-space: nowrap;
        }

        .order-detail-page .order-items-head {
            align-items: center;
            gap: 1rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--admin-text-muted, #6c757d);
            border-bottom: 1px solid var(--admin-border, #dee2e6);
        }

        .order-detail-page .order-bundle-head {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.25rem;
            background: var(--admin-table-stripe, rgba(0, 0, 0, 0.03));
            border-bottom: 1px solid var(--admin-border, #dee2e6);
        }

        .order-detail-page .order-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--admin-border, #dee2e6);
        }

        .order-detail-page .order-item--in-bundle {
            padding-inline-start: 2.25rem;
        }

        .order-detail-page .order-item__thumb {
            width: 80px;
            height: 52px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .order-detail-page .order-item__title {
            font-weight: 600;
            line-height: 1.4;
            color: inherit;
            text-decoration: none;
        }

        .order-detail-page .order-item__title:hover {
            text-decoration: underline;
        }

        .order-detail-page .order-item__meta {
            font-size: 0.8rem;
            color: var(--admin-text-muted, #6c757d);
            margin-top: 0.25rem;
        }

        .order-detail-page .order-totals {
            margin-inline-start: auto;
            max-width: 360px;
            padding: 1rem 1.25rem 1.25rem;
            display: grid;
            gap: 0.5rem;
        }

        .order-detail-page .order-totals > div {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .order-detail-page .order-totals__grand {
            font-size: 1.2rem;
            font-weight: 700;
            padding-top: 0.6rem;
            border-top: 1px dashed var(--admin-border, #dee2e6);
        }

        .order-detail-page .order-totals__grand span:last-child {
            color: #0d6efd;
        }

        .order-detail-page .order-action-card {
            border: 1px solid rgba(255, 193, 7, 0.55);
        }

        .order-detail-page .order-danger-card {
            border: 1px solid rgba(220, 53, 69, 0.45);
        }

        @media (max-width: 767.98px) {
            .order-detail-page .order-item {
                flex-wrap: wrap;
            }

            .order-detail-page .order-item .order-col-status {
                width: auto;
                text-align: start;
            }

            .order-detail-page .order-item .order-col-price {
                width: auto;
                margin-inline-start: auto;
            }

            .order-detail-page .order-totals {
                max-width: none;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('.order-copy').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!navigator.clipboard) {
                    return;
                }
                navigator.clipboard.writeText(this.dataset.copy).then(() => {
                    const icon = this.querySelector('i');
                    icon.className = 'fa fa-check text-success';
                    setTimeout(() => icon.className = 'fa fa-copy', 1500);
                });
            });
        });

        const confirmOrderBtn = document.getElementById('btnConfirmOrder');
        if (confirmOrderBtn) {
            confirmOrderBtn.addEventListener('click', function() {
                showActionConfirmModal({
                    title: 'Confirm Payment',
                    message: 'Confirm this payment and activate enrollments for this order?',
                    action: this.dataset.action,
                    method: 'POST',
                    headerClass: 'bg-success text-white',
                    btnClass: 'btn-success',
                    btnHtml: '<i class="fa fa-check me-1"></i>Confirm & Activate'
                });
            });
        }

        const rejectOrderBtn = document.getElementById('btnRejectOrder');
        if (rejectOrderBtn) {
            rejectOrderBtn.addEventListener('click', function() {
                showActionConfirmModal({
                    title: 'Reject Order',
                    message: 'Reject this order and cancel pending enrollments?',
                    action: this.dataset.action,
                    method: 'POST',
                    headerClass: 'bg-warning',
                    btnClass: 'btn-warning',
                    btnHtml: '<i class="fa fa-times me-1"></i>Reject Order'
                });
            });
        }

        const deleteOrderBtn = document.getElementById('btnDeleteOrder');
        if (deleteOrderBtn) {
            deleteOrderBtn.addEventListener('click', function() {
                const isCompleted = this.dataset.completed === '1';
                showActionConfirmModal({
                    title: 'Delete Order',
                    message: 'Delete order ' + this.dataset.order + ' and all linked enrollments? This action cannot be undone.',
                    warning: isCompleted ? 'This order is completed — the student will lose course access.' : null,
                    action: this.dataset.action,
                    method: 'DELETE',
                    headerClass: 'bg-danger text-white',
                    btnClass: 'btn-danger',
                    btnHtml: '<i class="fa fa-trash me-1"></i>Delete Order'
                });
            });
        }
    </script>
@endpush
