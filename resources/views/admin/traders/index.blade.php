@extends('admin.layout')

@section('title', 'Traders Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">{{ custom_trans('Traders Management', 'admin') }}</h1>
                        <p class="text-muted">Manage trader registrations and view their information.</p>
                    </div>
                    <div>
                        <span class="badge bg-primary fs-6">{{ $traders->total() }} {{ custom_trans('Total Traders', 'admin') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.traders.index') }}" id="filterForm" class="row g-3">
                            <div class="col-md-6">
                                <label for="search" class="form-label">{{ custom_trans('Search', 'admin') }}</label>
                                <input type="text" class="form-control" id="searchInput" name="search"
                                    value="{{ request('search') }}"
                                    placeholder="{{ custom_trans('Search by name, email, phone...', 'admin') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>{{ custom_trans('Filter', 'admin') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                    <i class="fas fa-times me-1"></i>{{ custom_trans('Clear', 'admin') }}
                                </button>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <a href="{{ route('admin.traders.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                                    class="btn btn-success">
                                    <i class="fas fa-download me-1"></i>{{ custom_trans('Export CSV', 'admin') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-4 d-none-initially" id="bulkActions">
            <div class="col-12">
                <div class="card shadow-sm border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="fw-bold text-warning" id="selectedCount">0</span> {{ custom_trans('traders selected', 'admin') }}
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                                    <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete Selected', 'admin') }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="clearSelection">
                                    <i class="fas fa-times me-1"></i>{{ custom_trans('Clear Selection', 'admin') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traders Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        @if ($traders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                                </div>
                                            </th>
                                            <th>{{ custom_trans('Name', 'admin') }}</th>
                                            <th>{{ custom_trans('Email', 'admin') }}</th>
                                            <th>{{ custom_trans('Phone', 'admin') }}</th>
                                            <th>{{ custom_trans('Gender', 'admin') }}</th>
                                            <th>{{ custom_trans('Trading Community', 'admin') }}</th>
                                            <th>{{ custom_trans('Languages', 'admin') }}</th>
                                            <th>{{ custom_trans('Registered', 'admin') }}</th>
                                            <th class="text-center">{{ custom_trans('Actions', 'admin') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($traders as $trader)
                                            <tr>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input trader-checkbox" type="checkbox"
                                                            value="{{ $trader->id }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div
                                                            class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center w-40 h-40">
                                                            <i class="fa fa-user text-white"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $trader->name }}</h6>
                                                            <small class="text-muted">{{ $trader->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $trader->email }}</td>
                                                <td>
                                                    @if ($trader->phone_number)
                                                        <span class="badge bg-info">{{ $trader->phone_number }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst($trader->sex) }}</span>
                                                </td>
                                                <td>
                                                    @if ($trader->trading_community)
                                                        <span
                                                            class="badge bg-success">{{ $trader->trading_community }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>
                                                        <small class="fw-bold">{{ $trader->first_language }}</small>
                                                        @if ($trader->second_language)
                                                            <br><small
                                                                class="text-muted">{{ $trader->second_language }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $trader->created_at->format('M d, Y', 'admin') }}<br>
                                                        {{ $trader->created_at->format('H:i', 'admin') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.traders.show', $trader) }}"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="{{ custom_trans('View Details', 'admin') }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <form action="{{ route('admin.traders.destroy', $trader) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('{{ custom_trans('Are you sure you want to delete this trader?', 'admin') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                title="{{ custom_trans('Delete', 'admin') }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fa fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">{{ custom_trans('No traders found', 'admin') }}</h4>
                                <p class="text-muted">{{ custom_trans('No trader registrations match your search criteria.', 'admin') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($traders->hasPages())
            <div class="row mt-4">
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
                    <div class="d-flex justify-content-center">
                        {{ $traders->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="row mt-4">
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
            </div>
        @endif
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" action="{{ route('admin.traders.bulk-delete') }}" method="POST" class="d-none-initially">
        @csrf
        @method('DELETE')
        <input type="hidden" name="trader_ids" id="selectedTraderIds">
    </form>
@endsection

@push('scripts')
    <script>
        // Change per page function
        function changePerPage(value) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Setup checkboxes function
            function setupCheckboxes() {
                const selectAll = document.getElementById('selectAll');
                const traderCheckboxes = document.querySelectorAll('.trader-checkbox');

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        traderCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateBulkActions();
                    });
                }

                traderCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedCount = document.querySelectorAll('.trader-checkbox:checked').length;
                        const totalCheckboxes = document.querySelectorAll('.trader-checkbox').length;
                        if (selectAll) {
                            selectAll.checked = checkedCount === totalCheckboxes;
                            selectAll.indeterminate = checkedCount > 0 && checkedCount < totalCheckboxes;
                        }
                        updateBulkActions();
                    });
                });
                updateBulkActions();
            }
            setupCheckboxes();

            // Update bulk actions visibility and state
            function updateBulkActions() {
                const selectedCount = document.querySelectorAll('.trader-checkbox:checked').length;
                const bulkActions = document.getElementById('bulkActions');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
                const selectedCountSpan = document.getElementById('selectedCount');

                if (bulkActions && bulkDeleteBtn && selectedCountSpan) {
                    if (selectedCount > 0) {
                        bulkActions.style.display = 'block';
                        bulkDeleteBtn.disabled = false;
                        selectedCountSpan.textContent = selectedCount;
                    } else {
                        bulkActions.style.display = 'none';
                        bulkDeleteBtn.disabled = true;
                    }
                }
            }

            // Clear selection
            const clearSelection = document.getElementById('clearSelection');
            if (clearSelection) {
                clearSelection.addEventListener('click', function() {
                    document.querySelectorAll('.trader-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    const selectAll = document.getElementById('selectAll');
                    if (selectAll) {
                        selectAll.checked = false;
                        selectAll.indeterminate = false;
                    }
                    updateBulkActions();
                });
            }

            // Bulk delete
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(document.querySelectorAll('.trader-checkbox:checked'))
                        .map(checkbox => checkbox.value);

                    if (selectedIds.length === 0) {
                        alert('{{ custom_trans('Please select traders to delete.', 'admin') }}');
                        return;
                    }

                    if (confirm('{{ custom_trans('Are you sure you want to delete the selected traders? This action cannot be undone.', 'admin') }}')) {
                        document.getElementById('selectedTraderIds').value = JSON.stringify(selectedIds);
                        document.getElementById('bulkDeleteForm').submit();
                    }
                });
            }

            // Initialize variables for AJAX search
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.querySelector('.table tbody');
            const paginationContainer = document.querySelector('.row.mt-4 .col-12');

            // AJAX search function
            function performAjaxSearch() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                // Show loading state
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="9" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }

                fetch(`{{ route('admin.traders.index') }}?${params.toString()}`, {
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
                        const newPagination = tempDiv.querySelector('.row.mt-4 .col-12');

                        if (newTableBody && tableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML;
                        }

                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        // Update URL without reload
                        const newUrl = `{{ route('admin.traders.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);

                        // Re-attach event listeners
                        setupCheckboxes();
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

            // Clear filters button - use AJAX
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Clear all form fields
                    document.getElementById('searchInput').value = '';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.traders.index') }}');

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
@endpush
