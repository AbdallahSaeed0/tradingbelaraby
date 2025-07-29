<!-- Admin Sidebar -->
<div class="offcanvas offcanvas-start offcanvas-lg-show bg-white" data-bs-backdrop="false" tabindex="-1" id="adminSidebar"
    aria-labelledby="adminSidebarLabel" style="--bs-offcanvas-width:15%;">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="adminSidebarLabel">Admin Panel</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            <a href="<?php echo e(route('admin.dashboard')); ?>"
                class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <i class="fa fa-chart-line me-2"></i> Dashboard
            </a>

            <!-- Course Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-graduation-cap me-2"></i> Course Management
            </div>
            <?php if(auth('admin')->user()->hasPermission('manage_courses') ||
                    auth('admin')->user()->hasPermission('manage_own_courses')): ?>
                <a href="<?php echo e(route('admin.courses.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.courses.*') ? 'active' : ''); ?>">
                    <i class="fa fa-book me-2"></i> Courses
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_quizzes') ||
                    auth('admin')->user()->hasPermission('manage_own_quizzes')): ?>
                <a href="<?php echo e(route('admin.quizzes.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.quizzes.*') ? 'active' : ''); ?>">
                    <i class="fa fa-question-circle me-2"></i> Quizzes
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_homework') ||
                    auth('admin')->user()->hasPermission('manage_own_homework')): ?>
                <a href="<?php echo e(route('admin.homework.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.homework.*') ? 'active' : ''); ?>">
                    <i class="fa fa-tasks me-2"></i> Homework
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_live_classes') ||
                    auth('admin')->user()->hasPermission('manage_own_live_classes')): ?>
                <a href="<?php echo e(route('admin.live-classes.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.live-classes.*') ? 'active' : ''); ?>">
                    <i class="fa fa-video me-2"></i> Live Classes
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_enrollments')): ?>
                <a href="<?php echo e(route('admin.enrollments.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.enrollments.*') ? 'active' : ''); ?>">
                    <i class="fa fa-user-graduate me-2"></i> Enrollments
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_questions_answers') ||
                    auth('admin')->user()->hasPermission('manage_own_questions_answers')): ?>
                <?php
                    $pendingQACount = \App\Models\QuestionsAnswer::where('status', 'pending')->count();
                ?>
                <a href="<?php echo e(route('admin.questions-answers.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.questions-answers.*') ? 'active' : ''); ?>">
                    <i class="fa fa-comments me-2"></i> Q&A Management
                    <?php if($pendingQACount > 0): ?>
                        <span class="badge bg-warning text-dark ms-auto"><?php echo e($pendingQACount); ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <!-- User Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-users me-2"></i> User Management
            </div>
            <?php if(auth('admin')->user()->hasPermission('manage_admins')): ?>
                <a href="<?php echo e(route('admin.admins.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.admins.*') ? 'active' : ''); ?>">
                    <i class="fa fa-user-shield me-2"></i> Admins
                </a>
                <a href="<?php echo e(route('admin.admin-types.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.admin-types.*') ? 'active' : ''); ?>">
                    <i class="fa fa-user-tag me-2"></i> Admin Types
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_users')): ?>
                <a href="<?php echo e(route('admin.users.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.users.*') ? 'active' : ''); ?>">
                    <i class="fa fa-users me-2"></i> Users
                </a>
            <?php endif; ?>

            <!-- Content Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-edit me-2"></i> Content Management
            </div>
            <?php if(auth('admin')->user()->hasPermission('manage_categories')): ?>
                <a href="<?php echo e(route('admin.categories.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.categories.*') ? 'active' : ''); ?>">
                    <i class="fa fa-list me-2"></i> Categories
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_blogs')): ?>
                <a href="<?php echo e(route('admin.blog-categories.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.blog-categories.*') ? 'active' : ''); ?>">
                    <i class="fa fa-tags me-2"></i> Blog Categories
                </a>
                <a href="<?php echo e(route('admin.blogs.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.blogs.*') ? 'active' : ''); ?>">
                    <i class="fa fa-blog me-2"></i> Blogs
                </a>
            <?php endif; ?>

            <!-- Translation Management Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-language me-2"></i> Translation Management
            </div>
            <?php if(auth('admin')->user()->hasPermission('manage_languages')): ?>
                <a href="<?php echo e(route('admin.languages.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.languages.*') ? 'active' : ''); ?>">
                    <i class="fa fa-flag me-2"></i> Languages
                </a>
            <?php endif; ?>
            <?php if(auth('admin')->user()->hasPermission('manage_translations')): ?>
                <a href="<?php echo e(route('admin.translations.index')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.translations.*') ? 'active' : ''); ?>">
                    <i class="fa fa-language me-2"></i> Translations
                </a>
                <a href="<?php echo e(route('admin.debug-translations')); ?>"
                    class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.debug-translations') ? 'active' : ''); ?>">
                    <i class="fa fa-bug me-2"></i> Debug Translations
                </a>
            <?php endif; ?>

            <!-- Settings Section -->
            <div class="list-group-item bg-light text-muted small fw-bold text-uppercase">
                <i class="fa fa-cog me-2"></i> Settings
            </div>
                            <a href="<?php echo e(route('admin.settings.index')); ?>"
                   class="list-group-item list-group-item-action <?php echo e(request()->routeIs('admin.settings*') ? 'active' : ''); ?>">
                <i class="fa fa-sliders-h me-2"></i> General Settings
            </a>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/admin/partials/sidebar.blade.php ENDPATH**/ ?>