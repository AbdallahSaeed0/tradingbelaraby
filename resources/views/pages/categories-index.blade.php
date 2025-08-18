@extends('layouts.app')

@section('title', 'All Categories - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Banner Section -->
    <section class="category-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="category-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="category-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-3 fw-bold text-white mb-3">{{ custom_trans('explore_categories') }}</h1>
            <!-- Category counter removed as requested -->
        </div>
    </section>

    <!-- Categories Grid Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-3">{{ custom_trans('all_categories') }}</h2>
                    <p class="text-muted lead">{{ custom_trans('browse_categories_description') }}</p>
                </div>
            </div>

            <div class="row g-4">
                @forelse($categories as $category)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="position-relative">
                                @if ($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                        class="card-img-top" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-primary"
                                        style="height: 200px;">
                                        <i class="fas fa-graduation-cap fa-3x text-white"></i>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75 opacity-0 transition-opacity"
                                    style="transition: opacity 0.3s ease;">
                                    <div class="text-center text-white">
                                        <h4 class="fw-bold mb-2">{{ $category->name }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold mb-3">{{ $category->name }}</h5>
                                <p class="card-text text-muted mb-3 flex-grow-1">
                                    {{ $category->description ?: custom_trans('no_description_available') }}
                                </p>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('categories.show', $category->slug) }}" class="btn btn-primary">
                                        {{ custom_trans('view_courses') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                            <h4 class="fw-bold text-muted mb-2">{{ custom_trans('no_categories_found') }}</h4>
                            <p class="text-muted">{{ custom_trans('no_categories_message') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($categories->hasPages())
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Categories pagination">
                            {{ $categories->links() }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Featured Categories Section -->
    @if ($featuredCategories->count() > 0)
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-8 text-center">
                        <h2 class="fw-bold mb-3">{{ custom_trans('featured_categories') }}</h2>
                        <p class="text-muted">{{ custom_trans('featured_categories_description') }}</p>
                    </div>
                </div>

                <div class="row g-4">
                    @foreach ($featuredCategories as $category)
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="card text-center h-100 shadow-sm border-0">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-3">
                                        <div class="mx-auto d-flex align-items-center justify-content-center bg-primary rounded-circle"
                                            style="width: 80px; height: 80px;">
                                            <i class="fas fa-graduation-cap fa-2x text-white"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title fw-bold mb-2">{{ $category->name }}</h5>
                                    <p class="card-text text-muted small mb-3 flex-grow-1">
                                        {{ Str::limit($category->description, 80) }}</p>
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('categories.show', $category->slug) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            {{ custom_trans('explore') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('styles')
    <style>
        .category-banner {
            min-height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .category-banner-bg {
            object-fit: cover;
            opacity: 0.1;
        }

        .category-banner-overlay {
            background: rgba(0, 0, 0, 0.4);
        }

        /* Hover effects for cards */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        }

        .card:hover .position-absolute {
            opacity: 1 !important;
        }

        .empty-state {
            padding: 3rem 1rem;
        }
    </style>
@endpush
