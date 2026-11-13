@extends('layouts.app')

@section('title', 'Search Results - ' . (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Hero Section -->
    <section class="hero-section py-5 bg-gradient-primary text-white">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="fw-bold mb-3">{{ custom_trans('search_results', 'front') }}</h1>
                    <p class="lead mb-0">
                        {{ custom_trans('search_results_for', 'front') }}: <strong>"{{ $searchQuery }}"</strong>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Results -->
    <section class="search-results py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar: categories -->
                @php
                    $activeCategory = request('category');
                    $sortParam = request('sort');
                    $allCount = $categories->sum('courses_count');
                @endphp
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <aside class="search-cats">
                        <h5 class="search-cats-title">
                            <i class="fas fa-layer-group" aria-hidden="true"></i>{{ custom_trans('categories', 'front') }}
                        </h5>
                        <nav class="search-cats-list" aria-label="{{ custom_trans('categories', 'front') }}">
                            <a href="{{ route('courses.search', array_filter(['q' => $searchQuery, 'sort' => $sortParam])) }}"
                                class="search-cat {{ !$activeCategory ? 'is-active' : '' }}"
                                @if (!$activeCategory) aria-current="page" @endif>
                                <span class="search-cat-name">{{ custom_trans('all_categories', 'front') }}</span>
                                <span class="search-cat-count">{{ $allCount }}</span>
                            </a>
                            @foreach ($categories as $category)
                                @php $isActive = (string) $activeCategory === (string) $category->id; @endphp
                                <a href="{{ route('courses.search', array_filter(['q' => $searchQuery, 'category' => $category->id, 'sort' => $sortParam])) }}"
                                    class="search-cat {{ $isActive ? 'is-active' : '' }} {{ $category->courses_count ? '' : 'is-empty' }}"
                                    @if ($isActive) aria-current="page" @endif>
                                    <span class="search-cat-name">{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}</span>
                                    <span class="search-cat-count">{{ $category->courses_count }}</span>
                                </a>
                            @endforeach
                        </nav>
                    </aside>
                </div>

                <!-- Results -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-0">{{ custom_trans('found_courses', 'front') }}</h4>
                            <small class="text-muted">{{ $courses->total() }} {{ custom_trans('courses_found', 'front') }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <select class="form-select sort-dropdown" aria-label="{{ custom_trans('sort', 'front') }}">
                                @foreach (['newest' => 'newest', 'popular' => 'popular', 'price_low' => 'price_low_to_high', 'price_high' => 'price_high_to_low'] as $val => $labelKey)
                                    <option value="{{ $val }}" {{ ($sortParam ?: 'newest') === $val ? 'selected' : '' }}>{{ custom_trans($labelKey, 'front') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($courses->count() > 0)
                        <div class="row g-4">
                            @foreach ($courses as $course)
                                <div class="col-12 col-md-6 col-xl-4">
                                    @include('partials.courses.category-course-card', ['course' => $course])
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($courses->hasPages())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <nav aria-label="Search results pagination">
                                        {{ $courses->links() }}
                                    </nav>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fa fa-search fa-4x text-muted mb-4"></i>
                            <h3 class="fw-bold mb-3">{{ custom_trans('no_courses_found', 'front') }}</h3>
                            <p class="text-muted mb-4">{{ custom_trans('no_courses_found_message', 'front') }}</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('categories.index') }}" class="btn btn-primary">
                                    <i class="fa fa-th-large me-2"></i>{{ custom_trans('browse_all_courses', 'front') }}
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                    <i class="fa fa-home me-2"></i>{{ custom_trans('go_home', 'front') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .search-cats {
            position: sticky;
            top: 96px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.1rem;
            box-shadow: 0 4px 16px rgba(15, 23, 42, .05);
        }
        .search-cats-title {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin: 0 0 .85rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid #eef2f7;
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e293b;
        }
        .search-cats-title i { color: #ff7043; }
        .search-cats-list { display: flex; flex-direction: column; gap: 4px; }
        .search-cat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: .6rem .75rem;
            border-radius: 10px;
            color: #334155;
            font-weight: 600;
            font-size: .95rem;
            text-decoration: none;
            transition: background-color .2s ease, color .2s ease;
        }
        .search-cat:hover { background: #f1f5f9; color: #17506b; }
        .search-cat.is-active { background: #17506b; color: #fff; }
        .search-cat.is-empty:not(.is-active) { color: #94a3b8; }
        .search-cat-name { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .search-cat-count {
            flex: 0 0 auto;
            min-width: 26px;
            padding: .15rem .5rem;
            border-radius: 999px;
            background: #eef2f7;
            color: #475569;
            font-size: .75rem;
            font-weight: 700;
            text-align: center;
        }
        .search-cat:hover .search-cat-count { background: rgba(23, 80, 107, .1); color: #17506b; }
        .search-cat.is-active .search-cat-count { background: rgba(255, 255, 255, .22); color: #fff; }
        .search-results .sort-dropdown { min-width: 190px; border-radius: 10px; }
        /* Phones/tablets: categories become a sideways-scrolling row of chips */
        @media (max-width: 991.98px) {
            .search-cats { position: static; padding: .75rem; }
            .search-cats-title { margin-bottom: .6rem; padding-bottom: .5rem; font-size: .95rem; }
            .search-cats-list { flex-direction: row; overflow-x: auto; scrollbar-width: none; gap: 6px; }
            .search-cats-list::-webkit-scrollbar { display: none; }
            .search-cat { flex: 0 0 auto; padding: .45rem .75rem; border: 1px solid #e2e8f0; border-radius: 999px; font-size: .88rem; }
            .search-cat.is-active { border-color: #17506b; }
        }
    </style>
@endpush

@push('scripts')
    @include('partials.courses.course-card-actions-script')
    <script>
        // Sort functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sortDropdown = document.querySelector('.sort-dropdown');

            if (sortDropdown) {
                sortDropdown.addEventListener('change', function() {
                    const sortValue = this.value;
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('sort', sortValue);
                    window.location.href = currentUrl.toString();
                });
            }
        });
    </script>
@endpush

