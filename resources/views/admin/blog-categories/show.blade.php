@extends('admin.layout')

@section('title', 'Blog Category Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-eye me-2"></i>Blog Category Details
                            </h4>
                            <div>
                                <a href="{{ route('admin.blog-categories.edit', $category) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <h5 class="text-primary">{{ $category->name }}</h5>
                                    <p class="text-muted mb-0">{{ $category->description ?: 'No description available' }}
                                    </p>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h3 class="text-primary mb-1">{{ $category->blogs_count ?? 0 }}</h3>
                                                <small class="text-muted">Total Blogs</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h3 class="text-success mb-1">{{ $category->created_at->format('M d, Y') }}
                                                </h3>
                                                <small class="text-muted">Created Date</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Category Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="150"><strong>Status:</strong></td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($category->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Featured:</strong></td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $category->is_featured ? 'warning' : 'secondary' }}">
                                                        {{ $category->is_featured ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Slug:</strong></td>
                                                <td><code>{{ $category->slug }}</code></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Last Updated:</strong></td>
                                                <td>{{ $category->updated_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Recent Blogs</h6>
                                    </div>
                                    <div class="card-body">
                                        @php
                                            $recentBlogs = $category->blogs()->latest()->limit(5)->get();
                                        @endphp

                                        @if ($recentBlogs->count() > 0)
                                            <div class="list-group list-group-flush">
                                                @foreach ($recentBlogs as $blog)
                                                    <div class="list-group-item px-0">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6 class="mb-1">{{ Str::limit($blog->title, 40) }}</h6>
                                                                <small
                                                                    class="text-muted">{{ $blog->created_at->format('M d, Y') }}</small>
                                                            </div>
                                                            <span
                                                                class="badge bg-{{ $blog->status === 'published' ? 'success' : 'warning' }}">
                                                                {{ ucfirst($blog->status) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No blogs in this category yet.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Quick Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.blogs.create') }}?category_id={{ $category->id }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-plus me-1"></i>Add Blog to Category
                                            </a>
                                            <a href="{{ route('admin.blog-categories.edit', $category) }}"
                                                class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit me-1"></i>Edit Category
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
