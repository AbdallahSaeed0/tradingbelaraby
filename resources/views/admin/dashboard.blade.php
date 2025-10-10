@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">{{ __('Dashboard') }}</h1>
                        <p class="text-muted">Welcome back! Here's what's happening with your platform.</p>
                    </div>
                    <div>
                        <small class="text-muted">Last updated: {{ now()->format('M d, Y H:i') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-primary d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-user-tie text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0">{{ __('Admins') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_admins'] }}</h4>
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
                            <h6 class="text-muted mb-0">{{ __('Courses') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_courses'] }}</h4>
                            <small class="text-success">{{ $stats['published_courses'] }} published</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-info d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-newspaper text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0">{{ __('Blogs') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_blogs'] }}</h4>
                            <small class="text-info">{{ $stats['published_blogs'] }} published</small>
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
                            <h6 class="text-muted mb-0">{{ __('Users') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_users'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-secondary d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-graduation-cap text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0">{{ __('Active Instructors') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_instructors'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-warning d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-chart-bar text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0">{{ __('Registered Traders') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_traders'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <span
                            class="icon-shape bg-dark d-inline-flex align-items-center justify-content-center rounded-2 me-3">
                            <i class="fa fa-chart-line text-white"></i>
                        </span>
                        <div>
                            <h6 class="text-muted mb-0">{{ __('Total Content') }}</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_courses'] + $stats['total_blogs'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Content Sections -->
        <div class="row g-4">
            <!-- Latest Blogs -->
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-newspaper me-2"></i>{{ __('Latest Blogs') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($latestBlogs->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($latestBlogs as $blog)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-start">
                                            @if ($blog->image)
                                                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}"
                                                    class="rounded me-3"
                                                    style="width: 50px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 40px;">
                                                    <i class="fa fa-newspaper text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($blog->title, 40) }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fa fa-calendar me-1"></i>{{ $blog->created_at->format('M d, Y') }}
                                                    @if ($blog->category)
                                                        <span class="ms-2">
                                                            <i
                                                                class="fa fa-folder me-1"></i>{{ \App\Helpers\TranslationHelper::getLocalizedContent($blog->category->name, $blog->category->name_ar) }}
                                                        </span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-newspaper fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('No blogs found') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-info btn-sm">
                            {{ __('View All Blogs') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Latest Courses -->
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-book me-2"></i>{{ __('Latest Courses') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($latestCourses->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($latestCourses as $course)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-start">
                                            @if ($course->image)
                                                <img src="{{ $course->image_url }}" alt="{{ $course->name }}"
                                                    class="rounded me-3"
                                                    style="width: 50px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 40px;">
                                                    <i class="fa fa-book text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ Str::limit($course->name, 40) }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fa fa-calendar me-1"></i>{{ $course->created_at->format('M d, Y') }}
                                                    @if ($course->category)
                                                        <span class="ms-2">
                                                            <i class="fa fa-folder me-1"></i>{{ $course->category->name }}
                                                        </span>
                                                    @endif
                                                </small>
                                                <div class="mt-1">
                                                    <span
                                                        class="badge bg-{{ $course->status === 'published' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($course->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('No courses found') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-success btn-sm">
                            {{ __('View All Courses') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Latest Instructors -->
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fa fa-graduation-cap me-2"></i>{{ __('Latest Instructors') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($latestInstructors->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($latestInstructors as $instructor)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-start">
                                            <img src="{{ $instructor->avatar_url }}" alt="{{ $instructor->name }}"
                                                class="rounded-circle me-3"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $instructor->name }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fa fa-calendar me-1"></i>{{ $instructor->created_at->format('M d, Y') }}
                                                </small>
                                                <div class="mt-1">
                                                    <span
                                                        class="badge bg-{{ $instructor->is_active ? 'success' : 'danger' }}">
                                                        {{ $instructor->is_active ? __('Active') : __('Inactive') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('No instructors found') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-primary btn-sm">
                            {{ __('View All Instructors') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Latest Traders -->
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fa fa-chart-bar me-2"></i>{{ __('Latest Traders') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($latestTraders->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($latestTraders as $trader)
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-warning rounded-circle me-3 d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="fa fa-user text-white"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $trader->name }}</h6>
                                                <small class="text-muted">
                                                    <i
                                                        class="fa fa-calendar me-1"></i>{{ $trader->created_at->format('M d, Y') }}
                                                </small>
                                                <div class="mt-1">
                                                    <span class="badge bg-info">{{ ucfirst($trader->sex) }}</span>
                                                    @if ($trader->trading_community)
                                                        <span
                                                            class="badge bg-secondary ms-1">{{ $trader->trading_community }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('No traders found') }}</p>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.traders.index') }}" class="btn btn-outline-warning btn-sm">
                            {{ __('View All Traders') }} <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
