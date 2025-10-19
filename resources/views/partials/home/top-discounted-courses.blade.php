<!-- Top Discounted Courses Section -->
@if ($topDiscountedCourses->count() > 0)
    <section class="courses-section position-relative py-5 bg-light-gray">
        <!-- Background image on the left behind the cards -->
        <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-03.png" alt="an-img-01"
            class="courses-bg-img d-none d-md-block">
        <div class="container position-relative z-2">
            <!-- Slider controls -->
            <div class="d-flex justify-content-between mb-3">
                <div class="text-start mb-4">
                    <span class="text-warning fw-bold mb-2 d-block fs-11">
                        <i class="fas fa-percent"></i> {{ __('Special Offers') }}
                    </span>
                    <h2 class="fw-bold mb-3 fs-25">{{ __('Top Discounted Courses') }}</h2>
                </div>
                <div class="buts d-flex align-items-center">
                    <button class="btn btn-danger me-2 px-4 py-2 rounded-3 swiper-button-prev-discounted"></button>
                    <button class="btn btn-danger px-4 py-2 rounded-3 swiper-button-next-discounted"></button>
                </div>
            </div>
            <!-- Swiper -->
            <div class="swiper topDiscountedSwiper">
                <div class="swiper-wrapper">
                    @foreach ($topDiscountedCourses as $course)
                        <div class="swiper-slide">
                            <div class="course-card-custom">
                                <div class="course-img-wrap">
                                    <img src="{{ $course->image_url }}" class="course-img" alt="{{ $course->name }}">

                                    @if ($course->is_featured)
                                        <span class="badge badge-green">{{ __('Featured') }}</span>
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
                                            {{ $course->name }}
                                        </a>
                                    </h5>
                                    <p class="course-desc">{{ Str::limit($course->description, 80) }}</p>
                                    <a href="{{ route('courses.show', $course) }}"
                                        class="read-more">{{ __('Read More') }}
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
