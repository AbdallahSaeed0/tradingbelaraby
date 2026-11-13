{{-- Course card used on category pages and search results.
     Expects $course (ideally loaded with withCount('lectures as total_lessons_count') and withAvg('ratings', 'rating')).
     Enroll / add-to-cart buttons are wired by partials.courses.course-card-actions-script. --}}
@php
    $avgRating    = $avgRating ?? ($course->average_rating ?? 0);
    $lessonsCount = $lessonsCount ?? ($course->total_lessons_count ?? 0);
    $isFree       = $isFree ?? ($course->price <= 0);
    $isEnrolled   = $isEnrolled ?? (auth()->check() && auth()->user()->hasCourseAccess($course->id));
    $inWishlist   = $inWishlist ?? (auth()->check() && auth()->user()->hasInWishlist($course));
    $enrolledCnt  = $enrolledCnt ?? ($course->enrolled_students ?? 0);
@endphp
<div class="course-card h-100">

    {{-- IMAGE --}}
    <div class="course-card-img-wrap">
        <a href="{{ route('courses.show', $course->id) }}" class="d-block h-100">
            @if ($course->image)
                <img src="{{ optimized_image_url($course->image, 360, 180) }}"
                    alt="{{ $course->localized_name }}"
                    class="course-card-img" width="360" height="180" loading="lazy"
                    srcset="{{ optimized_image_url($course->image, 360, 180) }} 360w, {{ optimized_image_url($course->image, 720, 360) }} 720w"
                    sizes="(max-width: 768px) 100vw, 360px">
            @else
                <img src="{{ $course->image_url }}"
                    alt="{{ $course->localized_name }}"
                    class="course-card-img" width="360" height="180" loading="lazy">
            @endif
        </a>

        {{-- Hover overlay --}}
        <div class="course-img-overlay">
            <a href="{{ route('courses.show', $course->id) }}" class="overlay-view-btn">
                <i class="fas fa-eye me-2"></i>{{ custom_trans('view_details', 'front') }}
            </a>
        </div>

        {{-- Featured badge --}}
        @if ($course->is_featured)
            <span class="cc-badge cc-badge-featured">
                <i class="fas fa-crown me-1"></i>{{ custom_trans('featured', 'front') }}
            </span>
        @endif

        {{-- Wishlist (floating on image) --}}
        @auth
            <button class="wishlist-btn cc-wishlist-btn"
                data-course-id="{{ $course->id }}"
                data-in-wishlist="{{ $inWishlist ? 'true' : 'false' }}"
                aria-label="Wishlist">
                <i class="fas fa-heart {{ $inWishlist ? 'text-danger' : '' }}"></i>
            </button>
        @endauth
    </div>

    {{-- BODY --}}
    <div class="course-card-body">

        {{-- Title --}}
        <h5 class="cc-title">
            <a href="{{ route('courses.show', $course->id) }}">
                {{ $course->localized_name }}
            </a>
        </h5>

        {{-- Rating + Price --}}
        <div class="cc-rating-row">
            <div class="cc-rating">
                @if ($avgRating > 0)
                    <div class="cc-stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa{{ $i <= $avgRating ? 's' : 'r' }} fa-star"></i>
                        @endfor
                    </div>
                    <span class="cc-rating-val">{{ number_format($avgRating, 1) }}</span>
                @else
                    <span class="cc-new-badge">
                        <i class="fas fa-certificate me-1"></i>{{ custom_trans('new', 'front') }}
                    </span>
                @endif
            </div>

            <div class="cc-price-wrap">
                @if ($isFree)
                    <span class="cc-price cc-price-free">
                        <i class="fas fa-gift"></i>{{ custom_trans('free', 'front') }}
                    </span>
                @else
                    <span class="cc-price cc-price-paid">
                        @if ($course->original_price > $course->price)
                            <del class="cc-price-original">{{ number_format($course->original_price, 0) }}</del>
                        @endif
                        <span class="cc-price-amount">{{ number_format($course->price, 0) }}</span>
                        <span class="cc-price-currency">SAR</span>
                    </span>
                @endif
            </div>
        </div>

        {{-- Meta --}}
        <div class="cc-meta">
            <span class="cc-meta-item">
                <i class="fas fa-book-open"></i>
                {{ $lessonsCount }} {{ custom_trans('lessons', 'front') }}
            </span>
            @if ($enrolledCnt > 0)
                <span class="cc-meta-item">
                    <i class="fas fa-users"></i>
                    {{ number_format($enrolledCnt) }}
                </span>
            @endif
        </div>

        {{-- Actions --}}
        <div class="cc-actions">
            @auth
                @if ($isEnrolled)
                    <a href="{{ route('courses.learn', $course->id) }}"
                        class="cc-btn cc-btn-primary w-100">
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ custom_trans('go_to_course', 'front') }}
                    </a>
                @else
                    <a href="{{ route('courses.show', $course->id) }}"
                        class="cc-btn cc-btn-outline">
                        {{ custom_trans('view_details', 'front') }}
                    </a>
                    <button class="cc-btn cc-btn-primary category-enroll-btn"
                        data-course-id="{{ $course->id }}"
                        data-enroll-type="{{ $isFree ? 'free' : 'paid' }}">
                        <i class="fas fa-{{ $isFree ? 'graduation-cap' : 'shopping-cart' }} me-1"></i>
                        <span>{{ custom_trans($isFree ? 'enroll_now' : 'add_to_cart', 'front') }}</span>
                    </button>
                @endif
            @else
                <a href="{{ route('courses.show', $course->id) }}"
                    class="cc-btn cc-btn-outline">
                    {{ custom_trans('view_details', 'front') }}
                </a>
                <a href="{{ route('register') }}" class="cc-btn cc-btn-primary">
                    <i class="fas fa-graduation-cap me-1"></i>
                    <span>{{ custom_trans('enroll_now', 'front') }}</span>
                </a>
            @endauth
        </div>
    </div>

</div>
