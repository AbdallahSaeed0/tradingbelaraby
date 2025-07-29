@extends('admin.layout')

@section('title', __('Content Management'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('Content Management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Content Management') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-forms.index') }}" class="btn btn-success">
                            <i class="fas fa-envelope me-2"></i>{{ __('View Contact Forms') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Scholarship Banner Management -->
            <div class="col-lg-6">
                <div class="card content-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap text-primary me-2"></i>{{ __('Scholarship Banner') }}
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input scholarship-status-toggle" type="checkbox"
                                {{ $scholarshipBanner && $scholarshipBanner->is_active ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="scholarshipBannerForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="scholarship_title" class="form-label">{{ __('Title') }} *</label>
                                        <input type="text" class="form-control" id="scholarship_title" name="title"
                                            value="{{ $scholarshipBanner ? $scholarshipBanner->title : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="scholarship_title_ar"
                                            class="form-label">{{ __('Title (Arabic)') }}</label>
                                        <input type="text" class="form-control" id="scholarship_title_ar" name="title_ar"
                                            value="{{ $scholarshipBanner ? $scholarshipBanner->title_ar : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="scholarship_button_text" class="form-label">{{ __('Button Text') }}
                                            *</label>
                                        <input type="text" class="form-control" id="scholarship_button_text"
                                            name="button_text"
                                            value="{{ $scholarshipBanner ? $scholarshipBanner->button_text : '' }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="scholarship_button_text_ar"
                                            class="form-label">{{ __('Button Text (Arabic)') }}</label>
                                        <input type="text" class="form-control" id="scholarship_button_text_ar"
                                            name="button_text_ar"
                                            value="{{ $scholarshipBanner ? $scholarshipBanner->button_text_ar : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="scholarship_button_url" class="form-label">{{ __('Button URL') }}</label>
                                <input type="url" class="form-control" id="scholarship_button_url" name="button_url"
                                    value="{{ $scholarshipBanner ? $scholarshipBanner->button_url : '' }}"
                                    placeholder="https://example.com">
                            </div>
                            <div class="mb-3">
                                <label for="scholarship_background_image"
                                    class="form-label">{{ __('Background Image') }}</label>
                                <input type="file" class="form-control" id="scholarship_background_image"
                                    name="background_image" accept="image/*">
                                @if ($scholarshipBanner && $scholarshipBanner->background_image)
                                    <div class="mt-2">
                                        <img src="{{ $scholarshipBanner->background_image_url }}" alt="Current background"
                                            class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="scholarship_is_active"
                                        name="is_active"
                                        {{ $scholarshipBanner && $scholarshipBanner->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="scholarship_is_active">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Save Scholarship Banner') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CTA Video Management -->
            <div class="col-lg-6">
                <div class="card content-card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-video text-info me-2"></i>{{ __('CTA Video Section') }}
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input cta-status-toggle" type="checkbox"
                                {{ $ctaVideo && $ctaVideo->is_active ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="ctaVideoForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_title" class="form-label">{{ __('Title') }} *</label>
                                        <input type="text" class="form-control" id="cta_title" name="title"
                                            value="{{ $ctaVideo ? $ctaVideo->title : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_title_ar" class="form-label">{{ __('Title (Arabic)') }}</label>
                                        <input type="text" class="form-control" id="cta_title_ar" name="title_ar"
                                            value="{{ $ctaVideo ? $ctaVideo->title_ar : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_description" class="form-label">{{ __('Description') }} *</label>
                                        <textarea class="form-control" id="cta_description" name="description" rows="3" required>{{ $ctaVideo ? $ctaVideo->description : '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_description_ar"
                                            class="form-label">{{ __('Description (Arabic)') }}</label>
                                        <textarea class="form-control" id="cta_description_ar" name="description_ar" rows="3">{{ $ctaVideo ? $ctaVideo->description_ar : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="cta_video_url" class="form-label">{{ __('Video URL (YouTube)') }}</label>
                                <input type="url" class="form-control" id="cta_video_url" name="video_url"
                                    value="{{ $ctaVideo ? $ctaVideo->video_url : '' }}"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div class="mb-3">
                                <label for="cta_background_image" class="form-label">{{ __('Background Image') }}</label>
                                <input type="file" class="form-control" id="cta_background_image"
                                    name="background_image" accept="image/*">
                                @if ($ctaVideo && $ctaVideo->background_image)
                                    <div class="mt-2">
                                        <img src="{{ $ctaVideo->background_image_url }}" alt="Current background"
                                            class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="cta_is_active" name="is_active"
                                        {{ $ctaVideo && $ctaVideo->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cta_is_active">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>{{ __('Save CTA Video') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('styles')
    <style>
        /* Enhanced Page Header */
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

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item.active {
            color: white;
        }

        /* Content Cards */
        .content-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .content-card .card-header {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px 15px 0 0;
        }

        .content-card .card-body {
            background: white;
            border-radius: 0 0 15px 15px;
        }

        /* Form Styling */
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Button Styling */
        .btn {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Table Styling */
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        /* Badge Styling */
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
        }

        /* Form Switch Styling */
        .form-check-input {
            width: 3rem;
            height: 1.5rem;
            border-radius: 1rem;
            background-color: #e9ecef;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Scholarship Banner Form
            $('#scholarshipBannerForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.settings.scholarship-banner.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    toastr.error(response.errors[key][0]);
                                });
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while saving the scholarship banner') }}'
                        );
                    }
                });
            });

            // CTA Video Form
            $('#ctaVideoForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.settings.cta-video.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            if (response.errors) {
                                Object.keys(response.errors).forEach(function(key) {
                                    toastr.error(response.errors[key][0]);
                                });
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while saving the CTA video') }}');
                    }
                });
            });

            // Scholarship Banner Status Toggle
            $('.scholarship-status-toggle').on('change', function() {
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: '{{ route('admin.settings.scholarship-banner.toggle-status') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while updating the scholarship banner status') }}'
                        );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // CTA Video Status Toggle
            $('.cta-status-toggle').on('change', function() {
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: '{{ route('admin.settings.cta-video.toggle-status') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while updating the CTA video status') }}'
                        );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });
        });
    </script>
@endpush
