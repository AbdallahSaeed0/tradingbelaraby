@extends('admin.layout')

@section('title', custom_trans('Contact Management', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Contact Management', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Contact Management', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-management.contact-forms') }}" class="btn btn-info">
                            <i class="fas fa-list me-2"></i>{{ custom_trans('View All Submissions', 'admin') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ custom_trans('Total Submissions', 'admin') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSubmissions }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-envelope fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    {{ custom_trans('Unread', 'admin') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unreadSubmissions }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-eye-slash fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    {{ custom_trans('Read', 'admin') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $readSubmissions }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-eye fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ custom_trans('Replied', 'admin') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $repliedSubmissions }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-reply fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contact Settings -->
            <div class="col-lg-8 mb-4">
                <div class="card content-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i
                                class="fas fa-cog text-primary me-2"></i>{{ custom_trans('Contact Information Settings', 'admin') }}
                        </h5>
                        <p class="text-muted mb-0">
                            {{ custom_trans('Manage contact details, map settings, and social media links.', 'admin') }}
                        </p>
                    </div>
                    <div class="card-body">
                        <form id="contactSettingsForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone"
                                            class="form-label">{{ custom_trans('Phone Number', 'admin') }}</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ $contactSettings->phone ?? '' }}" placeholder="+1 234 567 8900">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email"
                                            class="form-label">{{ custom_trans('Email Address', 'admin') }}</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $contactSettings->email ?? '' }}" placeholder="info@example.com">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address"
                                    class="form-label">{{ custom_trans('Office Address', 'admin') }}</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your office address">{{ $contactSettings->address ?? '' }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="map_latitude"
                                            class="form-label">{{ custom_trans('Map Latitude', 'admin') }}</label>
                                        <input type="text" class="form-control" id="map_latitude" name="map_latitude"
                                            value="{{ $contactSettings->map_latitude ?? '' }}" placeholder="40.7128">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="map_longitude"
                                            class="form-label">{{ custom_trans('Map Longitude', 'admin') }}</label>
                                        <input type="text" class="form-control" id="map_longitude"
                                            name="map_longitude" value="{{ $contactSettings->map_longitude ?? '' }}"
                                            placeholder="-74.0060">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="map_embed_url"
                                    class="form-label">{{ custom_trans('Google Maps Embed URL', 'admin') }}</label>
                                <input type="url" class="form-control" id="map_embed_url" name="map_embed_url"
                                    value="{{ $contactSettings->map_embed_url ?? '' }}"
                                    placeholder="https://www.google.com/maps/embed?pb=...">
                                <div class="form-text">
                                    {{ custom_trans('Paste the full Google Maps embed URL here.', 'admin') }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="office_hours"
                                    class="form-label">{{ custom_trans('Office Hours', 'admin') }}</label>
                                <textarea class="form-control" id="office_hours" name="office_hours" rows="2"
                                    placeholder="Monday - Friday: 9:00 AM - 6:00 PM">{{ $contactSettings->office_hours ?? '' }}</textarea>
                            </div>

                            <h6 class="mb-3">{{ custom_trans('Social Media Links', 'admin') }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_facebook"
                                            class="form-label">{{ custom_trans('Facebook URL', 'admin') }}</label>
                                        <input type="url" class="form-control" id="social_facebook"
                                            name="social_facebook" value="{{ $contactSettings->social_facebook ?? '' }}"
                                            placeholder="https://facebook.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_twitter"
                                            class="form-label">{{ custom_trans('Twitter URL', 'admin') }}</label>
                                        <input type="url" class="form-control" id="social_twitter"
                                            name="social_twitter" value="{{ $contactSettings->social_twitter ?? '' }}"
                                            placeholder="https://twitter.com/...">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_youtube"
                                            class="form-label">{{ custom_trans('YouTube URL', 'admin') }}</label>
                                        <input type="url" class="form-control" id="social_youtube"
                                            name="social_youtube" value="{{ $contactSettings->social_youtube ?? '' }}"
                                            placeholder="https://youtube.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_linkedin"
                                            class="form-label">{{ custom_trans('LinkedIn URL', 'admin') }}</label>
                                        <input type="url" class="form-control" id="social_linkedin"
                                            name="social_linkedin" value="{{ $contactSettings->social_linkedin ?? '' }}"
                                            placeholder="https://linkedin.com/...">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_snapchat"
                                            class="form-label">{{ custom_trans('Snapchat URL', 'admin') }}</label>
                                        <input type="url" class="form-control" id="social_snapchat"
                                            name="social_snapchat" value="{{ $contactSettings->social_snapchat ?? '' }}"
                                            placeholder="https://snapchat.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_tiktok"
                                            class="form-label">{{ custom_trans('TikTok URL', 'admin') }}</label>
                                        <input type="url" class="form-control" id="social_tiktok"
                                            name="social_tiktok" value="{{ $contactSettings->social_tiktok ?? '' }}"
                                            placeholder="https://tiktok.com/...">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                        value="1"
                                        {{ $contactSettings && $contactSettings->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="is_active">{{ custom_trans('Active', 'admin') }}</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ custom_trans('Save Settings', 'admin') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Submissions -->
            <div class="col-lg-4 mb-4">
                <div class="card content-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-inbox text-info me-2"></i>{{ custom_trans('Recent Submissions', 'admin') }}
                        </h5>
                        <p class="text-muted mb-0">{{ custom_trans('Latest contact form submissions.', 'admin') }}</p>
                    </div>
                    <div class="card-body">
                        @forelse($contactForms as $form)
                            <div class="submission-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $form->name }}</h6>
                                        <p class="text-muted small mb-1">{{ $form->email }}</p>
                                        <p class="text-muted small mb-2">{{ Str::limit($form->message, 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ $form->created_at->diffForHumans() }}</small>
                                            <span
                                                class="badge bg-{{ $form->status === 'unread' ? 'warning' : ($form->status === 'read' ? 'info' : 'success') }}">
                                                {{ ucfirst($form->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('admin.settings.contact-management.show', $form->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>{{ custom_trans('View', 'admin') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ custom_trans('No submissions yet.', 'admin') }}</p>
                            </div>
                        @endforelse

                        @if ($contactForms->count() > 0)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.settings.contact-management.contact-forms') }}"
                                    class="btn btn-outline-primary btn-sm">
                                    {{ custom_trans('View All Submissions', 'admin') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle contact settings form submission
            $('#contactSettingsForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ custom_trans('Saving...', 'admin') }}'
                );

                $.ajax({
                    url: '{{ route('admin.settings.contact-management.update-settings') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message ||
                                '{{ custom_trans('An error occurred while saving.', 'admin') }}'
                            );
                        }
                    },
                    error: function(xhr) {
                        let errorMessage =
                            '{{ custom_trans('An error occurred while saving.', 'admin') }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join('\n');
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endpush
