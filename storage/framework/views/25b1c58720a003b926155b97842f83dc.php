<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Stat cards using Bootstrap cards -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-primary d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-chart-bar text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0"><?php echo e(custom_trans('admins')); ?></h6>
                            <h4 class="fw-bold mb-0"><?php echo e(\App\Models\Admin::count()); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-success d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-book text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0"><?php echo e(custom_trans('courses')); ?></h6>
                            <h4 class="fw-bold mb-0">0</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-danger d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-blog text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0"><?php echo e(custom_trans('blogs')); ?></h6>
                            <h4 class="fw-bold mb-0">0</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-warning d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-users text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0"><?php echo e(custom_trans('users')); ?></h6>
                            <h4 class="fw-bold mb-0"><?php echo e(\App\Models\User::count()); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\courses-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>