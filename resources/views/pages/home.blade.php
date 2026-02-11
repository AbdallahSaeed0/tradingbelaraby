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

    <!-- Top Discounted Courses Section -->
    @include('partials.home.top-discounted-courses')

    <!-- Subscription Bundles Section -->
    @include('partials.home.subscription-bundles')

    <!-- Live Meeting Courses Section -->
    @include('partials.home.live-meeting-courses')

    <!-- Recent Courses Section -->
    @include('partials.home.recent-courses-section')

    <!-- Courses by Category Tabs Section -->
    @include('partials.home.courses-by-category-tabs')

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

    <!-- Partner Logos Slider -->
    @include('partials.home.partner-logos-slider')

@endsection

@if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
    @push('rtl-styles')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/home.css') }}">
    @endpush
@else
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pages/home.css') }}">
    @endpush
@endif

@push('scripts')
    <script>
        // Home course sliders: vanilla scroll (RTL-aware), no Swiper
        (function() {
            var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
            document.querySelectorAll('[data-home-courses-slider]').forEach(function(wrap) {
                var track = wrap.querySelector('[data-home-courses-track]');
                var prevBtn = wrap.querySelector('[data-home-courses-prev]');
                var nextBtn = wrap.querySelector('[data-home-courses-next]');
                if (!track || !prevBtn || !nextBtn) return;
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
                    track.scrollBy({ left: isRtl ? getScrollAmount() : -getScrollAmount(), behavior: 'smooth' });
                }
                function scrollNext() {
                    track.scrollBy({ left: isRtl ? -getScrollAmount() : getScrollAmount(), behavior: 'smooth' });
                }
                prevBtn.addEventListener('click', scrollPrev);
                nextBtn.addEventListener('click', scrollNext);
                prevBtn.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollPrev(); } });
                nextBtn.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollNext(); } });
            });
        })();

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

            // Initialize read more functionality for testimonials
            initTestimonialReadMore();
            
            // Reinitialize after slider changes (when navigating slides)
            $('.testimonial-slider').on('afterChange', function() {
                initTestimonialReadMore();
            });
        });

        // Read More/Less functionality for testimonial text
        function initTestimonialReadMore() {
            $('.testimonial-text-wrapper').each(function() {
                const $wrapper = $(this);
                const $text = $wrapper.find('.testimonial-text');
                const $btn = $wrapper.find('.btn-read-more');
                const $readMoreText = $btn.find('.read-more-text');
                const $readLessText = $btn.find('.read-less-text');
                
                // Remove any existing event handlers and reset state
                $btn.off('click');
                $text.removeClass('truncated');
                
                // Temporarily remove truncated class to measure full height
                const originalHeight = $text[0].scrollHeight;
                const lineHeight = parseFloat($text.css('line-height')) || parseFloat(window.getComputedStyle($text[0]).lineHeight);
                const maxHeight = lineHeight * 4; // 4 lines
                
                // If text height exceeds 4 lines, enable read more
                if (originalHeight > maxHeight) {
                    $text.addClass('truncated');
                    $btn.removeClass('d-none');
                    $readMoreText.removeClass('d-none');
                    $readLessText.addClass('d-none');
                    
                    // Handle read more/less click
                    $btn.on('click', function(e) {
                        e.preventDefault();
                        
                        if ($text.hasClass('truncated')) {
                            // Expand
                            $text.removeClass('truncated');
                            $readMoreText.addClass('d-none');
                            $readLessText.removeClass('d-none');
                        } else {
                            // Collapse
                            $text.addClass('truncated');
                            $readMoreText.removeClass('d-none');
                            $readLessText.addClass('d-none');
                            
                            // Scroll the text into view if needed
                            $text[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    });
                } else {
                    // Text is short enough, hide the button
                    $btn.addClass('d-none');
                    $text.removeClass('truncated');
                }
            });
        }

        // CTA Video Modal - Use dynamic video URL from database
        var videoModal = document.getElementById('videoModal');
        var youtubeVideo = document.getElementById('youtubeVideo');
        
        @php
            $ctaVideo = \App\Models\CTAVideo::active()->first();
            $videoUrl = '';
            if ($ctaVideo && $ctaVideo->video_url) {
                // Convert various YouTube URL formats to embed format
                $url = $ctaVideo->video_url;
                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
                    $videoId = $matches[1];
                    $videoUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1";
                } else {
                    // If already in embed format or other format, use as is
                    $videoUrl = str_replace('watch?v=', 'embed/', $url);
                    if (strpos($videoUrl, 'embed/') === false && strpos($videoUrl, 'youtu.be/') !== false) {
                        $videoUrl = str_replace('youtu.be/', 'youtube.com/embed/', $videoUrl);
                    }
                    if (strpos($videoUrl, '?') === false) {
                        $videoUrl .= '?autoplay=1';
                    } else {
                        $videoUrl .= '&autoplay=1';
                    }
                }
            }
        @endphp
        
        var videoURL = @json($videoUrl);

        if (videoModal && youtubeVideo) {
            videoModal.addEventListener('show.bs.modal', function() {
                if (videoURL) {
                    youtubeVideo.src = videoURL;
                }
            });
            videoModal.addEventListener('hidden.bs.modal', function() {
                youtubeVideo.src = "";
            });
        }

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
