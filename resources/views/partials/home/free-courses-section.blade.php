<!-- Free Courses Section - BEM, vanilla slider -->
@if (isset($freeCourses) && $freeCourses->count() > 0)
    <section class="home-courses-slider" id="freeCoursesSection" aria-labelledby="free-courses-heading" data-home-courses-slider>
        <div class="home-courses-slider__container">
            <header class="home-courses-slider__header">
                <div class="home-courses-slider__title-row">
                    <span class="home-courses-slider__label">
                        <i class="fas fa-gift" aria-hidden="true"></i> {{ custom_trans('Learn for Free', 'front') }}
                    </span>
                    <h2 class="home-courses-slider__title" id="free-courses-heading">{{ custom_trans('Free Courses', 'front') }}</h2>
                </div>
            </header>
            <div class="home-courses-slider__slider-wrap">
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--prev" aria-label="{{ custom_trans('Previous', 'front') }}" data-home-courses-prev>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--next" aria-label="{{ custom_trans('Next', 'front') }}" data-home-courses-next>
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
                <div class="home-courses-slider__track" data-home-courses-track role="region" aria-label="{{ custom_trans('Free Courses', 'front') }}">
                    <div class="home-courses-slider__list">
                        @foreach ($freeCourses as $course)
                            @include('partials.home.course-card', ['course' => $course])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
