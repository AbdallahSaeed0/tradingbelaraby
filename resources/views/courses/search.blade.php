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
                <!-- Sidebar Filters -->
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <div class="filter-section p-3 rounded-3 shadow-sm bg-white mb-4">
                        <h5 class="fw-bold mb-3">{{ custom_trans('categories', 'front') }}</h5>
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('courses.search', ['q' => $searchQuery]) }}"
                                    class="filter-link fw-bold text-primary">{{ custom_trans('all_categories', 'front') }}</a>
                            </li>
                            @foreach ($categories as $category)
                                <li><a href="{{ route('courses.search', ['q' => $searchQuery, 'category' => $category->id]) }}"
                                        class="filter-link">{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                        ({{ $category->courses_count }})
                                    </a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Results -->
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-0">{{ custom_trans('found_courses', 'front') }}</h4>
                            <small class="text-muted">{{ $courses->total() }} {{ custom_trans('courses_found', 'front') }}</small>
                        </div>
                        <div class="d-flex gap-2">
                            <select class="form-select sort-dropdown max-w-120">
                                <option>{{ custom_trans('sort', 'front') }}</option>
                                <option value="newest">{{ custom_trans('newest', 'front') }}</option>
                                <option value="popular">{{ custom_trans('popular', 'front') }}</option>
                                <option value="price_low">{{ custom_trans('price_low_to_high', 'front') }}</option>
                                <option value="price_high">{{ custom_trans('price_high_to_low', 'front') }}</option>
                            </select>
                        </div>
                    </div>

                    @if ($courses->count() > 0)
                        <div class="row g-4">
                            @foreach ($courses as $course)
                                <div class="col-12 col-md-6 col-lg-4">
                                    @include('partials.courses.course-card', ['course' => $course])
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if ($courses->hasPages())
                            <div class="row mt-4">
                                <div class="col-12">
                                    <nav aria-label="Search results pagination">
                                        {{ $courses->appends(['q' => $searchQuery])->links() }}
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

@push('scripts')
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

