@extends('layouts.app')

@section('title', 'Blog - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Blog Banner -->
    <section class="blog-banner position-relative d-flex align-items-center justify-content-center">
        <img src="{{ asset('images/breadcrumb-bg.png') }}" alt="Banner" width="1920" height="400"
            class="blog-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="blog-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-3 fw-bold text-white mb-3">{{ custom_trans('Blog', 'front') }}</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="blog-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">{{ custom_trans('home', 'front') }}</a> &nbsp;|&nbsp;
                    {{ custom_trans('Blog', 'front') }}
                </span>
            </div>
        </div>
    </section>

    <!-- Blog Cards Section -->
    <section class="blog-cards-section py-5 bg-white">
        <div class="container">
            <div class="row">
                <!-- Category Filter Sidebar -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="blog-sidebar">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-filter me-2"></i>{{ custom_trans('Categories', 'front') }}
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('blog.index') }}"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !request('category') ? 'active' : '' }}">
                                        <span>
                                            <i class="fas fa-th-large me-2"></i>{{ custom_trans('All Categories', 'front') }}
                                        </span>
                                        <span
                                            class="badge bg-primary rounded-pill">{{ $categories->sum('blogs_count') }}</span>
                                    </a>
                                    @foreach ($categories as $category)
                                        <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ request('category') == $category->slug ? 'active' : '' }}">
                                            <span>
                                                <i
                                                    class="fas fa-folder me-2"></i>{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                            </span>
                                            <span
                                                class="badge bg-secondary rounded-pill">{{ $category->blogs_count }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Search Box -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-search me-2"></i>{{ custom_trans('Search Blogs', 'front') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('blog.index') }}">
                                    @if (request('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search"
                                            placeholder="{{ custom_trans('Search blogs...', 'front') }}" value="{{ request('search') }}">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blog Cards -->
                <div class="col-lg-9 col-md-8">
                    @if ($blogs->count() > 0)
                        <!-- Active Filter Display -->
                        @if (request('category') || request('search'))
                            <div class="mb-4">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="text-muted">{{ custom_trans('Filtered by:', 'front') }}</span>
                                    @if (request('category'))
                                        @php $selectedCategory = $categories->where('slug', request('category'))->first() @endphp
                                        <span class="badge bg-primary">
                                            {{ custom_trans('Category:', 'front') }} {{ $selectedCategory ? \App\Helpers\TranslationHelper::getLocalizedContent($selectedCategory->name, $selectedCategory->name_ar) : request('category') }}
                                            <a href="{{ route('blog.index', ['search' => request('search')]) }}"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    @endif
                                    @if (request('search'))
                                        <span class="badge bg-info">
                                            {{ custom_trans('Search Blogs', 'front') }}: "{{ request('search') }}"
                                            <a href="{{ route('blog.index', ['category' => request('category')]) }}"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    @endif
                                    <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>{{ custom_trans('Clear All', 'front') }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="row g-4">
                            @foreach ($blogs as $blog)
                                <div class="col-md-6 col-xl-4 d-flex">
                                    @php
                                        $blogImgPath = get_current_language_code() === 'ar' && $blog->image_ar ? $blog->image_ar : $blog->image;
                                        $blogCategory = $blog->category ? \App\Helpers\TranslationHelper::getLocalizedContent($blog->category->name, $blog->category->name_ar) : null;
                                    @endphp
                                    <article class="blog-card w-100">
                                        <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-media" tabindex="-1" aria-hidden="true">
                                            @if ($blogImgPath)
                                                <img src="{{ optimized_image_url($blogImgPath, 400, 250) }}" class="blog-img"
                                                    alt="" width="400" height="250" loading="lazy"
                                                    srcset="{{ optimized_image_url($blogImgPath, 400, 250) }} 400w, {{ optimized_image_url($blogImgPath, 800, 500) }} 800w"
                                                    sizes="(max-width: 768px) 100vw, 400px">
                                            @elseif ($blog->getLocalizedImageUrl())
                                                <img src="{{ $blog->getLocalizedImageUrl() }}" class="blog-img" alt="" width="400" height="250" loading="lazy">
                                            @else
                                                <span class="blog-placeholder"><i class="fas fa-newspaper"></i></span>
                                            @endif
                                            @if ($blog->is_featured)
                                                <span class="blog-card-featured"><i class="fas fa-star"></i>{{ custom_trans('Featured', 'front') }}</span>
                                            @endif
                                            @if ($blogCategory)
                                                <span class="blog-card-category">{{ $blogCategory }}</span>
                                            @endif
                                        </a>
                                        <div class="blog-card-body">
                                            <div class="blog-card-meta">
                                                <span><i class="far fa-calendar" aria-hidden="true"></i>{{ $blog->created_at->format('d-m-Y') }}</span>
                                                <span><i class="far fa-eye" aria-hidden="true"></i>{{ $blog->views_count }} {{ custom_trans('views', 'front') }}</span>
                                            </div>
                                            <h3 class="blog-card-title">
                                                <a href="{{ route('blog.show', $blog->slug) }}">{{ $blog->getLocalizedTitle() }}</a>
                                            </h3>
                                            <p class="blog-card-excerpt">
                                                {{ Str::limit($blog->getLocalizedExcerpt() ?: strip_tags($blog->getLocalizedDescription()), 200) }}
                                            </p>
                                            <div class="blog-card-footer">
                                                @if ($blog->author_name)
                                                    <span class="blog-card-author"><i class="far fa-user" aria-hidden="true"></i>{{ $blog->author_name }}</span>
                                                @endif
                                                <a href="{{ route('blog.show', $blog->slug) }}" class="blog-card-more">
                                                    {{ custom_trans('Read More', 'front') }}
                                                    <i class="fas fa-arrow-right blog-card-arrow" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination: first, last and the pages around the current one, so it stays on one row -->
                        @if ($blogs->hasPages())
                            @php
                                $blogs->appends(request()->query());
                                $current = $blogs->currentPage();
                                $last = $blogs->lastPage();
                                // Up to 7 pages: show all. Otherwise first + last + a 5-page run around the current page
                                if ($last <= 7) {
                                    $window = collect(range(1, $last));
                                } else {
                                    $from = max(2, min($current - 2, $last - 5));
                                    $to = min($last - 1, max($current + 2, 6));
                                    $window = collect([1])->merge(range($from, $to))->push($last)->unique()->values();
                                }
                            @endphp
                            <nav class="blog-pager" aria-label="Blog pagination">
                                @if ($blogs->onFirstPage())
                                    <span class="blog-pager-btn is-disabled" aria-hidden="true"><i class="fas fa-chevron-left blog-pager-prev"></i></span>
                                @else
                                    <a class="blog-pager-btn" href="{{ $blogs->previousPageUrl() }}" rel="prev" aria-label="{{ custom_trans('Previous', 'front') }}"><i class="fas fa-chevron-left blog-pager-prev"></i></a>
                                @endif

                                @foreach ($window as $k => $page)
                                    @if ($k > 0 && $page - $window[$k - 1] > 1)
                                        <span class="blog-pager-gap" aria-hidden="true">…</span>
                                    @endif
                                    @if ($page == $current)
                                        <span class="blog-pager-btn is-current" aria-current="page">{{ $page }}</span>
                                    @else
                                        <a class="blog-pager-btn" href="{{ $blogs->url($page) }}">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($blogs->hasMorePages())
                                    <a class="blog-pager-btn" href="{{ $blogs->nextPageUrl() }}" rel="next" aria-label="{{ custom_trans('Next', 'front') }}"><i class="fas fa-chevron-right blog-pager-next"></i></a>
                                @else
                                    <span class="blog-pager-btn is-disabled" aria-hidden="true"><i class="fas fa-chevron-right blog-pager-next"></i></span>
                                @endif
                            </nav>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-newspaper fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted">{{ custom_trans('No blogs found', 'front') }}</h4>
                            <p class="text-muted">{{ custom_trans('Check back later for new blog posts.', 'front') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ versioned_asset('css/rtl/pages/blog.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ versioned_asset('css/pages/blog.css') }}">
    @endpush
@endif
