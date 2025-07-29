<div class="recent-activity-section" style="background-color: #EFF7FF; padding: 2rem 0;">
    <div class="container">
        <h3 class="fw-bold mb-4"><?php echo e(__('Recent Activity')); ?></h3>
        <div class="row g-4">
            <!-- Recent Questions Box -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <h4 class="fw-bold mb-4"><?php echo e(__('Recent Questions')); ?></h4>
                    <div class="recent-questions mb-4">
                        <?php $__empty_1 = true; $__currentLoopData = $course->questionsAnswers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="question-item d-flex align-items-start mb-4">
                                <div
                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                    <?php echo e(strtoupper(substr($question->user->name ?? 'U', 0, 2))); ?>

                                </div>
                                <div>
                                    <h6 class="mb-1"><?php echo e($question->question_title); ?></h6>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="question-item d-flex align-items-start mb-4">
                                <div
                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                    AM
                                </div>
                                <div>
                                    <h6 class="mb-1"><?php echo e(__('What are')); ?> <?php echo e($course->name); ?>?</h6>
                                </div>
                            </div>
                            <div class="question-item d-flex align-items-start">
                                <div
                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                    AM
                                </div>
                                <div>
                                    <h6 class="mb-1"><?php echo e(__('How can')); ?> <?php echo e($course->name); ?> <?php echo e(__('benefit')); ?>?</h6>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="#" class="btn btn-outline-primary"><?php echo e(__('Browse questions')); ?></a>
                </div>
            </div>
            <!-- Recent Announcements Box -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <h4 class="fw-bold mb-4"><?php echo e(__('Recent Announcements')); ?></h4>
                    <div class="recent-announcements mb-4">
                        <div class="accordion" id="announcementsAccordion">
                            <?php $__empty_1 = true; $__currentLoopData = $course->announcements ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent p-0" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#announcement<?php echo e($announcement->id); ?>">
                                            <div class="d-flex align-items-start w-100">
                                                <div
                                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                                    <?php echo e(strtoupper(substr($announcement->user->name ?? 'A', 0, 2))); ?>

                                                </div>
                                                <div>
                                                    <h6 class="mb-1"><?php echo e($announcement->user->name ?? 'Admin'); ?>

                                                        <?php echo e($announcement->created_at->format('jS F Y')); ?></h6>
                                                    <small class="text-muted"><?php echo e(__('Announcements')); ?></small>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="announcement<?php echo e($announcement->id); ?>" class="accordion-collapse collapse"
                                        data-bs-parent="#announcementsAccordion">
                                        <div class="accordion-body ps-5 pt-2">
                                            <?php echo e($announcement->content); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent p-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#announcement1">
                                            <div class="d-flex align-items-start w-100">
                                                <div
                                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                                    AM
                                                </div>
                                                <div>
                                                    <h6 class="mb-1"><?php echo e(__('Admin Mediacity')); ?> 4th October 2023
                                                    </h6>
                                                    <small class="text-muted"><?php echo e(__('Announcements')); ?></small>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="announcement1" class="accordion-collapse collapse"
                                        data-bs-parent="#announcementsAccordion">
                                        <div class="accordion-body ps-5 pt-2">
                                            <?php echo e(__('This is the announcement content. It can contain important updates and information about the course.')); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <a href="#" class="btn btn-outline-primary"><?php echo e(__('Browse announcements')); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/courses/partials/course-overview.blade.php ENDPATH**/ ?>