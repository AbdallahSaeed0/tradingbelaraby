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
                <form method="GET" action="{{ route('admin.coupons.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search coupons..."
                                    id="searchInput" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="is_active" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="discount_type" id="discountTypeFilter">
                                <option value="">All Types</option>
                                <option value="percentage" {{ request('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="per_page" id="perPageFilter">
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
                            <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                <i class="fa fa-times me-1"></i>Clear
                            </button>
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
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
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
                                            <div class="form-check">
                                                <input class="form-check-input coupon-checkbox" type="checkbox" value="{{ $coupon->id }}">
                                            </div>
                                        </td>
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
                                            @php
                                                $currentStatus = $coupon->is_active ? 'active' : 'inactive';
                                            @endphp
                                            <span
                                                class="badge {{ $coupon->is_active && $coupon->isValid() ? 'bg-success' : ($coupon->is_active ? 'bg-warning' : 'bg-secondary') }} status-badge"
                                                style="cursor: pointer;"
                                                onclick="showStatusModal({{ $coupon->id }}, '{{ $currentStatus }}', [
                                                    { value: 'active', label: 'Active' },
                                                    { value: 'inactive', label: 'Inactive' }
                                                ], '{{ route('admin.coupons.update_status', $coupon->id) }}')">
                                                @if($coupon->is_active && $coupon->isValid())
                                                    Active
                                                @elseif($coupon->is_active)
                                                    Scheduled
                                                @else
                                                    Inactive
                                                @endif
                                            </span>
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
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="d-flex align-items-center me-3">
                                    <label class="form-label me-2 mb-0 small">Per page:</label>
                                    <select class="form-select form-select-sm w-auto" id="perPageSelect" onchange="changePerPage(this.value)">
                                        @php
                                            $perPage = (int) request('per_page', 10);
                                        @endphp
                                        <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                                        <option value="20" {{ $perPage === 20 ? 'selected' : '' }}>20</option>
                                        <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $perPage === 100 ? 'selected' : '' }}>100</option>
                                        <option value="500" {{ $perPage === 500 ? 'selected' : '' }}>500</option>
                                        <option value="1000" {{ $perPage === 1000 ? 'selected' : '' }}>1000</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                {{ $coupons->links() }}
                            </div>
                        </div>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Change per page function
            function changePerPage(value) {
                // Use AJAX to update
                const formData = new FormData(document.getElementById('filterForm'));
                formData.set('per_page', value);
                performAjaxSearch();
            }

            // Initialize variables for AJAX search
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.querySelector('.table tbody');
            const paginationContainer = document.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');

            // AJAX search function
            function performAjaxSearch() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                // Show loading state
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }

                fetch(`{{ route('admin.coupons.index') }}?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        // Create a temporary container to parse the HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        // Extract table body
                        const newTableBody = tempDiv.querySelector('.table tbody');
                        const newPagination = tempDiv.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');

                        if (newTableBody && tableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML;
                        }

                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        // Update URL without reload
                        const newUrl = `{{ route('admin.coupons.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
                        }
                    });
            }

            // Prevent form submission - use AJAX instead
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performAjaxSearch();
            });

            // Dropdown filters - use AJAX
            document.getElementById('statusFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('discountTypeFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('perPageFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            // Clear filters button - use AJAX
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Clear all form fields
                    document.getElementById('searchInput').value = '';
                    document.getElementById('statusFilter').value = '';
                    document.getElementById('discountTypeFilter').value = '';
                    document.getElementById('perPageFilter').value = '10';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.coupons.index') }}');

                    // Perform AJAX search with cleared filters
                    performAjaxSearch();
                });
            }

            // Search with debounce - AJAX only
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        performAjaxSearch();
                    }, 500);
                });

                // Prevent form submission on Enter key in search
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(searchTimeout);
                        performAjaxSearch();
                    }
                });
            }
        });
    </script>
    @include('admin.partials.status-modal')
@endpush

