<!-- Live Meeting Courses Section - Refactored (BEM, vanilla slider, RTL) -->
@if ($liveMeetingCourses->count() > 0)
    @if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
        @push('rtl-styles')
            <link rel="stylesheet" href="{{ asset('css/rtl/components/live-meeting-courses.css') }}">
        @endpush
    @else
        @push('styles')
            <link rel="stylesheet" href="{{ asset('css/rtl/components/live-meeting-courses.css') }}">
        @endpush
    @endif
    <section class="live-meeting-courses-section" id="liveMeetingCoursesSection" aria-labelledby="live-meeting-courses-heading">
        <div class="live-meeting-courses-section__container">
            <header class="live-meeting-courses-section__header">
                <div class="live-meeting-courses-section__title-row">
                    <span class="live-meeting-courses-section__label">
                        <i class="fas fa-video" aria-hidden="true"></i> {{ custom_trans('Live Sessions', 'front') }}
                    </span>
                    <h2 class="live-meeting-courses-section__title" id="live-meeting-courses-heading">{{ custom_trans('Live Meeting Courses', 'front') }}</h2>
                </div>
            </header>

            <div class="live-meeting-courses-section__slider-wrap">
                <button type="button" class="live-meeting-courses-section__arrow live-meeting-courses-section__arrow--prev" aria-label="{{ custom_trans('Previous', 'front') }}" data-live-meeting-prev>
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
                <button type="button" class="live-meeting-courses-section__arrow live-meeting-courses-section__arrow--next" aria-label="{{ custom_trans('Next', 'front') }}" data-live-meeting-next>
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>

                <div class="live-meeting-courses-section__track" id="liveMeetingCoursesTrack" role="region" aria-label="{{ custom_trans('Live Meeting Courses', 'front') }}">
                    <div class="live-meeting-courses-section__list">
                        @foreach ($liveMeetingCourses as $course)
                            <article class="live-meeting-courses-section__card">
                                <div class="live-meeting-courses-section__card-image-wrap">
                                    <a href="{{ route('courses.show', $course) }}" class="live-meeting-courses-section__card-image-link">
                                        <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}" class="live-meeting-courses-section__card-image" width="280" height="170" loading="lazy">
                                    </a>
                                    @if ($course->is_featured)
                                        <span class="live-meeting-courses-section__badge live-meeting-courses-section__badge--featured">{{ custom_trans('Featured', 'front') }}</span>
                                    @endif
                                    <div class="live-meeting-courses-section__card-actions">
                                        <button type="button" class="live-meeting-courses-section__icon-btn wishlist-btn" data-course-id="{{ $course->id }}" aria-label="{{ custom_trans('Wishlist', 'front') }}">
                                            <i class="fas fa-heart {{ auth()->check() && auth()->user()->hasInWishlist($course) ? 'live-meeting-courses-section__icon--active' : '' }}" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="live-meeting-courses-section__icon-btn" aria-label="{{ custom_trans('Notify', 'front') }}">
                                            <i class="fa-regular fa-bell" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="live-meeting-courses-section__card-body">
                                    <h3 class="live-meeting-courses-section__card-title">
                                        <a href="{{ route('courses.show', $course) }}" class="live-meeting-courses-section__card-title-link">{{ $course->localized_name }}</a>
                                    </h3>
                                    <p class="live-meeting-courses-section__card-desc">{{ Str::limit($course->localized_description, 80) }}</p>
                                    <div class="live-meeting-courses-section__card-price">
                                        @if ($course->is_discounted)
                                            <span class="live-meeting-courses-section__price-current">{{ $course->formatted_price }}</span>
                                            <span class="live-meeting-courses-section__price-old" aria-hidden="true">{{ $course->formatted_original_price }}</span>
                                            @if ($course->discount_percentage)
                                                <span class="live-meeting-courses-section__discount-pill">{{ $course->discount_percentage }}% {{ custom_trans('Discount', 'front') }}</span>
                                            @endif
                                        @else
                                            <span class="live-meeting-courses-section__price-current">{{ $course->formatted_price }}</span>
                                        @endif
                                    </div>
                                    <div class="live-meeting-courses-section__card-ctas">
                                        <a href="{{ route('courses.show', $course) }}" class="live-meeting-courses-section__btn live-meeting-courses-section__btn--secondary">{{ custom_trans('Show details', 'front') }}</a>
                                        @auth
                                            @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                                <a href="{{ route('courses.learn', $course->id) }}" class="live-meeting-courses-section__btn live-meeting-courses-section__btn--primary">{{ custom_trans('go_to_course', 'front') }}</a>
                                            @else
                                                <button type="button" class="live-meeting-courses-section__btn live-meeting-courses-section__btn--primary enroll-btn" data-course-id="{{ $course->id }}" data-enroll-type="{{ $course->price > 0 ? 'paid' : 'free' }}">{{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}</button>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="live-meeting-courses-section__btn live-meeting-courses-section__btn--primary">{{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}</a>
                                        @endauth
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        (function() {
            var track = document.getElementById('liveMeetingCoursesTrack');
            var prevBtn = document.querySelector('[data-live-meeting-prev]');
            var nextBtn = document.querySelector('[data-live-meeting-next]');
            if (!track || !prevBtn || !nextBtn) return;
            var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
            var cardWidth = 0;
            function getScrollAmount() {
                if (!cardWidth && track.firstElementChild && track.firstElementChild.firstElementChild) {
                    var card = track.firstElementChild.firstElementChild;
                    var style = window.getComputedStyle(track.firstElementChild);
                    var gap = parseFloat(style.gap) || 0;
                    cardWidth = card.offsetWidth + gap;
                }
                return cardWidth || 280;
            }
            function scrollPrev() {
                var amount = getScrollAmount();
                track.scrollBy({ left: isRtl ? amount : -amount, behavior: 'smooth' });
            }
            function scrollNext() {
                var amount = getScrollAmount();
                track.scrollBy({ left: isRtl ? -amount : amount, behavior: 'smooth' });
            }
            prevBtn.addEventListener('click', scrollPrev);
            nextBtn.addEventListener('click', scrollNext);
            prevBtn.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollPrev(); } });
            nextBtn.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollNext(); } });
        })();
    </script>
    {{--
    Class mapping (old → new):
    .courses-section → .live-meeting-courses-section
    .course-card-custom → .live-meeting-courses-section__card
    .course-img-wrap → .live-meeting-courses-section__card-image-wrap
    .course-img → .live-meeting-courses-section__card-image
    .badge.badge-green → .live-meeting-courses-section__badge--featured
    .price-badge (overlay) → removed; price moved to .live-meeting-courses-section__card-price
    .course-card-body → .live-meeting-courses-section__card-body
    .course-title → .live-meeting-courses-section__card-title
    .course-desc → .live-meeting-courses-section__card-desc
    .course-hover-icons / .icon-btn → .live-meeting-courses-section__card-actions / __icon-btn
    .btn-orange (primary) → .live-meeting-courses-section__btn--primary
    .btn-outline-primary (secondary) → .live-meeting-courses-section__btn--secondary
    Swiper (.liveMeetingSwiper, .swiper-button-*) → vanilla scroll + .live-meeting-courses-section__track, __arrow--prev/--next
    --}}
@endif
