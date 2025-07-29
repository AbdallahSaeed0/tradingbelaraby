<div class="course-content-topbar d-flex justify-content-between align-items-center px-4 py-3">
    <div class="course-content-title fw-bold"><?php echo e($course->name); ?></div>
    <div class="d-flex gap-2">
        <button class="btn btn-orange"><i class="fa fa-certificate me-2"></i><?php echo e(__('Get Certificate')); ?></button>
        <a href="<?php echo e(route('courses.show', $course->id)); ?>" class="btn btn-outline-light course-details-btn">
            <i class="fa fa-arrow-right me-2"></i><?php echo e(__('Course details')); ?>

        </a>
    </div>
</div>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/courses/partials/course-header.blade.php ENDPATH**/ ?>