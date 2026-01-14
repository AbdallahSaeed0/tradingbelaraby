@extends('admin.layout')

@section('title', custom_trans('Features Management', 'admin'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ custom_trans('Features', 'admin') }}</li>
                        </ol>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title mb-0">{{ custom_trans('Features Section Management', 'admin') }}</h4>
                    <p class="text-muted mb-0">
                        {{ custom_trans('Manage the homepage features section with statistics and achievements', 'admin') }}</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                data-bs-target="#addFeatureModal">
                                <i class="fas fa-plus me-2"></i>{{ custom_trans('Add New Feature', 'admin') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.settings.features.index') }}" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-3">
                            <select class="form-select" name="status" id="status_filter">
                                            <option value="">{{ custom_trans('All Status', 'admin') }}</option>
                                            <option value="1">{{ custom_trans('Active', 'admin') }}</option>
                                            <option value="0">{{ custom_trans('Inactive', 'admin') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                                <input type="text" class="form-control" name="search" id="search_filter"
                                                placeholder="{{ custom_trans('Search by title or description...', 'admin') }}"
                                    value="{{ request('search') }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                            <select class="form-select" name="order" id="order_filter">
                                            <option value="order">{{ custom_trans('Order', 'admin') }}</option>
                                            <option value="title">{{ custom_trans('Title', 'admin') }}</option>
                                            <option value="created_at">{{ custom_trans('Created Date', 'admin') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search me-1"></i>{{ custom_trans('Filter', 'admin') }}
                                        </button>
                                <button type="button" class="btn btn-outline-secondary" id="clear_filters">
                                    <i class="fa fa-refresh me-1"></i>{{ custom_trans('Clear', 'admin') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                </form>
            </div>
        </div>

        <!-- Features Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="features-table">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select_all">
                                            </div>
                                        </th>
                                        <th width="80">{{ custom_trans('Order', 'admin') }}</th>
                                        <th width="100">{{ custom_trans('Icon', 'admin') }}</th>
                                        <th>{{ custom_trans('Title', 'admin') }}</th>
                                        <th>{{ custom_trans('Title (AR)', 'admin') }}</th>
                                        <th>{{ custom_trans('Description', 'admin') }}</th>
                                        <th>{{ custom_trans('Description (AR)', 'admin') }}</th>
                                        <th width="100">{{ custom_trans('Number', 'admin') }}</th>
                                        <th width="100">{{ custom_trans('Status', 'admin') }}</th>
                                        <th width="120">{{ custom_trans('Actions', 'admin') }}</th>
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
                                                <span class="badge bg-secondary">{{ $feature->order }}</span>
                                            </td>
                                            <td>
                                                <img src="{{ $feature->icon_url }}" alt="{{ $feature->title }}"
                                                    class="rounded w-60 h-60 img-h-60">
                                            </td>
                                            <td>{{ $feature->title }}</td>
                                            <td>{{ $feature->title_ar ?: '-' }}</td>
                                            <td>{{ Str::limit($feature->description, 80) }}</td>
                                            <td>{{ $feature->description_ar ? Str::limit($feature->description_ar, 80) : '-' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary fs-6">{{ $feature->number }}</span>
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
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary edit-feature"
                                                        data-feature="{{ json_encode($feature->toArray()) }}"
                                                        title="{{ custom_trans('Edit', 'admin') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-info view-feature"
                                                        data-feature="{{ json_encode($feature->toArray()) }}"
                                                        title="{{ custom_trans('View', 'admin') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-feature"
                                                        data-feature-id="{{ $feature->id }}"
                                                        title="{{ custom_trans('Delete', 'admin') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">{{ custom_trans('No features found', 'admin') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                    <h5 class="modal-title" id="addFeatureModalLabel">{{ custom_trans('Add New Feature', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addFeatureForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Language Tabs -->
                        <div class="language-tabs mb-3">
                            <button type="button" class="language-tab active" data-lang="en">
                                <i class="fas fa-globe me-1"></i> English
                            </button>
                            <button type="button" class="language-tab" data-lang="ar">
                                <i class="fas fa-globe me-1"></i> العربية
                            </button>
                        </div>

                        <!-- English Content -->
                        <div id="add-content-en" class="language-content active">
                                <div class="mb-3">
                                    <label for="title" class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">{{ custom_trans('Description', 'admin') }} *</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Arabic Content -->
                        <div id="add-content-ar" class="language-content">
                                <div class="mb-3">
                                    <label for="title_ar" class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="title_ar" name="title_ar" dir="rtl">
                                </div>
                            <div class="mb-3">
                                <label for="description_ar" class="form-label">{{ custom_trans('Description (Arabic)', 'admin') }}</label>
                                <textarea class="form-control" id="description_ar" name="description_ar" rows="3" dir="rtl"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">{{ custom_trans('Number', 'admin') }} *</label>
                                    <input type="number" class="form-control" id="number" name="number"
                                        min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                    <input type="number" class="form-control" id="order" name="order"
                                        value="0" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">{{ custom_trans('Icon', 'admin') }} *</label>
                                    <input type="file" class="form-control" id="icon" name="icon"
                                        accept="image/*" required>
                                    <small
                                        class="form-text text-muted">{{ custom_trans('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF', 'admin') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    {{ custom_trans('Active', 'admin') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                        <button type="submit" class="btn btn-warning">{{ custom_trans('Create Feature', 'admin') }}</button>
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
                    <h5 class="modal-title" id="editFeatureModalLabel">{{ custom_trans('Edit Feature', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editFeatureForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_feature_id" name="feature_id">
                    <div class="modal-body">
                        <!-- Language Tabs -->
                        <div class="language-tabs mb-3">
                            <button type="button" class="language-tab active" data-lang="edit-en">
                                <i class="fas fa-globe me-1"></i> English
                            </button>
                            <button type="button" class="language-tab" data-lang="edit-ar">
                                <i class="fas fa-globe me-1"></i> العربية
                            </button>
                        </div>

                        <!-- English Content -->
                        <div id="edit-content-en" class="language-content active">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">{{ custom_trans('Description', 'admin') }} *</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Arabic Content -->
                        <div id="edit-content-ar" class="language-content">
                                <div class="mb-3">
                                    <label for="edit_title_ar" class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_title_ar" name="title_ar" dir="rtl">
                                </div>
                            <div class="mb-3">
                                <label for="edit_description_ar" class="form-label">{{ custom_trans('Description (Arabic)', 'admin') }}</label>
                                <textarea class="form-control" id="edit_description_ar" name="description_ar" rows="3" dir="rtl"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_number" class="form-label">{{ custom_trans('Number', 'admin') }} *</label>
                                    <input type="number" class="form-control" id="edit_number" name="number"
                                        min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                    <input type="number" class="form-control" id="edit_order" name="order"
                                        min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                    <input type="number" class="form-control" id="edit_order" name="order"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_icon" class="form-label">{{ custom_trans('Icon', 'admin') }}</label>
                                    <input type="file" class="form-control" id="edit_icon" name="icon"
                                        accept="image/*">
                                    <small
                                        class="form-text text-muted">{{ custom_trans('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF', 'admin') }}</small>
                                    <div id="current_icon_preview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                <label class="form-check-label" for="edit_is_active">
                                    {{ custom_trans('Active', 'admin') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                        <button type="submit" class="btn btn-warning">{{ custom_trans('Update Feature', 'admin') }}</button>
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
                    <h5 class="modal-title" id="viewFeatureModalLabel">{{ custom_trans('Feature Details', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Language Tabs -->
                    <div class="language-tabs mb-3">
                        <button type="button" class="language-tab active" data-lang="view-en">
                            <i class="fas fa-globe me-1"></i> English
                        </button>
                        <button type="button" class="language-tab" data-lang="view-ar">
                            <i class="fas fa-globe me-1"></i> العربية
                        </button>
                    </div>

                    <!-- English Content -->
                    <div id="view-content-en" class="language-content active">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Title', 'admin') }}</label>
                                <p id="view_title" class="form-control-plaintext"></p>
                            </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Description', 'admin') }}</label>
                            <p id="view_description" class="form-control-plaintext"></p>
                        </div>
                    </div>

                    <!-- Arabic Content -->
                    <div id="view-content-ar" class="language-content">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                            <p id="view_title_ar" class="form-control-plaintext" dir="rtl"></p>
                            </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Description (Arabic)', 'admin') }}</label>
                            <p id="view_description_ar" class="form-control-plaintext" dir="rtl"></p>
                        </div>
                    </div>

                    <!-- Common Fields (not language-specific) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Number', 'admin') }}</label>
                                <p id="view_number" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Order', 'admin') }}</label>
                                <p id="view_order" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                <p id="view_status" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ custom_trans('Icon', 'admin') }}</label>
                        <div id="view_icon" class="text-center">
                            <img src="" alt="Feature Icon" class="img-fluid rounded max-w-200 max-h-200">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Close', 'admin') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ custom_trans('Confirm Delete', 'admin') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteConfirmMessage">{{ custom_trans('Are you sure you want to delete this item? This action cannot be undone.', 'admin') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ custom_trans('Cancel', 'admin') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete', 'admin') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
            // Language tab switching for Add Modal
            $('#addFeatureModal .language-tab').on('click', function() {
                const lang = $(this).data('lang');
                const modal = $(this).closest('.modal');
                
                modal.find('.language-tab').removeClass('active');
                modal.find('.language-content').removeClass('active');
                
                $(this).addClass('active');
                if (lang === 'en') {
                    modal.find('#add-content-en').addClass('active');
                } else if (lang === 'ar') {
                    modal.find('#add-content-ar').addClass('active');
                }
            });

            // Language tab switching for Edit Modal
            $('#editFeatureModal .language-tab').on('click', function() {
                const lang = $(this).data('lang');
                const modal = $(this).closest('.modal');
                
                modal.find('.language-tab').removeClass('active');
                modal.find('.language-content').removeClass('active');
                
                $(this).addClass('active');
                if (lang === 'edit-en') {
                    modal.find('#edit-content-en').addClass('active');
                } else if (lang === 'edit-ar') {
                    modal.find('#edit-content-ar').addClass('active');
                }
            });

            // Language tab switching for View Modal
            $('#viewFeatureModal .language-tab').on('click', function() {
                const lang = $(this).data('lang');
                const modal = $(this).closest('.modal');
                
                modal.find('.language-tab').removeClass('active');
                modal.find('.language-content').removeClass('active');
                
                $(this).addClass('active');
                if (lang === 'view-en') {
                    modal.find('#view-content-en').addClass('active');
                } else if (lang === 'view-ar') {
                    modal.find('#view-content-ar').addClass('active');
                }
            });

            // Select All Checkbox
            $('#select_all').on('change', function() {
                $('.feature-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActionButton();
            });

            // Individual Checkbox
            $('.feature-checkbox').on('change', function() {
                updateBulkActionButton();
                updateSelectAllCheckbox();
            });

            function updateBulkActionButton() {
                const checkedCount = $('.feature-checkbox:checked').length;
                $('#apply_bulk_action').prop('disabled', checkedCount === 0);
            }

            function updateSelectAllCheckbox() {
                const totalCheckboxes = $('.feature-checkbox').length;
                const checkedCheckboxes = $('.feature-checkbox:checked').length;
                $('#select_all').prop('checked', totalCheckboxes === checkedCheckboxes);
            }

            // Debounce function for search
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Apply filters function
            function applyFilters() {
                const status = $('#status_filter').val();
                const search = $('#search_filter').val();
                const orderBy = $('#order_filter').val();

                window.location.href = '{{ route('admin.settings.features.index') }}?' + $.param({
                    status: status,
                    search: search,
                    order_by: orderBy
                });
            }

            // Debounced search function
            const debouncedSearch = debounce(applyFilters, 500);

            // Automatic search on input change
            $('#search_filter').on('input', function() {
                debouncedSearch();
            });

            // Automatic filter on status change
            $('#status_filter, #order_filter').on('change', function() {
                applyFilters();
            });

            $('#clear_filters').on('click', function() {
                $('#status_filter').val('');
                $('#search_filter').val('');
                $('#order_filter').val('order');
                applyFilters();
            });

            // Bulk Actions
            $('#apply_bulk_action').on('click', function() {
                const action = $('#bulk_action').val();
                const selectedIds = $('.feature-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (!action) {
                    toastr.warning('{{ custom_trans('Please select an action', 'admin') }}');
                    return;
                }

                if (selectedIds.length === 0) {
                    toastr.warning('{{ custom_trans('Please select at least one feature', 'admin') }}');
                    return;
                }

                let confirmMessage = '';
                switch (action) {
                    case 'activate':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to activate the selected features?', 'admin') }}';
                        break;
                    case 'deactivate':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to deactivate the selected features?', 'admin') }}';
                        break;
                    case 'delete':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to delete the selected features? This action cannot be undone.', 'admin') }}';
                        break;
                }

                // Show delete confirmation modal for delete action
                if (action === 'delete') {
                    deleteFeatureId = null; // Clear single delete
                    $('#deleteConfirmMessage').text(confirmMessage);
                    $('#deleteConfirmModal').modal('show');
                    // Use one-time handler for bulk delete
                    $('#confirmDeleteBtn').off('click.bulkDelete').on('click.bulkDelete', function() {
                    $.ajax({
                        url: '{{ route('admin.settings.features.bulk-action') }}',
                        type: 'POST',
                        data: {
                            action: action,
                            feature_ids: selectedIds,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                    $('#deleteConfirmModal').modal('hide');
                                    $('#confirmDeleteBtn').off('click.bulkDelete');
                                toastr.success(response.message);
                                location.reload();
                            }
                        },
                        error: function() {
                                $('#deleteConfirmModal').modal('hide');
                                $('#confirmDeleteBtn').off('click.bulkDelete');
                            toastr.error(
                                '{{ custom_trans('An error occurred while performing bulk action', 'admin') }}'
                            );
                        }
                        });
                    });
                } else {
                    // For other actions, show confirmation modal
                    $('#deleteConfirmMessage').text(confirmMessage);
                    $('#deleteConfirmModal').modal('show');
                    $('#confirmDeleteBtn').off('click.bulkAction').on('click.bulkAction', function() {
                        $.ajax({
                            url: '{{ route('admin.settings.features.bulk-action') }}',
                            type: 'POST',
                            data: {
                                action: action,
                                feature_ids: selectedIds,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#deleteConfirmModal').modal('hide');
                                    $('#confirmDeleteBtn').off('click.bulkAction');
                                    toastr.success(response.message);
                                    location.reload();
                                }
                            },
                            error: function() {
                                $('#deleteConfirmModal').modal('hide');
                                $('#confirmDeleteBtn').off('click.bulkAction');
                                toastr.error(
                                    '{{ custom_trans('An error occurred while performing bulk action', 'admin') }}'
                                );
                            }
                        });
                    });
                }
            });

            // Add Feature Form Submission
            $('#addFeatureForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.settings.feature.store') }}',
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
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                '{{ custom_trans('An error occurred while creating the feature', 'admin') }}'
                            );
                        }
                    }
                });
            });

            // Edit Feature
            $('.edit-feature').on('click', function() {
                const feature = $(this).data('feature');

                $('#edit_feature_id').val(feature.id);
                $('#edit_title').val(feature.title);
                $('#edit_title_ar').val(feature.title_ar);
                $('#edit_description').val(feature.description);
                $('#edit_description_ar').val(feature.description_ar);
                $('#edit_number').val(feature.number);
                $('#edit_order').val(feature.order);
                $('#edit_is_active').prop('checked', feature.is_active);

                // Show current icon preview
                $('#current_icon_preview').html(`
                    <img src="${feature.icon_url}" alt="Current Icon" class="img-thumbnail max-w-100">
                `);

                $('#editFeatureModal').modal('show');
            });

            // View Feature
            $('.view-feature').on('click', function() {
                const feature = $(this).data('feature');

                $('#view_title').text(feature.title);
                $('#view_title_ar').text(feature.title_ar || '-');
                $('#view_description').text(feature.description);
                $('#view_description_ar').text(feature.description_ar || '-');
                $('#view_number').text(feature.number);
                $('#view_order').text(feature.order);
                $('#view_status').text(feature.is_active ? '{{ custom_trans('Active', 'admin') }}' :
                    '{{ custom_trans('Inactive', 'admin') }}');
                $('#view_icon img').attr('src', feature.icon_url);

                $('#viewFeatureModal').modal('show');
            });

            // Edit Feature Form Submission
            $('#editFeatureForm').on('submit', function(e) {
                e.preventDefault();

                const featureId = $('#edit_feature_id').val();
                const formData = new FormData(this);

                $.ajax({
                    url: `/admin/settings/feature/${featureId}`,
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
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                '{{ custom_trans('An error occurred while updating the feature', 'admin') }}'
                            );
                        }
                    }
                });
            });

            // Delete Feature
            let deleteFeatureId = null;
            $('.delete-feature').on('click', function() {
                deleteFeatureId = $(this).data('feature-id');
                $('#deleteConfirmMessage').text('{{ custom_trans('Are you sure you want to delete this feature? This action cannot be undone.', 'admin') }}');
                $('#deleteConfirmModal').modal('show');
            });

            // Single delete handler
            $(document).on('click', '#confirmDeleteBtn', function() {
                if (deleteFeatureId) {
                    const featureId = deleteFeatureId;
                    deleteFeatureId = null; // Reset
                    $.ajax({
                        url: `/admin/settings/feature/${featureId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#deleteConfirmModal').modal('hide');
                                toastr.success(response.message);
                                location.reload();
                            }
                        },
                        error: function() {
                            $('#deleteConfirmModal').modal('hide');
                            toastr.error(
                                '{{ custom_trans('An error occurred while deleting the feature', 'admin') }}'
                            );
                        }
                    });
                }
            });

            // Toggle Feature Status
            $('.toggle-status').on('change', function() {
                const featureId = $(this).data('feature-id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/settings/feature/${featureId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ custom_trans('An error occurred while updating the feature status', 'admin') }}'
                        );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Sortable table for reordering
            const featuresTbody = document.getElementById('features-tbody');
            if (featuresTbody) {
                new Sortable(featuresTbody, {
                    handle: 'td:nth-child(2)',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        const features = [];
                        const rows = featuresTbody.querySelectorAll('tr[data-feature-id]');

                        rows.forEach((row, index) => {
                            const featureId = row.getAttribute('data-feature-id');
                            if (featureId) {
                                features.push({
                                    id: featureId,
                                    order: index
                                });
                            }
                        });

                        $.ajax({
                            url: '{{ route('admin.settings.feature.order') }}',
                            type: 'POST',
                            data: {
                                features: features,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                }
                            },
                            error: function() {
                                toastr.error(
                                    '{{ custom_trans('An error occurred while updating the order', 'admin') }}'
                                );
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush

