@extends('admin.layout')

@section('title', custom_trans('Info Split Section Management', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Info Split Section Management', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Info Split Section', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button type="button" class="btn btn-success" id="toggleStatus"
                            data-status="{{ $infoSplit && $infoSplit->is_active ? 1 : 0 }}">
                            <i class="fas fa-toggle-{{ $infoSplit && $infoSplit->is_active ? 'on' : 'off' }} me-2"></i>
                            {{ $infoSplit && $infoSplit->is_active ? custom_trans('Deactivate', 'admin') : custom_trans('Activate', 'admin') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="row">
            <div class="col-12">
                <div class="card content-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>{{ custom_trans('Info Split Section Content', 'admin') }}
                        </h5>
                        <p class="text-muted mb-0">
                            {{ custom_trans('Manage the main content and settings for the Info Split section.', 'admin') }}</p>
                    </div>
                    <div class="card-body">
                        <form id="infoSplitForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ $infoSplit->title ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title_ar" class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                        <input type="text" class="form-control" id="title_ar" name="title_ar"
                                            value="{{ $infoSplit->title_ar ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">{{ custom_trans('Description', 'admin') }} *</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" required>{{ $infoSplit->description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description_ar"
                                            class="form-label">{{ custom_trans('Description (Arabic)', 'admin') }}</label>
                                        <textarea class="form-control" id="description_ar" name="description_ar" rows="4">{{ $infoSplit->description_ar ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="button_text" class="form-label">{{ custom_trans('Button Text', 'admin') }} *</label>
                                        <input type="text" class="form-control" id="button_text" name="button_text"
                                            value="{{ $infoSplit->button_text ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="button_text_ar"
                                            class="form-label">{{ custom_trans('Button Text (Arabic)', 'admin') }}</label>
                                        <input type="text" class="form-control" id="button_text_ar" name="button_text_ar"
                                            value="{{ $infoSplit->button_text_ar ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="button_url" class="form-label">{{ custom_trans('Button URL', 'admin') }} *</label>
                                        <input type="url" class="form-control" id="button_url" name="button_url"
                                            value="{{ $infoSplit->button_url ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">{{ custom_trans('Section Image', 'admin') }}</label>
                                        <input type="file" class="form-control" id="image" name="image"
                                            accept="image/*">
                                        @if ($infoSplit && $infoSplit->image)
                                            <input type="hidden" name="old_image" value="{{ $infoSplit->image }}">
                                            <div class="mt-2">
                                                <img src="{{ $infoSplit->image_url }}" alt="Info Split"
                                                    class="img-thumbnail max-w-200">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="is_active" value="0">
                                            <input class="form-check-input" type="checkbox" id="is_active"
                                                name="is_active" value="1"
                                                {{ $infoSplit && $infoSplit->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">{{ custom_trans('Active', 'admin') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>{{ custom_trans('Save Changes', 'admin') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card content-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye text-info me-2"></i>{{ custom_trans('Preview', 'admin') }}
                        </h5>
                        <p class="text-muted mb-0">{{ custom_trans('How the Info Split section will appear on the frontend.', 'admin') }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-lg-6 mb-4 px-4 mb-lg-0">
                                @if ($infoSplit && $infoSplit->image)
                                    <img src="{{ $infoSplit->image_url }}" alt="Info Split"
                                        class="img-fluid rounded-4 w-100">
                                @else
                                    <div
                                        class="bg-light rounded-4 shadow-sm w-100 h-300 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <h2 class="fw-bold mb-3">{{ $infoSplit->title ?? 'Title will appear here' }}</h2>
                                <p class="mb-4 text-muted fs-6">
                                    {{ $infoSplit->description ?? 'Description will appear here' }}
                                </p>
                                @if ($infoSplit && $infoSplit->button_url && $infoSplit->button_text)
                                    <a href="{{ $infoSplit->button_url }}" class="btn btn-primary px-4 py-3 rounded-3"
                                        target="_blank">
                                        {{ $infoSplit->button_text }} &rarr;
                                    </a>
                                @else
                                    <button class="btn btn-primary px-4 py-3 rounded-3" disabled>
                                        Button will appear here
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Toggle Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ custom_trans('Update Status', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="statusModalMessage">{{ custom_trans('Are you sure you want to update the status of this section?', 'admin') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusToggle">{{ custom_trans('Confirm', 'admin') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#infoSplitForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ custom_trans('Saving...', 'admin') }}');

                $.ajax({
                    url: '{{ route('admin.settings.info-split.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Reload page to show updated data
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message ||
                                '{{ custom_trans('An error occurred while saving.', 'admin') }}');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ custom_trans('An error occurred while saving.', 'admin') }}';
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

            // Handle status toggle
            $('#toggleStatus').on('click', function(e) {
                e.preventDefault();

                const currentStatus = $(this).data('status');
                const newStatus = currentStatus ? 0 : 1;
                const action = newStatus ? 'activate' : 'deactivate';

                $('#statusModalMessage').text(
                    `{{ custom_trans('Are you sure you want to', 'admin') }} ${action} {{ custom_trans('this section?', 'admin') }}`);

                $('#confirmStatusToggle').off('click').on('click', function() {
                    $.ajax({
                        url: '{{ route('admin.settings.info-split.toggle-status') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#statusModal').modal('hide');
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                toastr.error(response.message ||
                                    '{{ custom_trans('An error occurred while updating status.', 'admin') }}'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage =
                                '{{ custom_trans('An error occurred while updating status.', 'admin') }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                        }
                    });
                });

                $('#statusModal').modal('show');
            });

            // Real-time preview updates
            $('#title, #description, #button_text, #button_url').on('input', function() {
                updatePreview();
            });

            function updatePreview() {
                const title = $('#title').val() || 'Title will appear here';
                const description = $('#description').val() || 'Description will appear here';
                const buttonText = $('#button_text').val() || 'Button will appear here';
                const buttonUrl = $('#button_url').val();

                $('.card-body h2').text(title);
                $('.card-body p').text(description);

                if (buttonUrl && buttonText) {
                    $('.card-body .btn').removeAttr('disabled').text(buttonText + ' →').attr('href', buttonUrl);
                } else {
                    $('.card-body .btn').attr('disabled', true).text('Button will appear here').removeAttr('href');
                }
            }

            // Image preview
            $('#image').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('.card-body img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
