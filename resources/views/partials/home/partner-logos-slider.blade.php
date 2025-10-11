<!-- Partner Logos Slider -->
@php
    $partnerLogos = \App\Models\PartnerLogo::active()->ordered()->get();
@endphp

@if ($partnerLogos->count() > 0)
    <section class="partner-logos-section py-5 bg-light">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5">
                <span class="text-primary fw-bold mb-2 d-block fs-11">
                    <i class="fas fa-handshake"></i> {{ __('Our Partners') }}
                </span>
                <h2 class="fw-bold mb-3 fs-25">{{ __('Trusted By Leading Organizations') }}</h2>
                <p class="text-muted">
                    {{ __('We partner with industry leaders to provide you with the best learning experience') }}</p>
            </div>

            <!-- Logos Slider -->
            <div class="swiper partnerLogosSwiper">
                <div class="swiper-wrapper align-items-center">
                    @foreach ($partnerLogos as $logo)
                        <div class="swiper-slide">
                            <div class="partner-logo-card">
                                @if ($logo->link)
                                    <a href="{{ $logo->link }}" target="_blank" rel="noopener"
                                        class="partner-logo-link">
                                        <img src="{{ $logo->logo_url }}" alt="{{ $logo->name }}"
                                            class="partner-logo-img">
                                    </a>
                                @else
                                    <div class="partner-logo-link">
                                        <img src="{{ $logo->logo_url }}" alt="{{ $logo->name }}"
                                            class="partner-logo-img">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <style>
        .partner-logos-section {
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        }

        .partner-logo-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .partner-logo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .partner-logo-link {
            display: block;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .partner-logo-img {
            max-width: 100%;
            max-height: 90px;
            height: auto;
            width: auto;
            filter: grayscale(100%);
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .partner-logo-card:hover .partner-logo-img {
            filter: grayscale(0%);
            opacity: 1;
        }

        .partnerLogosSwiper {
            padding: 20px 0;
        }

        @media (max-width: 768px) {
            .partner-logo-card {
                padding: 20px;
                height: 120px;
            }

            .partner-logo-img {
                max-height: 70px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Swiper('.partnerLogosSwiper', {
                slidesPerView: 2,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 3,
                    },
                    768: {
                        slidesPerView: 4,
                    },
                    1024: {
                        slidesPerView: 5,
                    },
                    1200: {
                        slidesPerView: 6,
                    }
                }
            });
        });
    </script>
@endif
