<!-- Live Meeting Courses Section - BEM, vanilla slider (uses shared home-courses-slider) -->
@if ($liveMeetingCourses->count() > 0)
    <section class="home-courses-slider" id="liveMeetingCoursesSection" aria-labelledby="live-meeting-courses-heading" data-home-courses-slider>
        <div class="home-courses-slider__container">
            <header class="home-courses-slider__header">
                <div class="home-courses-slider__title-row">
                    <span class="home-courses-slider__label">
                        <i class="fas fa-video" aria-hidden="true"></i> {{ custom_trans('Live Sessions', 'front') }}
                    </span>
                    <h2 class="home-courses-slider__title" id="live-meeting-courses-heading">{{ custom_trans('Live Meeting Courses', 'front') }}</h2>
                </div>
            </header>
            <div class="home-courses-slider__slider-wrap">
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--prev" aria-label="{{ custom_trans('Previous', 'front') }}" data-home-courses-prev>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--next" aria-label="{{ custom_trans('Next', 'front') }}" data-home-courses-next>
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
                <div class="home-courses-slider__track" data-home-courses-track role="region" aria-label="{{ custom_trans('Live Meeting Courses', 'front') }}">
                    <div class="home-courses-slider__list">
                        @foreach ($liveMeetingCourses as $course)
                            @include('partials.home.course-card', ['course' => $course])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
