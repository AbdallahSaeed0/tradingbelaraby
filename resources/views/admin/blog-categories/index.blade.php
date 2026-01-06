@extends('admin.layout')

@section('title', 'Blog Categories Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Blog Categories Management</h1>
                        <p class="text-muted">Organize your blog content with categories</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.blog-categories.analytics') }}" class="btn btn-outline-info me-2">
                            <i class="fa fa-chart-bar me-2"></i>Analytics
                        </a>
                        <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Category
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Messages -->
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

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-tags text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Categories</h6>
                            <h4 class="fw-bold mb-0">{{ $categories->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft">
                            <i class="fa fa-check-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Active Categories</h6>
                            <h4 class="fw-bold mb-0">{{ $categories->where('status', 'active')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-blog text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">With Blogs</h6>
                            <h4 class="fw-bold mb-0">{{ $categories->where('blogs_count', '>', 0)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-file-alt text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Blogs</h6>
                            <h4 class="fw-bold mb-0">{{ $categories->sum('blogs_count') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.blog-categories.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search categories..." id="searchInput"
                                    value="{{ request('search', $search ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="blogs" id="blogsFilter">
                                <option value="">All Categories</option>
                                <option value="with_blogs" {{ request('blogs') == 'with_blogs' ? 'selected' : '' }}>With Blogs
                                </option>
                                <option value="without_blogs" {{ request('blogs') == 'without_blogs' ? 'selected' : '' }}>
                                    Without Blogs</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="per_page" id="perPageFilter">
                                <option value="15" {{ request('per_page', $per ?? 15) == 15 ? 'selected' : '' }}>15 per page</option>
                                <option value="25" {{ request('per_page', $per ?? 15) == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ request('per_page', $per ?? 15) == 50 ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page', $per ?? 15) == 100 ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
                                    <i class="fa fa-refresh me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                    <div class="col-md-1">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" id="gridView">
                                <i class="fa fa-th"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm active" id="listView">
                                <i class="fa fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Grid/List -->
        <div id="categoryContainer">
            <!-- List View -->
            <div id="listViewContainer">
                <div class="card">
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
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Blogs Count</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                        <tr class="category-row"
                                            data-search="{{ strtolower($category->name . ' ' . $category->description) }}"
                                            data-status="{{ $category->status }}"
                                            data-blogs="{{ $category->blogs_count > 0 ? 'with_blogs' : 'without_blogs' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input category-checkbox" type="checkbox"
                                                        value="{{ $category->id }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if ($category->image)
                                                            <img src="{{ $category->image_url }}"
                                                                alt="{{ $category->name }}"
                                                                class="rounded w-40 h-40 img-h-60">
                                                        @else
                                                            <div
                                                                class="bg-secondary rounded d-flex align-items-center justify-content-center w-40 h-40">
                                                                <i class="fa fa-tag text-white"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $category->name }}</h6>
                                                        <small class="text-muted">{{ $category->slug }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($category->description)
                                                    <span
                                                        class="text-muted">{{ Str::limit($category->description, 50) }}</span>
                                                @else
                                                    <span class="text-muted">No description</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="badge bg-primary me-2">{{ $category->blogs_count }}</span>
                                                    <small class="text-muted">blogs</small>
                                                </div>
                                                @if ($category->published_blogs_count > 0)
                                                    <small
                                                        class="text-success d-block">{{ $category->published_blogs_count }}
                                                        published</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span
                                                    class="badge status-badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}"
                                                    style="cursor: pointer;"
                                                    onclick="showStatusModal({{ $category->id }}, '{{ $category->status }}', [
                                                        { value: 'active', label: 'Active' },
                                                        { value: 'inactive', label: 'Inactive' }
                                                    ], '{{ route('admin.blog-categories.update_status', $category->id) }}')">
                                                    {{ ucfirst($category->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $category->order }}</span>
                                            </td>
                                            <td>
                                                <small>{{ $category->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="category-actions">
                                                    <a href="{{ route('admin.blog-categories.show', $category) }}"
                                                        class="btn btn-outline-info btn-sm" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.blog-categories.edit', $category) }}"
                                                        class="btn btn-outline-primary btn-sm" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    @if (!$category->hasBlogs())
                                                        <button onclick="deleteCategory({{ $category->id }})"
                                                            class="btn btn-outline-danger btn-sm" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-secondary btn-sm"
                                                            title="Cannot delete - has blogs" disabled>
                                                            <i class="fa fa-lock"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fa fa-tags fa-3x mb-3"></i>
                                                    <h5>No categories found</h5>
                                                    <p>Create your first blog category to get started</p>
                                                    <a href="{{ route('admin.blog-categories.create') }}"
                                                        class="btn btn-primary">
                                                        <i class="fa fa-plus me-1"></i>Add Category
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Bulk Actions -->
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
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="bulkDelete()">
                                            <i class="fa fa-trash me-1"></i>Delete Selected
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fa fa-cog me-1"></i>Bulk Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="bulkUpdateStatus('active')">Activate</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="bulkUpdateStatus('inactive')">Deactivate</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    {{ $categories->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Selected Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the selected categories? This action cannot be undone.</p>
                    <p class="text-warning"><i class="fa fa-exclamation-triangle me-1"></i> Categories with blogs cannot
                        be deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.blog-categories.bulk_delete') }}" method="POST" id="bulkDeleteForm">
                        @csrf
                        <input type="hidden" name="categories" id="bulkDeleteCategories">
                        <button type="submit" class="btn btn-danger">Delete Selected</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const blogsFilter = document.getElementById('blogsFilter');
            const perPageFilter = document.getElementById('perPageFilter');
            const clearFilters = document.getElementById('clearFilters');
            const bulkDeleteModal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));


            // Change per page function
            function changePerPage(value) {
                // Use AJAX to update
                const formData = new FormData(document.getElementById('filterForm'));
                formData.set('per_page', value);
                performAjaxSearch();
            }

            // Initialize variables for AJAX search
            let searchTimeout;
            const tableBody = document.querySelector('.table tbody');
            const paginationContainer = document.querySelector('.row.mt-3 .col-md-6:last-child .d-flex.justify-content-end');

            // AJAX search function
            function performAjaxSearch() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData);

                // Show loading state
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }

                fetch(`{{ route('admin.blog-categories.index') }}?${params.toString()}`, {
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
                        const newBulkActions = tempDiv.querySelector('.row.mt-3 .col-md-6:first-child');

                        if (newTableBody && tableBody) {
                            tableBody.innerHTML = newTableBody.innerHTML;
                        }

                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }

                        if (newBulkActions) {
                            const bulkActionsContainer = document.querySelector('.row.mt-3 .col-md-6:first-child');
                            if (bulkActionsContainer) {
                                bulkActionsContainer.innerHTML = newBulkActions.innerHTML;
                            }
                        }

                        // Update URL without reload
                        const newUrl = `{{ route('admin.blog-categories.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);

                        // Re-attach event listeners
                        setupCheckboxes();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
                        }
                    });
            }

            // Setup checkboxes function
            function setupCheckboxes() {
                const selectAll = document.getElementById('selectAll');
                const categoryCheckboxes = document.querySelectorAll('.category-checkbox');

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        categoryCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                    });
                }
            }
            setupCheckboxes();

            // Prevent form submission - use AJAX instead
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performAjaxSearch();
            });

            // Dropdown filters - use AJAX
            document.getElementById('statusFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('blogsFilter').addEventListener('change', function() {
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
                    document.getElementById('blogsFilter').value = '';
                    document.getElementById('perPageFilter').value = '15';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.blog-categories.index') }}');

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

            // Per page filter
            perPageFilter.addEventListener('change', function() {
                const url = new URL(window.location);
                url.searchParams.set('per_page', this.value);
                window.location.href = url.toString();
            });

            // Bulk delete functionality
            window.bulkDelete = function() {
                const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select categories to delete');
                    return;
                }

                const categoryIds = Array.from(checkedBoxes).map(cb => cb.value);
                document.getElementById('bulkDeleteCategories').value = JSON.stringify(categoryIds);
                bulkDeleteModal.show();
            };

            // toggleStatus function removed - now using modal

            // Delete category
            window.deleteCategory = function(categoryId) {
                if (confirm('Are you sure you want to delete this category?')) {
                    fetch(`/admin/blog-categories/${categoryId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                        },
                    }).then(response => {
                        if (response.ok) {
                            location.reload();
                        }
                    });
                }
            };

            // Bulk update status
            window.bulkUpdateStatus = function(status) {
                const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select categories to update');
                    return;
                }

                if (confirm(`Are you sure you want to ${status} the selected categories?`)) {
                    const categoryIds = Array.from(checkedBoxes).map(cb => cb.value);
                    // Implement bulk status update
                    console.log('Bulk update status:', status, categoryIds);
                }
            };
        });
    </script>
    @include('admin.partials.status-modal')
@endpush

