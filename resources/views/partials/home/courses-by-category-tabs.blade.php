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
            <ul class="nav nav-pills justify-content-center mb-4 category-tabs" id="categoryTabs" role="tablist">
                @foreach ($allCategories->take(6) as $index => $category)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                            id="category-{{ $category->id }}-tab" data-bs-toggle="pill"
                            data-bs-target="#category-{{ $category->id }}" type="button" role="tab"
                            aria-controls="category-{{ $category->id }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            @if ($category->icon)
                                <i class="{{ $category->icon }} me-2"></i>
                            @endif
                            {{ $category->localized_name }}
                            <span class="badge ms-2">{{ $category->courses_count }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="categoryTabsContent">
                @foreach ($allCategories->take(6) as $index => $category)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                        id="category-{{ $category->id }}" role="tabpanel"
                        aria-labelledby="category-{{ $category->id }}-tab">

                        @php
                            $categoryCourses = \App\Models\Course::where('category_id', $category->id)
                                ->published()
                                ->with(['category', 'instructor', 'instructors', 'ratings'])
                                ->withCount(['enrollments', 'ratings'])
                                ->orderBy('created_at', 'desc')
                                ->limit(4)
                                ->get();
                        @endphp

                        @if ($categoryCourses->count() > 0)
                            @php
                                $direction = \App\Helpers\TranslationHelper::getFrontendLanguage()->direction ?? 'ltr';
                            @endphp
                            <!-- Swiper for this category -->
                            <div class="swiper categorySwiper-{{ $category->id }}">
                                <!-- Navigation buttons for category slider -->
                                <div class="swiper-button-prev swiper-button-prev-category-{{ $category->id }}"></div>
                                <div class="swiper-button-next swiper-button-next-category-{{ $category->id }}"></div>
                                <div class="swiper-wrapper">
                                    @foreach ($categoryCourses as $course)
                                        <div class="swiper-slide">
                                            <div class="course-card-custom">
                                                <div class="course-img-wrap">
                                                    <a href="{{ route('courses.show', $course) }}" class="d-block text-decoration-none">
                                                        <img src="{{ $course->image_url }}" class="course-img"
                                                            alt="{{ $course->localized_name }}">
                                                    </a>

                                                    @if ($course->is_featured)
                                                        <span
                                                            class="badge badge-green">{{ custom_trans('Featured', 'front') }}</span>
                                                    @endif

                                                    @if ($course->is_discounted)
                                                        <span class="price-badge">
                                                            <span
                                                                class="discounted">{{ $course->formatted_price }}</span>
                                                            <span
                                                                class="original">{{ $course->formatted_original_price }}</span>
                                                        </span>
                                                    @else
                                                        <span class="price-badge">
                                                            <span
                                                                class="discounted">{{ $course->formatted_price }}</span>
                                                        </span>
                                                    @endif


                                                    <div class="course-hover-icons">
                                                        <button class="icon-btn wishlist-btn"
                                                            data-course-id="{{ $course->id }}">
                                                            <i
                                                                class="fas fa-heart {{ auth()->check() && auth()->user()->hasInWishlist($course) ? 'text-danger' : '' }}"></i>
                                                        </button>
                                                        <button class="icon-btn">
                                                            <i class="fa-regular fa-bell"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="course-card-body">
                                                    <h5 class="course-title">
                                                        <a href="{{ route('courses.show', $course) }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $course->localized_name }}
                                                        </a>
                                                    </h5>
                                                    <p class="course-desc">
                                                        {{ Str::limit($course->localized_description, 80) }}
                                                    </p>
                                                    <div class="d-flex gap-2 flex-wrap mb-2">
                                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary btn-sm">{{ custom_trans('Show details', 'front') }}</a>
                                                        @auth
                                                            @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                                                <a href="{{ route('courses.learn', $course->id) }}" class="btn btn-success btn-sm">{{ custom_trans('go_to_course', 'front') }}</a>
                                                            @else
                                                                <button type="button" class="btn btn-orange btn-sm enroll-btn" data-course-id="{{ $course->id }}" data-enroll-type="{{ $course->price > 0 ? 'paid' : 'free' }}">{{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}</button>
                                                            @endif
                                                        @else
                                                            <a href="{{ route('login') }}" class="btn btn-orange btn-sm">{{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}</a>
                                                        @endauth
                                                    </div>
                                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                                        class="book-icon" alt="book">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper for each category
            @foreach ($allCategories->take(6) as $category)
                new Swiper('.categorySwiper-{{ $category->id }}', {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    loop: false,
                    navigation: {
                        nextEl: '.swiper-button-next-category-{{ $category->id }}',
                        prevEl: '.swiper-button-prev-category-{{ $category->id }}',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 2,
                        },
                        1024: {
                            slidesPerView: 3,
                        },
                        1200: {
                            slidesPerView: 4,
                        }
                    }
                });
            @endforeach

            // Re-initialize Swiper when tab is shown (fixes display issues)
            document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(button => {
                button.addEventListener('shown.bs.tab', function(event) {
                    // Get the target category ID
                    const targetId = event.target.getAttribute('data-bs-target').replace(
                        '#category-', '');
                    // Find the swiper instance and update it
                    const swiperElement = document.querySelector('.categorySwiper-' + targetId);
                    if (swiperElement && swiperElement.swiper) {
                        swiperElement.swiper.update();
                    }
                });
            });
        });
    </script>
@endif
