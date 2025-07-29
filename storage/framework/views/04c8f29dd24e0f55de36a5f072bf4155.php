<!-- Testimonial Slider Section -->
<section class="testimonial-section position-relative py-5">
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-01.png" alt="Left"
        class="testimonial-img-left d-none d-md-block">
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-02.png" alt="Right"
        class="testimonial-img-right d-none d-md-block">
    <div class="container">
        <div class="text-center mb-4">
            <span class="text-warning fw-bold d-block mb-2 fs-11">
                <i class="fas fa-graduation-cap"></i> <?php echo e(__('Testimonial')); ?>

            </span>
            <h2 class="fw-bold mb-3"><?php echo e(__('What Our Clients Says')); ?></h2>
        </div>
        <div class="testimonial-slider">
            <?php
                // Fetch active testimonials from the database
                $testimonials = \App\Models\Testimonial::active()->ordered()->get();
            ?>

            <?php if($testimonials->count() > 0): ?>
                <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="testimonial-card text-center p-4 mx-2">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-right fa-2x text-warning"></i>
                        </div>
                        <p class="testimonial-text mb-4"><?php echo e($testimonial->getDisplayContent()); ?></p>

                        <!-- Rating Stars -->
                        <div class="testimonial-rating mb-3">
                            <div class="text-warning">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?php echo e($i <= $testimonial->rating ? '' : '-o'); ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <img src="<?php echo e($testimonial->avatar_url); ?>" class="testimonial-avatar mb-2"
                            alt="<?php echo e($testimonial->getDisplayName()); ?>"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                        <h5 class="mb-0"><?php echo e($testimonial->getDisplayName()); ?></h5>
                        <small class="text-muted"><?php echo e($testimonial->getDisplayPosition()); ?> at
                            <?php echo e($testimonial->getDisplayCompany()); ?></small>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <!-- Fallback content when no testimonials are available -->
                <?php
                    $fallbackTestimonials = [
                        [
                            'img' => 'https://randomuser.me/api/portraits/men/32.jpg',
                            'name' => 'Marry Ieee',
                            'role' => 'Student',
                        ],
                        [
                            'img' => 'https://randomuser.me/api/portraits/women/44.jpg',
                            'name' => 'Kristin Joy',
                            'role' => 'Employee',
                        ],
                        [
                            'img' => 'https://randomuser.me/api/portraits/men/45.jpg',
                            'name' => 'Tom Hardy',
                            'role' => 'Assistant Director',
                        ],
                    ];
                ?>
                <?php $__currentLoopData = $fallbackTestimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="testimonial-card text-center p-4 mx-2">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-right fa-2x text-warning"></i>
                        </div>
                        <p class="testimonial-text mb-4">
                            <?php echo e(__('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.')); ?>

                        </p>
                        <img src="<?php echo e($t['img']); ?>" class="testimonial-avatar mb-2" alt="<?php echo e($t['name']); ?>"
                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                        <h5 class="mb-0"><?php echo e($t['name']); ?></h5>
                        <small class="text-muted"><?php echo e($t['role']); ?></small>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/home/testimonials.blade.php ENDPATH**/ ?>