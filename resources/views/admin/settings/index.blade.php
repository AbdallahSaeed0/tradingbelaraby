@extends('admin.layout')

@section('title', custom_trans('Settings', 'admin'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ custom_trans('Settings', 'admin') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ custom_trans('Content Management', 'admin') }}</h4>
                    <p class="text-muted mb-0">
                        {{ custom_trans('Manage your website content and settings from one central location', 'admin') }}</p>
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
                        <h3 class="card-title mb-3">{{ custom_trans('Slider Management', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage homepage sliders, banners, and promotional content with advanced features including filters, bulk actions, and drag-and-drop reordering.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-search text-info me-2"></i>
                                        <span>{{ custom_trans('Advanced Search & Filters', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-tasks text-success me-2"></i>
                                        <span>{{ custom_trans('Bulk Actions', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-arrows-alt text-warning me-2"></i>
                                        <span>{{ custom_trans('Drag & Drop Reordering', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-eye text-primary me-2"></i>
                                        <span>{{ custom_trans('Preview & Edit', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.sliders.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-cog me-2"></i>{{ custom_trans('Manage Sliders', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Features Section', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage the homepage features section with statistics, icons, and descriptions. Control the display of key metrics and achievements.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-chart-bar text-info me-2"></i>
                                        <span>{{ custom_trans('Statistics Management', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ custom_trans('Icon Management', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-warning me-2"></i>
                                        <span>{{ custom_trans('Order Control', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-primary me-2"></i>
                                        <span>{{ custom_trans('Active/Inactive', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.features.index') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-star me-2"></i>{{ custom_trans('Manage Features', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Hero Features Section', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage the hero section features with icons, titles, and descriptions. Control the display of key benefits and highlights.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-icons text-info me-2"></i>
                                        <span>{{ custom_trans('Icon Management', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-success me-2"></i>
                                        <span>{{ custom_trans('Multilingual Support', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-warning me-2"></i>
                                        <span>{{ custom_trans('Order Control', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-primary me-2"></i>
                                        <span>{{ custom_trans('Active/Inactive', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.hero-features.index') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-sliders-h me-2"></i>{{ custom_trans('Manage Hero Features', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('About University Section', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage the about university section with main content, features, and images. Control the display of university information and key highlights.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-edit text-success me-2"></i>
                                        <span>{{ custom_trans('Content Management', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-info me-2"></i>
                                        <span>{{ custom_trans('Image Upload', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-list text-warning me-2"></i>
                                        <span>{{ custom_trans('Features List', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ custom_trans('Multilingual', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.about-university.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-university me-2"></i>{{ custom_trans('Manage About University', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('FAQ Section', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage frequently asked questions with expandable content. Control the display of common inquiries and their answers.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-list text-warning me-2"></i>
                                        <span>{{ custom_trans('Q&A Management', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-info me-2"></i>
                                        <span>{{ custom_trans('Order Control', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-expand text-success me-2"></i>
                                        <span>{{ custom_trans('Expand/Collapse', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ custom_trans('Multilingual', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.faqs.index') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-question-circle me-2"></i>{{ custom_trans('Manage FAQs', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Content Management', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage scholarship banner, CTA video sections, and contact form submissions. Control the display of promotional content and user inquiries.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                                        <span>{{ custom_trans('Scholarship Banner', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-video text-info me-2"></i>
                                        <span>{{ custom_trans('CTA Video', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-envelope text-success me-2"></i>
                                        <span>{{ custom_trans('Contact Forms', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-warning me-2"></i>
                                        <span>{{ custom_trans('Multilingual', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.content-management.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-cogs me-2"></i>{{ custom_trans('Manage Content', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Testimonials Management', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage customer testimonials and reviews. Add, edit, and organize testimonials with multilingual support and rating system.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-star text-warning me-2"></i>
                                        <span>{{ custom_trans('Rating System', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-info me-2"></i>
                                        <span>{{ custom_trans('Drag & Drop', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ custom_trans('Multilingual', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ custom_trans('Avatar Upload', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.testimonials.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-quote-left me-2"></i>{{ custom_trans('Manage Testimonials', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Features Split Section', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage the features split section with main content and individual feature items. Control the display of key features with icons and descriptions.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-cogs text-info me-2"></i>
                                        <span>{{ custom_trans('Main Content', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-list text-warning me-2"></i>
                                        <span>{{ custom_trans('Feature Items', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ custom_trans('Image Upload', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ custom_trans('Multilingual', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.features-split.index') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-puzzle-piece me-2"></i>{{ custom_trans('Manage Features Split', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Partner Logos', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage partner and sponsor logos displayed on your website. Add clickable logos with custom links and control their order and visibility.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-image text-primary me-2"></i>
                                        <span>{{ custom_trans('Logo Upload', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-link text-info me-2"></i>
                                        <span>{{ custom_trans('Custom Links', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-sort text-warning me-2"></i>
                                        <span>{{ custom_trans('Order Control', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-success me-2"></i>
                                        <span>{{ custom_trans('Active/Inactive', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.partner-logos.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-handshake me-2"></i>{{ custom_trans('Manage Partner Logos', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Info Split Section', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage the info split section content. Control the display of information with images, descriptions, and call-to-action buttons.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-edit text-secondary me-2"></i>
                                        <span>{{ custom_trans('Content Editor', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-image text-success me-2"></i>
                                        <span>{{ custom_trans('Image Upload', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-link text-warning me-2"></i>
                                        <span>{{ custom_trans('CTA Button', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ custom_trans('Multilingual', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.info-split.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-info-circle me-2"></i>{{ custom_trans('Manage Info Split', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Newsletter Management', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage newsletter subscriptions and subscribers. View, filter, and export subscriber data with status management and analytics.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-users text-danger me-2"></i>
                                        <span>{{ custom_trans('Subscribers', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-chart-bar text-info me-2"></i>
                                        <span>{{ custom_trans('Analytics', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-download text-success me-2"></i>
                                        <span>{{ custom_trans('Export Data', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-warning me-2"></i>
                                        <span>{{ custom_trans('Status Control', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.newsletters.index') }}" class="btn btn-danger btn-lg">
                            <i class="fas fa-newspaper me-2"></i>{{ custom_trans('Manage Newsletters', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Main Content', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage your website\'s main content including logo, site information, and social media links. Control the display of key branding elements.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-image text-primary me-2"></i>
                                        <span>{{ custom_trans('Logo Management', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <span>{{ custom_trans('Site Information', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-share-alt text-warning me-2"></i>
                                        <span>{{ custom_trans('Social Media', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-address-book text-success me-2"></i>
                                        <span>{{ custom_trans('Contact Info', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.main-content.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-cog me-2"></i>{{ custom_trans('Manage Main Content', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Terms and Conditions', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage your website\'s terms and conditions page with bilingual support for English and Arabic.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-language text-primary me-2"></i>
                                        <span>{{ custom_trans('Bilingual Content', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-edit text-info me-2"></i>
                                        <span>{{ custom_trans('Rich Text Editor', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-link text-warning me-2"></i>
                                        <span>{{ custom_trans('SEO-Friendly URLs', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-toggle-on text-success me-2"></i>
                                        <span>{{ custom_trans('Active/Inactive', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.terms-conditions.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-file-contract me-2"></i>{{ custom_trans('Manage Terms & Conditions', 'admin') }}
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
                        <h3 class="card-title mb-3">{{ custom_trans('Contact Management', 'admin') }}</h3>
                        <p class="card-text text-muted mb-4">
                            {{ custom_trans('Manage contact information, map settings, social media links, and handle contact form submissions with status tracking and analytics.', 'admin') }}
                        </p>
                        <div class="features-list mb-4">
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span>{{ custom_trans('Map Settings', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-envelope text-info me-2"></i>
                                        <span>{{ custom_trans('Form Submissions', 'admin') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-item">
                                        <i class="fas fa-share-alt text-warning me-2"></i>
                                        <span>{{ custom_trans('Social Media', 'admin') }}</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-chart-line text-success me-2"></i>
                                        <span>{{ custom_trans('Analytics', 'admin') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings.contact-management.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-address-book me-2"></i>{{ custom_trans('Manage Contact', 'admin') }}
                        </a>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection



