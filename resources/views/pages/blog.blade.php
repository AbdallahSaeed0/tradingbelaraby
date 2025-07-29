@extends('layouts.app')

@section('title', 'Blog - E-Class')

@section('content')
    <!-- Blog Banner -->
    <section class="blog-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="blog-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="blog-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-3 fw-bold text-white mb-3">Blog</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="blog-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">Home &nbsp;|&nbsp;
                    Blog</span>
            </div>
        </div>
    </section>

    <!-- Blog Cards Section -->
    <section class="blog-cards-section py-5 bg-white">
        <div class="container">
            @if ($blogs->count() > 0)
                <div class="row g-4 justify-content-center">
                    @foreach ($blogs as $blog)
                        <div class="col-md-6 col-lg-4 d-flex">
                            <div class="blog-card bg-white rounded-4 shadow-sm w-100 d-flex flex-column">
                                <div class="blog-img-wrap position-relative overflow-hidden rounded-top-4">
                                    @if ($blog->image)
                                        <img src="{{ $blog->image_url }}" class="blog-img w-100" alt="{{ $blog->title }}">
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
                                        <i class="fa fa-calendar me-1"></i> {{ $blog->created_at->format('d-m-Y') }}
                                        @if ($blog->category)
                                            <span class="ms-2">
                                                <i class="fas fa-folder me-1"></i> {{ $blog->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold mb-2">{{ Str::limit($blog->title, 50) }}</h5>
                                    <p class="mb-3 text-muted flex-grow-1">
                                        {{ Str::limit($blog->excerpt ?: $blog->description, 120) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i> {{ $blog->views_count }} views
                                        </small>
                                        <a href="{{ route('blog.show', $blog->slug) }}" class="btn btn-blog-read">Read More
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
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/blog.css') }}">
@endpush
