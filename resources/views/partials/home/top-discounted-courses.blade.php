<!-- Top Discounted Courses Section -->
@if ($topDiscountedCourses->count() > 0)
    <section class="courses-section position-relative py-5 bg-light-gray">
        <!-- Background image on the left behind the cards -->
        <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-03.png" alt="an-img-01"
            class="courses-bg-img d-none d-md-block">
        <div class="container position-relative z-2">
            <!-- Slider controls -->
            <div class="d-flex justify-content-between mb-3">
                <div class="mb-4">
                    <span class="text-warning fw-bold mb-2 d-block fs-11">
                        <i class="fas fa-percent"></i> {{ custom_trans('Special Offers', 'front') }}
                    </span>
                    <h2 class="fw-bold mb-3 fs-25">{{ custom_trans('Top Discounted Courses', 'front') }}</h2>
                </div>
                @php
                    $direction = \App\Helpers\TranslationHelper::getFrontendLanguage()->direction ?? 'ltr';
                @endphp
            </div>
            <!-- Swiper -->
            <div class="swiper topDiscountedSwiper">
                <!-- Navigation buttons -->
                <div class="swiper-button-prev swiper-button-prev-discounted"></div>
                <div class="swiper-button-next swiper-button-next-discounted"></div>
                <div class="swiper-wrapper">
                    @foreach ($topDiscountedCourses as $course)
                        <div class="swiper-slide">
                            <div class="course-card-custom">
                                <div class="course-img-wrap">
                                    <img src="{{ $course->image_url }}" class="course-img"
                                        alt="{{ $course->localized_name }}">

                                    @if ($course->is_featured)
                                        <span class="badge badge-green">{{ custom_trans('Featured', 'front') }}</span>
                                    @endif

                                    @if ($course->is_discounted)
                                        <span class="price-badge">
                                            <span class="discounted">{{ $course->formatted_price }}</span>
                                            <span class="original">{{ $course->formatted_original_price }}</span>
                                        </span>
                                    @else
                                        <span class="price-badge">
                                            <span class="discounted">{{ $course->formatted_price }}</span>
                                        </span>
                                    @endif

                                    @if ($course->instructor)
                                        <img src="{{ $course->instructor->avatar ?? 'https://randomuser.me/api/portraits/men/32.jpg' }}"
                                            class="author-avatar" alt="{{ $course->instructor->name }}">
                                    @endif

                                    <div class="course-hover-icons">
                                        <button class="icon-btn wishlist-btn" data-course-id="{{ $course->id }}">
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
                                    <p class="course-desc">{{ Str::limit($course->localized_description, 80) }}</p>
                                    <a href="{{ route('courses.show', $course) }}"
                                        class="read-more">{{ custom_trans('Read More', 'front') }}
                                        &rarr;</a>
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                        class="book-icon" alt="book">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <script>
        // Initialize Top Discounted Courses Swiper
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.topDiscountedSwiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                navigation: {
                    nextEl: '.swiper-button-next-discounted',
                    prevEl: '.swiper-button-prev-discounted',
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
        });
    </script>
@endif
