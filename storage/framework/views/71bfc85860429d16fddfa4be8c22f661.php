<!-- Features Section -->
<section class="features py-5 bg-white">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php
                $features = \App\Models\Feature::active()->ordered()->get();
            ?>

            <?php $__empty_1 = true; $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="<?php echo e($feature->icon_url); ?>" class="img-fluid wh-56" alt="<?php echo e($feature->title); ?>">
                            </span>
                            <span class="feature-number-circle"><?php echo e($feature->number); ?></span>
                        </div>
                        <h5 class="fw-bold mb-2">
                            <?php echo e(get_current_language_code() === 'ar' && $feature->title_ar ? $feature->title_ar : $feature->title); ?>

                        </h5>
                        <p class="mb-0 text-muted">
                            <?php echo e(get_current_language_code() === 'ar' && $feature->description_ar ? $feature->description_ar : $feature->description); ?>

                        </p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <!-- Fallback to default features if no data in database -->
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/facts/16751563391644382079instructor.png"
                                    class="img-fluid wh-56" alt="45">
                            </span>
                            <span class="feature-number-circle">45</span>
                        </div>
                        <h5 class="fw-bold mb-2">Skillful Instructor</h5>
                        <p class="mb-0 text-muted">Skillful Instructor is a LMS designed to help instructors create,
                            manage, and deliver online courses.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/facts/16751563901644382489student.png"
                                    class="img-fluid wh-56" alt="84">
                            </span>
                            <span class="feature-number-circle">84</span>
                        </div>
                        <h5 class="fw-bold mb-2">Happy Student</h5>
                        <p class="mb-0 text-muted">Happy Student is likely a company or brand name that provides
                            educational services, although without further context.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/facts/16751564601644382519live.png"
                                    class="img-fluid wh-56" alt="94">
                            </span>
                            <span class="feature-number-circle">94</span>
                        </div>
                        <h5 class="fw-bold mb-2">Live Classes</h5>
                        <p class="mb-0 text-muted">Live classes (LMS) refer to educational or training sessions that are
                            delivered in real-time, usually over the internet.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="https://eclass.mediacity.co.in/demo2/public/images/facts/16751566461644382554video.png"
                                    class="img-fluid wh-56" alt="63">
                            </span>
                            <span class="feature-number-circle">63</span>
                        </div>
                        <h5 class="fw-bold mb-2">Video</h5>
                        <p class="mb-0 text-muted">LMS videos refer to videos that are used as part of a (LMS) to
                            deliver educational content to students.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/home/features-section.blade.php ENDPATH**/ ?>