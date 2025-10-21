@extends('admin.layout')

@section('title', __('Settings'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Settings') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Content Management') }}</h4>
                    <p class="text-muted mb-0">
                        {{ __('Manage your website content and settings from one central location') }}</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <!-- Slider Management Card -->
            <div class="col-xl-4 col-lg-6 col-md-10 mb-4">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-images fa-4x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Slider Management') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage homepage sliders, banners, and promotional content with advanced features including filters, bulk actions, and drag-and-drop reordering.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-search text-info me-2"></i>
                                        <span>{{ __('Advanced Search & Filters') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-tasks text-success me-2"></i>
                                        <span>{{ __('Bulk Actions') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-arrows-alt text-warning me-2"></i>
                                        <span>{{ __('Drag & Drop Reordering') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-eye text-primary me-2"></i>
                                        <span>{{ __('Preview & Edit') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.sliders.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-cog me-2"></i>{{ __('Manage Sliders') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features Section Management Card -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-star fa-4x text-warning"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Features Section') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage the homepage features section with statistics, icons, and descriptions. Control the display of key metrics and achievements.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-chart-bar text-info me-2"></i>
                                        <span>{{ __('Statistics Management') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ __('Icon Management') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-warning me-2"></i>
                                        <span>{{ __('Order Control') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-primary me-2"></i>
                                        <span>{{ __('Active/Inactive') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.features.index') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-star me-2"></i>{{ __('Manage Features') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hero Features Section Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-sliders-h fa-4x text-info"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Hero Features Section') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage the hero section features with icons, titles, and descriptions. Control the display of key benefits and highlights.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-icons text-info me-2"></i>
                                        <span>{{ __('Icon Management') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-success me-2"></i>
                                        <span>{{ __('Multilingual Support') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-warning me-2"></i>
                                        <span>{{ __('Order Control') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-primary me-2"></i>
                                        <span>{{ __('Active/Inactive') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.hero-features.index') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-sliders-h me-2"></i>{{ __('Manage Hero Features') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- About University Section Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-university fa-4x text-success"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('About University Section') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage the about university section with main content, features, and images. Control the display of university information and key highlights.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-edit text-success me-2"></i>
                                        <span>{{ __('Content Management') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-info me-2"></i>
                                        <span>{{ __('Image Upload') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-list text-warning me-2"></i>
                                        <span>{{ __('Features List') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ __('Multilingual') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.about-university.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-university me-2"></i>{{ __('Manage About University') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- FAQ Section Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-question-circle fa-4x text-warning"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('FAQ Section') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage frequently asked questions with expandable content. Control the display of common inquiries and their answers.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-list text-warning me-2"></i>
                                        <span>{{ __('Q&A Management') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-info me-2"></i>
                                        <span>{{ __('Order Control') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-expand text-success me-2"></i>
                                        <span>{{ __('Expand/Collapse') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ __('Multilingual') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.faqs.index') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-question-circle me-2"></i>{{ __('Manage FAQs') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-cogs fa-4x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Content Management') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage scholarship banner, CTA video sections, and contact form submissions. Control the display of promotional content and user inquiries.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                                        <span>{{ __('Scholarship Banner') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-video text-info me-2"></i>
                                        <span>{{ __('CTA Video') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-envelope text-success me-2"></i>
                                        <span>{{ __('Contact Forms') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-warning me-2"></i>
                                        <span>{{ __('Multilingual') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.content-management.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-cogs me-2"></i>{{ __('Manage Content') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Testimonials Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-quote-left fa-4x text-success"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Testimonials Management') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage customer testimonials and reviews. Add, edit, and organize testimonials with multilingual support and rating system.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-star text-warning me-2"></i>
                                        <span>{{ __('Rating System') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-info me-2"></i>
                                        <span>{{ __('Drag & Drop') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ __('Multilingual') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ __('Avatar Upload') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.testimonials.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-quote-left me-2"></i>{{ __('Manage Testimonials') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features Split Section Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-puzzle-piece fa-4x text-info"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Features Split Section') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage the features split section with main content and individual feature items. Control the display of key features with icons and descriptions.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-cogs text-info me-2"></i>
                                        <span>{{ __('Main Content') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-list text-warning me-2"></i>
                                        <span>{{ __('Feature Items') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ __('Image Upload') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ __('Multilingual') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.features-split.index') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-puzzle-piece me-2"></i>{{ __('Manage Features Split') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Partner Logos Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-handshake fa-4x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Partner Logos') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage partner and sponsor logos displayed on your website. Add clickable logos with custom links and control their order and visibility.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-image text-primary me-2"></i>
                                        <span>{{ __('Logo Upload') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-link text-info me-2"></i>
                                        <span>{{ __('Custom Links') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-warning me-2"></i>
                                        <span>{{ __('Order Control') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-success me-2"></i>
                                        <span>{{ __('Active/Inactive') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.partner-logos.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-handshake me-2"></i>{{ __('Manage Partner Logos') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Info Split Section Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-info-circle fa-4x text-secondary"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Info Split Section') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage the info split section content. Control the display of information with images, descriptions, and call-to-action buttons.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-edit text-secondary me-2"></i>
                                        <span>{{ __('Content Editor') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ __('Image Upload') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-link text-warning me-2"></i>
                                        <span>{{ __('CTA Button') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ __('Multilingual') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.info-split.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-info-circle me-2"></i>{{ __('Manage Info Split') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Newsletter Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-newspaper fa-4x text-danger"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Newsletter Management') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage newsletter subscriptions and subscribers. View, filter, and export subscriber data with status management and analytics.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-users text-danger me-2"></i>
                                        <span>{{ __('Subscribers') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-chart-bar text-info me-2"></i>
                                        <span>{{ __('Analytics') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-download text-success me-2"></i>
                                        <span>{{ __('Export Data') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-warning me-2"></i>
                                        <span>{{ __('Status Control') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.newsletters.index') }}" class="btn btn-danger btn-lg">
                            <i class="fas fa-newspaper me-2"></i>{{ __('Manage Newsletters') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-cog fa-4x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Main Content') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage your website\'s main content including logo, site information, and social media links. Control the display of key branding elements.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-image text-primary me-2"></i>
                                        <span>{{ __('Logo Management') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <span>{{ __('Site Information') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-share-alt text-warning me-2"></i>
                                        <span>{{ __('Social Media') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-address-book text-success me-2"></i>
                                        <span>{{ __('Contact Info') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.main-content.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-cog me-2"></i>{{ __('Manage Main Content') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-file-contract fa-4x text-secondary"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Terms and Conditions') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage your website\'s terms and conditions page with bilingual support for English and Arabic.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ __('Bilingual Content') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-edit text-info me-2"></i>
                                        <span>{{ __('Rich Text Editor') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-link text-warning me-2"></i>
                                        <span>{{ __('SEO-Friendly URLs') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-success me-2"></i>
                                        <span>{{ __('Active/Inactive') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.terms-conditions.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-file-contract me-2"></i>{{ __('Manage Terms & Conditions') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Management -->
            <div class="col-xl-4 col-lg-6 col-md-10">
                <div class="card settings-card">
                    <div class="card-body text-center p-5">
                        <div class="settings-icon mb-4">
                            <div class="icon-wrapper">
                                <i class="fas fa-address-book fa-4x text-success"></i>
                            </div>
                        </div>
                        <h3 class="card-title mb-3">{{ __('Contact Management') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ __('Manage contact information, map settings, social media links, and handle contact form submissions with status tracking and analytics.') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span>{{ __('Map Settings') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-envelope text-info me-2"></i>
                                        <span>{{ __('Form Submissions') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-share-alt text-warning me-2"></i>
                                        <span>{{ __('Social Media') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-chart-line text-success me-2"></i>
                                        <span>{{ __('Analytics') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.contact-management.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-address-book me-2"></i>{{ __('Manage Contact') }}
                        </a>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection



