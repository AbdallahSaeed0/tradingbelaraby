<!-- Hero Section -->
<section class="hero hero-colored">
    <!-- Hero Slider -->
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
                <div class="swiper-slide hero-slide hero-slide-bg" data-bg-image="{{ $slider->background_image_url }}">
                    <div class="hero-slide-overlay"></div>
                    <div class="container-fluid h-100">
                        <div class="row {{ $alignClass }} min-vh-75 min-h-520 {{ $justifyClass }} h-100">
                            <div class="col-lg-6 col-md-8 col-sm-10 {{ $textAlign }}">
                                <span
                                    class="hero-welcome d-block mb-2">{{ get_current_language_code() === 'ar' && $slider->welcome_text_ar ? $slider->welcome_text_ar : $slider->welcome_text }}
                                    <span class="hero-underline"></span></span>
                                <h1 class="fw-bold mb-3">
                                    {{ get_current_language_code() === 'ar' && $slider->title_ar ? $slider->title_ar : $slider->title }}
                                </h1>
                                <p class="hero-sub mb-4">
                                    {{ get_current_language_code() === 'ar' && $slider->subtitle_ar ? $slider->subtitle_ar : $slider->subtitle }}
                                </p>
                                <form
                                    class="search-box search-box-colored d-flex max-w-480 @if ($horizontal == 'center') mx-auto @elseif($horizontal == 'right') ms-auto @endif">
                                    <input type="text" class="form-control rounded-start-pill"
                                        placeholder="{{ get_current_language_code() === 'ar' && $slider->search_placeholder_ar ? $slider->search_placeholder_ar : $slider->search_placeholder }}">
                                    <button type="submit"
                                        class="btn btn-primary rounded-end-pill px-4 btn-orange">{{ get_current_language_code() === 'ar' && $slider->button_text_ar ? $slider->button_text_ar : $slider->button_text }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Default Slide (if no sliders in database) -->
                <div class="swiper-slide hero-slide hero-slide-bg"
                    data-bg-image="https://eclass.mediacity.co.in/demo2/public/images/slider/slider_img02.png">
                    <div class="hero-slide-overlay"></div>
                    <div class="row align-items-center min-vh-75 min-h-520">
                        <div class="col-lg-12 container col-md-10 mx-auto text-center text-lg-start">
                            <span class="hero-welcome d-block mb-2">WELCOME TO E-CLASS <span
                                    class="hero-underline"></span></span>
                            <h1 class="fw-bold mb-3">Education is the best key success in life</h1>
                            <p class="hero-sub mb-4">Online Courses</p>
                            <form class="search-box search-box-colored d-flex mx-auto mx-lg-0 max-w-480">
                                <input type="text" class="form-control rounded-start-pill"
                                    placeholder="Search Courses">
                                <button type="submit"
                                    class="btn btn-primary rounded-end-pill px-4 btn-orange">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Slider Navigation Buttons -->
        <div class="swiper-button-next hero-swiper-button-next"></div>
        <div class="swiper-button-prev hero-swiper-button-prev"></div>

        <!-- Slider Pagination -->
        <div class="swiper-pagination hero-swiper-pagination"></div>
    </div>

    <!-- Desktop Hero Features (hidden on mobile) -->
    <div class="row hero-features text-center mt-5 d-none d-md-flex">
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
</section>

<!-- Mobile Hero Features (visible only on mobile, outside hero section) -->
<section class="mobile-hero-features d-block d-md-none py-4">
    <div class="container">
        <div class="row">
            @php
                $heroFeatures = \App\Models\HeroFeature::active()->ordered()->get();
            @endphp
            @forelse($heroFeatures as $heroFeature)
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
                <!-- Fallback to original hardcoded content -->
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
</section>

<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" /><!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set background images from data-bg-image attribute
        const bgElements = document.querySelectorAll('[data-bg-image]');
        bgElements.forEach(function(element) {
            const bgImage = element.getAttribute('data-bg-image');
            if (bgImage) {
                element.style.backgroundImage = `url('${bgImage}')`;
                element.style.backgroundSize = 'cover';
                element.style.backgroundPosition = 'center center';
                element.style.backgroundRepeat = 'no-repeat';
            }
        });

        const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
        const heroSwiper = new Swiper('.hero-swiper', {
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
        });
    });
</script>
