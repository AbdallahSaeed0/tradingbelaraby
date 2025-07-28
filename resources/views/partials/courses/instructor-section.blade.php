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
    <div class="container position-relative" style="z-index:2;">
        <!-- Slider controls -->
        <div class="d-flex justify-content-between mb-3">
            <div class="text-start mb-4">
                <span class="text-warning fw-bold mb-2 d-block" style="font-size:1.1rem;">
                    <i class="fas fa-graduation-cap"></i> {{ __('Instructor') }}
                </span>
                <h2 class="fw-bold mb-3" style="font-size:2.5rem;"> {{ __('Instructor') }}</h2>
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
                                    <img src="{{ $instructor->avatar_url }}" class="course-img"
                                        alt="{{ $instructor->name }}">
                                    <span class="badge badge-green">{{ __('Instructor') }}</span>
                                    <span class="price-badge">
                                        <span class="discounted">{{ $instructor->courses->count() }}</span>
                                        <span class="original">{{ __('Courses') }}</span>
                                    </span>
                                    <img src="{{ $instructor->avatar_url }}" class="author-avatar"
                                        alt="{{ $instructor->name }}">
                                    <div class="course-hover-icons">
                                        <button class="icon-btn"><i class="fas fa-heart"></i></button>
                                        <button class="icon-btn"><i class="fa-regular fa-bell"></i></button>
                                    </div>
                                </div>
                                <div class="course-card-body">
                                    <h5 class="course-title">{{ $instructor->name }}</h5>
                                    <p class="course-desc">{{ $instructor->email }}</p>
                                    @if ($instructor->phone)
                                        <p class="course-desc">{{ $instructor->phone }}</p>
                                    @endif
                                    <a href="{{ route('instructor.show', $instructor->id) }}"
                                        class="read-more">{{ __('View Profile') }} &rarr;</a>
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
                                <span class="badge badge-green">{{ __('Bestseller') }}</span>
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
                                <h5 class="course-title">{{ __('Photography') }}</h5>
                                <p class="course-desc">
                                    {{ __('This is an all-encompassing guide for making an independent feature le...') }}
                                </p>
                                <a href="#" class="read-more">{{ __('Read More') }} &rarr;</a>
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
                                <span class="badge badge-yellow">{{ __('Trending') }}</span>
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
                                <h5 class="course-title">{{ __('Designing') }}</h5>
                                <p class="course-desc">
                                    {{ __('Details of a fashion design course may include: Fundamentals of fashion...') }}
                                </p>
                                <a href="#" class="read-more">{{ __('Read More') }} &rarr;</a>
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
                                <span class="badge badge-green">{{ __('Bestseller') }}</span>
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
                                <h5 class="course-title">{{ __('IT & Software') }}</h5>
                                <p class="course-desc">
                                    {{ __('Artificial Intelligence is finally here and most of us are already act...') }}
                                </p>
                                <a href="#" class="read-more">{{ __('Read More') }} &rarr;</a>
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
