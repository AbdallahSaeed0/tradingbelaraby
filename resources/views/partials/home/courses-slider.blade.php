<!-- Our Courses Section - BEM, vanilla slider -->
@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/components/home-courses-slider.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/components/home-courses-slider.css') }}">
    @endpush
@endif
<section class="home-courses-slider home-courses-slider--white" id="ourCoursesSection" aria-labelledby="our-courses-heading" data-home-courses-slider>
    <div class="home-courses-slider__container">
        <header class="home-courses-slider__header">
            <div class="home-courses-slider__title-row">
                <span class="home-courses-slider__label">
                    <i class="fas fa-star" aria-hidden="true"></i> {{ custom_trans('Our Courses', 'front') }}
                </span>
                <h2 class="home-courses-slider__title" id="our-courses-heading">{{ custom_trans('Our Courses', 'front') }}</h2>
            </div>
        </header>
        <div class="home-courses-slider__slider-wrap">
            <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--prev" aria-label="{{ custom_trans('Previous', 'front') }}" data-home-courses-prev>
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>
            <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--next" aria-label="{{ custom_trans('Next', 'front') }}" data-home-courses-next>
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="home-courses-slider__track" data-home-courses-track role="region" aria-label="{{ custom_trans('Our Courses', 'front') }}">
                <div class="home-courses-slider__list">
                    @forelse($featuredCourses ?? [] as $course)
                        @include('partials.home.course-card', ['course' => $course])
                    @empty
                        <article class="home-courses-slider__card">
                            <div class="home-courses-slider__card-image-wrap">
                                <a href="#" class="home-courses-slider__card-image-link">
                                    <img src="https://eclass.mediacity.co.in/demo2/public/images/course/man-filming-with-professional-camera.jpg" alt="{{ custom_trans('Sample Course', 'front') }}" class="home-courses-slider__card-image" width="280" height="170" loading="lazy">
                                </a>
                                <span class="home-courses-slider__badge home-courses-slider__badge--featured">{{ custom_trans('Featured', 'front') }}</span>
                            </div>
                            <div class="home-courses-slider__card-body">
                                <h3 class="home-courses-slider__card-title">{{ custom_trans('Sample Course', 'front') }}</h3>
                                <p class="home-courses-slider__card-desc">{{ custom_trans('This is a sample course description. Add some featured courses to see them here.', 'front') }}</p>
                                <div class="home-courses-slider__card-price">
                                    <span class="home-courses-slider__price-current">99.99 SAR</span>
                                </div>
                                <div class="home-courses-slider__card-ctas">
                                    <a href="#" class="home-courses-slider__btn home-courses-slider__btn--secondary">{{ custom_trans('Show details', 'front') }}</a>
                                </div>
                            </div>
                        </article>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
