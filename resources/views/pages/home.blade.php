@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    @include('partials.home.hero')

    <!-- Features Section -->
    @include('partials.home.features-section')

    <!-- About Our University Section -->
    @include('partials.home.about-university')

    <!-- Courses Slider Section -->
    @include('partials.home.courses-slider')



    <!-- Featured Categories Section -->
    @include('partials.home.featured-categories')

    <!-- Scholarship Programs Banner Section -->
    @include('partials.home.scholarship-banner')

    <!-- FAQ Section -->
    @include('partials.courses.faq-section')

    <!-- CTA Video Section -->
    @include('partials.home.cta-video')

    <!-- Testimonial Slider Section -->
    @include('partials.home.testimonials')

    <!-- Info Split Section -->
    @include('partials.courses.info-split')

    <!-- Blog & News Section -->
    @include('partials.courses.blog-news')

    <!-- Features Split Section -->
    @php
        $featuresSplit = \App\Models\FeaturesSplit::active()->first();
        $featuresSplitItems = \App\Models\FeaturesSplitItem::active()->ordered()->get();
    @endphp

    @if ($featuresSplit && $featuresSplitItems->count() > 0)
        <section class="features-split-section">
            <div class="features-split-container">
                <!-- Left: Features and background image -->
                @if ($featuresSplit->background_image)
                    <img src="{{ $featuresSplit->background_image_url }}" alt="Decorative" class="features-bg-img">
                @endif
                <div class="features-split-left">
                    <div class="features-content">
                        <h2 class="features-title">{{ $featuresSplit->getDisplayTitle() }}</h2>
                        <p class="features-desc">
                            {{ $featuresSplit->getDisplayDescription() }}
                        </p>
                        <div class="features-list">
                            @foreach ($featuresSplitItems as $item)
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        @if ($item->icon)
                                            <i class="{{ $item->icon }}"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="feature-label">{{ $item->getDisplayTitle() }}</div>
                                        <div class="feature-text">{{ $item->getDisplayDescription() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- Right: Main image -->
                <div class="features-split-right">
                    @if ($featuresSplit->main_image)
                        <img src="{{ $featuresSplit->main_image_url }}" alt="Feature" class="features-main-img">
                    @endif
                </div>
            </div>
        </section>
    @endif



@endsection

@push('scripts')
    <script>
        const swiper = new Swiper('.mySwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                768: {
                    slidesPerView: 2
                },
                992: {
                    slidesPerView: 3
                },
                1200: {
                    slidesPerView: 4
                }
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            grabCursor: true,
        });

        // Slick Testimonial Slider
        $(document).ready(function() {
            $('.testimonial-slider').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                dots: true,
                arrows: false,
                centerMode: false,
                responsive: [{
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        });

        var videoModal = document.getElementById('videoModal');
        var youtubeVideo = document.getElementById('youtubeVideo');
        var videoURL = "https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1";

        videoModal.addEventListener('show.bs.modal', function() {
            youtubeVideo.src = videoURL;
        });
        videoModal.addEventListener('hidden.bs.modal', function() {
            youtubeVideo.src = "";
        });

        // Wishlist functionality
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const courseId = this.dataset.courseId;
                const heartIcon = this.querySelector('i.fas.fa-heart');

                fetch(`/wishlist/toggle/${courseId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.in_wishlist) {
                                heartIcon.classList.add('text-danger');
                            } else {
                                heartIcon.classList.remove('text-danger');
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
@endpush
