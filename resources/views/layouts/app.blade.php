<!DOCTYPE html>
<html lang="{{ \App\Helpers\TranslationHelper::getCurrentLanguage()->code }}"
    dir="{{ \App\Helpers\TranslationHelper::getCurrentLanguage()->direction }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $mainContentSettings = \App\Models\MainContentSettings::getActive();
        $siteName =
            $mainContentSettings && $mainContentSettings->site_name
                ? $mainContentSettings->site_name
                : config('app.name') ?? 'E-Class - Online Learning Platform';
        $faviconUrl =
            $mainContentSettings && $mainContentSettings->favicon
                ? $mainContentSettings->favicon_url
                : asset('favicon.ico');
    @endphp
    <title>@yield('title', $siteName)</title>
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <!-- External Libraries - Always loaded -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Language-specific Styles -->
    @if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
        <!-- RTL Base Styles -->
        <link rel="stylesheet" href="{{ asset('css/rtl/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/utilities.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/utilities-extended.css') }}">

        <!-- RTL Layout Styles -->
        <link rel="stylesheet" href="{{ asset('css/rtl/layout.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/layout/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/layout/rtl.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/layout/header-nav.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/layout/language-switcher.css') }}">

        <!-- RTL Component Styles -->
        <link rel="stylesheet" href="{{ asset('css/rtl/components/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/components/buttons.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/components/cards.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/components/alerts.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/components/badges.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/components/dropdowns.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/components/forms.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/components/whatsapp-float.css') }}">

        <!-- RTL Additional Styles -->
        <link rel="stylesheet" href="{{ asset('css/rtl/responsive/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/base/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/fonts/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/webfonts/main.css') }}">
    @else
        <!-- LTR Base Styles -->
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
        <link rel="stylesheet" href="{{ asset('css/utilities-extended.css') }}">

        <!-- LTR Layout Styles -->
        <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layout/language-switcher.css') }}">
        <link rel="stylesheet" href="{{ asset('css/layout/header-nav.css') }}">

        <!-- LTR Component Styles -->
        <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/alerts.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/badges.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/dropdowns.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/forms.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components/whatsapp-float.css') }}">
    @endif

    <!-- Page-specific RTL Styles -->
    @if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
        @stack('rtl-styles')
    @endif

    @stack('styles')
</head>

<body>
    @php
        $contactSettings = \App\Models\ContactSettings::getActive();
    @endphp

    <!-- Header Section -->
    <header>
        <div class="top-bar-colored">
            <div class="top-bar-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 top-bar-left-colored">
                            <div class="top-bar-left">
                                @php
                                    $hasSocialLinks =
                                        $contactSettings &&
                                        ($contactSettings->social_facebook ||
                                            $contactSettings->social_twitter ||
                                            $contactSettings->social_youtube ||
                                            $contactSettings->social_linkedin ||
                                            $contactSettings->social_snapchat ||
                                            $contactSettings->social_tiktok);
                                @endphp
                                @if ($hasSocialLinks)
                                    <span>{{ custom_trans('Follow Us', 'front') }}:</span>
                                    @if ($contactSettings->social_facebook)
                                        <a href="{{ $contactSettings->social_facebook }}" target="_blank"><i
                                                class="fab fa-facebook-f"></i></a>
                                    @endif
                                    @if ($contactSettings->social_twitter)
                                        <a href="{{ $contactSettings->social_twitter }}" target="_blank"><i
                                                class="fab fa-twitter"></i></a>
                                    @endif
                                    @if ($contactSettings->social_youtube)
                                        <a href="{{ $contactSettings->social_youtube }}" target="_blank"><i
                                                class="fab fa-youtube"></i></a>
                                    @endif
                                    @if ($contactSettings->social_linkedin)
                                        <a href="{{ $contactSettings->social_linkedin }}" target="_blank"><i
                                                class="fab fa-linkedin-in"></i></a>
                                    @endif
                                    @if ($contactSettings->social_snapchat)
                                        <a href="{{ $contactSettings->social_snapchat }}" target="_blank"><i
                                                class="fab fa-snapchat"></i></a>
                                    @endif
                                    @if ($contactSettings->social_tiktok)
                                        <a href="{{ $contactSettings->social_tiktok }}" target="_blank"><i
                                                class="fab fa-tiktok"></i></a>
                                    @endif
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8 top-bar-right">
                            @if ($contactSettings && $contactSettings->phone)
                                <div class="contact-block">
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/phone-call.png"
                                        alt="Phone" class="contact-icon">
                                    <div class="contact-info">
                                        <span class="contact-label">{{ custom_trans('Call Now!', 'front') }}</span>
                                        <span class="contact-value"><b><a href="tel:{{ $contactSettings->phone }}"
                                                    class="text-decoration-none">{{ $contactSettings->phone }}</a></b></span>
                                    </div>
                                </div>
                            @endif
                            @if ($contactSettings && $contactSettings->email)
                                <div class="contact-block">
                                    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/icon/mailing.png"
                                        alt="Email" class="contact-icon">
                                    <div class="contact-info">
                                        <span class="contact-label">{{ custom_trans('Email Now', 'front') }}</span>
                                        <span class="contact-value"><b><a href="mailto:{{ $contactSettings->email }}"
                                                    class="text-decoration-none">{{ $contactSettings->email }}</a></b></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="main-nav main-nav-white">
            <div class="container nav-flex">
                @php
                    $mainContentSettings = \App\Models\MainContentSettings::getActive();
                @endphp
                <div class="logo">
                    <a href="{{ route('home') }}" class="logo-link">
                        <img src="{{ $mainContentSettings ? $mainContentSettings->logo_url : asset('images/default-logo.svg') }}"
                            alt="{{ $mainContentSettings ? $mainContentSettings->logo_alt_text : 'Site Logo' }}"
                            class="logo-img">
                    </a>
                </div>
                <div class="mobile-header-actions d-lg-none">
                    <button type="button" class="mobile-icon-btn mobile-menu-btn" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileNavOffcanvas" aria-controls="mobileNavOffcanvas"
                        aria-label="{{ __('Toggle navigation') }}">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button type="button" class="mobile-icon-btn mobile-search-btn" data-bs-toggle="modal"
                        data-bs-target="#mobileSearchModal"
                        aria-label="{{ custom_trans('search_courses', 'front') }}">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="{{ route('cart.index') }}" class="mobile-icon-btn mobile-cart-btn position-relative"
                        aria-label="{{ custom_trans('cart', 'front') }}">
                        <i class="fas fa-shopping-cart"></i>
                        @if (auth()->check() && auth()->user()->cartItems && auth()->user()->cartItems->count() > 0)
                            <span
                                class="badge bg-danger cart-count-badge">{{ auth()->user()->cartItems->count() }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-desktop-actions d-none d-lg-flex">
                    <ul class="nav-links">
                        <li><a href="{{ route('home') }}">{{ custom_trans('home', 'front') }}</a></li>
                        <li class="dropdown">
                            <a href="{{ route('categories.index') }}">{{ custom_trans('categories', 'front') }}</a>
                            <ul class="dropdown-menu">
                                @forelse($navigationCategories as $category)
                                    <li><a
                                            href="{{ route('categories.show', $category->slug) }}">{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}</a>
                                    </li>
                                @empty
                                    <li><a
                                            href="{{ route('categories.index') }}">{{ custom_trans('no_category_found', 'front') }}</a>
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                        <li><a href="{{ route('blog.index') }}">{{ custom_trans('blog', 'front') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ custom_trans('contact', 'front') }}</a></li>
                    </ul>
                    <!-- Language Switcher -->
                    @php
                        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
                        $availableLanguages = \App\Helpers\TranslationHelper::getAvailableLanguages();
                    @endphp
                    <div class="language-switcher me-3">
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle text-black" type="button"
                                id="frontendLangDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                aria-expanded="false">
                                <i class="fas fa-globe me-1"></i>
                                {{ strtoupper($currentLanguage->code) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="frontendLangDropdown">
                                @foreach ($availableLanguages as $language)
                                    <li>
                                        <a class="dropdown-item {{ $currentLanguage->id == $language->id ? 'active' : '' }}"
                                            href="{{ route('language.switch', $language->code) }}?redirect={{ urlencode(url()->full()) }}">
                                            <span class="me-2">
                                                @if ($language->direction == 'rtl')
                                                    <i class="fas fa-text-width" title="RTL"></i>
                                                @else
                                                    <i class="fas fa-text-width" title="LTR"></i>
                                                @endif
                                            </span>
                                            {{ $language->native_name }} ({{ $language->code }})
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="search-container me-3">
                        <form action="{{ route('courses.search') }}" method="GET" class="d-flex">
                            <input type="text" name="q"
                                placeholder="{{ custom_trans('search_courses', 'front') }}"
                                class="form-control search-input" value="{{ request('q') }}">
                            <button type="submit" class="btn btn-outline-dark ms-2">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- User Actions -->
                    <div class="user-actions me-3">
                        @auth
                            <!-- Notifications -->
                            <div class="dropdown">
                                <button class="btn btn-outline-dark position-relative notification-btn" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell"></i>
                                    @if (auth()->user()->unreadNotifications->count() > 0)
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                                            {{ auth()->user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                                    <div
                                        class="dropdown-header d-flex justify-content-between align-items-center p-3 border-bottom">
                                        <h6 class="mb-0 fw-bold text-dark">
                                            <i class="fas fa-bell me-2 text-primary"></i>
                                            {{ custom_trans('notifications', 'front') }}
                                        </h6>
                                        @if (auth()->check() && auth()->user()->notifications && auth()->user()->notifications->count() > 0)
                                            <span
                                                class="badge bg-warning text-dark rounded-pill">{{ auth()->user()->unreadNotifications->count() }}
                                                {{ custom_trans('new', 'front') }}</span>
                                        @endif
                                    </div>

                                    <div class="notification-list">
                                        @forelse(auth()->user()->notifications->take(8) as $notification)
                                            <div class="dropdown-item notification-item p-3 {{ $notification->read_at ? '' : 'unread' }}"
                                                data-notification-id="{{ $notification->id }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="notification-icon me-3">
                                                        @if (str_contains($notification->type, 'Wishlist'))
                                                            <i class="fas fa-heart text-danger"></i>
                                                        @elseif (str_contains($notification->type, 'Course'))
                                                            <i class="fas fa-graduation-cap text-primary"></i>
                                                        @elseif (str_contains($notification->type, 'Quiz'))
                                                            <i class="fas fa-question-circle text-warning"></i>
                                                        @else
                                                            <i class="fas fa-bell text-info"></i>
                                                        @endif
                                                    </div>
                                                    <div class="notification-content flex-grow-1">
                                                        <div class="notification-message fw-semibold text-dark mb-1">
                                                            {{ $notification->data['message'] ?? 'New notification' }}
                                                        </div>
                                                        <div class="notification-time text-muted small">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    @if (!$notification->read_at)
                                                        <div class="notification-status ms-2">
                                                            <span class="badge bg-danger rounded-circle w-8 h-8"></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="dropdown-item text-center py-4">
                                                <div class="text-muted">
                                                    <i class="far fa-bell-slash fa-2x mb-2"></i>
                                                    <div>{{ custom_trans('no_notifications', 'front') }}</div>
                                                    <small
                                                        class="text-muted">{{ custom_trans('you_will_see_notifications_here', 'front') }}</small>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>

                                    @if (auth()->check() && auth()->user()->notifications && auth()->user()->notifications->count() > 0)
                                        <div class="dropdown-divider"></div>
                                        <div class="dropdown-item text-center">
                                            <a href="{{ route('notifications.index') }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>
                                                {{ custom_trans('view_all_notifications', 'front') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Wishlist -->
                            <a href="{{ route('wishlist.index') }}" class="btn btn-outline-dark position-relative">
                                <i class="fas fa-heart"></i>
                                @if (auth()->check() && auth()->user()->wishlistItems && auth()->user()->wishlistItems->count() > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ auth()->user()->wishlistItems->count() }}
                                    </span>
                                @endif
                            </a>

                            <!-- Cart -->
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-dark position-relative">
                                <i class="fas fa-shopping-cart"></i>
                                @if (auth()->check() && auth()->user()->cartItems && auth()->user()->cartItems->count() > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ auth()->user()->cartItems->count() }}
                                    </span>
                                @endif
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
                                    <span class="user-name">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down ms-2"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                                    <li class="dropdown-header">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-3">
                                                <i class="fas fa-user-circle fa-2x text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ auth()->user()->name }}</div>
                                                <small class="text-muted">{{ auth()->user()->email }}</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('student.my-courses') }}">
                                            <i class="fas fa-graduation-cap me-2 text-primary"></i>
                                            {{ custom_trans('my_courses', 'front') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                            <i class="fas fa-heart me-2 text-danger"></i>
                                            {{ custom_trans('wishlist', 'front') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('purchase.history') }}">
                                            <i class="fas fa-history me-2 text-info"></i>
                                            {{ custom_trans('purchase_history', 'front') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fas fa-user-edit me-2 text-success"></i>
                                            {{ custom_trans('profile', 'front') }}
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>
                                                {{ custom_trans('logout', 'front') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endauth
                    </div>

                    <div class="auth-buttons">
                        @guest
                            <a href="{{ route('login') }}"
                                class="btn btn-login-colored">{{ custom_trans('login', 'front') }}</a>
                        @else
                            <!-- User dropdown is now handled above -->
                        @endguest

                        @guest
                            <a href="{{ route('register') }}"
                                class="btn btn-register-colored">{{ custom_trans('register', 'front') }}</a>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>
        @php
            $currentLanguage = $currentLanguage ?? \App\Helpers\TranslationHelper::getCurrentLanguage();
            $availableLanguages = $availableLanguages ?? \App\Helpers\TranslationHelper::getAvailableLanguages();
        @endphp
        <div class="offcanvas offcanvas-start mobile-nav-offcanvas" tabindex="-1" id="mobileNavOffcanvas"
            aria-labelledby="mobileNavOffcanvasLabel">
            <div class="offcanvas-header">
                <div class="d-flex align-items-center flex-grow-1">
                    <a href="{{ route('home') }}" class="mobile-offcanvas-logo me-3">
                        <img src="{{ $mainContentSettings ? $mainContentSettings->logo_url : asset('images/default-logo.svg') }}"
                            alt="{{ $mainContentSettings ? $mainContentSettings->logo_alt_text : 'Site Logo' }}"
                            class="mobile-offcanvas-logo-img">
                    </a>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column gap-4">
                <form action="{{ route('courses.search') }}" method="GET" class="mobile-offcanvas-search">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" class="form-control border-start-0"
                            placeholder="{{ custom_trans('search_courses', 'front') }}"
                            value="{{ request('q') }}">
                    </div>
                </form>

                <nav class="mobile-offcanvas-nav">
                    <ul class="list-unstyled m-0">
                        <li class="mb-2">
                            <a href="{{ route('home') }}" class="mobile-offcanvas-link">
                                <i class="fas fa-home me-2"></i>{{ custom_trans('home', 'front') }}
                            </a>
                        </li>
                        <li class="mb-2">
                            <button
                                class="mobile-offcanvas-link w-100 text-start d-flex align-items-center justify-content-between"
                                type="button" data-bs-toggle="collapse" data-bs-target="#mobileCategoriesCollapse"
                                aria-expanded="false">
                                <span><i
                                        class="fas fa-th-large me-2"></i>{{ custom_trans('categories', 'front') }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="collapse mt-2" id="mobileCategoriesCollapse">
                                <ul class="list-unstyled ps-3">
                                    @forelse($navigationCategories as $category)
                                        <li class="mb-2">
                                            <a href="{{ route('categories.show', $category->slug) }}"
                                                class="mobile-offcanvas-sublink">
                                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}
                                            </a>
                                        </li>
                                    @empty
                                        <li class="text-muted small">
                                            {{ custom_trans('no_category_found', 'front') }}
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('blog.index') }}" class="mobile-offcanvas-link">
                                <i class="fas fa-newspaper me-2"></i>{{ custom_trans('blog', 'front') }}
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('contact') }}" class="mobile-offcanvas-link">
                                <i class="fas fa-envelope me-2"></i>{{ custom_trans('contact', 'front') }}
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="mobile-offcanvas-languages">
                    <h6 class="fw-semibold mb-2">{{ custom_trans('language', 'front') }}</h6>
                    <div class="btn-group w-100">
                        <button
                            class="btn btn-outline-secondary dropdown-toggle d-flex justify-content-between align-items-center"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span><i class="fas fa-globe me-2"></i>{{ strtoupper($currentLanguage->code) }}</span>
                        </button>
                        <ul class="dropdown-menu w-100">
                            @foreach ($availableLanguages as $language)
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center {{ $currentLanguage->id == $language->id ? 'active' : '' }}"
                                        href="{{ route('language.switch', $language->code) }}?redirect={{ urlencode(url()->full()) }}">
                                        <span>{{ $language->native_name }} ({{ $language->code }})</span>
                                        @if ($currentLanguage->id == $language->id)
                                            <i class="fas fa-check text-success"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="mobile-offcanvas-actions mt-auto">
                    @auth
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.my-courses') }}" class="btn btn-outline-primary">
                                <i class="fas fa-graduation-cap me-2"></i>{{ custom_trans('my_courses', 'front') }}
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-heart me-2"></i>{{ custom_trans('wishlist', 'front') }}
                            </a>
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart me-2"></i>{{ custom_trans('cart', 'front') }}
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ custom_trans('logout', 'front') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>{{ custom_trans('login', 'front') }}
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>{{ custom_trans('register', 'front') }}
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Newsletter Subscribe Section -->
    @php
        $newsletterSettings = \App\Models\NewsletterSettings::active()->first();
    @endphp

    @if ($newsletterSettings)
        <section class="newsletter-section py-5">
            <div class="container-fluid px-0">
                <div class="row justify-content-center align-items-center g-0">
                    <div
                        class="col-lg-6 d-flex flex-column flex-md-row align-items-center justify-content-center text-center text-md-start mb-4 mb-lg-0 ps-lg-5">
                        <div class="me-md-4 mb-3 mb-md-0">
                            @if ($newsletterSettings->icon)
                                <i class="{{ $newsletterSettings->icon }} fa-3x paper-plane-icon"></i>
                            @else
                                <i class="fa-solid fa-paper-plane fa-3x paper-plane-icon"></i>
                            @endif
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 fs-2-5rem">
                                {{ $newsletterSettings->getDisplayTitle() }}</h2>
                            <p class="mb-0 fs-1-1rem text-light">
                                {{ $newsletterSettings->getDisplayDescription() }}</p>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex justify-content-center align-items-center">
                        <form id="newsletterForm" class="w-100 max-w-520">
                            @csrf
                            <div class="input-group input-group-lg flex-row-reverse">
                                <input type="email" name="email" id="newsletterEmail"
                                    class="form-control border-0 rounded-0 rounded-start"
                                    placeholder="{{ $newsletterSettings->getDisplayPlaceholder() }}"
                                    aria-label="Email Address" required>
                                <button class="btn btn-outline-light rounded-0 rounded-end px-4 fw-bold newsletter-btn"
                                    type="submit" id="newsletterSubmitBtn">
                                    <span class="btn-text">{{ $newsletterSettings->getDisplayButtonText() }}</span>
                                    <span class="btn-loading d-none-initially">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Footer -->
    <footer class="footer-main footer-bg position-relative pt-5">
        <div class="container">
            <div class="row text-white pb-4">
                <!-- About Us + Social -->
                <div class="col-6 col-sm-6 col-md-3 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2">{{ custom_trans('about_us', 'front') }}</h4>
                    <div class="footer-title-underline mb-3"></div>
                    @php
                        $hasFooterSocialLinks =
                            $contactSettings &&
                            ($contactSettings->social_facebook ||
                                $contactSettings->social_twitter ||
                                $contactSettings->social_youtube ||
                                $contactSettings->social_linkedin ||
                                $contactSettings->social_snapchat ||
                                $contactSettings->social_tiktok);
                    @endphp
                    @if ($hasFooterSocialLinks)
                        <div class="footer-social mb-3">
                            @if ($contactSettings->social_facebook)
                                <a href="{{ $contactSettings->social_facebook }}" target="_blank"
                                    class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if ($contactSettings->social_twitter)
                                <a href="{{ $contactSettings->social_twitter }}" target="_blank"
                                    class="footer-social-icon"><i class="fab fa-twitter"></i></a>
                            @endif
                            @if ($contactSettings->social_youtube)
                                <a href="{{ $contactSettings->social_youtube }}" target="_blank"
                                    class="footer-social-icon"><i class="fab fa-youtube"></i></a>
                            @endif
                            @if ($contactSettings->social_linkedin)
                                <a href="{{ $contactSettings->social_linkedin }}" target="_blank"
                                    class="footer-social-icon"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                            @if ($contactSettings->social_snapchat)
                                <a href="{{ $contactSettings->social_snapchat }}" target="_blank"
                                    class="footer-social-icon"><i class="fab fa-snapchat"></i></a>
                            @endif
                            @if ($contactSettings->social_tiktok)
                                <a href="{{ $contactSettings->social_tiktok }}" target="_blank"
                                    class="footer-social-icon"><i class="fab fa-tiktok"></i></a>
                            @endif
                        </div>
                    @endif
                    <!-- PayPal Payment Method -->
                    <div class="mt-3">
                        <p class="text-white-50 small mb-2">{{ custom_trans('We Accept', 'front') }}:</p>
                        <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg" 
                             alt="PayPal" 
                             style="max-width: 120px; height: auto; background: white; padding: 5px; border-radius: 4px;">
                    </div>
                </div>
                <!-- Links -->
                <div class="col-6 col-sm-6 col-md-2 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2">{{ custom_trans('our_links', 'front') }}</h4>
                    <div class="footer-title-underline mb-3"></div>
                    <ul class="footer-links list-unstyled">
                        <li><a href="{{ route('home') }}">{{ custom_trans('home', 'front') }}</a></li>
                        <li><a href="{{ route('categories.index') }}">{{ custom_trans('courses', 'front') }}</a>
                        </li>
                        <li><a href="{{ route('contact') }}">{{ custom_trans('contact_us', 'front') }}</a></li>
                        <li><a href="{{ route('blog.index') }}">{{ custom_trans('blog', 'front') }}</a></li>
                        @php
                            $termsPage = \App\Models\TermsConditions::where('is_active', true)->first();
                        @endphp
                        @if ($termsPage)
                            <li><a
                                    href="{{ route('terms-conditions', $termsPage->slug) }}">{{ custom_trans('terms_and_conditions', 'front') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <!-- Latest Post -->
                <div class="col-12 col-sm-12 col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2">{{ custom_trans('latest_posts', 'front') }}</h4>
                    <div class="footer-title-underline mb-3"></div>
                    @php
                        $latestBlogs = \App\Models\Blog::published()
                            ->with(['category', 'author'])
                            ->latest()
                            ->take(2)
                            ->get();
                    @endphp
                    @forelse($latestBlogs as $blog)
                        <div class="footer-post d-flex align-items-center {{ !$loop->last ? 'mb-3' : '' }}">
                            <img src="{{ $blog->getLocalizedImageUrl() ?? asset('images/placeholder-image.png') }}"
                                class="footer-post-img me-3" alt="{{ $blog->getLocalizedTitle() }}">
                            <div>
                                <div class="footer-post-title">
                                    <a href="{{ route('blog.show', $blog->slug) }}"
                                        class="text-white text-decoration-none">
                                        {{ Str::limit($blog->getLocalizedTitle(), 50) }}
                                    </a>
                                </div>
                                <div class="footer-post-date">{{ $blog->created_at->format('d-m-Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="footer-post d-flex align-items-center mb-3">
                            <img src="{{ asset('images/placeholder-image.png') }}" class="footer-post-img me-3"
                                alt="No posts">
                            <div>
                                <div class="footer-post-title text-muted">
                                    {{ custom_trans('no_blog_posts', 'front') }}</div>
                                <div class="footer-post-date text-muted">{{ custom_trans('coming_soon', 'front') }}
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
                <!-- Contact + Language -->
                <div class="col-12 col-sm-12 col-md-3">
                    <h4 class="footer-title mb-2">{{ custom_trans('contact_us', 'front') }}</h4>
                    <div class="footer-title-underline mb-3"></div>
                    <div class="footer-contact mb-3">
                        @if ($contactSettings && $contactSettings->phone)
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-phone"></i></span>
                                <a href="tel:{{ $contactSettings->phone }}"
                                    class="text-white text-decoration-none">{{ $contactSettings->phone }}</a>
                            </div>
                        @endif
                        @if ($contactSettings && $contactSettings->email)
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-envelope"></i></span>
                                <a href="mailto:{{ $contactSettings->email }}"
                                    class="text-white text-decoration-none">{{ $contactSettings->email }}</a>
                            </div>
                        @endif
                        @if ($contactSettings && $contactSettings->address)
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                                {{ $contactSettings->address }}
                            </div>
                        @endif
                        @if ($contactSettings && $contactSettings->office_hours)
                            <div class="footer-contact-item">
                                <span class="footer-contact-icon"><i class="fas fa-clock"></i></span>
                                {{ $contactSettings->office_hours }}
                            </div>
                        @endif
                    </div>
                    <div class="footer-lang-dropdown mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-white border-0"><i
                                    class="fas fa-globe"></i></span>
                            <select class="form-select bg-dark text-white border-0" id="footerLangSelect">
                                @foreach ($availableLanguages as $language)
                                    <option value="{{ $language->code }}"
                                        {{ $currentLanguage->id == $language->id ? 'selected' : '' }}>
                                        {{ strtoupper($language->code) }} - {{ $language->native_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright & Back to Top -->
        <div class="footer-bottom-bar d-flex justify-content-between align-items-center px-3 px-md-5 py-3">
            <div class="footer-logo d-flex align-items-center">
                <a href="{{ route('home') }}" class="footer-logo-link">
                    <img src="{{ $mainContentSettings ? $mainContentSettings->logo_url : asset('images/default-logo.svg') }}"
                        alt="{{ $mainContentSettings ? $mainContentSettings->logo_alt_text : 'Site Logo' }}"
                        class="footer-logo-img">
                </a>
            </div>
            <div class="footer-copyright text-center flex-grow-1">Copyright © {{ date('Y') }} Tadawul Bel Araby.
            </div>
            <button id="backToTopBtn" class="btn btn-light btn-lg rounded-circle shadow-sm"><i
                    class="fa fa-arrow-up"></i></button>
        </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar -->
    <div class="mobile-bottom-bar">
        <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('categories.index') }}"
            class="mobile-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>{{ custom_trans('categories', 'front') }}</span>
        </a>
        <a href="{{ route('blog.index') }}"
            class="mobile-nav-item {{ request()->routeIs('blog.index') ? 'active' : '' }}">
            <i class="fas fa-newspaper"></i>
            <span>{{ custom_trans('blog', 'front') }}</span>
        </a>
        <a href="{{ route('contact') }}"
            class="mobile-nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>{{ custom_trans('contact_us', 'front') }}</span>
        </a>
    </div>

    @php
        $currentLanguage = isset($currentLanguage)
            ? $currentLanguage
            : \App\Helpers\TranslationHelper::getCurrentLanguage();
    @endphp

    <!-- Mobile Search Modal -->
    <div class="modal fade mobile-search-modal" id="mobileSearchModal" tabindex="-1"
        aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" dir="{{ $currentLanguage->direction }}">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="mobileSearchModalLabel">
                        {{ custom_trans('search_courses', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body pt-2">
                    <form action="{{ route('courses.search') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="q" class="form-control"
                            placeholder="{{ custom_trans('search_courses', 'front') }}"
                            value="{{ request('q') }}" autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/script.js') }}"></script>
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
        @if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
            toastr.options.positionClass = "toast-top-left";
            document.body.style.direction = 'rtl';
            document.body.style.textAlign = 'right';
        @endif

        // Wishlist functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Footer language selector
            const footerLangSelect = document.getElementById('footerLangSelect');
            if (footerLangSelect) {
                footerLangSelect.addEventListener('change', function() {
                    const selectedLanguage = this.value;
                    window.location.href = '{{ url('/language') }}/' + selectedLanguage;
                });
            }

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
                    fetch(`/wishlist/toggle/${courseId}`, {
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

            // Enrollment functionality for generic course cards (outside detail page)
            const enrollButtons = document.querySelectorAll('.enroll-btn');

            enrollButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.dataset.courseId;
                    const enrollType = this.dataset.enrollType || 'free';
                    const originalText = this.innerHTML;

                    // Disable button and show loading
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';

                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        toastr.error('CSRF token not found. Please refresh the page.');
                        this.disabled = false;
                        this.innerHTML = originalText;
                        return;
                    }

                    // Helper: handle error UI reset
                    const resetButton = () => {
                        this.disabled = false;
                        this.innerHTML = originalText;
                    };

                    // Paid courses -> add to cart instead of direct enroll
                    if (enrollType === 'paid') {
                        fetch(`/cart/add/${courseId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                                },
                                body: JSON.stringify({})
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
                                    toastr.success(data.message ||
                                        'Course added to cart successfully.');
                                } else if (data) {
                                    // Already in cart / already enrolled messages
                                    toastr.info(data.message ||
                                        'Unable to add course to cart.');
                                }
                                resetButton();
                            })
                            .catch(error => {
                                console.error('Cart add error:', error);
                                toastr.error('An error occurred while adding course to cart.');
                                resetButton();
                            });

                        return; // Skip direct enrollment for paid courses
                    }

                    // Free courses -> direct enroll
                    fetch(`/courses/${courseId}/enroll`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                            },
                            body: JSON.stringify({})
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
                                // Show enrolled state first
                                this.className = 'btn btn-success flex-fill';
                                this.innerHTML = '<i class="fa fa-check me-1"></i>Enrolled';
                                this.disabled = true;

                                // Show success message
                                toastr.success(data.message ||
                                    'Successfully enrolled in course!');

                                // After 2 seconds, replace with "Go to Course" link
                                setTimeout(() => {
                                    const goToCourseLink = document.createElement('a');
                                    goToCourseLink.href = `/courses/${courseId}/learn`;
                                    goToCourseLink.className =
                                        'btn btn-success flex-fill';
                                    goToCourseLink.innerHTML =
                                        '<i class="fa fa-graduation-cap me-1"></i>Go to Course';

                                    this.parentNode.replaceChild(goToCourseLink, this);
                                }, 2000);
                            } else if (data) {
                                toastr.error(data.message || 'An error occurred');
                                resetButton();
                            }
                        })
                        .catch(error => {
                            console.error('Enroll error:', error);
                            toastr.error('An error occurred. Please try again.');
                            resetButton();
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
                const dropdownInstance = new bootstrap.Dropdown(dropdownToggle, {
                    autoClose: true
                });
                let hideTimeout = null;

                // Show dropdown on hover
                userDropdown.addEventListener('mouseenter', function() {
                    if (hideTimeout) {
                        clearTimeout(hideTimeout);
                        hideTimeout = null;
                    }
                    dropdownInstance.show();
                });

                // Hide dropdown when mouse leaves
                userDropdown.addEventListener('mouseleave', function() {
                    hideTimeout = setTimeout(() => dropdownInstance.hide(), 200);
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

    @stack('scripts')

    <!-- Floating WhatsApp Button -->
    @php
        $contactSettings = \App\Models\ContactSettings::getActive();
        $whatsappPhone =
            $contactSettings && $contactSettings->phone ? preg_replace('/[^0-9]/', '', $contactSettings->phone) : '';
    @endphp

    @if ($whatsappPhone)
        <a href="https://wa.me/{{ $whatsappPhone }}" target="_blank" class="whatsapp-float"
            title="Chat with us on WhatsApp" aria-label="Chat with us on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    @endif

</body>

</html>
