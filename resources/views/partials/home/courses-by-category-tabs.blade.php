<!-- Courses by Category Tabs Section -->
@if ($allCategories->count() > 0)
    <section class="courses-by-category-section py-5 bg-white">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5">
                <span class="text-warning fw-bold mb-2 d-block fs-11">
                    <i class="fas fa-th-large"></i> {{ custom_trans('Explore by Category', 'front') }}
                </span>
                <h2 class="fw-bold mb-3 fs-25">{{ custom_trans('Browse Courses by Category', 'front') }}</h2>
                <p class="text-muted">{{ custom_trans('Find the perfect course for your learning journey', 'front') }}
                </p>
            </div>

            <!-- Category Tabs -->
            <div class="category-tabs-wrap mb-4">
            <ul class="nav nav-pills category-tabs" id="categoryTabs" role="tablist">
                @foreach ($allCategories->take(6) as $index => $category)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                            id="category-{{ $category->id }}-tab" data-bs-toggle="pill"
                            data-bs-target="#category-{{ $category->id }}" type="button" role="tab"
                            aria-controls="category-{{ $category->id }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            @if ($category->icon)
                                <i class="{{ $category->icon }}" aria-hidden="true"></i>
                            @endif
                            <span class="category-tab-name">{{ $category->localized_name }}</span>
                            <span class="category-tab-count">{{ $category->courses_count }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="categoryTabsContent">
                @foreach ($allCategories->take(6) as $index => $category)
                    @php
                        $categoryCourses = \App\Models\Course::where('category_id', $category->id)
                            ->published()
                            ->with(['category', 'instructor', 'instructors', 'ratings'])
                            ->withCount(['enrollments', 'ratings'])
                            ->orderBy('created_at', 'desc')
                            ->limit(8)
                            ->get();
                    @endphp
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                        id="category-{{ $category->id }}" role="tabpanel"
                        aria-labelledby="category-{{ $category->id }}-tab">

                        @if ($categoryCourses->count() > 0)
                            <section class="home-courses-slider home-courses-slider--white" id="category-slider-{{ $category->id }}" data-home-courses-slider>
                                <div class="home-courses-slider__container">
                                    <header class="home-courses-slider__header">
                                        <div class="home-courses-slider__title-row">
                                            <span class="home-courses-slider__label">
                                                <i class="fas fa-th-large" aria-hidden="true"></i> {{ $category->localized_name }}
                                            </span>
                                            <h3 class="home-courses-slider__title">{{ $category->localized_name }} {{ custom_trans('Courses', 'front') }}</h3>
                                        </div>
                                    </header>
                                    <div class="home-courses-slider__slider-wrap">
                                        <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--prev" aria-label="{{ custom_trans('Previous', 'front') }}" data-home-courses-prev>
                                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--next" aria-label="{{ custom_trans('Next', 'front') }}" data-home-courses-next>
                                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                                        </button>
                                        <div class="home-courses-slider__track" data-home-courses-track role="region" aria-label="{{ $category->localized_name }}">
                                            <div class="home-courses-slider__list">
                                                @foreach ($categoryCourses as $course)
                                                    @include('partials.home.course-card', ['course' => $course])
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <div class="text-center mt-4">
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-primary">
                                    {{ custom_trans('View All', 'front') }} {{ $category->localized_name }}
                                    {{ custom_trans('Courses', 'front') }}
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">
                                    {{ custom_trans('No courses available in this category yet.', 'front') }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- View All Categories Button -->
            @if ($allCategories->count() > 6)
                <div class="text-center mt-5">
                    <a href="{{ route('categories.index') }}" class="btn btn-primary btn-lg">
                        {{ custom_trans('View All Categories', 'front') }}
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <script>
        // Start loading a tab's course images as soon as its tab is hovered/focused/touched,
        // so they are ready (or close) by the time the panel is shown.
        (function () {
            var tabs = document.getElementById('categoryTabs');
            if (!tabs) return;
            function warm(e) {
                var btn = e.target.closest && e.target.closest('[data-bs-target]');
                if (!btn || btn.dataset.warmed) return;
                btn.dataset.warmed = '1';
                var pane = document.querySelector(btn.getAttribute('data-bs-target'));
                if (!pane) return;
                pane.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
                    img.loading = 'eager';
                });
            }
            ['pointerenter', 'focusin', 'touchstart'].forEach(function (evt) {
                tabs.addEventListener(evt, warm, { capture: true, passive: true });
            });
        })();
    </script>
@endif
