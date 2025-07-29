<!DOCTYPE html>
<html lang="<?php echo e(\App\Helpers\TranslationHelper::getCurrentLanguage()->code); ?>"
    dir="<?php echo e(\App\Helpers\TranslationHelper::getCurrentLanguage()->direction); ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin Panel'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('admin/pages-ltr/blog.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/pages-ltr/admin.css')); ?>">
    <style>
        /* RTL Support */
        [dir="rtl"] .me-1 {
            margin-left: 0 !important;
            margin-right: 0.25rem !important;
        }

        [dir="rtl"] .me-2 {
            margin-left: 0 !important;
            margin-right: 0.5rem !important;
        }

        [dir="rtl"] .me-3 {
            margin-left: 0 !important;
            margin-right: 1rem !important;
        }

        [dir="rtl"] .ms-1 {
            margin-right: 0 !important;
            margin-left: 0.25rem !important;
        }

        [dir="rtl"] .ms-2 {
            margin-right: 0 !important;
            margin-left: 0.5rem !important;
        }

        /* Language Switcher Styling */
        .language-switcher .dropdown-item.active {
            background-color: var(--bs-primary) !important;
            color: white !important;
        }

        .language-switcher .dropdown-item:hover {
            background-color: var(--bs-light);
        }

        .language-switcher .dropdown-item.active:hover {
            background-color: var(--bs-primary) !important;
        }

        /* RTL specific adjustments */
        [dir="rtl"] .navbar-nav {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .dropdown-menu-end {
            right: auto !important;
            left: 0 !important;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="bg-light">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <button id="sidebarToggle" class="btn btn-outline-secondary me-3 shadow-sm" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                <i class="fa fa-bars"></i>
            </button>
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Visit Site -->
                <li class="nav-item me-3">
                    <a class="nav-link" href="<?php echo e(url('/')); ?>" target="_blank">
                        <i class="fa fa-globe"></i>
                    </a>
                </li>
                <!-- Notifications -->
                <?php
                    $pendingQuestions = \App\Models\QuestionsAnswer::where('status', 'pending')->count();
                    $urgentQuestions = \App\Models\QuestionsAnswer::where('priority', 'urgent')->count();
                    $totalNotifications = $pendingQuestions + $urgentQuestions;
                ?>
                <li class="nav-item dropdown me-3">
                    <a class="nav-link position-relative" href="#" id="notifDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <?php if($totalNotifications > 0): ?>
                            <span
                                class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle p-1 small"><?php echo e($totalNotifications); ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notifDropdown"
                        style="min-width: 250px;">
                        <li class="dropdown-header">Notifications</li>
                        <?php if($totalNotifications > 0): ?>
                            <?php if($pendingQuestions > 0): ?>
                                <li>
                                    <a class="dropdown-item"
                                        href="<?php echo e(route('admin.questions-answers.index', ['status' => 'pending'])); ?>">
                                        <i class="fa fa-clock text-warning me-2"></i>
                                        <?php echo e($pendingQuestions); ?> pending question<?php echo e($pendingQuestions > 1 ? 's' : ''); ?>

                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if($urgentQuestions > 0): ?>
                                <li>
                                    <a class="dropdown-item"
                                        href="<?php echo e(route('admin.questions-answers.index', ['priority' => 'urgent'])); ?>">
                                        <i class="fa fa-exclamation-triangle text-danger me-2"></i>
                                        <?php echo e($urgentQuestions); ?> urgent question<?php echo e($urgentQuestions > 1 ? 's' : ''); ?>

                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted">No new notifications</span></li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Language Switcher -->
                <?php
                    $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
                    $availableLanguages = \App\Helpers\TranslationHelper::getAvailableLanguages();
                ?>
                <li class="nav-item dropdown me-3">
                    <a class="nav-link d-flex align-items-center" href="#" id="langDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false" title="<?php echo e(custom_trans('switch_language')); ?>">
                        <i class="fa fa-globe me-1"></i>
                        <span class="d-none d-md-inline"><?php echo e($currentLanguage->code); ?></span>
                        <i class="fa fa-chevron-down ms-1 small"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end language-switcher" aria-labelledby="langDropdown"
                        style="min-width: 200px;">
                        <li class="dropdown-header">
                            <i class="fa fa-language me-2"></i><?php echo e(custom_trans('select_language')); ?>

                        </li>
                        <?php $__currentLoopData = $availableLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a class="dropdown-item d-flex align-items-center <?php echo e($currentLanguage->id == $language->id ? 'active bg-primary text-white' : ''); ?>"
                                    href="<?php echo e(route('language.switch', $language->code)); ?>">
                                    <span class="me-2">
                                        <?php if($language->direction == 'rtl'): ?>
                                            <i class="fa fa-text-width" title="RTL"></i>
                                        <?php else: ?>
                                            <i class="fa fa-text-width" title="LTR"></i>
                                        <?php endif; ?>
                                    </span>
                                    <span class="flex-grow-1"><?php echo e($language->native_name); ?></span>
                                    <small class="text-muted">(<?php echo e($language->code); ?>)</small>
                                    <?php if($currentLanguage->id == $language->id): ?>
                                        <i class="fa fa-check ms-2"></i>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('admin.languages.index')); ?>">
                                <i class="fa fa-cog me-2"></i><?php echo e(custom_trans('manage_languages')); ?>

                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link d-flex align-items-center profile-dropdown" href="#" id="profileDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://i.pravatar.cc/32?img=1" class="rounded-circle me-2" width="32"
                            height="32" alt="avatar">
                        <span>Hi <?php echo e(auth('admin')->user()->name); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.profile')); ?>">My Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="d-lg-flex">
        <?php echo $__env->make('admin.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- Page Content -->
        <main class="flex-grow-1 py-4">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        // extra toggle to hide sidebar on lg when button clicked
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('adminSidebar');
            if (window.innerWidth >= 992) {
                sidebar.classList.toggle('d-none');
            }
        });

        // Display session messages as toasts
        <?php if(session('success')): ?>
            toastr.success("<?php echo e(session('success')); ?>");
        <?php endif; ?>

        <?php if(session('error')): ?>
            toastr.error("<?php echo e(session('error')); ?>");
        <?php endif; ?>

        <?php if(session('warning')): ?>
            toastr.warning("<?php echo e(session('warning')); ?>");
        <?php endif; ?>

        <?php if(session('info')): ?>
            toastr.info("<?php echo e(session('info')); ?>");
        <?php endif; ?>
    </script>
</body>

</html>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/admin/layout.blade.php ENDPATH**/ ?>