@extends('layouts.app')

@section('title', ($course->localized_name ?? 'Course') . ' - ' .
    (\App\Models\MainContentSettings::getActive()?->site_name ?? 'Site Name'))

@section('content')
    <!-- Banner Section -->
    <section class="course-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="course-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="course-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">Course Detail</h1>
            <div class="d-flex justify-content-center mb-2">
                <span class="course-label px-4 py-2 rounded-pill bg-white text-dark fw-semibold shadow">
                    <a href="{{ route('home', 'front') }}" class="text-dark text-decoration-none hover-primary">Home</a>
                    &nbsp;|&nbsp;
                    Course Details
                </span>
            </div>
            <div class="d-flex justify-content-center">
                @include('partials.language-switcher')
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
                        <h5 class="fw-bold mb-3">What learn</h5>
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
                                <p class="mb-0">Not Found</p>
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
                        <h4 class="fw-bold mb-3">Description</h4>
                        @if (!empty($course->localized_description))
                            <div class="mb-4" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                {!! $course->localized_description !!}
                            </div>
                        @else
                            <div class="mb-4 text-muted">
                                <i class="fas fa-info-circle me-2"></i>Not Found
                            </div>
                        @endif
                        <!-- FAQ Section -->
                        <h4 class="fw-bold mb-4">Frequently Asked Questions</h4>
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
                                <p class="mb-0">Not Found</p>
                            </div>
                        @endif
                        <!-- About Instructor Section -->
                        <section class="about-instructor-section mt-5">
                            <h4 class="fw-bold mb-4">
                                @if ($course->instructors && $course->instructors->count() > 1)
                                    About Instructors
                                @else
                                    About Instructor
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
                                                {{ $instructor->name ?? 'Not Found' }}</div>
                                            <div class="text-muted mb-2 instructor-subtitle">
                                                {{ $instructor->title ?? 'Instructor' }}</div>
                                            <div class="text-muted instructor-desc">
                                                {{ $instructor->bio ?? 'No bio available' }}</div>
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
                                            {{ $course->instructor->name ?? 'Not Found' }}</div>
                                        <div class="text-muted mb-2 instructor-subtitle">
                                            {{ $course->instructor->title ?? 'Not Found' }}</div>
                                        <div class="text-muted instructor-desc">
                                            {{ $course->instructor->bio ?? 'Not Found' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-user-tie fa-2x mb-2"></i>
                                    <p class="mb-0">No instructor assigned</p>
                                </div>
                            @endif
                        </section>
                        <!-- Student Feedback Section -->
                        <section class="student-feedback-section mt-5">
                            <h4 class="fw-bold mb-4">Student Feedback</h4>
                            @if ($course->ratings && $course->ratings->count() > 0)
                                <div class="row align-items-center g-4">
                                    <div class="col-auto text-center">
                                        <div class="display-4 fw-bold mb-0 feedback-rating">
                                            {{ number_format($course->average_rating ?? 0, 1) }}</div>
                                        <div class="text-muted feedback-rating-label">Course Rating</div>
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
                                    <p class="mb-0">Not Found</p>
                                </div>
                            @endif
                        </section>
                        <hr class="my-5">
                        <!-- Reviews Section -->
                        <section class="reviews-section mb-5">
                            <h4 class="fw-bold mb-4">Reviews</h4>
                            <div class="review-stars-list mb-3">
                                @php $categories = ['learn', 'price', 'value']; @endphp
                                @foreach ($categories as $cat)
                                    <div class="row align-items-center mb-2 gx-3">
                                        <div class="col-auto review-label text-capitalize">{{ $cat }}</div>
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
                                <label for="reviewText" class="form-label">Write review:</label>
                                <textarea class="form-control review-textarea" id="reviewText" rows="3" placeholder=""></textarea>
                            </div>
                            <button class="btn btn-orange px-5 py-2 fw-bold review-submit-btn"
                                type="button">Submit</button>
                        </section>

                        <!-- Q&A Section -->
                        @include('courses.partials.course-qa', ['course' => $course])
                    </div>
                </div>
                <!-- Right Side -->
                <div class="col-lg-4">
                    <div class="course-features-box rounded-4 shadow-sm bg-white mb-4">
                        <div class="course-features-header p-3 rounded-top-4 text-white fw-bold">Course Features</div>
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
                                <span class="ms-auto">{{ $course->language ?? 'Not Found' }}</span>
                            </li>
                        </ul>
                        <div class="p-3">
                            @auth
                                @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                                    <a href="{{ route('courses.learn', $course->id) }}"
                                        class="btn btn-orange w-100 fw-bold mb-3">{{ custom_trans('go_to_course', 'front') }}</a>
                                @else
                                    <button class="btn btn-orange w-100 fw-bold mb-3 enroll-btn"
                                        data-course-id="{{ $course->id }}">
                                        <i class="fas fa-graduation-cap me-2"></i>{{ custom_trans('enroll_now', 'front') }}
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login', 'front') }}" class="btn btn-orange w-100 fw-bold mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>{{ custom_trans('enroll_now', 'front') }}
                                </a>
                            @endauth
                            <div class="d-flex justify-content-between">
                                <button class="icon-btn"><i class="fa fa-calendar-alt"></i></button>
                                <button class="icon-btn"><i class="fa fa-share-alt"></i></button>
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
                                <button class="icon-btn"><i class="fa fa-flag"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Video Section -->
                    @if ($course->preview_video_embed_url)
                        <div class="course-preview-video rounded-4 shadow-sm bg-white mb-4">
                            <div class="video-header p-3 rounded-top-4 text-white fw-bold">
                                <i class="fa fa-play-circle me-2"></i>Course Preview
                            </div>
                            <div class="video-container">
                                <div class="ratio ratio-16x9">
                                    <iframe src="{{ $course->preview_video_embed_url }}" title="Course Preview Video"
                                        frameborder="0"
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Interactive Star Rating System (copied from HTML)
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
        });
    </script>
@endpush
