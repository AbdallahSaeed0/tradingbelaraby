<div class="instructor-section">
    <div class="d-flex align-items-start mb-3">
        <img src="<?php echo e($course->instructor->avatar ? asset('storage/' . $course->instructor->avatar) : 'https://eclass.mediacity.co.in/demo2/public/images/user_img/159116543729.jpg'); ?>"
            alt="<?php echo e($course->instructor->name); ?>" class="instructor-avatar rounded-circle me-3" width="64"
            height="64">
        <div>
            <h4 class="fw-bold mb-1"><?php echo e($course->instructor->name); ?></h4>
            <p class="text-muted mb-0"><?php echo e($course->instructor->email ?? ''); ?></p>
        </div>
    </div>
    <p class="instructor-description">
        <?php echo e($course->instructor->bio); ?>

    </p>
</div>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/courses/partials/course-instructor.blade.php ENDPATH**/ ?>