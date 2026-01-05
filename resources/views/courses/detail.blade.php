@extends('layouts.app')

@section('title', ($course->localized_name ?? custom_trans('Course', 'front')) . ' - ' .
    (\App\Models\MainContentSettings::getActive()?->site_name ?? custom_trans('Site Name', 'front')))

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/course-detail.css') }}">
    @endpush

    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/course-detail.css') }}">
    @endpush

@section('content')
    <!-- Banner Section -->
    <section class="course-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="course-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="course-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative py-5 z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">{{ custom_trans('Course detail', 'front') }}</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="course-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home', 'front') }}"
                        class="text-dark text-decoration-none hover-primary">{{ custom_trans('home', 'front') }}</a>
                    &nbsp;|&nbsp;
                    {{ custom_trans('Course details', 'front') }}
                </span>
            </div>
        </div>
    </section>

    <!-- Course Detail Section -->
    <section class="course-detail-section py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <!-- Left Side -->
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4">
                        {{ $course->localized_name }}</h2>
                    <img src="{{ $course->image_url }}" class="img-fluid rounded-4 mb-4"
                        alt="{{ $course->localized_name }}">
                    <div class="what-learn-box p-4 rounded-4 border">
                        <h5 class="fw-bold mb-3">{{ custom_trans('What learn', 'front') }}</h5>
                        @php
                            $localizedLearningObjectives = \App\Helpers\TranslationHelper::getLocalizedArray(
                                $course->what_to_learn,
                                $course->what_to_learn_ar,
                            );
                        @endphp
                        @if (!empty($localizedLearningObjectives) && is_array($localizedLearningObjectives))
                            <div class="row g-3">
                                @foreach (array_chunk($localizedLearningObjectives, 2) as $chunk)
                                    <div class="col-md-6">
                                        @foreach ($chunk as $item)
                                            @if (!empty($item))
                                                <div class="d-flex align-items-start mb-2">
                                                    <span class="learn-check-icon me-2"><i class="fa fa-check"></i></span>
                                                    <span>{{ $item }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p class="mb-0">{{ custom_trans('Not Found', 'front') }}</p>
                            </div>
                        @endif
                    </div>
                    <!-- Meetings Requirements & Description -->
                    <div class="mt-5">
                        <h4 class="fw-bold mb-3">{{ custom_trans('Meetings Requirements', 'front') }}</h4>
                        @if (!empty($course->localized_requirements))
                            <div class="mb-3 text-muted fs-1rem">{{ $course->localized_requirements }}</div>
                        @else
                            <div class="mb-3 text-muted fs-1rem">
                                <i class="fas fa-info-circle me-2"></i>{{ custom_trans('Not Found', 'front') }}
                            </div>
                        @endif
                        <h4 class="fw-bold mb-3">{{ custom_trans('Description', 'front') }}</h4>
                        @if (!empty($course->localized_description))
                            <div class="mb-4" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                {!! $course->localized_description !!}
                            </div>
                        @else
                            <div class="mb-4 text-muted">
                                <i class="fas fa-info-circle me-2"></i>{{ custom_trans('Not Found', 'front') }}
                            </div>
                        @endif
                        <!-- FAQ Section -->
                        <h4 class="fw-bold mb-4">{{ custom_trans('Frequently Asked Questions', 'front') }}</h4>
                        @php
                            $localizedFaq = \App\Helpers\TranslationHelper::getLocalizedArray(
                                $course->faq_course,
                                $course->faq_course_ar,
                            );
                        @endphp
                        @if (!empty($localizedFaq) && is_array($localizedFaq))
                            <section class="faq-section">
                                <div class="container px-0">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="accordion faq-accordion" id="accordionExample">
                                                @foreach ($localizedFaq as $index => $faq)
                                                    @if (!empty($faq['question']) && !empty($faq['answer']))
                                                        <div class="accordion-item mb-3">
                                                            <h2 class="accordion-header">
                                                                <button
                                                                    class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} faq-accordion-header"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#collapse{{ $index }}"
                                                                    {{ $index === 0 ? 'aria-expanded="true"' : '' }}
                                                                    aria-controls="collapse{{ $index }}"
                                                                    @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                                                    <span class="fw-bold">{{ $faq['question'] }}</span>
                                                                    <span class="ms-auto faq-chevron"><i
                                                                            class="fa-solid fa-chevron-down"></i></span>
                                                                </button>
                                                            </h2>
                                                            <div id="collapse{{ $index }}"
                                                                class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                                data-bs-parent="#accordionExample">
                                                                <div class="accordion-body faq-accordion-body"
                                                                    @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                                                    {{ $faq['answer'] }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-question-circle fa-2x mb-2"></i>
                                <p class="mb-0">{{ custom_trans('Not Found', 'front') }}</p>
                            </div>
                        @endif
                        <!-- About Instructor Section -->
                        <section class="about-instructor-section mt-5">
                            <h4 class="fw-bold mb-4">
                                @if ($course->instructors && $course->instructors->count() > 1)
                                    {{ custom_trans('About Instructors', 'front') }}
                                @else
                                    {{ custom_trans('About Instructor', 'front') }}
                                @endif
                            </h4>
                            @if ($course->instructors && $course->instructors->count() > 0)
                                @foreach ($course->instructors as $instructor)
                                    <div
                                        class="row align-items-center g-4 {{ !$loop->last ? 'mb-4 pb-4 border-bottom' : '' }}">
                                        <div class="col-auto">
                                            @if ($instructor->avatar)
                                                <img src="{{ asset('storage/' . $instructor->avatar) }}"
                                                    alt="{{ $instructor->name }}" class="rounded-circle instructor-img">
                                            @else
                                                <div
                                                    class="rounded-circle instructor-img bg-primary text-white d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-user fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col">
                                            <div class="fw-bold fs-5 mb-1 text-primary">
                                                {{ $instructor->name ?? custom_trans('Not Found', 'front') }}</div>
                                            <div class="text-muted mb-2 instructor-subtitle">
                                                {{ $instructor->title ?? custom_trans('Instructor', 'front') }}</div>
                                            <div class="text-muted instructor-desc">
                                                @if ($instructor->getLocalizedAboutMe())
                                                    {!! $instructor->getLocalizedAboutMe() !!}
                                                @else
                                                    {{ custom_trans('No bio available', 'front') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($course->instructor)
                                <!-- Fallback to legacy single instructor -->
                                <div class="row align-items-center g-4">
                                    <div class="col-auto">
                                        @if ($course->instructor->avatar)
                                            <img src="{{ asset('storage/' . $course->instructor->avatar) }}"
                                                alt="{{ $course->instructor->name }}"
                                                class="rounded-circle instructor-img">
                                        @else
                                            <div
                                                class="rounded-circle instructor-img bg-primary text-white d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold fs-5 mb-1 text-primary">
                                            {{ $course->instructor->name ?? custom_trans('Not Found', 'front') }}</div>
                                        <div class="text-muted mb-2 instructor-subtitle">
                                            {{ $course->instructor->title ?? custom_trans('Not Found', 'front') }}</div>
                                        <div class="text-muted instructor-desc">
                                            @if ($course->instructor->getLocalizedAboutMe())
                                                {!! $course->instructor->getLocalizedAboutMe() !!}
                                            @else
                                                {{ custom_trans('No bio available', 'front') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-user-tie fa-2x mb-2"></i>
                                    <p class="mb-0">{{ custom_trans('No instructor assigned', 'front') }}</p>
                                </div>
                            @endif
                        </section>
                        <!-- Student Feedback Section -->
                        <section class="student-feedback-section mt-5">
                            <h4 class="fw-bold mb-4">{{ custom_trans('Student Feedback', 'front') }}</h4>
                            @if ($course->ratings && $course->ratings->count() > 0)
                                <div class="row align-items-center g-4">
                                    <div class="col-auto text-center">
                                        <div class="display-4 fw-bold mb-0 feedback-rating">
                                            {{ number_format($course->average_rating ?? 0, 1) }}</div>
                                        <div class="text-muted feedback-rating-label">
                                            {{ custom_trans('Course Rating', 'front') }}
                                        </div>
                                    </div>
                                    <div class="col">
                                        @for ($i = 5; $i >= 1; $i--)
                                            @php
                                                $ratingCount = $course->ratings->where('rating', $i)->count();
                                                $percentage =
                                                    $course->ratings->count() > 0
                                                        ? ($ratingCount / $course->ratings->count()) * 100
                                                        : 0;
                                            @endphp
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="progress flex-grow-1 me-2 feedback-progress">
                                                    <div class="progress-bar bg-dark" role="progressbar"
                                                        style="width: {{ $percentage }}%;"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                                <span
                                                    class="ms-2 text-muted feedback-percent">{{ round($percentage) }}%</span>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-star fa-2x mb-2"></i>
                                    <p class="mb-0">{{ custom_trans('Not Found', 'front') }}</p>
                                </div>
                            @endif
                        </section>
                        <hr class="my-5">
                        <!-- Reviews Section -->
                        <section class="reviews-section mb-5">
                            <h4 class="fw-bold mb-4">{{ custom_trans('Reviews', 'front') }}</h4>

                            @if ($course->ratings && $course->ratings->count() > 0)
                                <div class="mb-4">
                                    @foreach ($course->ratings->take(5) as $rating)
                                        <div class="mb-3 pb-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div class="fw-semibold">
                                                    {{ $rating->user->name ?? custom_trans('Student', 'front') }}
                                                </div>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fa fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="text-muted small mb-1">
                                                {{ $rating->created_at->format('Y-m-d') }}
                                            </div>
                                            @if ($rating->review)
                                                <div>{{ $rating->review }}</div>
                                            @endif
                                        </div>
                                    @endforeach

                                    @if ($course->ratings->count() > 5)
                                        <div class="text-center">
                                            <a href="#" onclick="event.preventDefault(); window.location.reload();"
                                                class="text-decoration-none">
                                                {{ custom_trans('View all reviews', 'front') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="review-stars-list mb-3">
                                @php $categories = ['learn', 'price', 'value']; @endphp
                                @foreach ($categories as $cat)
                                    <div class="row align-items-center mb-2 gx-3">
                                        <div class="col-auto review-label text-capitalize">
                                            {{ custom_trans($cat, 'front') }}
                                        </div>
                                        <div class="col review-stars">
                                            <div class="star-rating" data-category="{{ $cat }}">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star star-item"
                                                        data-rating="{{ $i }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-3">
                                <label for="reviewText" class="form-label">
                                    {{ custom_trans('Write review:', 'front') }}
                                </label>
                                <textarea class="form-control review-textarea" id="reviewText" rows="3" placeholder=""></textarea>
                            </div>
                            <button class="btn btn-orange px-5 py-2 fw-bold review-submit-btn" type="button">
                                {{ custom_trans('submit', 'front') }}
                            </button>
                        </section>

                        <!-- Q&A Section -->
                        @include('courses.partials.course-qa', ['course' => $course])
                    </div>
                </div>
                <!-- Right Side -->
                <div class="col-lg-4">
                    <div class="course-features-box rounded-4 shadow-sm bg-white mb-4">
                        <div class="course-features-header p-3 rounded-top-4 text-white fw-bold">
                            {{ custom_trans('Course Features', 'front') }}
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <span class="fs-5 text-orange fw-bold">
                                    @if ($course->price > 0)
                                        {{ number_format($course->price, 2) }} SAR
                                        @if ($course->original_price > $course->price)
                                            <span
                                                class="fs-6 text-decoration-line-through text-muted ms-2">{{ number_format($course->original_price, 2) }}
                                                SAR</span>
                                        @endif
                                    @else
                                        {{ custom_trans('free', 'front') }}
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-home me-2 text-orange"></i> <span
                                    class="fw-bold">{{ custom_trans('instructor', 'front') }}:</span>
                                <span class="ms-auto">{{ $course->instructor->name ?? 'Not Found' }}</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-book me-2 text-orange"></i> <span
                                    class="fw-bold">{{ custom_trans('lectures', 'front') }}:</span>
                                <span
                                    class="ms-auto">{{ $course->sections->sum(function ($section) {return $section->lectures->count();}) }}</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-clock me-2 text-orange"></i> <span
                                    class="fw-bold">{{ custom_trans('duration', 'front') }}:</span>
                                <span class="ms-auto">{{ $course->duration ?? 'Not Found' }}</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-user me-2 text-orange"></i> <span
                                    class="fw-bold">{{ custom_trans('enrolled', 'front') }}:</span>
                                <span class="ms-auto">{{ $course->enrolled_students ?? 0 }}</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center"><i
                                    class="fa fa-globe me-2 text-orange"></i> <span
                                    class="fw-bold">{{ custom_trans('language', 'front') }}:</span>
                                <span class="ms-auto">
                                    @if ($course->default_language)
                                        {{ \App\Helpers\MultilingualHelper::getLanguageName($course->default_language) }}
                                    @else
                                        {{ custom_trans('Not Found', 'front') }}
                                    @endif
                                </span>
                            </li>
                        </ul>
                        <div class="p-3">
                            @auth
                                @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                    <a href="{{ route('courses.learn', $course->id) }}"
                                        class="btn btn-orange w-100 fw-bold mb-3">{{ custom_trans('go_to_course', 'front') }}</a>
                                @else
                                    @if ($course->price > 0)
                                        <button class="btn btn-orange w-100 fw-bold mb-3 detail-enroll-btn"
                                            data-course-id="{{ $course->id }}" data-enroll-type="paid">
                                            <i
                                                class="fas fa-graduation-cap me-2"></i>{{ custom_trans('enroll_now', 'front') }}
                                        </button>
                                    @else
                                        <button class="btn btn-orange w-100 fw-bold mb-3 detail-enroll-btn"
                                            data-course-id="{{ $course->id }}" data-enroll-type="free">
                                            <i
                                                class="fas fa-graduation-cap me-2"></i>{{ custom_trans('enroll_now', 'front') }}
                                        </button>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login', 'front') }}" class="btn btn-orange w-100 fw-bold mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>{{ custom_trans('enroll_now', 'front') }}
                                </a>
                            @endauth
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <div class="btn-group share-dropdown">
                                    <button class="icon-btn share-btn" type="button" data-bs-toggle="dropdown"
                                        aria-expanded="false" data-share-url="{{ url()->current() }}"
                                        data-share-title="{{ $course->localized_name }}">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <button class="dropdown-item share-option" type="button"
                                                data-platform="facebook">
                                                <i
                                                    class="fab fa-facebook me-2 text-primary"></i>{{ custom_trans('Share on Facebook', 'front') }}
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item share-option" type="button"
                                                data-platform="twitter">
                                                <i
                                                    class="fab fa-x-twitter me-2"></i>{{ custom_trans('Share on Twitter (X)', 'front') }}
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item share-option" type="button"
                                                data-platform="linkedin">
                                                <i
                                                    class="fab fa-linkedin me-2 text-primary"></i>{{ custom_trans('Share on LinkedIn', 'front') }}
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item share-option" type="button"
                                                data-platform="whatsapp">
                                                <i
                                                    class="fab fa-whatsapp me-2 text-success"></i>{{ custom_trans('Share on WhatsApp', 'front') }}
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <button class="dropdown-item share-copy" type="button">
                                                <i
                                                    class="fa fa-link me-2"></i>{{ custom_trans('Copy course link', 'front') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                @auth
                                    <!-- Wishlist Button -->
                                    <button class="icon-btn wishlist-btn" data-course-id="{{ $course->id }}"
                                        data-in-wishlist="{{ auth()->user()->hasInWishlist($course) ? 'true' : 'false' }}">
                                        <i
                                            class="fa fa-heart {{ auth()->user()->hasInWishlist($course) ? 'text-danger' : '' }}"></i>
                                    </button>
                                @else
                                    <button class="icon-btn" onclick="window.location.href='{{ route('login', 'front') }}'">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <!-- Preview Video Section -->
                    @if ($course->preview_video_embed_url)
                        <div class="course-preview-video rounded-4 shadow-sm bg-white mb-4">
                            <div class="video-header p-3 rounded-top-4 text-white fw-bold">
                                <i class="fa fa-play-circle me-2"></i>{{ custom_trans('Course Preview', 'front') }}
                            </div>
                            <div class="video-container">
                                <div class="ratio ratio-16x9">
                                    <iframe src="{{ $course->preview_video_embed_url }}"
                                        title="{{ custom_trans('Course Preview Video', 'front') }}" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Related Courses Slider Section -->
    @if ($relatedCourses && $relatedCourses->count() > 0)
        <section class="related-courses-section position-relative py-5 bg-light">
            <div class="container position-relative z-2">
                <!-- Slider controls -->
                <div class="d-flex justify-content-between mb-4">
                    <div class="mb-4">
                        <span class="text-warning fw-bold mb-2 d-block fs-11">
                            <i class="fas fa-star"></i> {{ custom_trans('Related Courses', 'front') }}
                        </span>
                        <h2 class="fw-bold mb-3 fs-25">{{ custom_trans('Related Courses', 'front') }}</h2>
                    </div>
                    @php
                        $direction = \App\Helpers\TranslationHelper::getFrontendLanguage()->direction ?? 'ltr';
                    @endphp
                </div>
                <!-- Swiper -->
                <div class="swiper relatedCoursesSwiper">
                    <!-- Navigation buttons -->
                    <div class="swiper-button-prev related-courses-swiper-button-prev"></div>
                    <div class="swiper-button-next related-courses-swiper-button-next"></div>
                    <div class="swiper-wrapper">
                        @foreach ($relatedCourses as $relatedCourse)
                            <div class="swiper-slide">
                                <div class="course-card-custom">
                                    <div class="course-img-wrap">
                                        <img src="{{ $relatedCourse->image_url }}" class="course-img"
                                            alt="{{ $relatedCourse->localized_name }}">

                                        @if ($relatedCourse->is_featured)
                                            <span
                                                class="badge badge-green">{{ custom_trans('Featured', 'front') }}</span>
                                        @endif

                                        @if ($relatedCourse->is_discounted)
                                            <span class="price-badge">
                                                <span class="discounted">{{ $relatedCourse->formatted_price }}</span>
                                                <span
                                                    class="original">{{ $relatedCourse->formatted_original_price }}</span>
                                            </span>
                                        @else
                                            <span class="price-badge">
                                                <span class="discounted">{{ $relatedCourse->formatted_price }}</span>
                                            </span>
                                        @endif

                                        @if ($relatedCourse->instructor)
                                            <img src="{{ $relatedCourse->instructor->avatar ?? 'https://randomuser.me/api/portraits/men/32.jpg' }}"
                                                class="author-avatar" alt="{{ $relatedCourse->instructor->name }}">
                                        @endif

                                        <div class="course-hover-icons">
                                            @auth
                                                <button class="icon-btn wishlist-btn"
                                                    data-course-id="{{ $relatedCourse->id }}">
                                                    <i
                                                        class="fas fa-heart {{ auth()->user()->hasInWishlist($relatedCourse) ? 'text-danger' : '' }}"></i>
                                                </button>
                                            @else
                                                <button class="icon-btn"
                                                    onclick="window.location.href='{{ route('login', 'front') }}'">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            @endauth
                                            <button class="icon-btn">
                                                <i class="fa-regular fa-bell"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="course-card-body">
                                        <h5 class="course-title">
                                            <a href="{{ route('courses.show', $relatedCourse) }}"
                                                class="text-decoration-none text-dark">
                                                {{ $relatedCourse->localized_name }}
                                            </a>
                                        </h5>
                                        <p class="course-desc">
                                            {{ Str::limit($relatedCourse->localized_description, 80) }}</p>
                                        <a href="{{ route('courses.show', $relatedCourse) }}"
                                            class="read-more">{{ custom_trans('Read More', 'front') }}
                                            &rarr;</a>
                                        <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/cou-icon.png"
                                            class="book-icon" alt="book">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Add-to-cart success modal --}}
    <div class="modal fade" id="cartSuccessModal" tabindex="-1" aria-labelledby="cartSuccessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="cartSuccessModalLabel">
                        {{ custom_trans('Course added to cart', 'front') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        {{ custom_trans('You can continue shopping or proceed to checkout to complete your purchase.', 'front') }}
                    </p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        {{ custom_trans('Continue shopping', 'front') }}
                    </button>
                    <a href="{{ route('checkout.index') }}" class="btn btn-orange fw-bold">
                        {{ custom_trans('Go to checkout', 'front') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Interactive Star Rating System + enroll/cart/review handlers
        document.addEventListener('DOMContentLoaded', function() {
            const starRatings = document.querySelectorAll('.star-rating');
            const ratings = {}; // Store ratings for each category

            starRatings.forEach(rating => {
                const category = rating.getAttribute('data-category');
                const stars = rating.querySelectorAll('.star-item');

                // Initialize rating for this category
                ratings[category] = 0;

                stars.forEach((star, index) => {
                    const starRating = index + 1;

                    // Mouse enter event
                    star.addEventListener('mouseenter', function() {
                        highlightStars(stars, starRating);
                        showRatingTooltip(rating, starRating);
                    });

                    // Mouse leave event
                    star.addEventListener('mouseleave', function() {
                        resetStars(stars);
                        updateSelectedStars(stars, ratings[category]);
                        hideRatingTooltip(rating);
                    });

                    // Click event
                    star.addEventListener('click', function() {
                        ratings[category] = starRating;

                        // Add click animation
                        star.classList.add('star-clicked');
                        setTimeout(() => {
                            star.classList.remove('star-clicked');
                        }, 400);

                        updateSelectedStars(stars, starRating);
                        showRatingFeedback(rating, category, starRating);

                        // Store rating in localStorage
                        localStorage.setItem(`rating_${category}`, starRating);
                    });
                });

                // Load saved rating from localStorage
                const savedRating = localStorage.getItem(`rating_${category}`);
                if (savedRating) {
                    ratings[category] = parseInt(savedRating);
                    updateSelectedStars(stars, ratings[category]);
                }
            });

            function highlightStars(stars, rating) {
                stars.forEach((star, index) => {
                    star.classList.remove('star-hover');
                    if (index < rating) {
                        star.classList.add('star-hover');
                    }
                });
            }

            function resetStars(stars) {
                stars.forEach(star => {
                    star.classList.remove('star-hover');
                });
            }

            function updateSelectedStars(stars, rating) {
                stars.forEach((star, index) => {
                    star.classList.remove('star-selected', 'star-filled');
                    if (index < rating) {
                        star.classList.add('star-selected');
                    }
                });
            }

            function showRatingTooltip(ratingContainer, rating) {
                // Remove existing tooltip
                const existingTooltip = ratingContainer.querySelector('.rating-tooltip');
                if (existingTooltip) {
                    existingTooltip.remove();
                }

                // Create new tooltip
                const tooltip = document.createElement('span');
                tooltip.className = 'rating-tooltip';
                tooltip.textContent = `${rating} star${rating > 1 ? 's' : ''}`;
                tooltip.style.cssText = `
                    position: absolute;
                    background: #333;
                    color: #fff;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 0.8rem;
                    top: -30px;
                    left: 50%;
                    transform: translateX(-50%);
                    white-space: nowrap;
                    z-index: 1000;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;

                ratingContainer.style.position = 'relative';
                ratingContainer.appendChild(tooltip);

                // Show tooltip with animation
                setTimeout(() => {
                    tooltip.style.opacity = '1';
                }, 10);
            }

            function hideRatingTooltip(ratingContainer) {
                const tooltip = ratingContainer.querySelector('.rating-tooltip');
                if (tooltip) {
                    tooltip.style.opacity = '0';
                    setTimeout(() => {
                        tooltip.remove();
                    }, 300);
                }
            }

            function showRatingFeedback(ratingContainer, category, rating) {
                // Remove existing feedback
                const existingFeedback = ratingContainer.parentNode.querySelector('.rating-display');
                if (existingFeedback) {
                    existingFeedback.remove();
                }

                // Create feedback display
                const feedback = document.createElement('span');
                feedback.className = 'rating-display show';
                feedback.textContent = `${rating}/5`;

                ratingContainer.parentNode.appendChild(feedback);

                // Show success message
                showToast(`${category.charAt(0).toUpperCase() + category.slice(1)} rated: ${rating} stars`,
                    'success');
            }

            function showToast(message, type = 'success') {
                // Remove existing toasts
                const existingToasts = document.querySelectorAll('.rating-toast');
                existingToasts.forEach(toast => toast.remove());

                const toast = document.createElement('div');
                toast.className =
                    `rating-toast alert alert-${type === 'success' ? 'success' : 'info'} position-fixed`;
                toast.style.cssText = `
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    min-width: 250px;
                    opacity: 0;
                    transform: translateX(100%);
                    transition: all 0.3s ease;
                `;
                toast.innerHTML = `
                    <i class="fa fa-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    ${message}
                `;

                document.body.appendChild(toast);

                // Show toast
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateX(0)';
                }, 10);

                // Hide toast after 3 seconds
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        toast.remove();
                    }, 300);
                }, 3000);
            }

            // Submit review functionality
            const reviewSubmitBtn = document.querySelector('.review-submit-btn');
            const reviewTextarea = document.querySelector('.review-textarea');

            if (reviewSubmitBtn) {
                reviewSubmitBtn.addEventListener('click', async function() {
                    const hasAnyRating = (ratings.learn || ratings.price || ratings.value);

                    if (!hasAnyRating) {
                        showToast(
                            '{{ custom_trans('Please select a star rating before submitting your review.', 'front') }}',
                            'info');
                        return;
                    }

                    const reviewText = reviewTextarea ? reviewTextarea.value.trim() : '';

                    try {
                        const response = await fetch(
                            '{{ route('courses.review.store', $course->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    review: reviewText,
                                    content_quality: ratings.learn || null,
                                    value_for_money: ratings.price || null,
                                    course_material: ratings.value || null,
                                }),
                            });

                        const data = await response.json();

                        if (!response.ok || !data.success) {
                            const message = data.message ||
                                '{{ custom_trans('Unable to submit review. Please try again.', 'front') }}';
                            showToast(message, 'info');
                            return;
                        }

                        // Clear local selections for this course
                        ['learn', 'price', 'value'].forEach(cat => {
                            localStorage.removeItem(`rating_${cat}`);
                        });

                        if (reviewTextarea) {
                            reviewTextarea.value = '';
                        }

                        showToast('{{ custom_trans('Review submitted successfully!', 'front') }}',
                            'success');

                        // Optionally, reload to show updated ratings/reviews
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } catch (error) {
                        console.error('Review submit error:', error);
                        showToast(
                            '{{ custom_trans('An unexpected error occurred. Please try again later.', 'front') }}',
                            'info');
                    }
                });
            }

            // Enroll button logic (paid -> cart + modal, free -> direct enroll) - detail page only
            const enrollBtn = document.querySelector('.detail-enroll-btn');
            if (enrollBtn) {
                enrollBtn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const enrollType = this.dataset.enrollType;
                    const courseId = this.dataset.courseId;

                    if (!courseId) {
                        return;
                    }

                    if (enrollType === 'free') {
                        // Direct enrollment via JSON
                        try {
                            const response = await fetch(
                                '{{ route('courses.enroll', $course->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    },
                                    body: JSON.stringify({}),
                                });

                            const data = await response.json();

                            if (data.success) {
                                showToast('{{ custom_trans('Enrolled successfully!', 'front') }}',
                                    'success');
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('courses.learn', $course->id) }}';
                                }, 1000);
                            } else {
                                showToast(data.message ||
                                    '{{ custom_trans('Unable to enroll in this course.', 'front') }}',
                                    'info');
                            }
                        } catch (error) {
                            console.error('Free enroll error:', error);
                            showToast(
                                '{{ custom_trans('An unexpected error occurred. Please try again later.', 'front') }}',
                                'info');
                        }

                        return;
                    }

                    // Paid course: add to cart via AJAX and show modal
                    try {
                        console.log('Adding paid course to cart, course ID:', courseId);

                        const response = await fetch('{{ route('cart.add', $course->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({}),
                        });

                        console.log('Cart add response status:', response.status);
                        const data = await response.json();
                        console.log('Cart add response data:', data);

                        if (!data.success) {
                            showToast(data.message ||
                                '{{ custom_trans('Unable to add course to cart.', 'front') }}',
                                'info');
                            return;
                        }

                        // Show modal
                        const modalElement = document.getElementById('cartSuccessModal');
                        console.log('Modal element found:', !!modalElement);

                        if (modalElement && typeof bootstrap !== 'undefined') {
                            const modal = new bootstrap.Modal(modalElement);
                            modal.show();
                            console.log('Modal shown successfully');
                        } else {
                            // Fallback if Bootstrap JS not available
                            console.log('Bootstrap not available or modal not found, showing toast');
                            showToast(
                                '{{ custom_trans('Course added to cart successfully.', 'front') }}',
                                'success');
                        }
                    } catch (error) {
                        console.error('Add to cart error:', error);
                        showToast(
                            '{{ custom_trans('An unexpected error occurred. Please try again later.', 'front') }}',
                            'info');
                    }
                });
            }

            // Share functionality
            const shareDropdowns = document.querySelectorAll('.share-dropdown');
            shareDropdowns.forEach(dropdown => {
                const trigger = dropdown.querySelector('.share-btn');
                if (!trigger) {
                    return;
                }

                const shareUrl = trigger.dataset.shareUrl || window.location.href;
                const shareTitle = trigger.dataset.shareTitle || document.title;

                dropdown.querySelectorAll('.share-option').forEach(option => {
                    option.addEventListener('click', () => {
                        const platform = option.dataset.platform;
                        const encodedUrl = encodeURIComponent(shareUrl);
                        const encodedTitle = encodeURIComponent(shareTitle);
                        let targetUrl = '';

                        switch (platform) {
                            case 'facebook':
                                targetUrl =
                                    `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
                                break;
                            case 'twitter':
                                targetUrl =
                                    `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`;
                                break;
                            case 'linkedin':
                                targetUrl =
                                    `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
                                break;
                            case 'whatsapp':
                                targetUrl =
                                    `https://api.whatsapp.com/send?text=${encodedTitle}%20${encodedUrl}`;
                                break;
                            default:
                                break;
                        }

                        if (targetUrl) {
                            window.open(targetUrl, '_blank', 'noopener');
                        }
                    });
                });

                const copyButton = dropdown.querySelector('.share-copy');
                if (copyButton) {
                    copyButton.addEventListener('click', async () => {
                        const notify = (type, message) => {
                            if (window.toastr) {
                                toastr[type](message);
                            } else {
                                alert(message);
                            }
                        };

                        try {
                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                await navigator.clipboard.writeText(shareUrl);
                            } else {
                                const tempInput = document.createElement('input');
                                tempInput.value = shareUrl;
                                document.body.appendChild(tempInput);
                                tempInput.select();
                                document.execCommand('copy');
                                document.body.removeChild(tempInput);
                            }
                            notify('success',
                                '{{ custom_trans('Course link copied to clipboard!', 'front') }}'
                            );
                        } catch (error) {
                            console.error('Clipboard error:', error);
                            notify('error',
                                '{{ custom_trans('Unable to copy link. Please try again.', 'front') }}'
                            );
                        }
                    });
                }
            });

            // Initialize Related Courses Swiper
            @if ($relatedCourses && $relatedCourses->count() > 0)
                const relatedCoursesSwiper = new Swiper('.relatedCoursesSwiper', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: {{ $relatedCourses->count() > 3 ? 'true' : 'false' }},
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 25,
                        },
                        1024: {
                            slidesPerView: 4,
                            spaceBetween: 30,
                        },
                    },
                    navigation: {
                        nextEl: '.related-courses-swiper-button-next',
                        prevEl: '.related-courses-swiper-button-prev',
                    },
                });
            @endif
        });
    </script>
@endpush
