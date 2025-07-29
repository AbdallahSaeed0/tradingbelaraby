<div class="quiz-section" style="background-color: #EFF7FF; padding: 2rem 0;">
    <div class="container">
        <h3 class="fw-bold mb-4"><?php echo e(__('Objective')); ?></h3>

        <!-- Quiz Cards -->
        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $course->quizzes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-lg-6">
                    <div class="quiz-card bg-white p-4 rounded-4 shadow-sm h-100">
                        <h4 class="quiz-title fw-bold mb-3"><?php echo e($quiz->title); ?></h4>
                        <p class="quiz-description text-muted mb-4">
                            <?php echo e($quiz->description); ?>

                        </p>

                        <!-- Quiz Features -->
                        <div class="quiz-features mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Per Question Mark')); ?></span>
                                        <span
                                            class="feature-value fw-bold text-primary"><?php echo e($quiz->marks_per_question ?? 5); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Total Marks')); ?></span>
                                        <span
                                            class="feature-value fw-bold text-primary"><?php echo e($quiz->total_marks ?? 10); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Total Questions')); ?></span>
                                        <span
                                            class="feature-value fw-bold text-primary"><?php echo e($quiz->questions->count() ?? 2); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Quiz Price')); ?></span>
                                        <span
                                            class="feature-value fw-bold <?php echo e($quiz->price > 0 ? 'text-warning' : 'text-success'); ?>">
                                            <?php echo e($quiz->price > 0 ? '$' . number_format($quiz->price, 2) : __('Free')); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Start Quiz Button -->
                        <a href="<?php echo e(route('quizzes.show', $quiz->id)); ?>" class="btn btn-orange w-100 fw-bold">
                            <i class="fa fa-play me-2"></i><?php echo e(__('Start Quiz')); ?>

                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <!-- Default Quiz Cards -->
                <div class="col-lg-6">
                    <div class="quiz-card bg-white p-4 rounded-4 shadow-sm h-100">
                        <h4 class="quiz-title fw-bold mb-3"><?php echo e($course->name); ?></h4>
                        <p class="quiz-description text-muted mb-4">
                            <?php echo e(__('Test your knowledge of')); ?> <?php echo e($course->name); ?>

                            <?php echo e(__('fundamentals and concepts.')); ?>

                        </p>

                        <!-- Quiz Features -->
                        <div class="quiz-features mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Per Question Mark')); ?></span>
                                        <span class="feature-value fw-bold text-primary">5</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Total Marks')); ?></span>
                                        <span class="feature-value fw-bold text-primary">10</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Total Questions')); ?></span>
                                        <span class="feature-value fw-bold text-primary">2</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Quiz Price')); ?></span>
                                        <span class="feature-value fw-bold text-success"><?php echo e(__('Free')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Start Quiz Button -->
                        <a href="#" class="btn btn-orange w-100 fw-bold">
                            <i class="fa fa-play me-2"></i><?php echo e(__('Start Quiz')); ?>

                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="quiz-card bg-white p-4 rounded-4 shadow-sm h-100">
                        <h4 class="quiz-title fw-bold mb-3"><?php echo e(__('Digital Marketing Fundamentals')); ?></h4>
                        <p class="quiz-description text-muted mb-4">
                            <?php echo e(__('Test your knowledge of digital marketing fundamentals including SEO, social media marketing, content marketing, and online advertising strategies.')); ?>

                        </p>

                        <!-- Quiz Features -->
                        <div class="quiz-features mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Per Question Mark')); ?></span>
                                        <span class="feature-value fw-bold text-primary">3</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Total Marks')); ?></span>
                                        <span class="feature-value fw-bold text-primary">15</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Total Questions')); ?></span>
                                        <span class="feature-value fw-bold text-primary">5</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted"><?php echo e(__('Quiz Price')); ?></span>
                                        <span class="feature-value fw-bold text-success"><?php echo e(__('Free')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Start Quiz Button -->
                        <a href="#" class="btn btn-orange w-100 fw-bold">
                            <i class="fa fa-play me-2"></i><?php echo e(__('Start Quiz')); ?>

                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/courses/partials/course-quiz.blade.php ENDPATH**/ ?>