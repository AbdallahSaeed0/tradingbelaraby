@extends('admin.layout')

@section('title', 'Blog Analytics')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Blog Analytics
                            </h4>
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Blogs
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Statistics Cards -->
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Blogs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBlogs }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Published Blogs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $publishedBlogs }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Draft Blogs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $draftBlogs }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-edit fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Total Views</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalViews }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-eye fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Top Performing Blogs -->
                            <div class="col-lg-8">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Top Performing Blogs</h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($topBlogs->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Blog Title</th>
                                                            <th>Category</th>
                                                            <th>Views</th>
                                                            <th>Status</th>
                                                            <th>Created</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($topBlogs as $blog)
                                                            <tr>
                                                                <td>
                                                                    <a href="{{ route('admin.blogs.show', $blog) }}"
                                                                        class="text-decoration-none">
                                                                        {{ Str::limit($blog->title, 50) }}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    @if ($blog->category)
                                                                        <span
                                                                            class="badge bg-info">{{ $blog->category->name }}</span>
                                                                    @else
                                                                        <span class="text-muted">No category</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-primary">{{ $blog->views_count }}</span>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-{{ $blog->status === 'published' ? 'success' : 'warning' }}">
                                                                        {{ ucfirst($blog->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $blog->created_at->format('M d, Y') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center">No blogs found.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Category Distribution -->
                            <div class="col-lg-4">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Blogs by Category</h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($categoryStats->count() > 0)
                                            @foreach ($categoryStats as $stat)
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ $stat->category_name ?: 'Uncategorized' }}</span>
                                                        <span class="badge bg-primary">{{ $stat->blog_count }}</span>
                                                    </div>
                                                    <div class="progress mt-1" style="height: 5px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ ($stat->blog_count / $totalBlogs) * 100 }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center">No categories found.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Recent Activity -->
                            <div class="col-12">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Recent Blog Activity</h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($recentBlogs->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Blog Title</th>
                                                            <th>Author</th>
                                                            <th>Status</th>
                                                            <th>Views</th>
                                                            <th>Last Updated</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($recentBlogs as $blog)
                                                            <tr>
                                                                <td>{{ Str::limit($blog->title, 50) }}</td>
                                                                <td>{{ $blog->author ?: 'Admin' }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-{{ $blog->status === 'published' ? 'success' : 'warning' }}">
                                                                        {{ ucfirst($blog->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $blog->views_count }}</td>
                                                                <td>{{ $blog->updated_at->format('M d, Y H:i') }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.blogs.edit', $blog) }}"
                                                                        class="btn btn-sm btn-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.blogs.show', $blog) }}"
                                                                        class="btn btn-sm btn-info">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted text-center">No recent activity found.</p>
                                        @endif
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
