@extends('admin.layout')

@section('title', 'Enrollments Report - ' . $user->name)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Enrollments Report</h1>
                <p class="text-muted mb-0">{{ $user->name }} · {{ $user->email }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i>Back to Users
            </a>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Enrollments</h6>
                        <h3 class="fw-bold mb-0">{{ $stats['total_enrollments'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Active</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $stats['active_enrollments'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Pending</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $stats['pending_enrollments'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Total Paid</h6>
                        <h3 class="fw-bold mb-0 text-primary">SAR {{ number_format($stats['total_paid'], 2) }}</h3>
                        <small class="text-muted">After discounts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-1">Orders (Completed)</h6>
                        <h3 class="fw-bold mb-0 text-success">SAR {{ number_format($stats['orders_total'], 2) }}</h3>
                        <small class="text-muted">{{ $stats['orders_count'] }} orders · Pending: SAR {{ number_format($stats['orders_pending_total'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Course Enrollments</h5></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->course->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $enrollment->status === 'active' ? 'bg-success' : ($enrollment->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ ucfirst(str_replace('_', ' ', $enrollment->payment_method ?? '—')) }}
                                            @if ($enrollment->transaction_id)
                                                <br><small><code>{{ $enrollment->transaction_id }}</code></small>
                                            @endif
                                        </td>
                                        <td>SAR {{ number_format($enrollment->effective_amount_paid, 2) }}</td>
                                        <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No enrollments yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Orders</h5></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Method</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
                                        </td>
                                        <td>{{ $order->payment_method_label }}</td>
                                        <td>SAR {{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="badge {{ $order->status === 'completed' ? 'bg-success' : ($order->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
