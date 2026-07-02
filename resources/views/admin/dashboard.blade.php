@extends('admin.layout')

@section('body_class', 'admin-dashboard-page')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid py-4 admin-dashboard">

        {{-- ── Welcome Banner ── --}}
        <div class="db-welcome-banner mb-4">
            <div class="db-welcome-content">
                <div>
                    <h1 class="db-welcome-title">{{ custom_trans('Dashboard', 'admin') }}</h1>
                    <p class="db-welcome-sub">Welcome back! Here's what's happening with your platform.</p>
                </div>
                <div class="db-welcome-meta">
                    <span class="db-live-badge">
                        <span class="db-live-dot"></span>{{ custom_trans('Live', 'admin') }}
                    </span>
                    <span class="db-updated-time">{{ now()->format('M d, Y H:i') }}</span>
                </div>
            </div>
        </div>

        {{-- ── Primary Stats Row ── --}}
        <div class="row g-3 g-md-4 mb-4">

            <div class="col-6 col-md-3">
                <div class="db-stat-card db-stat-primary">
                    <div class="db-stat-icon-wrap">
                        <i class="fa fa-user-tie"></i>
                    </div>
                    <div class="db-stat-body">
                        <span class="db-stat-label">{{ custom_trans('Admins', 'admin') }}</span>
                        <span class="db-stat-value">{{ $stats['total_admins'] }}</span>
                    </div>
                    <div class="db-stat-accent"></div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="db-stat-card db-stat-success">
                    <div class="db-stat-icon-wrap">
                        <i class="fa fa-book"></i>
                    </div>
                    <div class="db-stat-body">
                        <span class="db-stat-label">{{ custom_trans('Courses', 'admin') }}</span>
                        <span class="db-stat-value">{{ $stats['total_courses'] }}</span>
                        <span class="db-stat-sub">{{ $stats['published_courses'] }} {{ custom_trans('published', 'admin') }}</span>
                    </div>
                    <div class="db-stat-accent"></div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="db-stat-card db-stat-info">
                    <div class="db-stat-icon-wrap">
                        <i class="fa fa-newspaper"></i>
                    </div>
                    <div class="db-stat-body">
                        <span class="db-stat-label">{{ custom_trans('Blogs', 'admin') }}</span>
                        <span class="db-stat-value">{{ $stats['total_blogs'] }}</span>
                        <span class="db-stat-sub">{{ $stats['published_blogs'] }} {{ custom_trans('published', 'admin') }}</span>
                    </div>
                    <div class="db-stat-accent"></div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="db-stat-card db-stat-warning">
                    <div class="db-stat-icon-wrap">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="db-stat-body">
                        <span class="db-stat-label">{{ custom_trans('Users', 'admin') }}</span>
                        <span class="db-stat-value">{{ $stats['total_users'] }}</span>
                    </div>
                    <div class="db-stat-accent"></div>
                </div>
            </div>

        </div>

        {{-- ── Secondary Stats Row ── --}}
        <div class="row g-3 g-md-4 mb-4 mb-md-5">

            <div class="col-12 col-sm-4">
                <div class="db-metric-card">
                    <div class="db-metric-icon db-metric-secondary">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="db-metric-body">
                        <span class="db-metric-value">{{ $stats['total_instructors'] }}</span>
                        <span class="db-metric-label">{{ custom_trans('Active Instructors', 'admin') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="db-metric-card">
                    <div class="db-metric-icon db-metric-amber">
                        <i class="fa fa-chart-bar"></i>
                    </div>
                    <div class="db-metric-body">
                        <span class="db-metric-value">{{ $stats['total_traders'] }}</span>
                        <span class="db-metric-label">{{ custom_trans('Registered Traders', 'admin') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="db-metric-card">
                    <div class="db-metric-icon db-metric-dark">
                        <i class="fa fa-chart-line"></i>
                    </div>
                    <div class="db-metric-body">
                        <span class="db-metric-value">{{ $stats['total_courses'] + $stats['total_blogs'] }}</span>
                        <span class="db-metric-label">{{ custom_trans('Total Content', 'admin') }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Widget Cards Row ── --}}
        <div class="row g-3 g-md-4">

            {{-- Latest Blogs --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="db-widget">
                    <div class="db-widget-header db-widget-info">
                        <i class="fa fa-newspaper"></i>
                        <span>{{ custom_trans('Latest Blogs', 'admin') }}</span>
                    </div>
                    <div class="db-widget-body">
                        @if ($latestBlogs->count() > 0)
                            @foreach ($latestBlogs as $blog)
                                <div class="db-list-item">
                                    <div class="db-list-thumb">
                                        @if ($blog->image)
                                            <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}">
                                        @else
                                            <div class="db-list-thumb-placeholder">
                                                <i class="fa fa-newspaper"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="db-list-info">
                                        <span class="db-list-title">{{ Str::limit($blog->title, 38) }}</span>
                                        <span class="db-list-meta">
                                            <i class="fa fa-calendar-alt"></i>
                                            {{ $blog->created_at->format('M d, Y') }}
                                            @if ($blog->category)
                                                &nbsp;·&nbsp;
                                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($blog->category->name, $blog->category->name_ar) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="db-widget-empty">
                                <i class="fa fa-newspaper"></i>
                                <span>{{ custom_trans('No blogs found', 'admin') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="db-widget-footer">
                        <a href="{{ route('admin.blogs.index') }}" class="db-widget-link db-link-info">
                            {{ custom_trans('View All Blogs', 'admin') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Latest Courses --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="db-widget">
                    <div class="db-widget-header db-widget-success">
                        <i class="fa fa-book"></i>
                        <span>{{ custom_trans('Latest Courses', 'admin') }}</span>
                    </div>
                    <div class="db-widget-body">
                        @if ($latestCourses->count() > 0)
                            @foreach ($latestCourses as $course)
                                <div class="db-list-item">
                                    <div class="db-list-thumb">
                                        @if ($course->image)
                                            <img src="{{ $course->image_url }}" alt="{{ $course->name }}">
                                        @else
                                            <div class="db-list-thumb-placeholder">
                                                <i class="fa fa-book"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="db-list-info">
                                        <span class="db-list-title">{{ Str::limit($course->name, 38) }}</span>
                                        <span class="db-list-meta">
                                            <i class="fa fa-calendar-alt"></i>
                                            {{ $course->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="db-status-badge db-badge-{{ $course->status === 'published' ? 'success' : 'warning' }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="db-widget-empty">
                                <i class="fa fa-book"></i>
                                <span>{{ custom_trans('No courses found', 'admin') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="db-widget-footer">
                        <a href="{{ route('admin.courses.index') }}" class="db-widget-link db-link-success">
                            {{ custom_trans('View All Courses', 'admin') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Latest Instructors --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="db-widget">
                    <div class="db-widget-header db-widget-primary">
                        <i class="fa fa-graduation-cap"></i>
                        <span>{{ custom_trans('Latest Instructors', 'admin') }}</span>
                    </div>
                    <div class="db-widget-body">
                        @if ($latestInstructors->count() > 0)
                            @foreach ($latestInstructors as $instructor)
                                <div class="db-list-item">
                                    <div class="db-list-thumb">
                                        <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}" class="db-avatar-circle">
                                    </div>
                                    <div class="db-list-info">
                                        <span class="db-list-title">{{ $instructor->name }}</span>
                                        <span class="db-list-meta">
                                            <i class="fa fa-calendar-alt"></i>
                                            {{ $instructor->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="db-status-badge db-badge-{{ $instructor->is_active ? 'success' : 'danger' }}">
                                            {{ $instructor->is_active ? custom_trans('Active', 'admin') : custom_trans('Inactive', 'admin') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="db-widget-empty">
                                <i class="fa fa-graduation-cap"></i>
                                <span>{{ custom_trans('No instructors found', 'admin') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="db-widget-footer">
                        <a href="{{ route('admin.admins.index') }}" class="db-widget-link db-link-primary">
                            {{ custom_trans('View All Instructors', 'admin') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Latest Traders --}}
            <div class="col-12 col-md-6 col-lg-3">
                <div class="db-widget">
                    <div class="db-widget-header db-widget-warning">
                        <i class="fa fa-chart-bar"></i>
                        <span>{{ custom_trans('Latest Traders', 'admin') }}</span>
                    </div>
                    <div class="db-widget-body">
                        @if ($latestTraders->count() > 0)
                            @foreach ($latestTraders as $trader)
                                <div class="db-list-item">
                                    <div class="db-list-thumb">
                                        <div class="db-list-thumb-placeholder db-thumb-warning">
                                            <i class="fa fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="db-list-info">
                                        <span class="db-list-title">{{ $trader->name }}</span>
                                        <span class="db-list-meta">
                                            <i class="fa fa-calendar-alt"></i>
                                            {{ $trader->created_at->format('M d, Y') }}
                                        </span>
                                        <div class="db-badge-group">
                                            <span class="db-status-badge db-badge-info">{{ ucfirst($trader->sex) }}</span>
                                            @if ($trader->trading_community)
                                                <span class="db-status-badge db-badge-secondary">{{ $trader->trading_community }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="db-widget-empty">
                                <i class="fa fa-chart-bar"></i>
                                <span>{{ custom_trans('No traders found', 'admin') }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="db-widget-footer">
                        <a href="{{ route('admin.traders.index') }}" class="db-widget-link db-link-warning">
                            {{ custom_trans('View All Traders', 'admin') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
