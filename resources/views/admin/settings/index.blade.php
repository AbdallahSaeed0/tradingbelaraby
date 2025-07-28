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

@push('styles')
    <style>
        .settings-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            overflow: hidden;
        }

        .settings-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .settings-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #007bff, #6610f2, #007bff);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .icon-wrapper {
            display: inline-block;
            padding: 2rem;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(102, 16, 242, 0.1) 100%);
            transition: all 0.3s ease;
        }

        .settings-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.2) 0%, rgba(102, 16, 242, 0.2) 100%);
        }

        .settings-icon {
            transition: all 0.3s ease;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.75rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .card-text {
            font-size: 1rem;
            line-height: 1.6;
            color: #6c757d;
        }

        .features-list {
            background: rgba(248, 249, 250, 0.8);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            color: #495057;
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        .btn-lg {
            padding: 0.875rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-lg:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }

        .page-title-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .page-title-box .page-title {
            color: white;
            margin-bottom: 0.5rem;
        }

        .page-title-box .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item.active {
            color: white;
        }
    </style>
@endpush
