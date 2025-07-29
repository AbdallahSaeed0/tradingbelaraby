<!-- Scholarship Programs Banner Section -->
<?php
    $scholarshipBanner = \App\Models\ScholarshipBanner::active()->first();
?>

<?php if($scholarshipBanner): ?>
    <section class="scholarship-banner-section">
        <div class="scholarship-banner-overlay">
            <div class="scholarship-banner-content text-center">
                <h2 class="scholarship-banner-title">
                    <?php echo e(get_current_language_code() === 'ar' && $scholarshipBanner->title_ar ? $scholarshipBanner->title_ar : $scholarshipBanner->title); ?>

                </h2>
                <a href="<?php echo e($scholarshipBanner->button_url ?: '#'); ?>" class="scholarship-banner-btn">
                    <?php echo e(get_current_language_code() === 'ar' && $scholarshipBanner->button_text_ar ? $scholarshipBanner->button_text_ar : $scholarshipBanner->button_text); ?>

                </a>
            </div>
        </div>
    </section>
<?php else: ?>
    <!-- Fallback to original hardcoded content -->
    <section class="scholarship-banner-section">
        <div class="scholarship-banner-overlay">
            <div class="scholarship-banner-content text-center">
                <h2 class="scholarship-banner-title">Scholarship Programs</h2>
                <a href="#" class="scholarship-banner-btn">Become An Instructor</a>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/home/scholarship-banner.blade.php ENDPATH**/ ?>