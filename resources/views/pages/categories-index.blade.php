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
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->localized_name }}"
                                        class="card-img-top img-h-200">
                                @else
                                    <div
                                        class="card-img-top d-flex align-items-center justify-content-center bg-primary h-200">
                                        <i class="fas fa-graduation-cap fa-3x text-white"></i>
                                    </div>
                                @endif
                                <div
                                    class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-75 opacity-0 transition-opacity">
                                    <div class="text-center text-white">
                                        <h4 class="fw-bold mb-2">{{ $category->localized_name }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title category-title fw-bold mb-3">
                                    <a href="{{ route('categories.show', $category->slug) }}"
                                        class="text-decoration-none text-dark">
                                        {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted mb-3 flex-grow-1">
                                    {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->description, $category->description_ar) ?: custom_trans('no_description_available') }}
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
                                        <div
                                            class="mx-auto d-flex align-items-center justify-content-center bg-primary rounded-circle w-80 h-80">
                                            <i class="fas fa-graduation-cap fa-2x text-white"></i>
                                        </div>
                                    </div>
                                    <h5 class="card-title category-title fw-bold mb-2">
                                        <a href="{{ route('categories.show', $category->slug) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $category->localized_name }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small mb-3 flex-grow-1">
                                        {{ Str::limit($category->localized_description ?? '', 80) }}</p>
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

@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/categories.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/categories.css') }}">
    @endpush
@endif
