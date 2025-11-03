@extends('layouts.app')

@section('title', 'Blog - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Blog Banner -->
    <section class="blog-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="blog-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="blog-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-3 fw-bold text-white mb-3">Blog</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="blog-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home') }}" class="text-dark text-decoration-none hover-primary">Home</a> &nbsp;|&nbsp;
                    Blog
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
                                    <i class="fas fa-filter me-2"></i>Categories
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('blog.index') }}"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !request('category') ? 'active' : '' }}">
                                        <span>
                                            <i class="fas fa-th-large me-2"></i>All Categories
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
                                    <i class="fas fa-search me-2"></i>Search Blogs
                                </h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('blog.index') }}">
                                    @if (request('category'))
                                        <input type="hidden" name="category" value="{{ request('category') }}">
                                    @endif
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search"
                                            placeholder="Search blogs..." value="{{ request('search') }}">
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
                                    <span class="text-muted">Filtered by:</span>
                                    @if (request('category'))
                                        @php $selectedCategory = $categories->where('slug', request('category'))->first() @endphp
                                        <span class="badge bg-primary">
                                            Category: {{ $selectedCategory ? \App\Helpers\TranslationHelper::getLocalizedContent($selectedCategory->name, $selectedCategory->name_ar) : request('category') }}
                                            <a href="{{ route('blog.index', ['search' => request('search')]) }}"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    @endif
                                    @if (request('search'))
                                        <span class="badge bg-info">
                                            Search: "{{ request('search') }}"
                                            <a href="{{ route('blog.index', ['category' => request('category')]) }}"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    @endif
                                    <a href="{{ route('blog.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear All
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="row g-4">
                            @foreach ($blogs as $blog)
                                <div class="col-md-6 col-xl-4 d-flex">
                                    <div class="blog-card bg-white rounded-4 shadow-sm w-100 d-flex flex-column">
                                        <div class="blog-img-wrap position-relative overflow-hidden rounded-top-4">
                                            @if ($blog->getLocalizedImageUrl())
                                                <img src="{{ $blog->getLocalizedImageUrl() }}" class="blog-img w-100"
                                                    alt="{{ $blog->getLocalizedTitle() }}">
                                            @else
                                                <div
                                                    class="blog-placeholder d-flex align-items-center justify-content-center bg-light">
                                                    <i class="fas fa-newspaper fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            @if ($blog->is_featured)
                                                <span class="badge bg-warning position-absolute top-0 start-0 m-2">
                                                    <i class="fas fa-star me-1"></i>Featured
                                                </span>
                                            @endif
                                        </div>
                                        <div class="p-4 flex-grow-1 d-flex flex-column">
                                            <div class="mb-2 text-muted small">
                                                <i class="fa fa-calendar me-1"></i>
                                                {{ $blog->created_at->format('d-m-Y') }}
                                                @if ($blog->author_name)
                                                    <span class="ms-2">
                                                        <i class="fas fa-user me-1"></i> {{ $blog->author_name }}
                                                    </span>
                                                @endif
                                                @if ($blog->category)
                                                    <span class="ms-2">
                                                        <i class="fas fa-folder me-1"></i>
                                                        {{ \App\Helpers\TranslationHelper::getLocalizedContent($blog->category->name, $blog->category->name_ar) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h5 class="blog-title fw-bold mb-2">
                                                <a href="{{ route('blog.show', $blog->slug) }}"
                                                    class="text-decoration-none text-dark">
                                                    {{ Str::limit($blog->getLocalizedTitle(), 50) }}
                                                </a>
                                            </h5>
                                            <p class="mb-3 text-muted flex-grow-1">
                                                {{ Str::limit($blog->getLocalizedExcerpt() ?: strip_tags($blog->getLocalizedDescription()), 120) }}
                                            </p>

                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <small class="text-muted">
                                                    <i class="fas fa-eye me-1"></i> {{ $blog->views_count }} views
                                                </small>
                                                <a href="{{ route('blog.show', $blog->slug) }}"
                                                    class="btn btn-blog-read">Read More
                                                    &rarr;</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($blogs->hasPages())
                            <div class="d-flex justify-content-center mt-5">
                                {{ $blogs->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-newspaper fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted">No blogs found</h4>
                            <p class="text-muted">Check back later for new blog posts.</p>
                        </div>
                    @endif
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
