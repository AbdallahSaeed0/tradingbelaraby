@php
    // Fetch active instructors from the database
    $instructors = \App\Models\Admin::whereHas('adminType', function ($query) {
        $query->where('name', 'instructor');
    })
        ->where('is_active', true)
        ->get();
@endphp

<!-- Instructor Section -->
<section class="instructor-section position-relative py-5 bg-light">
    <!-- Background image on the left behind the cards -->
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-03.png" alt="an-img-01"
        class="courses-bg-img d-none d-md-block">
    <div class="container position-relative z-2">
        <!-- Slider controls -->
        <div class="d-flex justify-content-between mb-3">
            <div class="mb-4">
                <span class="text-warning fw-bold mb-2 d-block fs-1-1rem">
                    <i class="fas fa-graduation-cap"></i> {{ custom_trans('Instructor', 'front') }}
                </span>
                <h2 class="fw-bold mb-3 fs-2-5rem"> {{ custom_trans('Instructor', 'front') }}</h2>
            </div>
            <div class="buts d-flex align-items-center">
                <button class="btn btn-danger me-2 px-4 py-2 rounded-3 swiper-button-prev"></button>
                <button class="btn btn-danger px-4 py-2 rounded-3 swiper-button-next"></button>
            </div>
        </div>

        @if ($instructors->count() > 0)
            <!-- Swiper -->
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @foreach ($instructors as $instructor)
                        <div class="swiper-slide">
                            <div class="course-card-custom">
                                <div class="course-img-wrap">
                                    <!-- Instructor Cover Image -->
                                    <img src="{{ $instructor->cover_url }}" class="course-img"
                                        alt="{{ $instructor->name }} Cover">
                                    <span class="badge badge-green">{{ custom_trans('Instructor', 'front') }}</span>
                                    <span class="price-badge">
                                        <span class="discounted">{{ $instructor->courses->count() }}</span>
                                        <span class="original">{{ custom_trans('Courses', 'front') }}</span>
                                    </span>
                                    <!-- Instructor Avatar -->
                                    <img src="{{ $instructor->avatar_url }}" class="author-avatar"
                                        alt="{{ $instructor->name }}">
                                    <div class="course-hover-icons">
                                        <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                        <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                    </div>
                                </div>
                                <div class="course-card-body">
                                    <h5 class="course-title">
                                        <a href="{{ route('instructor.show', $instructor->id) }}"
                                            class="text-decoration-none text-dark">
                                            {{ $instructor->name }}
                                        </a>
                                    </h5>
                                    <p class="course-desc">{{ custom_trans('Instructor', 'front') }}</p>
                                    <a href="{{ route('instructor.show', $instructor->id) }}"
                                        class="read-more">{{ custom_trans('View Profile', 'front') }} &rarr;</a>
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                        class="book-icon" alt="book">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Fallback content when no instructors are available -->
            <div class="text-center py-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/course/man-filming-with-professional-camera.jpg"
                                    class="course-img" alt="Photography">
                                <span class="badge badge-green">{{ custom_trans('Bestseller', 'front') }}</span>
                                <span class="price-badge">
                                    <span class="discounted">345.99₹</span>
                                    <span class="original">1037.99₹</span>
                                </span>
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="author-avatar"
                                    alt="author">
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title">{{ custom_trans('Photography', 'front') }}</h5>
                                <p class="course-desc">
                                    {{ custom_trans('This is an all-encompassing guide for making an independent feature le...', 'front') }}
                                </p>
                                <a href="#" class="read-more">{{ custom_trans('Read More', 'front') }} &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/course/beautiful-indian-young-hindu-woman-model-traditional-indian-costume-yellow-saree%20(1).jpg"
                                    class="course-img" alt="Designing">
                                <span class="badge badge-yellow">{{ custom_trans('Trending', 'front') }}</span>
                                <span class="price-badge">
                                    <span class="discounted">1556.99₹</span>
                                    <span class="original">3114.00₹</span>
                                </span>
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" class="author-avatar"
                                    alt="author">
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title">{{ custom_trans('Designing', 'front') }}</h5>
                                <p class="course-desc">
                                    {{ custom_trans('Details of a fashion design course may include: Fundamentals of fashion...', 'front') }}
                                </p>
                                <a href="#" class="read-more">{{ custom_trans('Read More', 'front') }} &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="course-card-custom">
                            <div class="course-img-wrap">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/course/couress-img-3.jpg"
                                    class="course-img" alt="IT & Software">
                                <span class="badge badge-green">{{ custom_trans('Bestseller', 'front') }}</span>
                                <span class="price-badge">
                                    <span class="discounted">1037.99₹</span>
                                    <span class="original">1730.00₹</span>
                                </span>
                                <img src="https://randomuser.me/api/portraits/men/45.jpg" class="author-avatar"
                                    alt="author">
                                <div class="course-hover-icons">
                                    <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                    <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                </div>
                            </div>
                            <div class="course-card-body">
                                <h5 class="course-title">{{ custom_trans('IT & Software', 'front') }}</h5>
                                <p class="course-desc">
                                    {{ custom_trans('Artificial Intelligence is finally here and most of us are already act...', 'front') }}
                                </p>
                                <a href="#" class="read-more">{{ custom_trans('Read More', 'front') }} &rarr;</a>
                                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                    class="book-icon" alt="book">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
