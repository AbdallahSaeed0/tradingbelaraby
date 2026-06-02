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
                            <i class="fas fa-calendar me-1"></i> {{ $blog->created_at->translatedFormat('F d, Y') }}
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
                                    <i class="fas fa-calendar me-1"></i> {{ $blog->created_at->translatedFormat('M d, Y') }}
                                </span>
                                <span class="text-muted me-3">
                                    <i class="fas fa-eye me-1"></i> {{ $blog->views_count }} {{ custom_trans('views', 'front') }}
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
                                    <i class="fas fa-share me-1"></i> {{ custom_trans('Share', 'front') }}
                                </button>
                                <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> {{ custom_trans('Back to Blog', 'front') }}
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="blog-sidebar">
                        <!-- Categories -->
                        @if ($categories->count() > 0)
                            <div class="card shadow-sm mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ custom_trans('Categories', 'front') }}</h5>
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
                                <h5 class="mb-0">{{ custom_trans('Recent Posts', 'front') }}</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $recentBlogs = \App\Models\Blog::visible()
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
                                                    class="text-muted">{{ $recentBlog->created_at->translatedFormat('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">{{ custom_trans('No recent posts.', 'front') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Blogs Slider Section -->
    @if ($relatedBlogs->count() > 0)
        @php $isRtl = \App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl'; @endphp
        <section class="related-blogs-section py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="related-blogs-title mb-0">{{ custom_trans('Related Articles', 'front') }}</h3>
                    <div class="d-flex gap-2">
                        <button class="related-blogs-prev related-blogs-nav-btn">
                            <i class="fas fa-chevron-{{ $isRtl ? 'right' : 'left' }}"></i>
                        </button>
                        <button class="related-blogs-next related-blogs-nav-btn">
                            <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}"></i>
                        </button>
                    </div>
                </div>
                <div class="swiper related-blogs-swiper" @if($isRtl) dir="rtl" @endif>
                    <div class="swiper-wrapper">
                        @foreach ($relatedBlogs as $relatedBlog)
                            <div class="swiper-slide">
                                <div class="related-blog-card card shadow-sm h-100">
                                    @if ($relatedBlog->image)
                                        <div class="related-blog-card-img-wrap">
                                            <img src="{{ $relatedBlog->image_url }}" class="card-img-top"
                                                alt="{{ $relatedBlog->getLocalizedTitle() }}">
                                        </div>
                                    @else
                                        <div class="related-blog-card-img-wrap bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-newspaper fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">
                                            {{ Str::limit($relatedBlog->getLocalizedTitle(), 60) }}
                                        </h6>
                                        <p class="card-text text-muted small flex-grow-1">
                                            {{ Str::limit($relatedBlog->getLocalizedExcerpt() ?: strip_tags($relatedBlog->getLocalizedDescription()), 110) }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $relatedBlog->created_at->translatedFormat('M d, Y') }}
                                            </small>
                                            <a href="{{ route('blog.show', $relatedBlog->slug) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                {{ custom_trans('Read More', 'front') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination related-blogs-pagination"></div>
                </div>
            </div>
        </section>
    @endif
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
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('Link copied to clipboard!');
                });
            }
        }

        @if ($relatedBlogs->count() > 0)
        document.addEventListener('DOMContentLoaded', function () {
            new Swiper('.related-blogs-swiper', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: {{ $relatedBlogs->count() > 3 ? 'true' : 'false' }},
                autoplay: {
                    delay: 4500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                pagination: {
                    el: '.related-blogs-pagination',
                    clickable: true,
                },
                navigation: {
                    prevEl: '.related-blogs-prev',
                    nextEl: '.related-blogs-next',
                },
                breakpoints: {
                    576: { slidesPerView: 2, spaceBetween: 20 },
                    992: { slidesPerView: 3, spaceBetween: 24 },
                },
            });
        });
        @endif
    </script>
@endpush
