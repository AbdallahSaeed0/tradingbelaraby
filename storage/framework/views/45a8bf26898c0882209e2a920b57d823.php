<?php $__env->startSection('title', 'E-Class - Online Learning Platform'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero Section -->
    <?php echo $__env->make('partials.home.hero', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Features Section -->
    <?php echo $__env->make('partials.home.features-section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- About Our University Section -->
    <?php echo $__env->make('partials.home.about-university', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Courses Slider Section -->
    <?php echo $__env->make('partials.home.courses-slider', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



    <!-- Featured Categories Section -->
    <?php echo $__env->make('partials.home.featured-categories', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Scholarship Programs Banner Section -->
    <?php echo $__env->make('partials.home.scholarship-banner', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- FAQ Section -->
    <?php echo $__env->make('partials.courses.faq-section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- CTA Video Section -->
    <?php echo $__env->make('partials.home.cta-video', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Testimonial Slider Section -->
    <?php echo $__env->make('partials.home.testimonials', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Info Split Section -->
    <?php echo $__env->make('partials.courses.info-split', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Blog & News Section -->
    <?php echo $__env->make('partials.courses.blog-news', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Features Split Section -->
    <?php
        $featuresSplit = \App\Models\FeaturesSplit::active()->first();
        $featuresSplitItems = \App\Models\FeaturesSplitItem::active()->ordered()->get();
    ?>

    <?php if($featuresSplit && $featuresSplitItems->count() > 0): ?>
        <section class="features-split-section">
            <div class="features-split-container">
                <!-- Left: Features and background image -->
                <?php if($featuresSplit->background_image): ?>
                    <img src="<?php echo e($featuresSplit->background_image_url); ?>" alt="Decorative" class="features-bg-img">
                <?php endif; ?>
                <div class="features-split-left">
                    <div class="features-content">
                        <h2 class="features-title"><?php echo e($featuresSplit->getDisplayTitle()); ?></h2>
                        <p class="features-desc">
                            <?php echo e($featuresSplit->getDisplayDescription()); ?>

                        </p>
                        <div class="features-list">
                            <?php $__currentLoopData = $featuresSplitItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <?php if($item->icon): ?>
                                            <i class="<?php echo e($item->icon); ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="feature-label"><?php echo e($item->getDisplayTitle()); ?></div>
                                        <div class="feature-text"><?php echo e($item->getDisplayDescription()); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <!-- Right: Main image -->
                <div class="features-split-right">
                    <?php if($featuresSplit->main_image): ?>
                        <img src="<?php echo e($featuresSplit->main_image_url); ?>" alt="Feature" class="features-main-img">
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>



<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\courses-laravel\resources\views/pages/home.blade.php ENDPATH**/ ?>