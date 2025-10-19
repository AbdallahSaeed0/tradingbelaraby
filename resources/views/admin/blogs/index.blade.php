@extends('admin.layout')

@section('title', 'Blogs Management')

@push('styles')
    <style>
        .blog-card {
            transition: transform 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .blog-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .blog-stats {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .blog-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin-right: 0.25rem;
        }

        .status-badge {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .status-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .stat-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1);
        }
    </style>
@endpush

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
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search blogs..." id="searchInput"
                                value="{{ $search }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="published" {{ $status == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ $status == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="featuredFilter">
                            <option value="">All Posts</option>
                            <option value="featured" {{ request('featured') == 'featured' ? 'selected' : '' }}>Featured
                            </option>
                            <option value="not_featured" {{ request('featured') == 'not_featured' ? 'selected' : '' }}>Not
                                Featured</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="perPageFilter">
                            <option value="15" {{ $per == 15 ? 'selected' : '' }}>15 per page</option>
                            <option value="25" {{ $per == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ $per == 50 ? 'selected' : '' }}>50 per page</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" id="clearFilters">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Blogs List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
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
                                        <input type="checkbox" class="blog-checkbox" value="{{ $blog->id }}">
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
                                            onclick="toggleStatus({{ $blog->id }})">
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
                    <div class="col-md-6">
                        <div class="d-flex justify-content-end">
                            {{ $blogs->appends(request()->query())->links() }}
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const blogCheckboxes = document.querySelectorAll('.blog-checkbox');
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const featuredFilter = document.getElementById('featuredFilter');
            const perPageFilter = document.getElementById('perPageFilter');
            const clearFilters = document.getElementById('clearFilters');
            const bulkDeleteModal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));

            // Select all functionality
            selectAll.addEventListener('change', function() {
                blogCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Filter functionality
            function filterBlogs() {
                const searchTerm = searchInput.value.toLowerCase();
                const category = categoryFilter.value;
                const status = statusFilter.value;
                const featured = featuredFilter.value;

                const rows = document.querySelectorAll('.blog-row');

                rows.forEach(row => {
                    const searchData = row.dataset.search;
                    const rowCategory = row.dataset.category;
                    const rowStatus = row.dataset.status;
                    const rowFeatured = row.dataset.featured;

                    let show = true;

                    if (searchTerm && !searchData.includes(searchTerm)) show = false;
                    if (category && rowCategory !== category) show = false;
                    if (status && rowStatus !== status) show = false;
                    if (featured && rowFeatured !== featured) show = false;

                    row.style.display = show ? '' : 'none';
                });
            }

            // Event listeners for filters
            searchInput.addEventListener('input', filterBlogs);
            categoryFilter.addEventListener('change', filterBlogs);
            statusFilter.addEventListener('change', filterBlogs);
            featuredFilter.addEventListener('change', filterBlogs);

            // Clear filters
            clearFilters.addEventListener('click', function() {
                searchInput.value = '';
                categoryFilter.value = '';
                statusFilter.value = '';
                featuredFilter.value = '';
                filterBlogs();
            });

            // Per page filter
            perPageFilter.addEventListener('change', function() {
                const url = new URL(window.location);
                url.searchParams.set('per_page', this.value);
                window.location.href = url.toString();
            });

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

            // Toggle status
            window.toggleStatus = function(blogId) {
                if (confirm('Are you sure you want to toggle the status of this blog?')) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        alert('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    fetch(`/admin/blogs/${blogId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                    }).then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Error updating blog status');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('Error updating blog status');
                    });
                }
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
