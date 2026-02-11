{{-- Shared course card for home course sliders (BEM: home-courses-slider__*) --}}
<article class="home-courses-slider__card">
    <div class="home-courses-slider__card-image-wrap">
        <a href="{{ route('courses.show', $course) }}" class="home-courses-slider__card-image-link">
            <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}" class="home-courses-slider__card-image" width="280" height="170" loading="lazy">
        </a>
        @if ($course->is_featured)
            <span class="home-courses-slider__badge home-courses-slider__badge--featured">{{ custom_trans('Featured', 'front') }}</span>
        @endif
        <div class="home-courses-slider__card-actions">
            <button type="button" class="home-courses-slider__icon-btn wishlist-btn" data-course-id="{{ $course->id }}" aria-label="{{ custom_trans('Wishlist', 'front') }}">
                <i class="fas fa-heart {{ auth()->check() && auth()->user()->hasInWishlist($course) ? 'home-courses-slider__icon--active' : '' }}" aria-hidden="true"></i>
            </button>
            <button type="button" class="home-courses-slider__icon-btn" aria-label="{{ custom_trans('Notify', 'front') }}">
                <i class="fa-regular fa-bell" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <div class="home-courses-slider__card-body">
        <h3 class="home-courses-slider__card-title">
            <a href="{{ route('courses.show', $course) }}" class="home-courses-slider__card-title-link">{{ $course->localized_name }}</a>
        </h3>
        <p class="home-courses-slider__card-desc">{{ Str::limit($course->localized_description, 80) }}</p>
        <div class="home-courses-slider__card-price">
            @if ($course->is_discounted)
                <span class="home-courses-slider__price-current">{{ $course->formatted_price }}</span>
                <span class="home-courses-slider__price-old" aria-hidden="true">{{ $course->formatted_original_price }}</span>
                @if ($course->discount_percentage)
                    <span class="home-courses-slider__discount-pill">{{ $course->discount_percentage }}% {{ custom_trans('Discount', 'front') }}</span>
                @endif
            @else
                <span class="home-courses-slider__price-current">{{ $course->formatted_price }}</span>
            @endif
        </div>
        <div class="home-courses-slider__card-ctas">
            <a href="{{ route('courses.show', $course) }}" class="home-courses-slider__btn home-courses-slider__btn--secondary">{{ custom_trans('Show details', 'front') }}</a>
            @auth
                @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                    <a href="{{ route('courses.learn', $course->id) }}" class="home-courses-slider__btn home-courses-slider__btn--primary">{{ custom_trans('go_to_course', 'front') }}</a>
                @else
                    <button type="button" class="home-courses-slider__btn home-courses-slider__btn--primary enroll-btn" data-course-id="{{ $course->id }}" data-enroll-type="{{ $course->price > 0 ? 'paid' : 'free' }}">{{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}</button>
                @endif
            @else
                <a href="{{ route('login') }}" class="home-courses-slider__btn home-courses-slider__btn--primary">{{ $course->price > 0 ? custom_trans('Add to cart', 'front') : custom_trans('enroll_now', 'front') }}</a>
            @endauth
        </div>
    </div>
</article>
