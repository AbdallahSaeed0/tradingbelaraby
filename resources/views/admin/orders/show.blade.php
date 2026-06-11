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
                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="mb-2"
                                onsubmit="return confirm('Confirm this payment and activate enrollments?');">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fa fa-check me-1"></i>Confirm & Activate Enrollments
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.reject', $order) }}" method="POST"
                                onsubmit="return confirm('Reject this order and cancel pending enrollments?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fa fa-times me-1"></i>Reject Order
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
