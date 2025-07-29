<!DOCTYPE html>
<html lang="<?php echo e(\App\Helpers\TranslationHelper::getCurrentLanguage()->code); ?>"
    dir="<?php echo e(\App\Helpers\TranslationHelper::getCurrentLanguage()->direction); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'E-Class - Online Learning Platform'); ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/layout.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/utilities.css')); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* RTL Support for Frontend */
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

        /* RTL specific adjustments for frontend */
        [dir="rtl"] .nav-links {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .auth-buttons {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .nav-flex {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .footer-contact-item {
            text-align: right;
        }

        [dir="rtl"] .footer-post {
            flex-direction: row-reverse;
        }

        /* Search and User Actions Styling */
        .search-container {
            min-width: 250px;
        }

        .search-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }

        .user-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .user-actions .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
        }

        /* RTL support for search and actions */
        [dir="rtl"] .search-container {
            margin-left: 0;
            margin-right: 1rem;
        }

        [dir="rtl"] .user-actions {
            margin-left: 0;
            margin-right: 1rem;
        }

        /* Enhanced Notification Dropdown Styles */
        .notification-dropdown {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 0;
            margin-top: 8px;
        }

        .notification-dropdown .dropdown-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
        }

        .notification-dropdown .dropdown-header h6 {
            color: white !important;
        }

        .notification-item {
            border: none;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }

        .notification-item.unread {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
        }

        .notification-item.unread:hover {
            background-color: #ffeaa7;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .notification-item.unread .notification-icon {
            background: #fff3cd;
        }

        .notification-content {
            min-width: 0;
        }

        .notification-message {
            font-size: 0.9rem;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .notification-time {
            font-size: 0.75rem;
        }

        .notification-status .badge {
            margin-top: 8px;
        }

        .notification-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .notification-btn {
            transition: all 0.3s ease;
        }

        .notification-btn:hover {
            background-color: #f8f9fa;
            border-color: #6c757d;
        }

        /* User Dropdown Styles */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown-btn {
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 8px 16px;
            min-width: 120px;
            justify-content: space-between;
        }

        .user-dropdown-btn:hover {
            background-color: #f8f9fa;
            border-color: #6c757d;
            transform: translateY(-1px);
        }

        .user-name {
            font-weight: 500;
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 0;
            margin-top: 8px;
            min-width: 250px;
        }

        .user-dropdown-menu .dropdown-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
            padding: 15px;
        }

        .user-dropdown-menu .dropdown-item {
            padding: 12px 20px;
            border: none;
            transition: all 0.3s ease;
        }

        .user-dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .user-dropdown-menu .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        /* Like Button Styles */
        .like-btn {
            transition: all 0.3s ease;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .like-btn:hover {
            background-color: #f8f9fa;
            border-color: #6c757d;
            transform: scale(1.1);
        }

        .like-btn.liked {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }

        .like-btn.liked:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        /* RTL Support for User Dropdown */
        [dir="rtl"] .user-dropdown-menu .dropdown-item:hover {
            transform: translateX(-5px);
        }

        [dir="rtl"] .user-dropdown-btn {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .user-name {
            margin-left: 0;
            margin-right: 8px;
        }

        .notification-btn:hover {
            transform: scale(1.05);
        }

        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-list::-webkit-scrollbar {
            width: 4px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        .notification-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Empty state styling */
        .notification-dropdown .dropdown-item.text-center {
            border: none;
            padding: 2rem 1rem;
        }

        .notification-dropdown .dropdown-item.text-center i {
            opacity: 0.5;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <?php
        $contactSettings = \App\Models\ContactSettings::getActive();
    ?>

    <!-- Header Section -->
    <header>
        <div class="top-bar-colored">
            <div class="top-bar-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 top-bar-left-colored">
                            <div class="top-bar-left">
                                <span>Follow us:-</span>
                                <?php if($contactSettings && $contactSettings->social_facebook): ?>
                                    <a href="<?php echo e($contactSettings->social_facebook); ?>" target="_blank"><i
                                            class="fab fa-facebook-f"></i></a>
                                <?php endif; ?>
                                <?php if($contactSettings && $contactSettings->social_twitter): ?>
                                    <a href="<?php echo e($contactSettings->social_twitter); ?>" target="_blank"><i
                                            class="fab fa-twitter"></i></a>
                                <?php endif; ?>
                                <?php if($contactSettings && $contactSettings->social_youtube): ?>
                                    <a href="<?php echo e($contactSettings->social_youtube); ?>" target="_blank"><i
                                            class="fab fa-youtube"></i></a>
                                <?php endif; ?>
                                <?php if($contactSettings && $contactSettings->social_linkedin): ?>
                                    <a href="<?php echo e($contactSettings->social_linkedin); ?>" target="_blank"><i
                                            class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-8 top-bar-right">
                            <?php if($contactSettings && $contactSettings->phone): ?>
                                <div class="contact-block">
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/phone-call.png"
                                        alt="Phone" class="contact-icon">
                                    <div class="contact-info">
                                        <span class="contact-label">Call Now !</span>
                                        <span class="contact-value"><b><?php echo e($contactSettings->phone); ?></b></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if($contactSettings && $contactSettings->email): ?>
                                <div class="contact-block">
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/mailing.png"
                                        alt="Email" class="contact-icon">
                                    <div class="contact-info">
                                        <span class="contact-label">Email Now</span>
                                        <span class="contact-value"><b><?php echo e($contactSettings->email); ?></b></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="main-nav main-nav-white">
            <div class="container nav-flex">
                <div class="logo">
                    <img src="https://eclass.mediacity.co.in/demo2/public/images/logo/logo.png" alt="E-Class Logo"
                        class="logo-img">
                </div>
                <ul class="nav-links">
                    <li><a href="<?php echo e(route('home')); ?>"><?php echo e(custom_trans('home')); ?></a></li>
                    <li class="dropdown">
                        <a href="<?php echo e(route('categories.index')); ?>"><?php echo e(custom_trans('categories')); ?></a>
                        <ul class="dropdown-menu">
                            <?php $__empty_1 = true; $__currentLoopData = $navigationCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li><a
                                        href="<?php echo e(route('categories.show', $category->slug)); ?>"><?php echo e($category->name); ?></a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <li><a
                                        href="<?php echo e(route('categories.index')); ?>"><?php echo e(custom_trans('no_category_found')); ?></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li><a href="<?php echo e(route('blog.index')); ?>"><?php echo e(custom_trans('blog')); ?></a></li>
                    <li><a href="<?php echo e(route('contact')); ?>"><?php echo e(custom_trans('contact')); ?></a></li>
                </ul>
                <!-- Language Switcher -->
                <?php
                    $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
                    $availableLanguages = \App\Helpers\TranslationHelper::getAvailableLanguages();
                ?>
                <div class="language-switcher me-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="frontendLangDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-1"></i>
                            <?php echo e(strtoupper($currentLanguage->code)); ?>

                        </button>
                        <ul class="dropdown-menu" aria-labelledby="frontendLangDropdown">
                            <?php $__currentLoopData = $availableLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a class="dropdown-item <?php echo e($currentLanguage->id == $language->id ? 'active' : ''); ?>"
                                        href="<?php echo e(route('language.switch', $language->code)); ?>">
                                        <span class="me-2">
                                            <?php if($language->direction == 'rtl'): ?>
                                                <i class="fas fa-text-width" title="RTL"></i>
                                            <?php else: ?>
                                                <i class="fas fa-text-width" title="LTR"></i>
                                            <?php endif; ?>
                                        </span>
                                        <?php echo e($language->native_name); ?> (<?php echo e($language->code); ?>)
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="search-container me-3">
                    <form action="<?php echo e(route('courses.search')); ?>" method="GET" class="d-flex">
                        <input type="text" name="q" placeholder="<?php echo e(custom_trans('search_courses')); ?>"
                            class="form-control search-input" value="<?php echo e(request('q')); ?>">
                        <button type="submit" class="btn btn-outline-dark ms-2">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="user-actions me-3">
                    <?php if(auth()->guard()->check()): ?>
                        <!-- Notifications -->
                        <div class="dropdown">
                            <button class="btn btn-outline-dark position-relative notification-btn" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <?php if(auth()->user()->unreadNotifications->count() > 0): ?>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                                        <?php echo e(auth()->user()->unreadNotifications->count()); ?>

                                    </span>
                                <?php endif; ?>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown"
                                style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <div
                                    class="dropdown-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="fas fa-bell me-2 text-primary"></i>
                                        <?php echo e(custom_trans('notifications')); ?>

                                    </h6>
                                    <?php if(auth()->check() && auth()->user()->notifications && auth()->user()->notifications->count() > 0): ?>
                                        <span
                                            class="badge bg-warning text-dark rounded-pill"><?php echo e(auth()->user()->unreadNotifications->count()); ?>

                                            <?php echo e(custom_trans('new')); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="notification-list">
                                    <?php $__empty_1 = true; $__currentLoopData = auth()->user()->notifications->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="dropdown-item notification-item p-3 <?php echo e($notification->read_at ? '' : 'unread'); ?>"
                                            data-notification-id="<?php echo e($notification->id); ?>">
                                            <div class="d-flex align-items-start">
                                                <div class="notification-icon me-3">
                                                    <?php if(str_contains($notification->type, 'Wishlist')): ?>
                                                        <i class="fas fa-heart text-danger"></i>
                                                    <?php elseif(str_contains($notification->type, 'Course')): ?>
                                                        <i class="fas fa-graduation-cap text-primary"></i>
                                                    <?php elseif(str_contains($notification->type, 'Quiz')): ?>
                                                        <i class="fas fa-question-circle text-warning"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-bell text-info"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="notification-content flex-grow-1">
                                                    <div class="notification-message fw-semibold text-dark mb-1">
                                                        <?php echo e($notification->data['message'] ?? 'New notification'); ?>

                                                    </div>
                                                    <div class="notification-time text-muted small">
                                                        <i class="far fa-clock me-1"></i>
                                                        <?php echo e($notification->created_at->diffForHumans()); ?>

                                                    </div>
                                                </div>
                                                <?php if(!$notification->read_at): ?>
                                                    <div class="notification-status ms-2">
                                                        <span class="badge bg-danger rounded-circle"
                                                            style="width: 8px; height: 8px;"></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="dropdown-item text-center py-4">
                                            <div class="text-muted">
                                                <i class="far fa-bell-slash fa-2x mb-2"></i>
                                                <div><?php echo e(custom_trans('no_notifications')); ?></div>
                                                <small
                                                    class="text-muted"><?php echo e(custom_trans('you_will_see_notifications_here')); ?></small>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if(auth()->check() && auth()->user()->notifications && auth()->user()->notifications->count() > 0): ?>
                                    <div class="dropdown-divider"></div>
                                    <div class="dropdown-item text-center">
                                        <a href="<?php echo e(route('notifications.index')); ?>"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>
                                            <?php echo e(custom_trans('view_all_notifications')); ?>

                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Wishlist -->
                        <a href="<?php echo e(route('wishlist.index')); ?>" class="btn btn-outline-dark position-relative">
                            <i class="fas fa-heart"></i>
                            <?php if(auth()->check() && auth()->user()->wishlistItems && auth()->user()->wishlistItems->count() > 0): ?>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo e(auth()->user()->wishlistItems->count()); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- Cart -->
                        <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-outline-dark position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if(auth()->check() && auth()->user()->cartItems && auth()->user()->cartItems->count() > 0): ?>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo e(auth()->user()->cartItems->count()); ?>

                                </span>
                            <?php endif; ?>
                        </a>

                        <!-- Like Button -->
                        <button class="btn btn-outline-dark like-btn" id="likeBtn">
                            <i class="fas fa-thumbs-up"></i>
                        </button>

                        <!-- User Dropdown -->
                        <div class="dropdown user-dropdown">
                            <button class="btn btn-outline-dark user-dropdown-btn d-flex align-items-center"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2"></i>
                                <span class="user-name"><?php echo e(auth()->user()->name); ?></span>
                                <i class="fas fa-chevron-down ms-2"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                                <li class="dropdown-header">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            <i class="fas fa-user-circle fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?php echo e(auth()->user()->name); ?></div>
                                            <small class="text-muted"><?php echo e(auth()->user()->email); ?></small>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('student.my-courses')); ?>">
                                        <i class="fas fa-graduation-cap me-2 text-primary"></i>
                                        <?php echo e(custom_trans('my_courses')); ?>

                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('wishlist.index')); ?>">
                                        <i class="fas fa-heart me-2 text-danger"></i>
                                        <?php echo e(custom_trans('wishlist')); ?>

                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('purchase.history')); ?>">
                                        <i class="fas fa-history me-2 text-info"></i>
                                        <?php echo e(custom_trans('purchase_history')); ?>

                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>">
                                        <i class="fas fa-user-edit me-2 text-success"></i>
                                        <?php echo e(custom_trans('profile')); ?>

                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            <?php echo e(custom_trans('logout')); ?>

                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="auth-buttons">
                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('login')); ?>" class="btn btn-login-colored"><?php echo e(custom_trans('login')); ?></a>
                    <?php else: ?>
                        <!-- User dropdown is now handled above -->
                    <?php endif; ?>

                    <?php if(auth()->guard()->guest()): ?>
                        <a href="<?php echo e(route('register')); ?>"
                            class="btn btn-register-colored"><?php echo e(custom_trans('register')); ?></a>
                    <?php endif; ?>
                </div>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Newsletter Subscribe Section -->
    <?php
        $newsletterSettings = \App\Models\NewsletterSettings::active()->first();
    ?>

    <?php if($newsletterSettings): ?>
        <section class="newsletter-section py-5">
            <div class="container-fluid px-0">
                <div class="row justify-content-center align-items-center g-0">
                    <div
                        class="col-lg-6 d-flex flex-column flex-md-row align-items-center justify-content-center text-center text-md-start mb-4 mb-lg-0 ps-lg-5">
                        <div class="me-md-4 mb-3 mb-md-0">
                            <?php if($newsletterSettings->icon): ?>
                                <i class="<?php echo e($newsletterSettings->icon); ?> fa-3x paper-plane-icon"></i>
                            <?php else: ?>
                                <i class="fa-solid fa-paper-plane fa-3x paper-plane-icon"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1" style="font-size:2.5rem;">
                                <?php echo e($newsletterSettings->getDisplayTitle()); ?></h2>
                            <p class="mb-0" style="font-size:1.1rem; color: #e0f7fa;">
                                <?php echo e($newsletterSettings->getDisplayDescription()); ?></p>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex justify-content-center align-items-center">
                        <form id="newsletterForm" class="w-100" style="max-width: 520px;">
                            <?php echo csrf_field(); ?>
                            <div class="input-group input-group-lg">
                                <input type="email" name="email" id="newsletterEmail"
                                    class="form-control border-0 rounded-0 rounded-start"
                                    placeholder="<?php echo e($newsletterSettings->getDisplayPlaceholder()); ?>"
                                    aria-label="Email Address" required>
                                <button class="btn btn-outline-light rounded-0 rounded-end px-4 fw-bold newsletter-btn"
                                    type="submit" id="newsletterSubmitBtn">
                                    <span class="btn-text"><?php echo e($newsletterSettings->getDisplayButtonText()); ?></span>
                                    <span class="btn-loading" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer-main footer-bg position-relative pt-5">
        <div class="container">
            <div class="row text-white pb-4">
                <!-- About Us + Social -->
                <div class="col-md-3 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2"><?php echo e(custom_trans('about_us')); ?></h4>
                    <div class="footer-title-underline mb-3"></div>
                    <div class="footer-social mb-3">
                        <?php if($contactSettings && $contactSettings->social_facebook): ?>
                            <a href="<?php echo e($contactSettings->social_facebook); ?>" target="_blank"
                                class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if($contactSettings && $contactSettings->social_twitter): ?>
                            <a href="<?php echo e($contactSettings->social_twitter); ?>" target="_blank"
                                class="footer-social-icon"><i class="fab fa-twitter"></i></a>
                        <?php endif; ?>
                        <?php if($contactSettings && $contactSettings->social_youtube): ?>
                            <a href="<?php echo e($contactSettings->social_youtube); ?>" target="_blank"
                                class="footer-social-icon"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                        <?php if($contactSettings && $contactSettings->social_linkedin): ?>
                            <a href="<?php echo e($contactSettings->social_linkedin); ?>" target="_blank"
                                class="footer-social-icon"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Links -->
                <div class="col-md-2 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2"><?php echo e(custom_trans('our_links')); ?></h4>
                    <div class="footer-title-underline mb-3"></div>
                    <ul class="footer-links list-unstyled">
                        <li><a href="<?php echo e(route('home')); ?>"><?php echo e(custom_trans('home')); ?></a></li>
                        <li><a href="<?php echo e(route('categories.index')); ?>"><?php echo e(custom_trans('courses')); ?></a></li>
                        <li><a href="<?php echo e(route('contact')); ?>"><?php echo e(custom_trans('contact_us')); ?></a></li>
                        <li><a href="<?php echo e(route('blog.index')); ?>"><?php echo e(custom_trans('blog')); ?></a></li>
                    </ul>
                </div>
                <!-- Latest Post -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2"><?php echo e(custom_trans('latest_posts')); ?></h4>
                    <div class="footer-title-underline mb-3"></div>
                    <?php
                        $latestBlogs = \App\Models\Blog::published()->with('category')->latest()->take(2)->get();
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $latestBlogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="footer-post d-flex align-items-center <?php echo e(!$loop->last ? 'mb-3' : ''); ?>">
                            <img src="<?php echo e($blog->image_url ?? asset('images/placeholder-image.png')); ?>"
                                class="footer-post-img me-3" alt="<?php echo e($blog->title); ?>">
                            <div>
                                <div class="footer-post-title">
                                    <a href="<?php echo e(route('blog.show', $blog->slug)); ?>"
                                        class="text-white text-decoration-none">
                                        <?php echo e(Str::limit($blog->title, 50)); ?>

                                    </a>
                                </div>
                                <div class="footer-post-date"><?php echo e($blog->created_at->format('d-m-Y')); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="footer-post d-flex align-items-center mb-3">
                            <img src="<?php echo e(asset('images/placeholder-image.png')); ?>" class="footer-post-img me-3"
                                alt="No posts">
                            <div>
                                <div class="footer-post-title text-muted"><?php echo e(custom_trans('no_blog_posts')); ?></div>
                                <div class="footer-post-date text-muted"><?php echo e(custom_trans('coming_soon')); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Contact + Language -->
                <div class="col-md-3">
                    <h4 class="footer-title mb-2"><?php echo e(custom_trans('contact_us')); ?></h4>
                    <div class="footer-title-underline mb-3"></div>
                    <div class="footer-contact mb-3">
                        <?php if($contactSettings && $contactSettings->phone): ?>
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-phone"></i></span>
                                <a href="tel:<?php echo e($contactSettings->phone); ?>"
                                    class="text-white text-decoration-none"><?php echo e($contactSettings->phone); ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if($contactSettings && $contactSettings->email): ?>
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-envelope"></i></span>
                                <a href="mailto:<?php echo e($contactSettings->email); ?>"
                                    class="text-white text-decoration-none"><?php echo e($contactSettings->email); ?></a>
                            </div>
                        <?php endif; ?>
                        <?php if($contactSettings && $contactSettings->address): ?>
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                                <?php echo e($contactSettings->address); ?>

                            </div>
                        <?php endif; ?>
                        <?php if($contactSettings && $contactSettings->office_hours): ?>
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-clock"></i></span>
                                <?php echo e($contactSettings->office_hours); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="footer-lang-dropdown mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white border-0"><i
                                    class="fas fa-globe"></i></span>
                            <select class="form-select bg-dark text-white border-0" id="footerLangSelect">
                                <?php $__currentLoopData = $availableLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($language->code); ?>"
                                        <?php echo e($currentLanguage->id == $language->id ? 'selected' : ''); ?>>
                                        <?php echo e(strtoupper($language->code)); ?> - <?php echo e($language->native_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright & Back to Top -->
        <div class="footer-bottom-bar d-flex justify-content-between align-items-center px-3 px-md-5 py-3">
            <div class="footer-logo d-flex align-items-center">
                <img src="https://eclass.mediacity.co.in/demo2/public/images/logo/logo.png" alt="E-Class Logo"
                    style="height:32px; margin-right:10px;">
            </div>
            <div class="footer-copyright text-center flex-grow-1">Copyright © <?php echo e(date('Y')); ?> eClass.</div>
            <button id="backToTopBtn" class="btn btn-light btn-lg rounded-circle shadow-sm"><i
                    class="fa fa-arrow-up"></i></button>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar -->
    <div class="mobile-bottom-bar">
        <a href="<?php echo e(route('home')); ?>" class="mobile-nav-item <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="<?php echo e(route('categories.index')); ?>"
            class="mobile-nav-item <?php echo e(request()->routeIs('categories.*') ? 'active' : ''); ?>">
            <i class="fas fa-th-large"></i>
            <span><?php echo e(custom_trans('categories')); ?></span>
        </a>
        <a href="<?php echo e(route('blog.index')); ?>"
            class="mobile-nav-item <?php echo e(request()->routeIs('blog.index') ? 'active' : ''); ?>">
            <i class="fas fa-newspaper"></i>
            <span><?php echo e(custom_trans('blog')); ?></span>
        </a>
        <a href="<?php echo e(route('contact')); ?>"
            class="mobile-nav-item <?php echo e(request()->routeIs('contact') ? 'active' : ''); ?>">
            <i class="fas fa-envelope"></i>
            <span><?php echo e(custom_trans('contact_us')); ?></span>
        </a>
    </div>

    <script src="<?php echo e(asset('js/script.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for Slick and Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Slick JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Language Switcher Script -->
    <script>
        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
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

        // RTL support for toastr
        <?php if(\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl'): ?>
            toastr.options.positionClass = "toast-top-left";
            document.body.style.direction = 'rtl';
            document.body.style.textAlign = 'right';
        <?php endif; ?>

        // Footer language selector
        document.getElementById('footerLangSelect').addEventListener('change', function() {
            const selectedLanguage = this.value;
            window.location.href = '<?php echo e(url('/language')); ?>/' + selectedLanguage;
        });

        // Wishlist functionality
        document.addEventListener('DOMContentLoaded', function() {
            const wishlistButtons = document.querySelectorAll('.wishlist-btn');

            wishlistButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const inWishlist = this.dataset.inWishlist === 'true';
                    const icon = this.querySelector('i');

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        toastr.error('CSRF token not found. Please refresh the page.');
                        return;
                    }

                    // Send AJAX request
                    fetch(`/wishlist/${courseId}/toggle`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                if (response.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                // Update button state
                                this.dataset.inWishlist = data.inWishlist;
                                if (data.inWishlist) {
                                    icon.classList.add('text-danger');
                                    toastr.success(data.message || 'Course added to wishlist!');
                                } else {
                                    icon.classList.remove('text-danger');
                                    toastr.info(data.message ||
                                        'Course removed from wishlist!');
                                }

                                // Update wishlist count in header
                                const wishlistCount = document.querySelector(
                                    '.user-actions .fa-heart').parentElement.querySelector(
                                    '.badge');
                                if (wishlistCount) {
                                    // You can update the count here if needed
                                }
                            } else if (data) {
                                toastr.error(data.message || 'An error occurred');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred. Please try again.');
                        });
                });
            });

            // Enrollment functionality
            const enrollButtons = document.querySelectorAll('.enroll-btn');

            enrollButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const originalText = this.innerHTML;

                    // Disable button and show loading
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enrolling...';

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        toastr.error('CSRF token not found. Please refresh the page.');
                        this.disabled = false;
                        this.innerHTML = originalText;
                        return;
                    }

                    // Send AJAX request to enroll
                    fetch(`/courses/${courseId}/enroll`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                if (response.status === 401) {
                                    window.location.href = '/login';
                                    return;
                                }
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data && data.success) {
                                // Replace button with "Go to Course" link
                                const goToCourseLink = document.createElement('a');
                                goToCourseLink.href = `/courses/${courseId}/learn`;
                                goToCourseLink.className = 'btn btn-success flex-fill';
                                goToCourseLink.innerHTML =
                                    '<i class="fa fa-graduation-cap me-1"></i>Go to Course';

                                this.parentNode.replaceChild(goToCourseLink, this);

                                toastr.success(data.message ||
                                    'Successfully enrolled in course!');
                            } else if (data) {
                                toastr.error(data.message || 'An error occurred');
                                this.disabled = false;
                                this.innerHTML = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred. Please try again.');
                            this.disabled = false;
                            this.innerHTML = originalText;
                        });
                });
            });

            // Notification functionality
            const notificationItems = document.querySelectorAll('.notification-item');

            notificationItems.forEach(item => {
                item.addEventListener('click', function() {
                    const notificationId = this.dataset.notificationId;

                    // Mark as read if unread
                    if (this.classList.contains('unread')) {
                        this.classList.remove('unread');
                        const statusBadge = this.querySelector('.notification-status .badge');
                        if (statusBadge) {
                            statusBadge.remove();
                        }

                        // Update notification count
                        updateNotificationCount();

                        // Send AJAX to mark as read
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (csrfToken) {
                            fetch(`/notifications/${notificationId}/mark-as-read`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                                }
                            }).catch(error => console.error(
                                'Error marking notification as read:', error));
                        }
                    }
                });
            });

            // Function to update notification count
            function updateNotificationCount() {
                const unreadCount = document.querySelectorAll('.notification-item.unread').length;
                const badge = document.querySelector('.notification-badge');
                const headerBadge = document.querySelector('.notification-dropdown .dropdown-header .badge');

                if (unreadCount === 0) {
                    if (badge) badge.style.display = 'none';
                    if (headerBadge) headerBadge.style.display = 'none';
                } else {
                    if (badge) {
                        badge.style.display = 'block';
                        badge.textContent = unreadCount;
                    }
                    if (headerBadge) {
                        headerBadge.style.display = 'inline-block';
                        headerBadge.textContent = `${unreadCount} New`;
                    }
                }
            }

            // Like Button functionality
            const likeBtn = document.getElementById('likeBtn');
            if (likeBtn) {
                likeBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');

                    // Toggle like state
                    if (this.classList.contains('liked')) {
                        this.classList.remove('liked');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        toastr.info('You unliked this page!');
                    } else {
                        this.classList.add('liked');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        toastr.success('You liked this page!');
                    }
                });
            }

            // User Dropdown hover functionality
            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown) {
                const dropdownToggle = userDropdown.querySelector('.user-dropdown-btn');

                // Show dropdown on hover
                userDropdown.addEventListener('mouseenter', function() {
                    const dropdown = new bootstrap.Dropdown(dropdownToggle);
                    dropdown.show();
                });

                // Hide dropdown when mouse leaves
                userDropdown.addEventListener('mouseleave', function() {
                    const dropdown = new bootstrap.Dropdown(dropdownToggle);
                    dropdown.hide();
                });
            }

            // Newsletter Subscription functionality
            const newsletterForm = document.getElementById('newsletterForm');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const email = document.getElementById('newsletterEmail').value;
                    const submitBtn = document.getElementById('newsletterSubmitBtn');
                    const btnText = submitBtn.querySelector('.btn-text');
                    const btnLoading = submitBtn.querySelector('.btn-loading');

                    // Show loading state
                    submitBtn.disabled = true;
                    btnText.style.display = 'none';
                    btnLoading.style.display = 'inline-block';

                    // Send AJAX request
                    fetch('/newsletter/subscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                email: email
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);
                                newsletterForm.reset();
                            } else {
                                toastr.error(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            toastr.error('An error occurred while subscribing to the newsletter.');
                        })
                        .finally(() => {
                            // Hide loading state
                            submitBtn.disabled = false;
                            btnText.style.display = 'inline-block';
                            btnLoading.style.display = 'none';
                        });
                });
            }
        });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\laragon\www\courses-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>