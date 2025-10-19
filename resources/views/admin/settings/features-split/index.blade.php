@extends('admin.layout')

@section('title', __('Features Split Section Management'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('Features Split Section Management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Features Split Section') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addFeatureModal">
                            <i class="fas fa-plus me-2"></i>{{ __('Add Feature Item') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card content-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs text-primary me-2"></i>{{ __('Main Content Settings') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="mainContentForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">{{ __('Title') }} *</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ $mainContent->title ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title_ar" class="form-label">{{ __('Title (Arabic)') }}</label>
                                        <input type="text" class="form-control" id="title_ar" name="title_ar"
                                            value="{{ $mainContent->title_ar ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">{{ __('Description') }} *</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" required>{{ $mainContent->description ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description_ar"
                                            class="form-label">{{ __('Description (Arabic)') }}</label>
                                        <textarea class="form-control" id="description_ar" name="description_ar" rows="4">{{ $mainContent->description_ar ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="background_image"
                                            class="form-label">{{ __('Background Image') }}</label>
                                        <input type="file" class="form-control" id="background_image"
                                            name="background_image" accept="image/*">
                                        @if ($mainContent && $mainContent->background_image)
                                            <input type="hidden" name="old_background_image"
                                                value="{{ $mainContent->background_image }}">
                                            <div class="mt-2">
                                                <img src="{{ $mainContent->background_image_url }}" alt="Background"
                                                    class="img-thumbnail max-w-150">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="main_image" class="form-label">{{ __('Main Image') }}</label>
                                        <input type="file" class="form-control" id="main_image" name="main_image"
                                            accept="image/*">
                                        @if ($mainContent && $mainContent->main_image)
                                            <input type="hidden" name="old_main_image"
                                                value="{{ $mainContent->main_image }}">
                                            <div class="mt-2">
                                                <img src="{{ $mainContent->main_image_url }}" alt="Main"
                                                    class="img-thumbnail max-w-150">
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
                                                {{ $mainContent && $mainContent->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>{{ __('Save Changes') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card content-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="search" class="form-label">{{ __('Search') }}</label>
                            <input type="text" class="form-control" id="search"
                                placeholder="{{ __('Search features...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="status">
                                <option value="">{{ __('All') }}</option>
                                <option value="active">{{ __('Active') }}</option>
                                <option value="inactive">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sort" class="form-label">{{ __('Sort By') }}</label>
                            <select class="form-select" id="sort">
                                <option value="order">{{ __('Order') }}</option>
                                <option value="title">{{ __('Title') }}</option>
                                <option value="created_at">{{ __('Created Date') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary w-100" id="clear_filters">
                                <i class="fas fa-times me-1"></i>{{ __('Clear') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Table -->
        <div class="card content-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-list text-primary me-2"></i>{{ __('Feature Items') }}
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success bulk-action-btn" data-action="activate"
                                disabled>
                                <i class="fas fa-check me-1"></i>{{ __('Activate') }}
                            </button>
                            <button type="button" class="btn btn-outline-warning bulk-action-btn"
                                data-action="deactivate" disabled>
                                <i class="fas fa-pause me-1"></i>{{ __('Deactivate') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger bulk-action-btn" data-action="delete"
                                disabled>
                                <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="features-table">
                        <thead>
                            <tr>
                                <th width="30">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                    </div>
                                </th>
                                <th width="80">{{ __('Order') }}</th>
                                <th width="100">{{ __('Icon') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Title (AR)') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Description (AR)') }}</th>
                                <th width="100">{{ __('Status') }}</th>
                                <th width="120">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="features-tbody">
                            @forelse($features as $feature)
                                <tr data-feature-id="{{ $feature->id }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input feature-checkbox" type="checkbox"
                                                value="{{ $feature->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-grip-vertical drag-handle cursor-move"></i>
                                        <span class="badge bg-secondary">{{ $feature->order }}</span>
                                    </td>
                                    <td>
                                        <i class="{{ $feature->icon }} fa-2x text-primary"></i>
                                    </td>
                                    <td>{{ $feature->title }}</td>
                                    <td>{{ $feature->title_ar ?: '-' }}</td>
                                    <td>{{ Str::limit($feature->description, 80) }}</td>
                                    <td>{{ $feature->description_ar ? Str::limit($feature->description_ar, 80) : '-' }}
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-status" type="checkbox"
                                                data-feature-id="{{ $feature->id }}"
                                                {{ $feature->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-feature"
                                                data-feature="{{ json_encode($feature->toArray()) }}"
                                                title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info view-feature"
                                                data-feature="{{ json_encode($feature->toArray()) }}"
                                                title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-feature"
                                                data-feature-id="{{ $feature->id }}" title="{{ __('Delete') }}">
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
                                            <h5>{{ __('No features found') }}</h5>
                                            <p>{{ __('No feature items match your current filters.') }}</p>
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
    <div class="modal fade" id="addFeatureModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add New Feature') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                    <textarea class="form-control" id="feature_description" name="description" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_description_ar"
                                        class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" id="feature_description_ar" name="description_ar" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_icon" class="form-label">{{ __('Icon') }} *</label>
                                    <input type="text" class="form-control" id="feature_icon" name="icon"
                                        placeholder="fas fa-user" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_order" class="form-label">{{ __('Order') }}</label>
                                    <input type="number" class="form-control" id="feature_order" name="order"
                                        min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="feature_is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="feature_is_active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Add Feature') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Feature Modal -->
    <div class="modal fade" id="editFeatureModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Feature') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                    <textarea class="form-control" id="edit_feature_description" name="description" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_description_ar"
                                        class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" id="edit_feature_description_ar" name="description_ar" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_icon" class="form-label">{{ __('Icon') }} *</label>
                                    <input type="text" class="form-control" id="edit_feature_icon" name="icon"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_feature_order" class="form-label">{{ __('Order') }}</label>
                                    <input type="number" class="form-control" id="edit_feature_order" name="order"
                                        min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="edit_feature_is_active"
                                    name="is_active" value="1">
                                <label class="form-check-label" for="edit_feature_is_active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Feature') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Feature Modal -->
    <div class="modal fade" id="viewFeatureModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Feature Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Title') }}</label>
                                <p class="form-control-plaintext" id="view_feature_title"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Title (Arabic)') }}</label>
                                <p class="form-control-plaintext" id="view_feature_title_ar"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Description') }}</label>
                                <p class="form-control-plaintext" id="view_feature_description"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                <p class="form-control-plaintext" id="view_feature_description_ar"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Icon') }}</label>
                                <p class="form-control-plaintext" id="view_feature_icon"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Order') }}</label>
                                <p class="form-control-plaintext" id="view_feature_order"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Status') }}</label>
                                <p class="form-control-plaintext" id="view_feature_status"></p>
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteFeatureModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Delete Feature') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete this feature? This action cannot be undone.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentFeatureId = null;

            // Main content form submission
            $('#mainContentForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('Saving...') }}');

                $.ajax({
                    url: '{{ route('admin.settings.features-split.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        } else {
                            toastr.error('{{ __('An error occurred. Please try again.') }}');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Add feature form submission
            $('#addFeatureForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('Adding...') }}');

                $.ajax({
                    url: '{{ route('admin.settings.features-split.feature.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#addFeatureModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        } else {
                            toastr.error('{{ __('An error occurred. Please try again.') }}');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Edit feature
            $(document).on('click', '.edit-feature', function() {
                const feature = $(this).data('feature');
                $('#edit_feature_id').val(feature.id);
                $('#edit_feature_title').val(feature.title);
                $('#edit_feature_title_ar').val(feature.title_ar);
                $('#edit_feature_description').val(feature.description);
                $('#edit_feature_description_ar').val(feature.description_ar);
                $('#edit_feature_icon').val(feature.icon);
                $('#edit_feature_order').val(feature.order);
                $('#edit_feature_is_active').prop('checked', feature.is_active);
                $('#editFeatureModal').modal('show');
            });

            // Edit feature form submission
            $('#editFeatureForm').on('submit', function(e) {
                e.preventDefault();
                const featureId = $('#edit_feature_id').val();
                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>{{ __('Updating...') }}');

                $.ajax({
                    url: `{{ url('admin/settings/features-split/feature') }}/${featureId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editFeatureModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        } else {
                            toastr.error('{{ __('An error occurred. Please try again.') }}');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // View feature
            $(document).on('click', '.view-feature', function() {
                const feature = $(this).data('feature');
                $('#view_feature_title').text(feature.title);
                $('#view_feature_title_ar').text(feature.title_ar || '-');
                $('#view_feature_description').text(feature.description);
                $('#view_feature_description_ar').text(feature.description_ar || '-');
                $('#view_feature_icon').text(feature.icon);
                $('#view_feature_order').text(feature.order);
                $('#view_feature_status').text(feature.is_active ? '{{ __('Active') }}' :
                    '{{ __('Inactive') }}');
                $('#viewFeatureModal').modal('show');
            });

            // Delete feature
            $(document).on('click', '.delete-feature', function() {
                currentFeatureId = $(this).data('feature-id');
                $('#deleteFeatureModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                if (!currentFeatureId) return;

                $.ajax({
                    url: `{{ url('admin/settings/features-split/feature') }}/${currentFeatureId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#deleteFeatureModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function() {
                        toastr.error('{{ __('An error occurred. Please try again.') }}');
                    }
                });
            });

            // Toggle status
            $(document).on('change', '.toggle-status', function() {
                const featureId = $(this).data('feature-id');
                const isActive = $(this).is(':checked');

                $.ajax({
                    url: `{{ url('admin/settings/features-split/feature') }}/${featureId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('{{ __('An error occurred. Please try again.') }}');
                    }
                });
            });

            // Select all checkbox
            $('#select_all').on('change', function() {
                $('.feature-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActions();
            });

            // Individual checkboxes
            $(document).on('change', '.feature-checkbox', function() {
                updateBulkActions();
            });

            function updateBulkActions() {
                const checkedCount = $('.feature-checkbox:checked').length;
                $('.bulk-action-btn').prop('disabled', checkedCount === 0);
            }

            // Bulk actions
            $('.bulk-action-btn').on('click', function() {
                const action = $(this).data('action');
                const selectedFeatures = $('.feature-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedFeatures.length === 0) {
                    toastr.warning('{{ __('Please select at least one feature.') }}');
                    return;
                }

                if (action === 'delete' && !confirm(
                        '{{ __('Are you sure you want to delete the selected features?') }}')) {
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.settings.features-split.features.bulk-action') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action,
                        features: selectedFeatures
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    },
                    error: function() {
                        toastr.error('{{ __('An error occurred. Please try again.') }}');
                    }
                });
            });

            // Drag and drop reordering
            const tbody = document.getElementById('features-tbody');
            if (tbody) {
                new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function(evt) {
                        const features = [];
                        $(tbody).find('tr').each(function(index) {
                            const id = $(this).data('feature-id');
                            if (id) {
                                features.push({
                                    id: id,
                                    order: index + 1
                                });
                            }
                        });

                        $.ajax({
                            url: '{{ route('admin.settings.features-split.features.order') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                features: features
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(tbody).find('tr').each(function(index) {
                                        $(this).find('.badge').text(index + 1);
                                    });
                                    toastr.success(response.message);
                                }
                            },
                            error: function() {
                                toastr.error(
                                    '{{ __('An error occurred while updating order.') }}'
                                );
                            }
                        });
                    }
                });
            }

            // Filters
            $('#search, #status, #sort').on('change keyup', function() {
                applyFilters();
            });

            $('#clear_filters').on('click', function() {
                $('#search').val('');
                $('#status').val('');
                $('#sort').val('order');
                applyFilters();
            });

            function applyFilters() {
                const search = $('#search').val();
                const status = $('#status').val();
                const sort = $('#sort').val();

                $('#features-tbody tr').each(function() {
                    const $row = $(this);
                    const title = $row.find('td:eq(3)').text().toLowerCase();
                    const titleAr = $row.find('td:eq(4)').text().toLowerCase();
                    const description = $row.find('td:eq(5)').text().toLowerCase();
                    const isActive = $row.find('.toggle-status').is(':checked');

                    let show = true;

                    // Search filter
                    if (search && !title.includes(search.toLowerCase()) && !titleAr.includes(search
                            .toLowerCase()) && !description.includes(search.toLowerCase())) {
                        show = false;
                    }

                    // Status filter
                    if (status === 'active' && !isActive) {
                        show = false;
                    } else if (status === 'inactive' && isActive) {
                        show = false;
                    }

                    $row.toggle(show);
                });
            }
        });
    </script>
@endpush
