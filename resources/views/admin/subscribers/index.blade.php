@extends('admin.layout')

@section('title', 'Subscribers Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Subscribers</h1>
                <p class="text-muted">Manage coming soon page subscribers</p>
            </div>
            <div class="d-flex gap-2">
                <button id="bulkDeleteBtn" class="btn btn-danger d-none-initially">
                    <i class="fa fa-trash me-2"></i>Delete Selected (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('admin.subscribers.export', request()->query()) }}" class="btn btn-success">
                    <i class="fa fa-download me-2"></i>Export CSV
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Subscribers</h6>
                            <h4 class="fw-bold mb-0">{{ $subscribers->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.subscribers.index') }}" class="row g-3" id="filterForm">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchInput" name="search"
                            value="{{ request('search') }}" placeholder="Search by name, email, phone, or country...">
                    </div>
                    <div class="col-md-2">
                        <label for="years_of_experience" class="form-label">Experience</label>
                            <select class="form-select" id="yearsOfExperienceFilter" name="years_of_experience">
                                <option value="">All Levels</option>
                                <option value="10" {{ request('years_of_experience') == '10' ? 'selected' : '' }}>10 Years
                                </option>
                                <option value="20" {{ request('years_of_experience') == '20' ? 'selected' : '' }}>20 Years
                                </option>
                                <option value="30" {{ request('years_of_experience') == '30' ? 'selected' : '' }}>30 Years
                                </option>
                                <option value="40" {{ request('years_of_experience') == '40' ? 'selected' : '' }}>40 Years
                                </option>
                                <option value="50" {{ request('years_of_experience') == '50' ? 'selected' : '' }}>50 Years
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="language" class="form-label">Language</label>
                            <select class="form-select" id="languageFilter" name="language">
                                <option value="">All Languages</option>
                                <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ request('language') == 'ar' ? 'selected' : '' }}>Arabic</option>
                            </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="dateFromFilter" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="dateToFilter" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary w-100" id="clearFiltersBtn"
                            title="Clear Filters">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Delete Form -->
        <form id="bulkDeleteForm" action="{{ route('admin.subscribers.bulk-delete') }}" method="POST"
            class="d-none-initially">
            @csrf
            @method('DELETE')
            <input type="hidden" id="selectedSubscribers" name="subscribers" value="">
        </form>

        <!-- Subscribers Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Country</th>
                                <th>Experience</th>
                                <th>Language</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscribers as $subscriber)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input subscriber-checkbox" type="checkbox"
                                                value="{{ $subscriber->id }}"
                                                data-subscriber-name="{{ $subscriber->name }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($subscriber->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $subscriber->name }}</div>
                                                @if ($subscriber->whatsapp_number)
                                                    <small class="text-muted">
                                                        <i class="fab fa-whatsapp text-success"></i>
                                                        {{ $subscriber->whatsapp_number }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $subscriber->email }}" class="text-decoration-none">
                                            {{ $subscriber->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $subscriber->phone }}" class="text-decoration-none">
                                            {{ $subscriber->phone }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $subscriber->country }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $subscriber->years_of_experience }}
                                            Years</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $subscriber->language === 'ar' ? 'bg-warning' : 'bg-primary' }}">
                                            {{ $subscriber->language === 'ar' ? 'العربية' : 'English' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            {{ $subscriber->created_at->format('M d, Y') }}
                                            <br>
                                            {{ $subscriber->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.subscribers.show', $subscriber) }}"
                                                class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.subscribers.destroy', $subscriber) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this subscriber?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-users fa-3x mb-3"></i>
                                            <p class="mb-0">No subscribers found.</p>
                                            <small>Subscribers will appear here when users sign up on the coming soon
                                                page.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

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
                        @if ($subscribers->hasPages())
                            <div class="d-flex justify-content-end">
                                {{ $subscribers->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div><script>
        // AJAX search functionality
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

                fetch(`{{ route('admin.subscribers.index') }}?${params.toString()}`, {
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
                        const newUrl = `{{ route('admin.subscribers.index') }}?${params.toString()}`;
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

            // Setup checkboxes function
            function setupCheckboxes() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const subscriberCheckboxes = document.querySelectorAll('.subscriber-checkbox');

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        subscriberCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                        updateBulkDeleteButton();
                    });
                }

                subscriberCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateSelectAllCheckbox();
                        updateBulkDeleteButton();
                    });
                });
                updateBulkDeleteButton();
            }
            setupCheckboxes();

            // Prevent form submission - use AJAX instead
            const filterForm = document.getElementById('filterForm');
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                performAjaxSearch();
            });

            // Dropdown filters - use AJAX
            document.getElementById('yearsOfExperienceFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('languageFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('dateFromFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('dateToFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            // Clear filters button - use AJAX
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Clear all form fields
                    document.getElementById('searchInput').value = '';
                    document.getElementById('yearsOfExperienceFilter').value = '';
                    document.getElementById('languageFilter').value = '';
                    document.getElementById('dateFromFilter').value = '';
                    document.getElementById('dateToFilter').value = '';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.subscribers.index') }}');

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

            // Multi-select functionality
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedCountSpan = document.getElementById('selectedCount');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const selectedSubscribersInput = document.getElementById('selectedSubscribers');

            // Update select all checkbox state
            function updateSelectAllCheckbox() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const subscriberCheckboxes = document.querySelectorAll('.subscriber-checkbox');
                const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
                const totalBoxes = subscriberCheckboxes.length;

                if (selectAllCheckbox) {
                    if (checkedBoxes.length === 0) {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                    } else if (checkedBoxes.length === totalBoxes) {
                        selectAllCheckbox.checked = true;
                        selectAllCheckbox.indeterminate = false;
                    } else {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = true;
                    }
                }
            }

            // Update bulk delete button visibility and count
            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
                const count = checkedBoxes.length;

                if (bulkDeleteBtn && selectedCountSpan) {
                    if (count > 0) {
                        bulkDeleteBtn.style.display = 'inline-block';
                        selectedCountSpan.textContent = count;
                    } else {
                        bulkDeleteBtn.style.display = 'none';
                    }
                }
            }

            // Bulk delete functionality
            bulkDeleteBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
                const selectedIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);
                const selectedNames = Array.from(checkedBoxes).map(checkbox => checkbox.dataset
                    .subscriberName);

                if (selectedIds.length === 0) {
                    alert('Please select at least one subscriber to delete.');
                    return;
                }

                const confirmMessage =
                    `Are you sure you want to delete ${selectedIds.length} subscriber(s)?\n\nSelected subscribers:\n${selectedNames.join('\n')}`;

                if (confirm(confirmMessage)) {
                    selectedSubscribersInput.value = JSON.stringify(selectedIds);
                    bulkDeleteForm.submit();
                }
            });
        });
    </script>
@endsection

