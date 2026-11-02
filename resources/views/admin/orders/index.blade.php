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
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3" data-live-filter="ordersResults">
                    <div class="col-md-6">
                        <input type="search" class="form-control" name="search" value="{{ request('search') }}"
                            placeholder="Search order #, transaction ref, student..." autocomplete="off"
                            aria-label="Search orders">
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
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100" data-live-clear>Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Bulk actions: shown while rows are selected -->
                <div class="enrollment-bulk-bar d-none" id="bulkActionsBar" role="region" aria-label="Bulk actions">
                    <div class="enrollment-bulk-bar__info">
                        <span class="enrollment-bulk-bar__count" id="bulkSelectedCount">0</span>
                        <div class="lh-sm">
                            <div class="fw-semibold" id="bulkSelectedLabel">orders selected</div>
                            <small class="text-muted">Choose an action to apply to them</small>
                        </div>
                    </div>
                    <div class="enrollment-bulk-bar__actions">
                        <button type="button" class="btn btn-sm btn-success" id="bulkConfirmBtn">
                            <i class="fa fa-check me-1"></i>Confirm
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="bulkRejectBtn">
                            <i class="fa fa-ban me-1"></i>Reject
                        </button>
                        <span class="enrollment-bulk-bar__divider" aria-hidden="true"></span>
                        <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                            <i class="fa fa-trash me-1"></i>Delete
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="bulkClearSelection" title="Deselect all">
                            <i class="fa fa-times me-1"></i>Deselect
                        </button>
                    </div>
                </div>

                <div id="ordersResults" data-live-region>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll" aria-label="Select all orders on this page">
                                    </div>
                                </th>
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
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input order-checkbox" type="checkbox" value="{{ $order->id }}"
                                                data-completed="{{ $order->status === 'completed' ? '1' : '0' }}"
                                                aria-label="Select order {{ $order->order_number }}">
                                        </div>
                                    </td>
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
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-order-delete" title="Delete"
                                                data-action="{{ route('admin.orders.destroy', $order) }}"
                                                data-order="{{ $order->order_number }}"
                                                data-completed="{{ $order->status === 'completed' ? '1' : '0' }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                    @include('admin.partials.per-page', ['paginator' => $orders])
                    {{ $orders->links() }}
                </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.confirm-modal')
@endsection

@push('scripts')
    <script>
        // Rows are replaced by live filtering (#ordersResults), so row handlers are delegated
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-order-delete');
            if (!btn) {
                return;
            }
            // The modal is shared with the bulk actions; drop leftover bulk fields first
            document.querySelectorAll('#actionConfirmForm .bulk-field').forEach(el => el.remove());
            showActionConfirmModal({
                title: 'Delete Order',
                message: 'Delete order ' + btn.dataset.order + ' and remove all linked enrollments? This action cannot be undone.',
                warning: btn.dataset.completed === '1' ? 'This order is completed — the student will lose course access.' : null,
                action: btn.dataset.action,
                method: 'DELETE',
                headerClass: 'bg-danger text-white',
                btnClass: 'btn-danger',
                btnHtml: '<i class="fa fa-trash me-1"></i>Delete Order'
            });
        });

        // Multi-select + bulk actions
        const bulkBar = document.getElementById('bulkActionsBar');

        function selectedOrderBoxes() {
            return Array.from(document.querySelectorAll('.order-checkbox:checked'));
        }

        function syncSelection() {
            const selectAll = document.getElementById('selectAll');
            const rows = document.querySelectorAll('.order-checkbox');
            const count = selectedOrderBoxes().length;
            if (selectAll) {
                selectAll.checked = rows.length > 0 && count === rows.length;
                selectAll.indeterminate = count > 0 && count < rows.length;
            }
            document.getElementById('bulkSelectedCount').textContent = count;
            document.getElementById('bulkSelectedLabel').textContent = (count === 1 ? 'order' : 'orders') + ' selected';
            bulkBar.classList.toggle('d-none', count === 0);
        }

        document.addEventListener('change', function(e) {
            if (e.target.id === 'selectAll') {
                document.querySelectorAll('.order-checkbox').forEach(cb => {
                    cb.checked = e.target.checked;
                });
                syncSelection();
            } else if (e.target.classList.contains('order-checkbox')) {
                syncSelection();
            }
        });

        // New results come in unselected
        document.addEventListener('live-filter:updated', syncSelection);

        document.getElementById('bulkClearSelection').addEventListener('click', function() {
            document.querySelectorAll('.order-checkbox').forEach(cb => {
                cb.checked = false;
            });
            syncSelection();
        });

        // Put the selected ids and the action into the confirm modal's form, then open it
        function confirmBulk(action, options) {
            const form = document.getElementById('actionConfirmForm');
            form.querySelectorAll('.bulk-field').forEach(el => el.remove());
            const fields = { action: action };
            selectedOrderBoxes().forEach((cb, i) => {
                fields['ids[' + i + ']'] = cb.value;
            });
            Object.entries(fields).forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                input.className = 'bulk-field';
                form.appendChild(input);
            });
            showActionConfirmModal(Object.assign({
                action: '{{ route('admin.orders.bulk_action') }}',
                method: 'POST'
            }, options));
        }

        function selectionSummary() {
            const boxes = selectedOrderBoxes();
            const completed = boxes.filter(cb => cb.dataset.completed === '1').length;
            return { count: boxes.length, completed: completed };
        }

        document.getElementById('bulkConfirmBtn').addEventListener('click', function() {
            const s = selectionSummary();
            if (s.count === 0) {
                return;
            }
            confirmBulk('confirm', {
                title: 'Confirm Orders',
                message: 'Confirm ' + s.count + ' selected ' + (s.count === 1 ? 'order' : 'orders') + ' and activate their enrollments?',
                warning: s.completed > 0 ? s.completed + ' already completed ' + (s.completed === 1 ? 'order' : 'orders') + ' will be skipped.' : null,
                headerClass: 'bg-success text-white',
                btnClass: 'btn-success',
                btnHtml: '<i class="fa fa-check me-1"></i>Confirm ' + s.count
            });
        });

        document.getElementById('bulkRejectBtn').addEventListener('click', function() {
            const s = selectionSummary();
            if (s.count === 0) {
                return;
            }
            confirmBulk('reject', {
                title: 'Reject Orders',
                message: 'Reject ' + s.count + ' selected ' + (s.count === 1 ? 'order' : 'orders') + ' and cancel their pending enrollments?',
                warning: s.completed > 0 ? s.completed + ' completed ' + (s.completed === 1 ? 'order' : 'orders') + ' cannot be rejected and will be skipped.' : null,
                headerClass: 'bg-warning text-dark',
                btnClass: 'btn-warning',
                btnHtml: '<i class="fa fa-ban me-1"></i>Reject ' + s.count
            });
        });

        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            const s = selectionSummary();
            if (s.count === 0) {
                return;
            }
            confirmBulk('delete', {
                title: 'Delete Orders',
                message: 'Delete ' + s.count + ' selected ' + (s.count === 1 ? 'order' : 'orders') + ' and remove all linked enrollments? This action cannot be undone.',
                warning: s.completed > 0 ? s.completed + ' of them ' + (s.completed === 1 ? 'is' : 'are') + ' completed — those students will lose course access.' : null,
                headerClass: 'bg-danger text-white',
                btnClass: 'btn-danger',
                btnHtml: '<i class="fa fa-trash me-1"></i>Delete ' + s.count
            });
        });
    </script>
@endpush
