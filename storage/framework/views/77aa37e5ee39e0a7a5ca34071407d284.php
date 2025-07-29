<!-- FAQ Section -->
<?php
    $faqs = \App\Models\FAQ::active()->ordered()->get();
?>

<section class="faq-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-2 d-none d-lg-flex justify-content-center">
                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-01.png" alt="Left Decoration"
                    class="img-fluid opacity-25 max-h-300">
            </div>
            <div class="col-12 col-lg-8">
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-3"><?php echo e(__('Get every single answer here.')); ?></h2>
                    <p class="lead text-muted mb-4">
                        <?php echo e(__('A business or organization established to provide a particular service, typically one that involves organizing transactions.')); ?>

                    </p>
                </div>
                <p class="text-muted mb-4 fs-095">
                    <?php echo e(__('It is a long established fact that a reader will be distracted by the readable content of a page.')); ?>

                </p>
                <div class="accordion faq-accordion" id="accordionExample">
                    <?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button
                                    class="accordion-button <?php echo e(!$faq->is_expanded ? 'collapsed' : ''); ?> faq-accordion-header"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?php echo e($faq->id); ?>"
                                    aria-expanded="<?php echo e($faq->is_expanded ? 'true' : 'false'); ?>"
                                    aria-controls="collapse<?php echo e($faq->id); ?>">
                                    <span
                                        class="fw-bold"><?php echo e(get_current_language_code() === 'ar' && $faq->title_ar ? $faq->title_ar : $faq->title); ?></span>
                                    <span class="ms-auto faq-chevron"><i class="fa-solid fa-chevron-down"></i></span>
                                </button>
                            </h2>
                            <div id="collapse<?php echo e($faq->id); ?>"
                                class="accordion-collapse collapse <?php echo e($faq->is_expanded ? 'show' : ''); ?>"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body faq-accordion-body">
                                    <?php echo e(get_current_language_code() === 'ar' && $faq->content_ar ? $faq->content_ar : $faq->content); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <!-- Fallback to original hardcoded content -->
                        <?php
                            $fallbackFaqs = [
                                [
                                    'id' => 'One',
                                    'title' => 'Lifetime Access',
                                    'content' =>
                                        'Lifetime access FAQ for an e-class refers to a set of frequently asked questions about how students ...',
                                    'show' => true,
                                ],
                                [
                                    'id' => 'Two',
                                    'title' => 'Account/Profile',
                                    'content' =>
                                        'Account/Profile FAQ for an e-class refers to questions about user accounts and profiles.',
                                ],
                                [
                                    'id' => 'Three',
                                    'title' => 'Course Taking',
                                    'content' =>
                                        'Course Taking FAQ for an e-class refers to a set of questions about taking courses.',
                                ],
                                [
                                    'id' => 'Four',
                                    'title' => 'Troubleshooting',
                                    'content' =>
                                        'Troubleshooting FAQ for an e-class refers to questions about resolving issues.',
                                ],
                            ];
                        ?>
                        <?php $__currentLoopData = $fallbackFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header">
                                    <button <?php $isShow = $faq['show'] ?? false; ?>
                                        class="accordion-button <?php echo e(!$isShow ? 'collapsed' : ''); ?> faq-accordion-header"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php echo e($faq['id']); ?>"
                                        aria-expanded="<?php echo e($isShow ? 'true' : 'false'); ?>"
                                        aria-controls="collapse<?php echo e($faq['id']); ?>">
                                        <span class="fw-bold"><?php echo e($faq['title']); ?></span>
                                        <span class="ms-auto faq-chevron"><i
                                                class="fa-solid fa-chevron-down"></i></span>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo e($faq['id']); ?>"
                                    class="accordion-collapse collapse <?php echo e($isShow ? 'show' : ''); ?>"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body faq-accordion-body">
                                        <?php echo e($faq['content']); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-2 d-none d-lg-flex justify-content-center">
                <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-02.png"
                    alt="Right Decoration" class="img-fluid opacity-25 max-h-300">
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact-form-section py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-white mb-3"><?php echo e(__('Get In Touch')); ?></h2>
                    <p class="text-white-50">
                        <?php echo e(__('Have questions? We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.')); ?>

                    </p>
                </div>
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <form id="contactForm">
                            <?php echo csrf_field(); ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-bold"><?php echo e(__('Full Name')); ?> *</label>
                                    <input type="text" class="form-control form-control-lg" id="name"
                                        name="name" placeholder="<?php echo e(__('Enter your full name')); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold"><?php echo e(__('Email Address')); ?>

                                        *</label>
                                    <input type="email" class="form-control form-control-lg" id="email"
                                        name="email" placeholder="<?php echo e(__('Enter your email address')); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="phone" class="form-label fw-bold"><?php echo e(__('Phone Number')); ?></label>
                                    <input type="tel" class="form-control form-control-lg" id="phone"
                                        name="phone" placeholder="<?php echo e(__('Enter your phone number')); ?>">
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-bold"><?php echo e(__('Comments/Questions')); ?>

                                        *</label>
                                    <textarea class="form-control form-control-lg" id="message" name="message" rows="5"
                                        placeholder="<?php echo e(__('Tell us about your inquiry...')); ?>" required></textarea>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold">
                                        <i class="fas fa-paper-plane me-2"></i><?php echo e(__('Send Message')); ?>

                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Instructor Section -->
<?php echo $__env->make('partials.courses.instructor-section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function() {
            // Contact Form Submission
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i><?php echo e(__('Sending...')); ?>');

                $.ajax({
                    url: '<?php echo e(route('contact.store')); ?>',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            toastr.success(response.message);
                            // Reset form
                            $('#contactForm')[0].reset();
                        } else {
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    toastr.error(response.errors[key][0]);
                                });
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                '<?php echo e(__('An error occurred while sending your message. Please try again.')); ?>'
                            );
                        }
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/partials/courses/faq-section.blade.php ENDPATH**/ ?>