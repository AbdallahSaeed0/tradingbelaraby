<!-- Courses Slider Section -->
<section class="courses-section position-relative py-5 bg-white">
    <!-- Background image on the left behind the cards -->
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-03.png" alt="an-img-01"
        class="courses-bg-img d-none d-md-block">
    <div class="container position-relative z-2">
        <!-- Slider controls -->
        <div class="d-flex justify-content-between mb-3">
            <div class="mb-4">
                <span class="text-warning fw-bold mb-2 d-block fs-11">
                    <i class="fas fa-star"></i> {{ custom_trans('Our Courses', 'front') }}
                </span>
                <h2 class="fw-bold mb-3 fs-25">{{ custom_trans('Our Courses', 'front') }}</h2>
            </div>
            @php
                $direction = \App\Helpers\TranslationHelper::getFrontendLanguage()->direction ?? 'ltr';
            @endphp
        </div>
        <!-- Swiper -->
        <div class="swiper mySwiper">
            <!-- Navigation buttons -->
            <div class="swiper-button-prev swiper-button-prev-courses"></div>
            <div class="swiper-button-next swiper-button-next-courses"></div>
            <div class="swiper-wrapper">
                @forelse($featuredCourses ?? [] as $course)
                    <div class="swiper-slide">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <a href="{{ route('courses.show', $course) }}" class="d-block text-decoration-none">
                                    <img src="{{ $course->image_url }}" class="course-img"
                                        alt="{{ $course->localized_name }}">
                                </a>

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
                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary btn-sm">{{ custom_trans('Show details', 'front') }}</a>
                                    @auth
                                        @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                            <a href="{{ route('courses.learn', $course->id) }}" class="btn btn-success btn-sm">{{ custom_trans('go_to_course', 'front') }}</a>
                                        @else
                                            <button type="button" class="btn btn-orange btn-sm enroll-btn" data-course-id="{{ $course->id }}" data-enroll-type="{{ $course->price > 0 ? 'paid' : 'free' }}">
                                                {{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}
                                            </button>
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
                @empty
                    <!-- Fallback content when no featured courses -->
                    <div class="swiper-slide">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <a href="#" class="d-block text-decoration-none">
                                    <img src="https://eclass.mediacity.co.in/demo2/public/images/course/man-filming-with-professional-camera.jpg"
                                        class="course-img" alt="Sample Course">
                                </a>
                                <span class="badge badge-green">{{ custom_trans('Featured', 'front') }}</span>
                                <span class="price-badge">
                                    <span class="discounted">99.99 SAR</span>
                                </span>
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title">{{ custom_trans('Sample Course', 'front') }}</h5>
                                <p class="course-desc">
                                    {{ custom_trans('This is a sample course description. Add some featured courses to see them here.', 'front') }}
                                </p>
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
