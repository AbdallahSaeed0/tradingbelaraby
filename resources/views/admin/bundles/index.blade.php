@extends('admin.layout')

@section('title', 'Bundle Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Bundle Management</h1>
                        <p class="text-muted">Manage course bundles</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Add New Bundle
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.bundles.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search bundles..."
                                    id="searchInput" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
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
                        <div class="col-md-4">
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

        <!-- Bundles Table -->
        <div class="card">
            <div class="card-body">
                @if($bundles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Courses</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bundles as $bundle)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input bundle-checkbox" type="checkbox" value="{{ $bundle->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <img src="{{ $bundle->image_url }}" alt="{{ $bundle->name }}" 
                                                class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <strong>{{ $bundle->name }}</strong>
                                            @if($bundle->name_ar)
                                                <br><small class="text-muted">{{ $bundle->name_ar }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $bundle->courses_count }} courses</span>
                                        </td>
                                        <td>
                                            <strong>{{ $bundle->formatted_price }}</strong>
                                            @if($bundle->original_price)
                                                <br><small class="text-muted text-decoration-line-through">{{ $bundle->formatted_original_price }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $bundle->status === 'published' ? 'bg-success' : ($bundle->status === 'draft' ? 'bg-warning' : 'bg-secondary') }} status-badge"
                                                style="cursor: pointer;"
                                                onclick="showStatusModal({{ $bundle->id }}, '{{ $bundle->status }}', [
                                                    { value: 'published', label: 'Published' },
                                                    { value: 'draft', label: 'Draft' },
                                                    { value: 'archived', label: 'Archived' }
                                                ], '{{ route('admin.bundles.update_status', $bundle->id) }}')">
                                                @if($bundle->status === 'published')
                                                    Published
                                                @elseif($bundle->status === 'draft')
                                                    Draft
                                                @else
                                                    Archived
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($bundle->is_featured)
                                                <span class="badge bg-primary">Featured</span>
                                            @else
                                                <span class="badge bg-light text-dark">No</span>
                                            @endif
                                        </td>
                                        <td>{{ $bundle->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.bundles.show', $bundle) }}" 
                                                    class="btn btn-sm btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.bundles.edit', $bundle) }}" 
                                                    class="btn btn-sm btn-primary" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.bundles.destroy', $bundle) }}" 
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this bundle?');">
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
                                {{ $bundles->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No bundles found.</p>
                        <a href="{{ route('admin.bundles.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Your First Bundle
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

                fetch(`{{ route('admin.bundles.index') }}?${params.toString()}`, {
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
                        const newUrl = `{{ route('admin.bundles.index') }}?${params.toString()}`;
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
                    document.getElementById('perPageFilter').value = '10';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.bundles.index') }}');

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

