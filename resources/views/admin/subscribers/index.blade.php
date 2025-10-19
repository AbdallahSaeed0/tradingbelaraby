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

        <!-- Statistics Card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-0">{{ $subscribers->total() }}</h4>
                                <p class="mb-0">Total Subscribers</p>
                            </div>
                            <div class="ms-3">
                                <i class="fa fa-users fa-2x opacity-75"></i>
                            </div>
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
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}" placeholder="Search by name, email, phone, or country...">
                    </div>
                    <div class="col-md-2">
                        <label for="years_of_experience" class="form-label">Experience</label>
                        <select class="form-select" id="years_of_experience" name="years_of_experience">
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
                        <select class="form-select" id="language" name="language">
                            <option value="">All Languages</option>
                            <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="ar" {{ request('language') == 'ar' ? 'selected' : '' }}>Arabic</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('admin.subscribers.index') }}" class="btn btn-outline-secondary w-100"
                            title="Clear Filters">
                            <i class="fa fa-times"></i>
                        </a>
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
                    <table class="table table-hover">
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

                @if ($subscribers->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $subscribers->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

    <style>
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }
    </style>

    <script>
        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const filterInputs = filterForm.querySelectorAll('input, select');

            filterInputs.forEach(input => {
                input.addEventListener('change', function() {
                    filterForm.submit();
                });
            });

            // Auto-submit search input with delay
            const searchInput = document.getElementById('search');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterForm.submit();
                }, 500); // 500ms delay
            });

            // Multi-select functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const subscriberCheckboxes = document.querySelectorAll('.subscriber-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const selectedCountSpan = document.getElementById('selectedCount');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            const selectedSubscribersInput = document.getElementById('selectedSubscribers');

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                subscriberCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });

            // Individual checkbox functionality
            subscriberCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelectAllCheckbox();
                    updateBulkDeleteButton();
                });
            });

            // Update select all checkbox state
            function updateSelectAllCheckbox() {
                const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
                const totalBoxes = subscriberCheckboxes.length;

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

            // Update bulk delete button visibility and count
            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.subscriber-checkbox:checked');
                const count = checkedBoxes.length;

                if (count > 0) {
                    bulkDeleteBtn.style.display = 'inline-block';
                    selectedCountSpan.textContent = count;
                } else {
                    bulkDeleteBtn.style.display = 'none';
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
