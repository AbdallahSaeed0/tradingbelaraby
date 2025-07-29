<div class="course-content-section" style="background-color: #EFF7FF; padding: 2rem 0;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><?php echo e(__('Course Content')); ?></h3>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" id="markCompleteBtn" disabled>
                    <i class="fa fa-check me-2"></i><?php echo e(__('Mark Selected as Complete')); ?>

                </button>
                <button class="btn btn-outline-warning" id="markIncompleteBtn" disabled>
                    <i class="fa fa-times me-2"></i><?php echo e(__('Mark Selected as Incomplete')); ?>

                </button>
            </div>
        </div>
        <div class="accordion" id="courseSectionsAccordion">
            <?php $__currentLoopData = $course->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="accordion-item border-0 mb-3 bg-white rounded-4 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#section<?php echo e($section->id); ?>" aria-expanded="false"
                            aria-controls="section<?php echo e($section->id); ?>">
                            <div class="d-flex align-items-center w-100">
                                <input type="checkbox" class="form-check-input me-3 section-select-checkbox"
                                    data-section="<?php echo e($section->id); ?>" onclick="event.stopPropagation();">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <div>
                                            <h5 class="mb-1 fw-bold"><?php echo e($section->title); ?></h5>
                                            <small class="text-muted"><?php echo e($section->description); ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-primary"><?php echo e($section->lectures->count()); ?>

                                                Classes</span><br>
                                            <small class="text-muted"><?php echo e($section->total_duration ?? '0 Min'); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="section<?php echo e($section->id); ?>" class="accordion-collapse collapse"
                        data-bs-parent="#courseSectionsAccordion">
                        <div class="accordion-body">
                            <div class="class-cards">
                                <?php $__currentLoopData = $section->lectures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $isCompleted = auth()
                                            ->user()
                                            ->lectureCompletions()
                                            ->where('lecture_id', $lecture->id)
                                            ->where('is_completed', true)
                                            ->exists();
                                    ?>
                                    <div
                                        class="class-card d-flex align-items-center p-3 mb-3 bg-light rounded <?php echo e($isCompleted ? 'completed-lecture' : ''); ?>">
                                        <!-- Selection checkbox for bulk operations -->
                                        <input type="checkbox" class="form-check-input me-2 lecture-select-checkbox"
                                            data-lecture="<?php echo e($lecture->id); ?>" data-section="<?php echo e($section->id); ?>">

                                        <!-- Completion status indicator -->
                                        <div class="me-3">
                                            <?php if($isCompleted): ?>
                                                <i class="fa fa-check-circle text-success" style="font-size: 1.2rem;"
                                                    title="<?php echo e(__('Completed')); ?>"></i>
                                            <?php else: ?>
                                                <i class="fa fa-circle text-muted" style="font-size: 1.2rem;"
                                                    title="<?php echo e(__('Not Completed')); ?>"></i>
                                            <?php endif; ?>
                                        </div>

                                        <button class="btn btn-outline-primary btn-sm me-3 play-video-btn"
                                            data-video="<?php echo e($lecture->video_url); ?>">
                                            <i class="fa fa-play"></i>
                                        </button>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 fw-bold class-title" style="cursor: pointer;"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#class<?php echo e($lecture->id); ?>-desc">
                                                        <?php echo e($lecture->title); ?>

                                                        <?php if($isCompleted): ?>
                                                            <span class="badge bg-success ms-2">
                                                                <i class="fa fa-check"></i> <?php echo e(__('Completed')); ?>

                                                            </span>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <div class="collapse class-description"
                                                        id="class<?php echo e($lecture->id); ?>-desc">
                                                        <p class="text-muted small mb-0">
                                                            <?php echo e($lecture->description); ?>

                                                        </p>
                                                    </div>
                                                </div>
                                                <span
                                                    class="text-muted small"><?php echo e($lecture->duration ?? '0 min'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/courses/partials/course-curriculum.blade.php ENDPATH**/ ?>