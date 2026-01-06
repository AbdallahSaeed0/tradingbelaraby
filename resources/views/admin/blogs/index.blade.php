@extends('admin.layout')

@section('title', 'Blogs Management')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Blogs Management</h1>
                        <p class="text-muted">Create and manage your blog posts</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.blogs.analytics') }}" class="btn btn-outline-info me-2">
                            <i class="fa fa-chart-bar me-2"></i>Analytics
                        </a>
                        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus me-2"></i>Create Blog
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
                            <i class="fa fa-blog text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Blogs</h6>
                            <h4 class="fw-bold mb-0">{{ $blogs->total() }}</h4>
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
                            <h6 class="text-muted mb-0">Published</h6>
                            <h4 class="fw-bold mb-0">{{ $blogs->where('status', 'published')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-edit text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Drafts</h6>
                            <h4 class="fw-bold mb-0">{{ $blogs->where('status', 'draft')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-star text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Featured</h6>
                            <h4 class="fw-bold mb-0">{{ $blogs->where('is_featured', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.blogs.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search blogs..." id="searchInput"
                                    value="{{ request('search', $search ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="category" id="categoryFilter">
                                <option value="">All Categories</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category', $category ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="published" {{ request('status', $status ?? '') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status', $status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ request('status', $status ?? '') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="featured" id="featuredFilter">
                                <option value="">All Posts</option>
                                <option value="featured" {{ request('featured') == 'featured' ? 'selected' : '' }}>Featured
                                </option>
                                <option value="not_featured" {{ request('featured') == 'not_featured' ? 'selected' : '' }}>Not
                                    Featured</option>
                            </select>
                        </div>
                        <div class="col-md-3">
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
            </div>
        </div>

        <!-- Blogs List -->
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
                                <th>Blog Post</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blogs as $blog)
                                <tr class="blog-row"
                                    data-search="{{ strtolower($blog->title . ' ' . $blog->description . ' ' . ($blog->author_name ?: 'admin')) }}"
                                    data-category="{{ $blog->category_id }}" data-status="{{ $blog->status }}"
                                    data-featured="{{ $blog->is_featured ? 'featured' : 'not_featured' }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input blog-checkbox" type="checkbox" value="{{ $blog->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if ($blog->image)
                                                    <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}"
                                                        class="rounded w-50 h-40 item-image-sm">
                                                @else
                                                    <div
                                                        class="bg-secondary rounded d-flex align-items-center justify-content-center w-50 h-40 item-placeholder-sm">
                                                        <i class="fa fa-image text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $blog->title }}</h6>
                                                <small
                                                    class="text-muted">{{ Str::limit($blog->excerpt ?: $blog->description, 60) }}</small>
                                                @if ($blog->is_featured)
                                                    <span class="badge bg-warning text-dark ms-1">Featured</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($blog->category)
                                            <span class="badge bg-info">{{ $blog->category->name }}</span>
                                        @else
                                            <span class="text-muted">No category</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $blog->author_name ?: 'Admin' }}</small>
                                    </td>
                                    <td>
                                        <span
                                            class="badge status-badge bg-{{ $blog->status === 'published' ? 'success' : ($blog->status === 'draft' ? 'warning' : 'secondary') }}"
                                            style="cursor: pointer;"
                                            onclick="showBlogStatusModal({{ $blog->id }}, '{{ $blog->status }}')">
                                            {{ ucfirst($blog->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $blog->views_count }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $blog->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="blog-actions">
                                            <a href="{{ route('admin.blogs.show', $blog) }}"
                                                class="btn btn-outline-info btn-sm" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blogs.edit', $blog) }}"
                                                class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button onclick="toggleFeatured({{ $blog->id }})"
                                                class="btn btn-outline-{{ $blog->is_featured ? 'success' : 'secondary' }} btn-sm"
                                                title="{{ $blog->is_featured ? 'Unfeature' : 'Feature' }}">
                                                <i class="fa fa-star"></i>
                                            </button>
                                            <button onclick="deleteBlog({{ $blog->id }})"
                                                class="btn btn-outline-danger btn-sm" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-blog fa-3x mb-3"></i>
                                            <h5>No blogs found</h5>
                                            <p>Create your first blog post to get started</p>
                                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus me-1"></i>Add Blog
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
                                                        onclick="bulkUpdateStatus('published')">Publish</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('draft')">Mark
                                                        as Draft</a></li>
                                                <li><a class="dropdown-item" href="#"
                                                        onclick="bulkUpdateStatus('archived')">Archive</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#" onclick="bulkToggleFeatured()">Toggle
                                                        Featured</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            @if ($blogs->hasPages())
                                <nav aria-label="Blogs pagination">
                                    <ul class="pagination pagination-sm justify-content-end mb-0">
                                        {{-- Previous Page Link --}}
                                        @if ($blogs->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fa fa-chevron-left"></i>
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $blogs->previousPageUrl() }}"
                                                    aria-label="Previous">
                                                    <i class="fa fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        {{-- First Page --}}
                                        @if ($blogs->currentPage() > 3)
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $blogs->url(1) }}">1</a>
                                            </li>
                                            @if ($blogs->currentPage() > 4)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach ($blogs->getUrlRange(max(1, $blogs->currentPage() - 2), min($blogs->lastPage(), $blogs->currentPage() + 2)) as $page => $url)
                                            @if ($page == $blogs->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endforeach

                                        {{-- Last Page --}}
                                        @if ($blogs->currentPage() < $blogs->lastPage() - 2)
                                            @if ($blogs->currentPage() < $blogs->lastPage() - 3)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="{{ $blogs->url($blogs->lastPage()) }}">{{ $blogs->lastPage() }}</a>
                                            </li>
                                        @endif

                                        {{-- Next Page Link --}}
                                        @if ($blogs->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $blogs->nextPageUrl() }}" aria-label="Next">
                                                    <i class="fa fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="fa fa-chevron-right"></i>
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            @else
                                <div class="text-end text-muted small">
                                    Page 1 of 1
                                </div>
                            @endif
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
                    <h5 class="modal-title">Delete Selected Blogs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the selected blogs? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.blogs.bulk_delete') }}" method="POST" id="bulkDeleteForm">
                        @csrf
                        <input type="hidden" name="blogs" id="bulkDeleteBlogs">
                        <button type="submit" class="btn btn-danger">Delete Selected</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('admin.partials.status-modal')

@push('scripts')
    <script>
        // Change per page function
        function changePerPage(value) {
            // Use AJAX to update
            const formData = new FormData(document.getElementById('filterForm'));
            formData.set('per_page', value);
            performAjaxSearch();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Setup checkboxes function
            function setupCheckboxes() {
                const selectAll = document.getElementById('selectAll');
                const blogCheckboxes = document.querySelectorAll('.blog-checkbox');

                if (selectAll) {
                    selectAll.addEventListener('change', function() {
                        blogCheckboxes.forEach(checkbox => {
                            checkbox.checked = this.checked;
                        });
                    });
                }
            }
            setupCheckboxes();

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
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
                }

                fetch(`{{ route('admin.blogs.index') }}?${params.toString()}`, {
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
                        const newUrl = `{{ route('admin.blogs.index') }}?${params.toString()}`;
                        window.history.pushState({}, '', newUrl);

                        // Re-attach event listeners
                        setupCheckboxes();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (tableBody) {
                            tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Error loading data. Please try again.</td></tr>';
                        }
                    });
            }

            // Prevent form submission - use AJAX instead
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                performAjaxSearch();
            });

            // Dropdown filters - use AJAX
            document.getElementById('categoryFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('statusFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            document.getElementById('featuredFilter').addEventListener('change', function() {
                performAjaxSearch();
            });

            // Clear filters button - use AJAX
            const clearFiltersBtn = document.getElementById('clearFiltersBtn');
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Clear all form fields
                    document.getElementById('searchInput').value = '';
                    document.getElementById('categoryFilter').value = '';
                    document.getElementById('statusFilter').value = '';
                    document.getElementById('featuredFilter').value = '';

                    // Update URL without parameters
                    window.history.pushState({}, '', '{{ route('admin.blogs.index') }}');

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

            // Bulk delete functionality
            window.bulkDelete = function() {
                const checkedBoxes = document.querySelectorAll('.blog-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select blogs to delete');
                    return;
                }

                if (confirm(
                        'Are you sure you want to delete the selected blogs? This action cannot be undone.')) {
                    const blogIds = Array.from(checkedBoxes).map(cb => cb.value);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        alert('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    fetch('/admin/blogs/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                blogs: blogIds
                            })
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error deleting blogs: ' + (data.message || 'Unknown error'));
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting blogs');
                        });
                }
            };

            // Show status modal for blogs
            window.showBlogStatusModal = function(blogId, currentStatus) {
                const availableStatuses = [
                    { value: 'published', label: 'Published' },
                    { value: 'draft', label: 'Draft' },
                    { value: 'archived', label: 'Archived' }
                ];
                const updateUrl = `/admin/blogs/${blogId}/update-status`;
                window.showStatusModal(blogId, currentStatus, availableStatuses, updateUrl);
            };

            // Toggle featured
            window.toggleFeatured = function(blogId) {
                if (confirm('Are you sure you want to toggle the featured status of this blog?')) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        alert('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    fetch(`/admin/blogs/${blogId}/toggle-featured`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    }).then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Error updating blog featured status');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('Error updating blog featured status');
                    });
                }
            };

            // Delete blog
            window.deleteBlog = function(blogId) {
                if (confirm('Are you sure you want to delete this blog?')) {
                    // Create a form for deletion
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/blogs/${blogId}`;

                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    // Add method spoofing for DELETE
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            };

            // Bulk update status
            window.bulkUpdateStatus = function(status) {
                const checkedBoxes = document.querySelectorAll('.blog-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select blogs to update');
                    return;
                }

                if (confirm(`Are you sure you want to ${status} the selected blogs?`)) {
                    const blogIds = Array.from(checkedBoxes).map(cb => cb.value);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        alert('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    fetch('/admin/blogs/bulk-update-status', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                blogs: blogIds,
                                status: status
                            })
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error updating blogs: ' + (data.message || 'Unknown error'));
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            alert('Error updating blogs');
                        });
                }
            };

            // Bulk toggle featured
            window.bulkToggleFeatured = function() {
                const checkedBoxes = document.querySelectorAll('.blog-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select blogs to update');
                    return;
                }

                if (confirm('Are you sure you want to toggle featured status for selected blogs?')) {
                    const blogIds = Array.from(checkedBoxes).map(cb => cb.value);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        alert('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    fetch('/admin/blogs/bulk-toggle-featured', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                blogs: blogIds
                            })
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error updating blogs: ' + (data.message || 'Unknown error'));
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            alert('Error updating blogs');
                        });
                }
            };
        });
    </script>
@endpush

