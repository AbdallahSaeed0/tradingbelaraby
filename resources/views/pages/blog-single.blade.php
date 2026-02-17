@extends('layouts.app')

@section('title', ($blog->getLocalizedTitle() ?? 'Blog') . ' - ' .
    (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Blog Single Banner -->
    <section class="blog-single-banner position-relative d-flex align-items-center justify-content-center">
        <div class="blog-single-banner-bg position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="blog-single-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold text-white mb-3">{{ $blog->getLocalizedTitle() }}</h1>
                    <div class="d-flex justify-content-center align-items-center mb-3 text-white">
                        <span class="me-3">
                            <i class="fas fa-calendar me-1"></i> {{ $blog->created_at->format('F d, Y') }}
                        </span>
                        <span class="me-3">
                            <i class="fas fa-eye me-1"></i> {{ $blog->views_count }} {{ custom_trans('views', 'front') }}
                        </span>
                        @if ($blog->category)
                            <span>
                                <i class="fas fa-folder me-1"></i>
                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($blog->category->name, $blog->category->name_ar) }}
                            </span>
                        @endif
                    </div>
                    @if ($blog->author_name)
                        <div class="text-white">
                            <i class="fas fa-user me-1"></i> {{ custom_trans('By', 'front') }} {{ $blog->author_name }}
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
                    @if ($blog->getLocalizedImageUrl())
                        <div class="blog-image mb-4">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="d-block">
                                <img src="{{ $blog->getLocalizedImageUrl() }}" alt="{{ $blog->getLocalizedTitle() }}"
                                    class="img-fluid rounded-4 w-100">
                            </a>
                        </div>
                    @endif

                    <!-- Blog Content -->
                    <div class="blog-content bg-white rounded-4 shadow-sm p-4 p-md-5 mb-5">
                        @if ($blog->getLocalizedExcerpt())
                            <div class="blog-excerpt mb-4 p-4 bg-light rounded">
                                <h5 class="text-primary mb-2">{{ custom_trans('Summary', 'front') }}</h5>
                                <p class="mb-0">{{ $blog->getLocalizedExcerpt() }}</p>
                            </div>
                        @endif

                        <div class="blog-body">
                            {!! $blog->getLocalizedDescription() !!}
                        </div>

                        <hr class="my-4">

                        <!-- Blog Tags -->
                        @php $tags = $blog->getTagsArray(); @endphp
                        @if (!empty($tags))
                            <div class="blog-tags mb-4" style="word-wrap: break-word; overflow-wrap: break-word;">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-tags me-1"></i> {{ custom_trans('Tags:', 'front') }}
                                </h6>
                                <div class="d-flex flex-wrap gap-2" style="max-width: 100%;">
                                    @foreach ($tags as $tag)
                                        <span class="badge bg-primary" style="white-space: normal; word-break: break-word;">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

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
                                        <i class="fas fa-folder me-1"></i>
                                        {{ \App\Helpers\TranslationHelper::getLocalizedContent($blog->category->name, $blog->category->name_ar) }}
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
                                                    alt="{{ $relatedBlog->getLocalizedTitle() }}">
                                            @else
                                                <div
                                                    class="card-img-top bg-light d-flex align-items-center justify-content-center h-200">
                                                    <i class="fas fa-newspaper fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    {{ Str::limit($relatedBlog->getLocalizedTitle(), 60) }}</h6>
                                                <p class="card-text text-muted small">
                                                    {{ Str::limit($relatedBlog->getLocalizedExcerpt() ?: strip_tags($relatedBlog->getLocalizedDescription()), 100) }}
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
                                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
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
                                                <img src="{{ $recentBlog->image_url }}"
                                                    class="rounded me-3 w-60 h-60 img-h-60"
                                                    alt="{{ $recentBlog->getLocalizedTitle() }}">
                                            @else
                                                <div
                                                    class="rounded me-3 bg-light d-flex align-items-center justify-content-center w-60 h-60">
                                                    <i class="fas fa-newspaper text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="{{ route('blog.show', $recentBlog->slug) }}"
                                                        class="text-decoration-none">
                                                        {{ Str::limit($recentBlog->getLocalizedTitle(), 40) }}
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

@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/blog.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/blog.css') }}">
    @endpush
@endif

@push('scripts')
    <script>
        function shareBlog() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $blog->getLocalizedTitle() }}',
                    text: '{{ Str::limit($blog->getLocalizedExcerpt() ?: strip_tags($blog->getLocalizedDescription()), 150) }}',
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
