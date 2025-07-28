<!-- Courses Slider Section -->
<section class="courses-section position-relative py-5 bg-white">
    <!-- Background image on the left behind the cards -->
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-03.png" alt="an-img-01"
        class="courses-bg-img d-none d-md-block">
    <div class="container position-relative z-2">
        <!-- Slider controls -->
        <div class="d-flex justify-content-between mb-3">
            <div class="text-start mb-4">
                <span class="text-warning fw-bold mb-2 d-block fs-11">
                    <i class="fas fa-star"></i> {{ __('Featured Courses') }}
                </span>
                <h2 class="fw-bold mb-3 fs-25">{{ __('Featured Courses') }}</h2>
            </div>
            <div class="buts d-flex align-items-center">
                <button class="btn btn-danger me-2 px-4 py-2 rounded-3 swiper-button-prev"></button>
                <button class="btn btn-danger px-4 py-2 rounded-3 swiper-button-next"></button>
            </div>
        </div>
        <!-- Swiper -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @forelse($featuredCourses ?? [] as $course)
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
                                <h5 class="course-title">{{ $course->name }}</h5>
                                <p class="course-desc">{{ Str::limit($course->description, 80) }}</p>
                                <a href="{{ route('courses.show', $course) }}" class="read-more">{{ __('Read More') }}
                                    &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback content when no featured courses -->
                    <div class="swiper-slide">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/course/man-filming-with-professional-camera.jpg"
                                    class="course-img" alt="Sample Course">
                                <span class="badge badge-green">{{ __('Featured') }}</span>
                                <span class="price-badge">
                                    <span class="discounted">$99.99</span>
                                </span>
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="author-avatar"
                                    alt="author">
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title">{{ __('Sample Course') }}</h5>
                                <p class="course-desc">
                                    {{ __('This is a sample course description. Add some featured courses to see them here.') }}
                                </p>
                                <a href="#" class="read-more">{{ __('Read More') }} &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
