@extends('admin.layout')

@section('title', 'Order ' . $order->order_number)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Order {{ $order->order_number }}</h1>
                <p class="text-muted mb-0">Placed {{ $order->created_at->format('M d, Y H:i') }}</p>
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

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Order Items</h5></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Course / Bundle</th>
                                    <th>Price</th>
                                    <th>Enrollment Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                    @if ($item->course)
                                        @php $enrollment = $enrollments->firstWhere('course_id', $item->course_id); @endphp
                                        <tr>
                                            <td>{{ $item->course->name }}</td>
                                            <td>SAR {{ number_format($item->price, 2) }}</td>
                                            <td>
                                                @if ($enrollment)
                                                    <span class="badge {{ $enrollment->status === 'active' ? 'bg-success' : ($enrollment->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                        {{ ucfirst($enrollment->status) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No enrollment</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif ($item->bundle)
                                        @foreach ($item->bundle->courses as $course)
                                            @php $enrollment = $enrollments->firstWhere('course_id', $course->id); @endphp
                                            <tr>
                                                <td>{{ $course->name }} <small class="text-muted">(Bundle: {{ $item->bundle->name }})</small></td>
                                                <td>—</td>
                                                <td>
                                                    @if ($enrollment)
                                                        <span class="badge {{ $enrollment->status === 'active' ? 'bg-success' : ($enrollment->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                            {{ ucfirst($enrollment->status) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">No enrollment</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Linked Enrollments</h5></div>
                    <div class="card-body">
                        @forelse($enrollments as $enrollment)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div>
                                    <div class="fw-semibold">{{ $enrollment->course->name ?? 'Course' }}</div>
                                    <small class="text-muted">
                                        Payment: {{ ucfirst(str_replace('_', ' ', $enrollment->payment_method ?? 'N/A')) }}
                                        @if ($enrollment->transaction_id)
                                            · Ref: <code>{{ $enrollment->transaction_id }}</code>
                                        @endif
                                    </small>
                                </div>
                                <span class="badge {{ $enrollment->status === 'active' ? 'bg-success' : ($enrollment->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No enrollments linked to this order.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Payment Details</h5></div>
                    <div class="card-body">
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted">Status</span>
                            <span class="badge {{ $order->status === 'completed' ? 'bg-success' : ($order->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted">Method</span>
                            <span class="fw-semibold">{{ $order->payment_method_label }}</span>
                        </div>
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted">Transaction Ref</span>
                            <code>{{ $order->transaction_reference ?? '—' }}</code>
                        </div>
                        <hr>
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="text-muted">Subtotal</span>
                            <span>SAR {{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="mb-2 d-flex justify-content-between text-success">
                                <span>Discount</span>
                                <span>- SAR {{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total</span>
                            <span class="text-primary">SAR {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Student</h5></div>
                    <div class="card-body">
                        <div class="fw-semibold">{{ $order->user->name }}</div>
                        <div class="text-muted">{{ $order->user->email }}</div>
                        @if ($order->user->phone)
                            <div class="text-muted">{{ $order->user->phone }}</div>
                        @endif
                        <a href="{{ route('admin.users.enrollments-report', $order->user) }}" class="btn btn-sm btn-outline-primary mt-3">
                            <i class="fa fa-chart-bar me-1"></i>Enrollments Report
                        </a>
                    </div>
                </div>

                @if ($order->status === 'pending')
                    <div class="card border-warning">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h5 class="mb-0 text-warning-emphasis">Confirm Payment</h5>
                        </div>
                        <div class="card-body">
                            <p class="small text-muted">
                                Verify the bank transfer using the transaction reference above, then confirm to activate enrollments.
                            </p>
                            <button type="button" class="btn btn-success w-100 mb-2" id="btnConfirmOrder"
                                data-action="{{ route('admin.orders.confirm', $order) }}">
                                <i class="fa fa-check me-1"></i>Confirm & Activate Enrollments
                            </button>
                            <button type="button" class="btn btn-outline-danger w-100" id="btnRejectOrder"
                                data-action="{{ route('admin.orders.reject', $order) }}">
                                <i class="fa fa-times me-1"></i>Reject Order
                            </button>
                        </div>
                    </div>
                @endif

                <div class="card border-danger mt-3">
                    <div class="card-header bg-danger bg-opacity-10">
                        <h5 class="mb-0 text-danger">Delete Order</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Permanently delete this order and remove all linked enrollments for this student.
                            @if ($order->status === 'completed')
                                <strong class="text-danger d-block mt-1">This order is completed — the student will lose course access.</strong>
                            @endif
                        </p>
                        <button type="button" class="btn btn-danger w-100" id="btnDeleteOrder"
                            data-action="{{ route('admin.orders.destroy', $order) }}"
                            data-order="{{ $order->order_number }}"
                            data-completed="{{ $order->status === 'completed' ? '1' : '0' }}">
                            <i class="fa fa-trash me-1"></i>Delete Order & Enrollments
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.confirm-modal')
@endsection

@push('scripts')
    <script>
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
