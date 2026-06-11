@extends('admin.layout')

@section('title', 'Orders')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Orders</h1>
                        <p class="text-muted">Review payments, transaction references, and confirm enrollments</p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft"><i class="fa fa-receipt text-white"></i></div>
                        <div>
                            <h6 class="text-muted mb-0">Total Orders</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft"><i class="fa fa-clock text-white"></i></div>
                        <div>
                            <h6 class="text-muted mb-0">Pending</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['pending'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft"><i class="fa fa-university text-white"></i></div>
                        <div>
                            <h6 class="text-muted mb-0">Bank Transfer Pending</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['bank_transfer_pending'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft"><i class="fa fa-dollar-sign text-white"></i></div>
                        <div>
                            <h6 class="text-muted mb-0">Completed Revenue</h6>
                            <h4 class="fw-bold mb-0">SAR {{ number_format($stats['revenue'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Search order #, transaction ref, student...">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="payment_method">
                            <option value="">All Methods</option>
                            <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="paypal" {{ request('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="free" {{ request('payment_method') === 'free' ? 'selected' : '' }}>Free</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search me-1"></i>Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Student</th>
                                <th>Payment</th>
                                <th>Transaction Ref</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td class="fw-semibold">{{ $order->order_number }}</td>
                                    <td>
                                        <div>{{ $order->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $order->payment_method_label }}</span>
                                    </td>
                                    <td>
                                        @if ($order->transaction_reference)
                                            <code>{{ $order->transaction_reference }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">SAR {{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = match ($order->status) {
                                                'completed' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'cancelled', 'failed' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
