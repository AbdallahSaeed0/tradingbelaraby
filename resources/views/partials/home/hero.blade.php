<!-- Hero Section - .hero-stage controls height; features in normal flow (no internal scroll) -->
<section class="hero hero-colored hero-slider">
    <div class="hero-stage">
        <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            @forelse($sliders as $slider)
                @php
                    $position = $slider->text_position ?? 'center-left';
                    [$vertical, $horizontal] = explode('-', $position);

                    // Map vertical positions
                    $alignClass = match ($vertical) {
                        'top' => 'align-items-start',
                        'center' => 'align-items-center',
                        'bottom' => 'align-items-end',
                        default => 'align-items-center',
                    };

                    // Map horizontal positions
                    $textAlign = match ($horizontal) {
                        'left' => 'text-start',
                        'center' => 'text-center',
                        'right' => 'text-end',
                        default => 'text-start',
                    };

                    // Map justify content
                    $justifyClass = match ($horizontal) {
                        'left' => 'justify-content-start',
                        'center' => 'justify-content-center',
                        'right' => 'justify-content-end',
                        default => 'justify-content-start',
                    };
                @endphp
                <!-- Slide {{ $loop->iteration }} -->
                @php
                    $slideUrl = $slider->button_url ?? route('courses.index');
                @endphp
                <div class="swiper-slide hero-slide hero-slide-bg" data-bg-image="{{ $slider->background_image_url }}" data-slide-url="{{ $slideUrl }}" @if(!empty($slider->background_position)) data-bg-pos="{{ $slider->background_position }}" @endif>
                    <div class="hero-slide-overlay"></div>
                    <div class="container-fluid h-100">
                        <div class="row {{ $alignClass }} min-vh-75 min-h-520 {{ $justifyClass }} h-100">
                            <div class="col-lg-6 col-md-8 col-sm-10 {{ $textAlign }}">
                                <div class="hero-content">
                                    <span class="hero-welcome d-block mb-2">{{ get_current_language_code() === 'ar' && $slider->welcome_text_ar ? $slider->welcome_text_ar : $slider->welcome_text }}
                                        <span class="hero-underline"></span>
                                    </span>
                                    <h1 class="fw-bold mb-3">
                                        {{ get_current_language_code() === 'ar' && $slider->title_ar ? $slider->title_ar : $slider->title }}
                                    </h1>
                                    <p class="hero-sub mb-4">
                                        {{ get_current_language_code() === 'ar' && $slider->subtitle_ar ? $slider->subtitle_ar : $slider->subtitle }}
                                    </p>
                                    <div class="hero-actions">
                                        @if ($slider->button_text && $slider->button_url)
                                            <a href="{{ $slider->button_url }}" class="btn btn-register-colored btn-lg">
                                                {{ get_current_language_code() === 'ar' && $slider->button_text_ar ? $slider->button_text_ar : $slider->button_text }}
                                            </a>
                                        @endif
                                        <a href="{{ $slideUrl }}" class="btn btn-hero-secondary btn-lg">{{ custom_trans('view_details', 'front') ?? 'اعرف التفاصيل' }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Default Slide (if no sliders in database) -->
                <div class="swiper-slide hero-slide hero-slide-bg"
                    data-bg-image="https://eclass.mediacity.co.in/demo2/public/images/slider/slider_img02.png"
                    data-slide-url="{{ route('courses.index') }}">
                    <div class="hero-slide-overlay"></div>
                    <div class="row align-items-center min-vh-75 min-h-520 h-100">
                        <div class="col-lg-12 container col-md-10 mx-auto text-center text-lg-start">
                            <div class="hero-content">
                                <span class="hero-welcome d-block mb-2">WELCOME TO E-CLASS <span class="hero-underline"></span></span>
                                <h1 class="fw-bold mb-3">Education is the best key success in life</h1>
                                <p class="hero-sub mb-4">Online Courses</p>
                                <div class="hero-actions">
                                    <a href="{{ route('courses.index') }}" class="btn btn-register-colored btn-lg">{{ custom_trans('browse_courses', 'front') ?? 'Browse Courses' }}</a>
                                    <a href="{{ route('courses.index') }}" class="btn btn-hero-secondary btn-lg">{{ custom_trans('view_details', 'front') ?? 'اعرف التفاصيل' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Slider Navigation Buttons (focusable for keyboard) -->
        <div class="swiper-button-next hero-swiper-button-next" role="button" tabindex="0" aria-label="{{ custom_trans('next_slide', 'front') ?? 'Next slide' }}"></div>
        <div class="swiper-button-prev hero-swiper-button-prev" role="button" tabindex="0" aria-label="{{ custom_trans('previous_slide', 'front') ?? 'Previous slide' }}"></div>

        <!-- Slider Pagination -->
        <div class="swiper-pagination hero-swiper-pagination" role="tablist" aria-label="{{ custom_trans('slider_pagination', 'front') ?? 'Slider pagination' }}"></div>
        </div>
    </div>

    <!-- Desktop Hero Features (overlap stage slightly, no gray gap) -->
    <div class="hero-features-wrap d-none d-md-block">
        <div class="container">
            <div class="row hero-features text-center">
        @php
            $heroFeatures = \App\Models\HeroFeature::active()->ordered()->get();
        @endphp
        @forelse($heroFeatures as $heroFeature)
            <div class="col-md-{{ 12 / min(count($heroFeatures), 3) }} mb-3 mb-md-0">
                <div class="hero-feature-box d-flex align-items-center h-100">
                    <i class="{{ $heroFeature->icon }} fa-3x me-4"></i>
                    <div class="text-start">
                        <div class="hero-feature-title fw-bold mb-1">
                            {{ get_current_language_code() === 'ar' && $heroFeature->title_ar ? $heroFeature->title_ar : $heroFeature->title }}
                        </div>
                        <div class="hero-feature-subtitle">
                            {{ get_current_language_code() === 'ar' && $heroFeature->subtitle_ar ? $heroFeature->subtitle_ar : $heroFeature->subtitle }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- Fallback to original hardcoded content -->
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="hero-feature-box d-flex align-items-center h-100">
                    <i class="fas fa-anchor fa-3x me-4"></i>
                    <div class="text-start">
                        <div class="hero-feature-title fw-bold mb-1">Learn Anytime, Anywhere</div>
                        <div class="hero-feature-subtitle">Online Courses for Creative</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <div class="hero-feature-box d-flex align-items-center h-100">
                    <i class="fas fa-bars fa-3x me-4"></i>
                    <div class="text-start">
                        <div class="hero-feature-title fw-bold mb-1">Become a researcher</div>
                        <div class="hero-feature-subtitle">Improve Your Skills Online</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="hero-feature-box d-flex align-items-center h-100">
                    <i class="fas fa-basketball-ball fa-3x me-4"></i>
                    <div class="text-start">
                        <div class="hero-feature-title fw-bold mb-1">Most Popular Courses</div>
                        <div class="hero-feature-subtitle">Learn on your schedule</div>
                    </div>
                </div>
            </div>
        @endforelse
            </div>
        </div>
    </div>

    <!-- Mobile Hero Features (stacked cards) -->
    <div class="hero-features-mobile d-block d-md-none">
        <div class="container py-4">
            <div class="row">
                @php
                    $heroFeaturesMobile = \App\Models\HeroFeature::active()->ordered()->get();
                @endphp
                @forelse($heroFeaturesMobile as $heroFeature)
                    <div class="col-12 {{ $loop->last ? '' : 'mb-3' }}">
                        <div class="hero-feature-box mobile-feature-box d-flex align-items-center">
                            <i class="{{ $heroFeature->icon }} fa-3x me-4"></i>
                            <div class="text-start">
                                <div class="hero-feature-title fw-bold mb-1">
                                    {{ get_current_language_code() === 'ar' && $heroFeature->title_ar ? $heroFeature->title_ar : $heroFeature->title }}
                                </div>
                                <div class="hero-feature-subtitle">
                                    {{ get_current_language_code() === 'ar' && $heroFeature->subtitle_ar ? $heroFeature->subtitle_ar : $heroFeature->subtitle }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 mb-3">
                        <div class="hero-feature-box mobile-feature-box d-flex align-items-center">
                            <i class="fas fa-anchor fa-3x me-4"></i>
                            <div class="text-start">
                                <div class="hero-feature-title fw-bold mb-1">Learn Anytime, Anywhere</div>
                                <div class="hero-feature-subtitle">Online Courses for Creative</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="hero-feature-box mobile-feature-box d-flex align-items-center">
                            <i class="fas fa-bars fa-3x me-4"></i>
                            <div class="text-start">
                                <div class="hero-feature-title fw-bold mb-1">Become a researcher</div>
                                <div class="hero-feature-subtitle">Improve Your Skills Online</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="hero-feature-box mobile-feature-box d-flex align-items-center">
                            <i class="fas fa-basketball-ball fa-3x me-4"></i>
                            <div class="text-start">
                                <div class="hero-feature-title fw-bold mb-1">Most Popular Courses</div>
                                <div class="hero-feature-subtitle">Learn on your schedule</div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" /><!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        /**
         * Apply background-image only to active, prev, and next slides (lazy-load backgrounds).
         * Removes background from other slides to save memory and improve performance.
         * Uses activeIndex so it works correctly with Swiper loop (duplicated slides).
         */
        function applyBgForVisibleSlides(swiper) {
            if (!swiper || !swiper.slides) return;
            var slides = swiper.slides;
            var active = swiper.activeIndex;
            var total = slides.length;
            var prevIdx = (active - 1 + total) % total;
            var nextIdx = (active + 1) % total;
            for (var i = 0; i < total; i++) {
                var slide = slides[i];
                var bgImage = slide.getAttribute && slide.getAttribute('data-bg-image');
                if (i === active || i === prevIdx || i === nextIdx) {
                    if (bgImage) {
                        slide.style.backgroundImage = "url('" + bgImage.replace(/'/g, "\\'") + "')";
                        slide.style.backgroundSize = 'contain';
                        slide.style.backgroundRepeat = 'no-repeat';
                        slide.style.setProperty('--bg-pos', (slide.dataset.bgPos || 'center center'));
                    }
                } else {
                    slide.style.backgroundImage = '';
                    slide.style.removeProperty('--bg-pos');
                }
            }
        }

        var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
        var heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            rtl: isRtl,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },
            speed: 1000,
            navigation: {
                nextEl: '.hero-swiper-button-next',
                prevEl: '.hero-swiper-button-prev',
            },
            pagination: {
                el: '.hero-swiper-pagination',
                clickable: true,
            },
            on: {
                init: function() {
                    applyBgForVisibleSlides(this);
                },
                slideChangeTransitionStart: function() {
                    applyBgForVisibleSlides(this);
                },
            },
        });
    });
</script>
