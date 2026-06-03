@extends('layouts.app')

@section('title', 'أكاديمية تداول بالعربي')
@section('meta_description', 'أكاديمية تداول بالعربي - تعلم الأسواق المالية والتداول الاحترافي')

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
                    <img src="{{ $featuresSplit->background_image_url }}" alt="Decorative" class="features-bg-img" width="600" height="400">
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
                        <img src="{{ $featuresSplit->main_image_url }}" alt="Feature" class="features-main-img" width="500" height="400">
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
        // Home course sliders — infinite loop, RTL-aware, clone-based
        (function () {
            var isRtl = document.documentElement.getAttribute('dir') === 'rtl';

            document.querySelectorAll('[data-home-courses-slider]').forEach(function (wrap) {
                var track   = wrap.querySelector('[data-home-courses-track]');
                var prevBtn = wrap.querySelector('[data-home-courses-prev]');
                var nextBtn = wrap.querySelector('[data-home-courses-next]');
                if (!track || !prevBtn || !nextBtn) return;

                var list = track.querySelector('.home-courses-slider__list');
                if (!list) return;

                var origCards = Array.from(list.children);
                var n = origCards.length;

                // Only 1 item (or 0): hide arrows, no loop needed
                if (n <= 1) {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';
                    return;
                }

                // ── Clone setup ──────────────────────────────────────────────
                // Result: [pre-clones: orig-1..orig-n] [originals] [post-clones: orig-1..orig-n]
                // In RTL flex the visual order is right→left, but scrollLeft math stays the same
                // because we're working with DOM index × step.
                origCards.forEach(function (c) {
                    list.appendChild(c.cloneNode(true));
                });
                origCards.slice().reverse().forEach(function (c) {
                    list.insertBefore(c.cloneNode(true), list.firstChild);
                });
                // children[0..n-1]   = pre-clones  (orig-1 … orig-n in DOM order)
                // children[n..2n-1]  = originals
                // children[2n..3n-1] = post-clones  (orig-1 … orig-n)

                // ── Helpers ──────────────────────────────────────────────────
                function getStep() {
                    var card = list.firstElementChild;
                    if (!card) return 280;
                    var gap = parseFloat(
                        window.getComputedStyle(list).columnGap ||
                        window.getComputedStyle(list).gap
                    ) || 20;
                    return card.offsetWidth + gap;
                }

                // scrollLeft sign: LTR positive, RTL negative (Chrome/FF modern spec)
                function posForIdx(idx) {
                    return isRtl ? -(idx * getStep()) : (idx * getStep());
                }

                function jumpTo(idx) {
                    // Instant (no animation) scroll
                    var pos = posForIdx(idx);
                    track.style.scrollBehavior = 'auto';
                    track.scrollLeft = pos;
                    track.offsetHeight; // force reflow so the browser doesn't batch with next smooth scroll
                    track.style.scrollBehavior = '';
                }

                function smoothTo(idx) {
                    track.scrollTo({ left: posForIdx(idx), behavior: 'smooth' });
                }

                // ── State ────────────────────────────────────────────────────
                var currentIdx  = n;   // start at first original card
                var isAnimating = false;
                var ANIM_MS     = 420; // must be ≥ CSS scroll-behavior duration

                // Initialise position after layout
                setTimeout(function () { jumpTo(n); }, 30);

                // Re-anchor on resize (card width may change)
                window.addEventListener('resize', function () {
                    jumpTo(currentIdx);
                });

                // ── Scroll action ────────────────────────────────────────────
                function slide(direction) {
                    if (isAnimating) return;
                    isAnimating = true;

                    if (direction === 'next') { currentIdx++; }
                    else                      { currentIdx--; }

                    smoothTo(currentIdx);

                    setTimeout(function () {
                        // If we drifted into clone territory, silently snap back
                        if (currentIdx >= n * 2) {
                            currentIdx -= n;
                            jumpTo(currentIdx);
                        } else if (currentIdx < n) {
                            currentIdx += n;
                            jumpTo(currentIdx);
                        }
                        isAnimating = false;
                    }, ANIM_MS);
                }

                prevBtn.addEventListener('click', function () { slide('prev'); });
                nextBtn.addEventListener('click', function () { slide('next'); });
                prevBtn.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); slide('prev'); }
                });
                nextBtn.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); slide('next'); }
                });
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
