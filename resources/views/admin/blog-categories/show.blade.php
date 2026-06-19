@extends('admin.layout')

@section('title', 'Blog Category Details')

@section('content')
    <div class="container-fluid py-4 admin-detail-page">
        @include('admin.partials.detail-page-header', [
            'title' => $category->name,
            'subtitle' => 'Blog Category · ' . $category->slug,
            'backUrl' => route('admin.blog-categories.index'),
            'backLabel' => 'Blog Categories',
            'primaryUrl' => route('admin.blog-categories.edit', $category),
            'primaryLabel' => 'Edit Category',
        ])

        <div class="row admin-detail-main-row">
            <div class="col-lg-4 order-lg-2 mb-4">
                <div class="card mb-4" id="detail-section-stats">
                    <div class="card-body admin-detail-grid">
                        <div class="row text-center mb-0">
                            <div class="col-6 admin-detail-field">
                                <strong>Total Blogs</strong>
                                <span class="admin-detail-value fs-4 text-primary">{{ $category->blogs_count ?? 0 }}</span>
                            </div>
                            <div class="col-6 admin-detail-field">
                                <strong>Created</strong>
                                <span class="admin-detail-value">{{ $category->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4" id="detail-section-recent">
                    <div class="card-header"><h6 class="mb-0">Recent Blogs</h6></div>
                    <div class="card-body">
                        @php $recentBlogs = $category->blogs()->latest()->limit(5)->get(); @endphp
                        @forelse ($recentBlogs as $blog)
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="fw-semibold">{{ Str::limit($blog->title, 40) }}</div>
                                    <small class="text-muted">{{ $blog->created_at->format('M d, Y') }}</small>
                                </div>
                                <span class="badge bg-{{ $blog->status === 'published' ? 'success' : 'warning text-dark' }}">{{ ucfirst($blog->status) }}</span>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No blogs in this category yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card" id="detail-section-actions">
                    <div class="card-header"><h6 class="mb-0">Quick Actions</h6></div>
                    <div class="card-body d-grid gap-2">
                        <a href="{{ route('admin.blogs.create') }}?category_id={{ $category->id }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Blog to Category
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 order-lg-1">
                <div class="card" id="detail-section-info">
                    <div class="card-header"><h6 class="mb-0">Category Information</h6></div>
                    <div class="card-body admin-detail-grid">
                        <div class="admin-detail-field">
                            <strong>Description</strong>
                            <span class="admin-detail-value">{{ $category->description ?: 'No description available' }}</span>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Status</strong>
                                <span class="admin-detail-value">
                                    <span class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($category->status) }}</span>
                                </span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Featured</strong>
                                <span class="admin-detail-value">
                                    <span class="badge bg-{{ $category->is_featured ? 'warning text-dark' : 'secondary' }}">{{ $category->is_featured ? 'Yes' : 'No' }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="admin-detail-field">
                            <strong>Slug</strong>
                            <span class="admin-detail-value"><code>{{ $category->slug }}</code></span>
                        </div>
                        <div class="admin-detail-field">
                            <strong>Last Updated</strong>
                            <span class="admin-detail-value">{{ $category->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
