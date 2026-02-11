@php
    $instructors = \App\Models\Admin::whereHas('adminType', function ($query) {
        $query->where('name', 'instructor');
    })
        ->where('is_active', true)
        ->get();
@endphp

@if ($instructors->count() > 0)
    @if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
        @push('rtl-styles')
            <link rel="stylesheet" href="{{ asset('css/rtl/components/home-courses-slider.css') }}">
        @endpush
    @else
        @push('styles')
            <link rel="stylesheet" href="{{ asset('css/rtl/components/home-courses-slider.css') }}">
        @endpush
    @endif
    <section class="home-courses-slider home-courses-slider--instructors" id="instructorSection" aria-labelledby="instructor-heading" data-home-courses-slider>
        <div class="home-courses-slider__container">
            <header class="home-courses-slider__header">
                <div class="home-courses-slider__title-row">
                    <span class="home-courses-slider__label">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i> {{ custom_trans('Instructor', 'front') }}
                    </span>
                    <h2 class="home-courses-slider__title" id="instructor-heading">{{ custom_trans('Instructor', 'front') }}</h2>
                </div>
            </header>
            <div class="home-courses-slider__slider-wrap">
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--prev" aria-label="{{ custom_trans('Previous', 'front') }}" data-home-courses-prev>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
                <button type="button" class="home-courses-slider__arrow home-courses-slider__arrow--next" aria-label="{{ custom_trans('Next', 'front') }}" data-home-courses-next>
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
                <div class="home-courses-slider__track" data-home-courses-track role="region" aria-label="{{ custom_trans('Instructor', 'front') }}">
                    <div class="home-courses-slider__list">
                        @foreach ($instructors as $instructor)
                            <article class="home-courses-slider__card">
                                <div class="home-courses-slider__card-image-wrap">
                                    <a href="{{ route('instructor.show', $instructor->id) }}" class="home-courses-slider__card-image-link">
                                        <img src="{{ $instructor->cover_url }}" class="home-courses-slider__card-image" alt="{{ $instructor->name }}" width="280" height="170" loading="lazy">
                                    </a>
                                    <span class="home-courses-slider__badge home-courses-slider__badge--featured">{{ custom_trans('Instructor', 'front') }}</span>
                                    <img src="{{ $instructor->avatar_url }}" class="home-courses-slider__card-avatar" alt="{{ $instructor->name }}" width="48" height="48">
                                </div>
                                <div class="home-courses-slider__card-body">
                                    <h3 class="home-courses-slider__card-title">
                                        <a href="{{ route('instructor.show', $instructor->id) }}" class="home-courses-slider__card-title-link">{{ $instructor->name }}</a>
                                    </h3>
                                    <p class="home-courses-slider__card-desc">{{ custom_trans('Instructor', 'front') }}</p>
                                    <div class="home-courses-slider__card-price">
                                        <span class="home-courses-slider__price-current">{{ $instructor->courses->count() }} {{ custom_trans('Courses', 'front') }}</span>
                                    </div>
                                    <div class="home-courses-slider__card-ctas">
                                        <a href="{{ route('instructor.show', $instructor->id) }}" class="home-courses-slider__btn home-courses-slider__btn--primary">{{ custom_trans('View Profile', 'front') }}</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
