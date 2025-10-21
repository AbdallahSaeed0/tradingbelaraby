@extends('admin.layout')

@section('title', __('About University Management'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('About University Management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('About University') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
                            <i class="fas fa-plus me-2"></i>{{ __('Add New Feature') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="card main-content-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Main Content') }}</h5>
            </div>
            <div class="card-body">
                <form id="aboutUniversityForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">{{ __('Title') }} *</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ $aboutUniversity->title ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title_ar" class="form-label">{{ __('Title (Arabic)') }}</label>
                                <input type="text" class="form-control" id="title_ar" name="title_ar"
                                    value="{{ $aboutUniversity->title_ar ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('Description') }} *</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required>{{ $aboutUniversity->description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description_ar" class="form-label">{{ __('Description (Arabic)') }}</label>
                                <textarea class="form-control" id="description_ar" name="description_ar" rows="4">{{ $aboutUniversity->description_ar ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('Main Image') }}</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                @if (isset($aboutUniversity) && $aboutUniversity->image)
                                    <div class="mt-2">
                                        <img src="{{ $aboutUniversity->image_url }}" alt="Current Image"
                                            class="img-thumbnail max-w-200">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="background_image" class="form-label">{{ __('Background Image') }}</label>
                                <input type="file" class="form-control" id="background_image" name="background_image"
                                    accept="image/*">
                                @if (isset($aboutUniversity) && $aboutUniversity->background_image)
                                    <div class="mt-2">
                                        <img src="{{ $aboutUniversity->background_image_url }}"
                                            alt="Current Background Image" class="img-thumbnail max-w-200">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                {{ isset($aboutUniversity) && $aboutUniversity->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                {{ __('Active') }}
                            </label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>{{ __('Save Content') }}
                        </button>
                        @if (isset($aboutUniversity))
                            <button type="button" class="btn btn-warning" id="toggle_status">
                                <i class="fas fa-toggle-on me-2"></i>{{ __('Toggle Status') }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Features Section -->
        <div class="card features-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Features') }}</h5>
                <div class="bulk-actions d-none-initially">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success btn-sm" id="bulk_activate">
                            <i class="fas fa-check me-1"></i>{{ __('Activate') }}
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" id="bulk_deactivate">
                            <i class="fas fa-pause me-1"></i>{{ __('Deactivate') }}
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="bulk_delete">
                            <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="features-table">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                    </div>
                                </th>
                                <th width="80">{{ __('Number') }}</th>
                                <th width="80">{{ __('Order') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Title (AR)') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Description (AR)') }}</th>
                                <th width="100">{{ __('Status') }}</th>
                                <th width="120">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($features as $feature)
                                <tr data-id="{{ $feature->id }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input feature-checkbox" type="checkbox"
                                                value="{{ $feature->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $feature->number }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $feature->order }}</span>
                                    </td>
                                    <td>{{ $feature->title }}</td>
                                    <td>{{ $feature->title_ar ?: '-' }}</td>
                                    <td>{{ Str::limit($feature->description, 50) }}</td>
                                    <td>{{ $feature->description_ar ? Str::limit($feature->description_ar, 50) : '-' }}
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                data-id="{{ $feature->id }}" {{ $feature->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info view-feature-btn"
                                                data-feature="{{ json_encode($feature->toArray()) }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning edit-feature-btn"
                                                data-feature="{{ json_encode($feature->toArray()) }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-feature-btn"
                                                data-id="{{ $feature->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>{{ __('No features found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Feature Modal -->
    <div class="modal fade" id="addFeatureModal" tabindex="-1" aria-labelledby="addFeatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFeatureModalLabel">{{ __('Add New Feature') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addFeatureForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_title" class="form-label">{{ __('Title') }} *</label>
                                    <input type="text" class="form-control" id="feature_title" name="title"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_title_ar" class="form-label">{{ __('Title (Arabic)') }}</label>
                                    <input type="text" class="form-control" id="feature_title_ar" name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_description" class="form-label">{{ __('Description') }} *</label>
                                    <textarea class="form-control" id="feature_description" name="description" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_description_ar"
                                        class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" id="feature_description_ar" name="description_ar" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_number" class="form-label">{{ __('Number') }} *</label>
                                    <input type="number" class="form-control" id="feature_number" name="number"
                                        min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_order" class="form-label">{{ __('Order') }}</label>
                                    <input type="number" class="form-control" id="feature_order" name="order"
                                        value="0" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="feature_is_active" name="is_active"
                                    checked>
                                <label class="form-check-label" for="feature_is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Create Feature') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Feature Modal -->
    <div class="modal fade" id="editFeatureModal" tabindex="-1" aria-labelledby="editFeatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFeatureModalLabel">{{ __('Edit Feature') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editFeatureForm">
                    @csrf
                    <input type="hidden" id="edit_feature_id" name="feature_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_title" class="form-label">{{ __('Title') }} *</label>
                                    <input type="text" class="form-control" id="edit_feature_title" name="title"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_title_ar"
                                        class="form-label">{{ __('Title (Arabic)') }}</label>
                                    <input type="text" class="form-control" id="edit_feature_title_ar"
                                        name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_description" class="form-label">{{ __('Description') }}
                                        *</label>
                                    <textarea class="form-control" id="edit_feature_description" name="description" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_description_ar"
                                        class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" id="edit_feature_description_ar" name="description_ar" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_number" class="form-label">{{ __('Number') }} *</label>
                                    <input type="number" class="form-control" id="edit_feature_number" name="number"
                                        min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_order" class="form-label">{{ __('Order') }}</label>
                                    <input type="number" class="form-control" id="edit_feature_order" name="order"
                                        min="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_feature_is_active"
                                    name="is_active">
                                <label class="form-check-label" for="edit_feature_is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('Update Feature') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Feature Modal -->
    <div class="modal fade" id="viewFeatureModal" tabindex="-1" aria-labelledby="viewFeatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFeatureModalLabel">{{ __('Feature Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Title') }}</label>
                                <p id="view_feature_title" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Title (Arabic)') }}</label>
                                <p id="view_feature_title_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Description') }}</label>
                                <p id="view_feature_description" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                <p id="view_feature_description_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Number') }}</label>
                                <p id="view_feature_number" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Order') }}</label>
                                <p id="view_feature_order" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Status') }}</label>
                                <p id="view_feature_status" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select all functionality
            $('#select_all').on('change', function() {
                $('.feature-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActions();
            });

            $('.feature-checkbox').on('change', function() {
                updateBulkActions();
                updateSelectAll();
            });

            function updateBulkActions() {
                const checkedBoxes = $('.feature-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    $('.bulk-actions').show();
                } else {
                    $('.bulk-actions').hide();
                }
            }

            function updateSelectAll() {
                const totalCheckboxes = $('.feature-checkbox').length;
                const checkedCheckboxes = $('.feature-checkbox:checked').length;

                if (checkedCheckboxes === 0) {
                    $('#select_all').prop('indeterminate', false).prop('checked', false);
                } else if (checkedCheckboxes === totalCheckboxes) {
                    $('#select_all').prop('indeterminate', false).prop('checked', true);
                } else {
                    $('#select_all').prop('indeterminate', true);
                }
            }

            // About University Form
            $('#aboutUniversityForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.settings.about-university.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
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
                        toastr.error('{{ __('An error occurred while saving the content') }}');
                    }
                });
            });

            // Toggle Status
            $('#toggle_status').on('click', function() {
                $.ajax({
                    url: '{{ route('admin.settings.about-university.toggle-status') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while updating the status') }}');
                    }
                });
            });

            // Feature Status Toggle
            $('.status-toggle').on('change', function() {
                const featureId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/settings/about-university/feature/${featureId}/toggle-status`,
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
                            '{{ __('An error occurred while updating the feature status') }}'
                        );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Add Feature Form
            $('#addFeatureForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('admin.settings.about-university.feature.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#addFeatureModal').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
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
                            '{{ __('An error occurred while creating the feature') }}');
                    }
                });
            });

            // Edit Feature Form
            $('#editFeatureForm').on('submit', function(e) {
                e.preventDefault();

                const featureId = $('#edit_feature_id').val();

                $.ajax({
                    url: `/admin/settings/about-university/feature/${featureId}`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editFeatureModal').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
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
                            '{{ __('An error occurred while updating the feature') }}');
                    }
                });
            });

            // Edit Feature Button
            $('.edit-feature-btn').on('click', function() {
                const feature = $(this).data('feature');

                $('#edit_feature_id').val(feature.id);
                $('#edit_feature_title').val(feature.title);
                $('#edit_feature_title_ar').val(feature.title_ar);
                $('#edit_feature_description').val(feature.description);
                $('#edit_feature_description_ar').val(feature.description_ar);
                $('#edit_feature_number').val(feature.number);
                $('#edit_feature_order').val(feature.order);
                $('#edit_feature_is_active').prop('checked', feature.is_active);

                $('#editFeatureModal').modal('show');
            });

            // View Feature Button
            $('.view-feature-btn').on('click', function() {
                const feature = $(this).data('feature');

                $('#view_feature_title').text(feature.title);
                $('#view_feature_title_ar').text(feature.title_ar || '-');
                $('#view_feature_description').text(feature.description);
                $('#view_feature_description_ar').text(feature.description_ar || '-');
                $('#view_feature_number').text(feature.number);
                $('#view_feature_order').text(feature.order);
                $('#view_feature_status').text(feature.is_active ? '{{ __('Active') }}' :
                    '{{ __('Inactive') }}');

                $('#viewFeatureModal').modal('show');
            });

            // Delete Feature Button
            $('.delete-feature-btn').on('click', function() {
                const featureId = $(this).data('id');

                if (confirm('{{ __('Are you sure you want to delete this feature?') }}')) {
                    $.ajax({
                        url: `/admin/settings/about-university/feature/${featureId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error(
                                '{{ __('An error occurred while deleting the feature') }}');
                        }
                    });
                }
            });

            // Make table rows sortable
            const tbody = document.querySelector('#features-table tbody');
            new Sortable(tbody, {
                animation: 150,
                onEnd: function(evt) {
                    const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                    const orders = rows.map((row, index) => ({
                        id: row.dataset.id,
                        order: index + 1
                    }));

                    $.ajax({
                        url: '{{ route('admin.settings.about-university.features.order') }}',
                        method: 'POST',
                        data: {
                            orders: orders,
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
                                '{{ __('An error occurred while updating the order') }}'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush
