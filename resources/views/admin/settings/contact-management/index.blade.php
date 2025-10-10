@extends('admin.layout')

@section('title', __('Contact Management'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('Contact Management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Contact Management') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-management.contact-forms') }}" class="btn btn-info">
                            <i class="fas fa-list me-2"></i>{{ __('View All Submissions') }}
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
                                    {{ __('Total Submissions') }}</div>
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
                                    {{ __('Unread') }}</div>
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
                                    {{ __('Read') }}</div>
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
                                    {{ __('Replied') }}</div>
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
                            <i class="fas fa-cog text-primary me-2"></i>{{ __('Contact Information Settings') }}
                        </h5>
                        <p class="text-muted mb-0">
                            {{ __('Manage contact details, map settings, and social media links.') }}</p>
                    </div>
                    <div class="card-body">
                        <form id="contactSettingsForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ $contactSettings->phone ?? '' }}" placeholder="+1 234 567 8900">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ $contactSettings->email ?? '' }}" placeholder="info@example.com">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('Office Address') }}</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your office address">{{ $contactSettings->address ?? '' }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="map_latitude" class="form-label">{{ __('Map Latitude') }}</label>
                                        <input type="text" class="form-control" id="map_latitude" name="map_latitude"
                                            value="{{ $contactSettings->map_latitude ?? '' }}" placeholder="40.7128">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="map_longitude" class="form-label">{{ __('Map Longitude') }}</label>
                                        <input type="text" class="form-control" id="map_longitude"
                                            name="map_longitude" value="{{ $contactSettings->map_longitude ?? '' }}"
                                            placeholder="-74.0060">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="map_embed_url" class="form-label">{{ __('Google Maps Embed URL') }}</label>
                                <input type="url" class="form-control" id="map_embed_url" name="map_embed_url"
                                    value="{{ $contactSettings->map_embed_url ?? '' }}"
                                    placeholder="https://www.google.com/maps/embed?pb=...">
                                <div class="form-text">{{ __('Paste the full Google Maps embed URL here.') }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="office_hours" class="form-label">{{ __('Office Hours') }}</label>
                                <textarea class="form-control" id="office_hours" name="office_hours" rows="2"
                                    placeholder="Monday - Friday: 9:00 AM - 6:00 PM">{{ $contactSettings->office_hours ?? '' }}</textarea>
                            </div>

                            <h6 class="mb-3">{{ __('Social Media Links') }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_facebook" class="form-label">{{ __('Facebook URL') }}</label>
                                        <input type="url" class="form-control" id="social_facebook"
                                            name="social_facebook" value="{{ $contactSettings->social_facebook ?? '' }}"
                                            placeholder="https://facebook.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_twitter" class="form-label">{{ __('Twitter URL') }}</label>
                                        <input type="url" class="form-control" id="social_twitter"
                                            name="social_twitter" value="{{ $contactSettings->social_twitter ?? '' }}"
                                            placeholder="https://twitter.com/...">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_youtube" class="form-label">{{ __('YouTube URL') }}</label>
                                        <input type="url" class="form-control" id="social_youtube"
                                            name="social_youtube" value="{{ $contactSettings->social_youtube ?? '' }}"
                                            placeholder="https://youtube.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_linkedin" class="form-label">{{ __('LinkedIn URL') }}</label>
                                        <input type="url" class="form-control" id="social_linkedin"
                                            name="social_linkedin" value="{{ $contactSettings->social_linkedin ?? '' }}"
                                            placeholder="https://linkedin.com/...">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_snapchat" class="form-label">{{ __('Snapchat URL') }}</label>
                                        <input type="url" class="form-control" id="social_snapchat"
                                            name="social_snapchat" value="{{ $contactSettings->social_snapchat ?? '' }}"
                                            placeholder="https://snapchat.com/...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="social_tiktok" class="form-label">{{ __('TikTok URL') }}</label>
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
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ __('Save Settings') }}
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
                            <i class="fas fa-inbox text-info me-2"></i>{{ __('Recent Submissions') }}
                        </h5>
                        <p class="text-muted mb-0">{{ __('Latest contact form submissions.') }}</p>
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
                                        <i class="fas fa-eye me-1"></i>{{ __('View') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">{{ __('No submissions yet.') }}</p>
                            </div>
                        @endforelse

                        @if ($contactForms->count() > 0)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.settings.contact-management.contact-forms') }}"
                                    class="btn btn-outline-primary btn-sm">
                                    {{ __('View All Submissions') }}
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
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('Saving...') }}');

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
                                '{{ __('An error occurred while saving.') }}');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ __('An error occurred while saving.') }}';
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
