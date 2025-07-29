<!-- CTA Video Section -->
<?php
    $ctaVideo = \App\Models\CTAVideo::active()->first();
?>

<?php if($ctaVideo): ?>
    <section class="cta-video-section d-flex align-items-center min-h-400 bg-cta-video position-relative">
        <div class="container position-relative z-2">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8 text-white">
                    <h2 class="fw-bold display-4 mb-3">
                        <?php echo e(get_current_language_code() === 'ar' && $ctaVideo->title_ar ? $ctaVideo->title_ar : $ctaVideo->title); ?>

                    </h2>
                    <p class="lead mb-4">
                        <?php echo e(get_current_language_code() === 'ar' && $ctaVideo->description_ar ? $ctaVideo->description_ar : $ctaVideo->description); ?>

                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <button type="button" class="btn btn-light btn-lg rounded-circle cta-play-btn" data-bs-toggle="modal"
                        data-bs-target="#videoModal">
                        <span class="visually-hidden">Play Video</span>
                        <i class="fa-solid fa-play fa-2x"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="cta-overlay"></div>
    </section>

    <!-- CTA Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="ratio ratio-16x9">
                        <iframe id="youtubeVideo"
                            src="<?php echo e($ctaVideo->video_url ? str_replace('watch?v=', 'embed/', $ctaVideo->video_url) : ''); ?>"
                            title="YouTube video" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Fallback to original hardcoded content -->
    <section class="cta-video-section d-flex align-items-center min-h-400 bg-cta-video position-relative">
        <div class="container position-relative z-2">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8 text-white">
                    <h2 class="fw-bold display-4 mb-3">Start learning<br>anywhere, anytime...</h2>
                    <p class="lead mb-4">Customers in today's tech-savvy market demand comprehensive information about
                        any
                        new good or service they are thinking about purchasing.</p>
                </div>
                <div class="col-lg-4 text-center">
                    <button type="button" class="btn btn-light btn-lg rounded-circle cta-play-btn"
                        data-bs-toggle="modal" data-bs-target="#videoModal">
                        <span class="visually-hidden">Play Video</span>
                        <i class="fa-solid fa-play fa-2x"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="cta-overlay"></div>
    </section>

    <!-- CTA Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark">
                <div class="modal-body p-0 position-relative">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="ratio ratio-16x9">
                        <iframe id="youtubeVideo" src="" title="YouTube video" allow="autoplay; encrypted-media"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/home/cta-video.blade.php ENDPATH**/ ?>