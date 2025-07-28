@extends('admin.layout')

@section('title', 'View Blog Post')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">View Blog Post</h1>
                <p class="text-muted">Blog post details and statistics</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Post
                </a>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Blogs
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Blog Content -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Blog Content</h6>
                    </div>
                    <div class="card-body">
                        @if ($blog->image)
                            <div class="text-center mb-4">
                                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="img-fluid rounded"
                                    style="max-height: 400px;">
                            </div>
                        @endif

                        <h2 class="mb-3">{{ $blog->title }}</h2>

                        <div class="mb-4">
                            <div class="d-flex align-items-center text-muted mb-2">
                                <i class="fas fa-user me-2"></i>
                                <span>{{ $blog->author ?: 'Admin' }}</span>
                                <i class="fas fa-calendar ms-3 me-2"></i>
                                <span>{{ $blog->created_at->format('F d, Y') }}</span>
                                <i class="fas fa-eye ms-3 me-2"></i>
                                <span>{{ $blog->views_count }} views</span>
                            </div>

                            @if ($blog->category)
                                <span class="badge bg-info me-2">{{ $blog->category->name }}</span>
                            @endif

                            @if ($blog->is_featured)
                                <span class="badge bg-warning text-dark">Featured</span>
                            @endif

                            <span
                                class="badge bg-{{ $blog->status === 'published' ? 'success' : ($blog->status === 'draft' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($blog->status) }}
                            </span>
                        </div>

                        @if ($blog->excerpt)
                            <div class="alert alert-light">
                                <h6 class="alert-heading">Excerpt</h6>
                                <p class="mb-0">{{ $blog->excerpt }}</p>
                            </div>
                        @endif

                        <div class="blog-content">
                            {!! nl2br(e($blog->description)) !!}
                        </div>


                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Blog Stats -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-bar me-1"></i> Blog Statistics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">{{ $blog->views_count }}</h4>
                                    <small class="text-muted">Total Views</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">{{ $blog->created_at->format('M d') }}</h4>
                                <small class="text-muted">Created Date</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h6 class="mb-1">{{ $blog->updated_at->format('M d') }}</h6>
                                    <small class="text-muted">Last Updated</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="mb-1">{{ $blog->slug }}</h6>
                                <small class="text-muted">URL Slug</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blog Details -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-1"></i> Blog Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span
                                        class="badge bg-{{ $blog->status === 'published' ? 'success' : ($blog->status === 'draft' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($blog->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Featured:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $blog->is_featured ? 'warning' : 'secondary' }}">
                                        {{ $blog->is_featured ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>
                                    @if ($blog->category)
                                        <span class="badge bg-info">{{ $blog->category->name }}</span>
                                    @else
                                        <span class="text-muted">No category</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Author:</strong></td>
                                <td>{{ $blog->author ?: 'Admin' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Word Count:</strong></td>
                                <td>{{ str_word_count(strip_tags($blog->description)) }} words</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bolt me-1"></i> Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.blogs.toggle_status', $blog) }}" method="POST" class="d-inline">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-toggle-{{ $blog->status === 'published' ? 'on' : 'off' }} me-1"></i>
                                    {{ $blog->status === 'published' ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.blogs.toggle_featured', $blog) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('POST')
                                <button type="submit"
                                    class="btn btn-outline-{{ $blog->is_featured ? 'success' : 'secondary' }} w-100">
                                    <i class="fas fa-star me-1"></i>
                                    {{ $blog->is_featured ? 'Unfeature' : 'Feature' }}
                                </button>
                            </form>

                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-1"></i> Edit Post
                            </a>

                            <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST"
                                class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this blog?')">
                                    <i class="fas fa-trash me-1"></i> Delete Post
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Category Info -->
                @if ($blog->category)
                    <div class="card shadow mt-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-tag me-1"></i> Category Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                @if ($blog->category->image)
                                    <img src="{{ $blog->category->image_url }}" alt="{{ $blog->category->name }}"
                                        class="rounded me-2" style="width: 50px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 40px;">
                                        <i class="fas fa-tag text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $blog->category->name }}</h6>
                                    <small class="text-muted">{{ $blog->category->blogs_count }} blogs</small>
                                </div>
                            </div>
                            @if ($blog->category->description)
                                <p class="text-muted small mb-0">{{ $blog->category->description }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .blog-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .blog-content p {
            margin-bottom: 1rem;
        }
    </style>
@endpush
