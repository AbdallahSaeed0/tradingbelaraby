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
                <div class="swiper-slide hero-slide"
                    style="background-image: url('{{ $slider->background_image_url }}');">
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
                <div class="swiper-slide hero-slide"
                    style="background-image: url('https://eclass.mediacity.co.in/demo2/public/images/slider/slider_img02.png');">
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

        <!-- Slider Navigation -->
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

<style>
    .hero-colored {
        position: relative;
        overflow: hidden;
        padding: 0;
    }

    .hero-swiper {
        width: 100%;
        height: 100vh;
        min-height: 600px;
    }

    .hero-slide {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        display: flex;
        align-items: center;
        color: white;
    }

    .hero-slide-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1;
    }

    .hero-slide .container-fluid {
        position: relative;
        z-index: 2;
        height: 100%;
    }

    .hero-slide .row {
        margin: 0;
        padding: 2rem;
    }

    .hero-slide .row.align-items-start {
        padding-top: 4rem;
    }

    .hero-slide .row.align-items-end {
        padding-bottom: 4rem;
    }

    .hero-slide .row>div {
        padding-left: 2rem;
        padding-right: 2rem;
    }

    .hero-welcome {
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 2px;
        position: relative;
        display: inline-block;
    }

    .hero-underline {
        position: relative;
    }

    .hero-underline::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #ff6b35;
    }

    .hero-slide h1 {
        font-size: 3.5rem;
        line-height: 1.2;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero-sub {
        font-size: 1.5rem;
        opacity: 0.9;
        margin-bottom: 2rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .search-box-colored {
        max-width: 480px;
        width: 100%;
    }

    .search-box-colored .form-control {
        border: none;
        padding: 15px 20px;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        flex: 1;
    }

    .search-box-colored .btn-orange {
        background: #ff6b35;
        border: none;
        padding: 15px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .search-box-colored .btn-orange:hover {
        background: #e55a2b;
        transform: translateY(-2px);
    }

    /* Swiper Navigation Styles */
    .hero-swiper-button-next,
    .hero-swiper-button-prev {
        color: white;
        background: rgba(255, 255, 255, 0.2);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .hero-swiper-button-next:hover,
    .hero-swiper-button-prev:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }

    .hero-swiper-button-next::after,
    .hero-swiper-button-prev::after {
        font-size: 20px;
        font-weight: bold;
    }

    /* Swiper Pagination Styles */
    .hero-swiper-pagination {
        bottom: 30px;
    }

    .hero-swiper-pagination .swiper-pagination-bullet {
        width: 12px;
        height: 12px;
        background: rgba(255, 255, 255, 0.5);
        opacity: 1;
        transition: all 0.3s ease;
    }

    .hero-swiper-pagination .swiper-pagination-bullet-active {
        background: #ff6b35;
        transform: scale(1.2);
    }

    .hero-features {
        margin-top: 3rem;
        z-index: 3;
    }

    .hero-feature-box {
        background: rgba(18, 88, 117, 0.95);
        padding: 2rem;
        border-radius: 15px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        height: 100%;
    }


    .hero-feature-box i {
        color: #ff6b35;
        margin-right: 1rem;
    }

    .hero-feature-title {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .hero-feature-subtitle {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .mobile-hero-features {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .mobile-feature-box {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .mobile-feature-box i {
        color: #667eea;
    }

    .mobile-feature-box .hero-feature-title {
        color: #333;
    }

    .mobile-feature-box .hero-feature-subtitle {
        color: #666;
    }

    .min-vh-75 {
        min-height: 75vh;
    }

    .min-h-520 {
        min-height: 520px;
    }

    .max-w-480 {
        max-width: 480px;
    }

    @media (max-width: 768px) {
        .hero-swiper {
            height: 70vh;
            min-height: 500px;
        }

        .hero-slide h1 {
            font-size: 2.5rem;
        }

        .hero-sub {
            font-size: 1.2rem;
        }

        .hero-slide .row>div {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .search-box-colored {
            flex-direction: column;
            max-width: 100% !important;
        }

        .search-box-colored .form-control,
        .search-box-colored .btn {
            border-radius: 50px;
            margin-bottom: 0.5rem;
            width: 100%;
        }

        .hero-swiper-button-next,
        .hero-swiper-button-prev {
            width: 40px;
            height: 40px;
        }

        .hero-swiper-button-next::after,
        .hero-swiper-button-prev::after {
            font-size: 16px;
        }
    }
</style>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
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
