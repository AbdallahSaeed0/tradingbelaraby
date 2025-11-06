@extends('admin.layout')

@section('title', custom_trans('Content Management', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Content Management', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Content Management', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-forms.index') }}" class="btn btn-success">
                            <i class="fas fa-envelope me-2"></i>{{ custom_trans('View Contact Forms', 'admin') }}
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
                            <i
                                class="fas fa-graduation-cap text-primary me-2"></i>{{ custom_trans('Scholarship Banner', 'admin') }}
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
                                        <label for="scholarship_title"
                                            class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                        <input type="text" class="form-control" id="scholarship_title" name="title"
                                            value="{{ $scholarshipBanner ? $scholarshipBanner->title : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="scholarship_title_ar"
                                            class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                        <input type="text" class="form-control" id="scholarship_title_ar" name="title_ar"
                                            value="{{ $scholarshipBanner ? $scholarshipBanner->title_ar : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="scholarship_button_text"
                                            class="form-label">{{ custom_trans('Button Text', 'admin') }}
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
                                            class="form-label">{{ custom_trans('Button Text (Arabic)', 'admin') }}</label>
                                        <input type="text" class="form-control" id="scholarship_button_text_ar"
                                            name="button_text_ar"
                                            value="{{ $scholarshipBanner ? $scholarshipBanner->button_text_ar : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="scholarship_button_url"
                                    class="form-label">{{ custom_trans('Button URL', 'admin') }}</label>
                                <input type="url" class="form-control" id="scholarship_button_url" name="button_url"
                                    value="{{ $scholarshipBanner ? $scholarshipBanner->button_url : '' }}"
                                    placeholder="https://example.com">
                            </div>
                            <div class="mb-3">
                                <label for="scholarship_background_image"
                                    class="form-label">{{ custom_trans('Background Image', 'admin') }}</label>
                                <input type="file" class="form-control" id="scholarship_background_image"
                                    name="background_image" accept="image/*">
                                @if ($scholarshipBanner && $scholarshipBanner->background_image)
                                    <div class="mt-2">
                                        <img src="{{ $scholarshipBanner->background_image_url }}" alt="Current background"
                                            class="img-thumbnail max-h-100">
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="scholarship_is_active"
                                        name="is_active"
                                        {{ $scholarshipBanner && $scholarshipBanner->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="scholarship_is_active">
                                        {{ custom_trans('Active', 'admin') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ custom_trans('Save Scholarship Banner', 'admin') }}
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
                            <i class="fas fa-video text-info me-2"></i>{{ custom_trans('CTA Video Section', 'admin') }}
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
                                        <label for="cta_title" class="form-label">{{ custom_trans('Title', 'admin') }}
                                            *</label>
                                        <input type="text" class="form-control" id="cta_title" name="title"
                                            value="{{ $ctaVideo ? $ctaVideo->title : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_title_ar"
                                            class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                        <input type="text" class="form-control" id="cta_title_ar" name="title_ar"
                                            value="{{ $ctaVideo ? $ctaVideo->title_ar : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_description"
                                            class="form-label">{{ custom_trans('Description', 'admin') }} *</label>
                                        <textarea class="form-control" id="cta_description" name="description" rows="3" required>{{ $ctaVideo ? $ctaVideo->description : '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cta_description_ar"
                                            class="form-label">{{ custom_trans('Description (Arabic)', 'admin') }}</label>
                                        <textarea class="form-control" id="cta_description_ar" name="description_ar" rows="3">{{ $ctaVideo ? $ctaVideo->description_ar : '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="cta_video_url"
                                    class="form-label">{{ custom_trans('Video URL (YouTube)', 'admin') }}</label>
                                <input type="url" class="form-control" id="cta_video_url" name="video_url"
                                    value="{{ $ctaVideo ? $ctaVideo->video_url : '' }}"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div class="mb-3">
                                <label for="cta_background_image"
                                    class="form-label">{{ custom_trans('Background Image', 'admin') }}</label>
                                <input type="file" class="form-control" id="cta_background_image"
                                    name="background_image" accept="image/*">
                                @if ($ctaVideo && $ctaVideo->background_image)
                                    <div class="mt-2">
                                        <img src="{{ $ctaVideo->background_image_url }}" alt="Current background"
                                            class="img-thumbnail max-h-100">
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="cta_is_active" name="is_active"
                                        {{ $ctaVideo && $ctaVideo->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cta_is_active">
                                        {{ custom_trans('Active', 'admin') }}
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-save me-2"></i>{{ custom_trans('Save CTA Video', 'admin') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

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
                            '{{ custom_trans('An error occurred while saving the scholarship banner', 'admin') }}'
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
                            '{{ custom_trans('An error occurred while saving the CTA video', 'admin') }}'
                        );
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
                            '{{ custom_trans('An error occurred while updating the scholarship banner status', 'admin') }}'
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
                            '{{ custom_trans('An error occurred while updating the CTA video status', 'admin') }}'
                        );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });
        });
    </script>
@endpush
