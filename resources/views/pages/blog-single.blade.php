@extends('layouts.app')

@section('title', $blog->title . ' - E-Class')

@section('content')
    <!-- Blog Single Banner -->
    <section class="blog-single-banner position-relative d-flex align-items-center justify-content-center">
        <div class="blog-single-banner-bg position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="blog-single-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold text-white mb-3">{{ $blog->title }}</h1>
                    <div class="d-flex justify-content-center align-items-center mb-3 text-white">
                        <span class="me-3">
                            <i class="fas fa-calendar me-1"></i> {{ $blog->created_at->format('F d, Y') }}
                        </span>
                        <span class="me-3">
                            <i class="fas fa-eye me-1"></i> {{ $blog->views_count }} views
                        </span>
                        @if ($blog->category)
                            <span>
                                <i class="fas fa-folder me-1"></i> {{ $blog->category->name }}
                            </span>
                        @endif
                    </div>
                    @if ($blog->author)
                        <div class="text-white">
                            <i class="fas fa-user me-1"></i> By {{ $blog->author }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Content Section -->
    <section class="blog-content-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Blog Image -->
                    @if ($blog->image)
                        <div class="blog-image mb-4">
                            <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" class="img-fluid rounded-4 w-100">
                        </div>
                    @endif

                    <!-- Blog Content -->
                    <div class="blog-content bg-white rounded-4 shadow-sm p-4 p-md-5 mb-5">
                        @if ($blog->excerpt)
                            <div class="blog-excerpt mb-4 p-4 bg-light rounded">
                                <h5 class="text-primary mb-2">Summary</h5>
                                <p class="mb-0">{{ $blog->excerpt }}</p>
                            </div>
                        @endif

                        <div class="blog-body">
                            {!! nl2br(e($blog->description)) !!}
                        </div>

                        <hr class="my-4">

                        <!-- Blog Meta -->
                        <div class="blog-meta d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-3">
                                    <i class="fas fa-calendar me-1"></i> {{ $blog->created_at->format('M d, Y') }}
                                </span>
                                <span class="text-muted me-3">
                                    <i class="fas fa-eye me-1"></i> {{ $blog->views_count }} views
                                </span>
                                @if ($blog->category)
                                    <span class="text-muted">
                                        <i class="fas fa-folder me-1"></i> {{ $blog->category->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="shareBlog()">
                                    <i class="fas fa-share me-1"></i> Share
                                </button>
                                <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Blog
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Related Blogs -->
                    @if ($relatedBlogs->count() > 0)
                        <div class="related-blogs">
                            <h3 class="mb-4">Related Articles</h3>
                            <div class="row g-4">
                                @foreach ($relatedBlogs as $relatedBlog)
                                    <div class="col-md-6">
                                        <div class="card h-100 shadow-sm">
                                            @if ($relatedBlog->image)
                                                <img src="{{ $relatedBlog->image_url }}" class="card-img-top"
                                                    alt="{{ $relatedBlog->title }}">
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                                    style="height: 200px;">
                                                    <i class="fas fa-newspaper fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title">{{ Str::limit($relatedBlog->title, 60) }}</h6>
                                                <p class="card-text text-muted small">
                                                    {{ Str::limit($relatedBlog->excerpt ?: $relatedBlog->description, 100) }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $relatedBlog->created_at->format('M d, Y') }}
                                                    </small>
                                                    <a href="{{ route('blog.show', $relatedBlog->slug) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Read More
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="blog-sidebar">
                        <!-- Categories -->
                        @if ($categories->count() > 0)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Categories</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        @foreach ($categories as $category)
                                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                {{ $category->name }}
                                                <span
                                                    class="badge bg-primary rounded-pill">{{ $category->blogs_count }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Recent Posts -->
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">Recent Posts</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $recentBlogs = \App\Models\Blog::published()
                                        ->with('category')
                                        ->latest()
                                        ->limit(5)
                                        ->get();
                                @endphp

                                @if ($recentBlogs->count() > 0)
                                    @foreach ($recentBlogs as $recentBlog)
                                        <div class="d-flex mb-3">
                                            @if ($recentBlog->image)
                                                <img src="{{ $recentBlog->image_url }}" class="rounded me-3"
                                                    style="width: 60px; height: 60px; object-fit: cover;"
                                                    alt="{{ $recentBlog->title }}">
                                            @else
                                                <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-newspaper text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('blog.show', $recentBlog->slug) }}"
                                                        class="text-decoration-none">
                                                        {{ Str::limit($recentBlog->title, 40) }}
                                                    </a>
                                                </h6>
                                                <small
                                                    class="text-muted">{{ $recentBlog->created_at->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No recent posts.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .blog-single-banner {
            min-height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }

        .blog-single-banner-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }

        .blog-single-banner-overlay {
            background: rgba(0, 0, 0, 0.5);
        }

        .blog-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .blog-excerpt {
            border-left: 4px solid #007bff;
        }

        .blog-meta {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }

        .related-blogs .card {
            transition: transform 0.3s ease;
        }

        .related-blogs .card:hover {
            transform: translateY(-5px);
        }

        .blog-sidebar .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function shareBlog() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $blog->title }}',
                    text: '{{ Str::limit($blog->excerpt ?: $blog->description, 150) }}',
                    url: window.location.href
                });
            } else {
                // Fallback: copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('Link copied to clipboard!');
                });
            }
        }
    </script>
@endpush
