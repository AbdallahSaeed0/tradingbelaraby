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
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('css/utilities-extended.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Layout Styles -->
    <link rel="stylesheet" href="{{ asset('css/layout/rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/language-switcher.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout/header-nav.css') }}">

    <!-- Component Styles -->
    <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/alerts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/badges.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/dropdowns.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/whatsapp-float.css') }}">

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
                                    <span>Follow us:-</span>
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
                                        <span class="contact-label">Call Now !</span>
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
                                        <span class="contact-label">Email Now</span>
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
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}">{{ custom_trans('home') }}</a></li>
                    <li class="dropdown">
                        <a href="{{ route('categories.index') }}">{{ custom_trans('categories') }}</a>
                        <ul class="dropdown-menu">
                            @forelse($navigationCategories as $category)
                                <li><a
                                        href="{{ route('categories.show', $category->slug) }}">{{ \App\Helpers\TranslationHelper::getLocalizedContent($category->name, $category->name_ar) }}</a>
                                </li>
                            @empty
                                <li><a
                                        href="{{ route('categories.index') }}">{{ custom_trans('no_category_found') }}</a>
                                </li>
                            @endforelse
                        </ul>
                    </li>
                    <li><a href="{{ route('blog.index') }}">{{ custom_trans('blog') }}</a></li>
                    <li><a href="{{ route('contact') }}">{{ custom_trans('contact') }}</a></li>
                </ul>
                <!-- Language Switcher -->
                @php
                    $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
                    $availableLanguages = \App\Helpers\TranslationHelper::getAvailableLanguages();
                @endphp
                <div class="language-switcher me-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle text-black" type="button"
                            id="frontendLangDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe me-1"></i>
                            {{ strtoupper($currentLanguage->code) }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="frontendLangDropdown">
                            @foreach ($availableLanguages as $language)
                                <li>
                                    <a class="dropdown-item {{ $currentLanguage->id == $language->id ? 'active' : '' }}"
                                        href="{{ route('language.switch', $language->code) }}">
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
                        <input type="text" name="q" placeholder="{{ custom_trans('search_courses') }}"
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
                                        {{ custom_trans('notifications') }}
                                    </h6>
                                    @if (auth()->check() && auth()->user()->notifications && auth()->user()->notifications->count() > 0)
                                        <span
                                            class="badge bg-warning text-dark rounded-pill">{{ auth()->user()->unreadNotifications->count() }}
                                            {{ custom_trans('new') }}</span>
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
                                                <div>{{ custom_trans('no_notifications') }}</div>
                                                <small
                                                    class="text-muted">{{ custom_trans('you_will_see_notifications_here') }}</small>
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
                                            {{ custom_trans('view_all_notifications') }}
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
                                        {{ custom_trans('my_courses') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('wishlist.index') }}">
                                        <i class="fas fa-heart me-2 text-danger"></i>
                                        {{ custom_trans('wishlist') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('purchase.history') }}">
                                        <i class="fas fa-history me-2 text-info"></i>
                                        {{ custom_trans('purchase_history') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-edit me-2 text-success"></i>
                                        {{ custom_trans('profile') }}
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
                                            {{ custom_trans('logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>

                <div class="auth-buttons">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-login-colored">{{ custom_trans('login') }}</a>
                    @else
                        <!-- User dropdown is now handled above -->
                    @endguest

                    @guest
                        <a href="{{ route('register') }}"
                            class="btn btn-register-colored">{{ custom_trans('register') }}</a>
                    @endguest
                </div>
                <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
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
                            <div class="input-group input-group-lg">
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
                <div class="col-md-3 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2">{{ custom_trans('about_us') }}</h4>
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
                </div>
                <!-- Links -->
                <div class="col-md-2 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2">{{ custom_trans('our_links') }}</h4>
                    <div class="footer-title-underline mb-3"></div>
                    <ul class="footer-links list-unstyled">
                        <li><a href="{{ route('home') }}">{{ custom_trans('home') }}</a></li>
                        <li><a href="{{ route('categories.index') }}">{{ custom_trans('courses') }}</a></li>
                        <li><a href="{{ route('contact') }}">{{ custom_trans('contact_us') }}</a></li>
                        <li><a href="{{ route('blog.index') }}">{{ custom_trans('blog') }}</a></li>
                        @php
                            $termsPage = \App\Models\TermsConditions::where('is_active', true)->first();
                        @endphp
                        @if ($termsPage)
                            <li><a
                                    href="{{ route('terms-conditions', $termsPage->slug) }}">{{ custom_trans('terms_and_conditions') }}</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <!-- Latest Post -->
                <div class="col-md-4 mb-4 mb-md-0">
                    <h4 class="footer-title mb-2">{{ custom_trans('latest_posts') }}</h4>
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
                                <div class="footer-post-title text-muted">{{ custom_trans('no_blog_posts') }}</div>
                                <div class="footer-post-date text-muted">{{ custom_trans('coming_soon') }}</div>
                            </div>
                        </div>
                    @endforelse
                </div>
                <!-- Contact + Language -->
                <div class="col-md-3">
                    <h4 class="footer-title mb-2">{{ custom_trans('contact_us') }}</h4>
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
            <span>{{ custom_trans('categories') }}</span>
        </a>
        <a href="{{ route('blog.index') }}"
            class="mobile-nav-item {{ request()->routeIs('blog.index') ? 'active' : '' }}">
            <i class="fas fa-newspaper"></i>
            <span>{{ custom_trans('blog') }}</span>
        </a>
        <a href="{{ route('contact') }}"
            class="mobile-nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>{{ custom_trans('contact_us') }}</span>
        </a>
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

        // Footer language selector
        document.getElementById('footerLangSelect').addEventListener('change', function() {
            const selectedLanguage = this.value;
            window.location.href = '{{ url('/language') }}/' + selectedLanguage;
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
