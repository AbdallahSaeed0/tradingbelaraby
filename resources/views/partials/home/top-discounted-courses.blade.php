<!-- Top Discounted Courses Section - BEM, vanilla slider -->
@if ($topDiscountedCourses->count() > 0)
    <section class="home-courses-slider" id="topDiscountedSection" aria-labelledby="top-discounted-heading" data-home-courses-slider>
        <div class="home-courses-slider__container">
            <header class="home-courses-slider__header">
                <div class="home-courses-slider__title-row">
                    <span class="home-courses-slider__label">
                        <i class="fas fa-percent" aria-hidden="true"></i> {{ custom_trans('Special Offers', 'front') }}
                    </span>
                    <h2 class="home-courses-slider__title" id="top-discounted-heading">{{ custom_trans('Top Discounted Courses', 'front') }}</h2>
                </div>
            </header>
            <div class="home-courses-slider__slider-wrap">
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--prev" aria-label="{{ custom_trans('Previous', 'front') }}" data-home-courses-prev>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--next" aria-label="{{ custom_trans('Next', 'front') }}" data-home-courses-next>
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
                <div class="home-courses-slider__track" data-home-courses-track role="region" aria-label="{{ custom_trans('Top Discounted Courses', 'front') }}">
                    <div class="home-courses-slider__list">
                        @foreach ($topDiscountedCourses as $course)
                            @include('partials.home.course-card', ['course' => $course])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
