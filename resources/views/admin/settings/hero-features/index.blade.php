@extends('admin.layout')

@section('title', custom_trans('Hero Features Management', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Hero Features Management', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Hero Features', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHeroFeatureModal">
                            <i class="fas fa-plus me-2"></i>{{ custom_trans('Add New Hero Feature', 'admin') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="card filter-card mb-4">
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="status_filter" class="form-label">{{ custom_trans('Status', 'admin') }}</label>
                        <select class="form-select form-select-lg" id="status_filter" name="status">
                            <option value="">{{ custom_trans('All Status', 'admin') }}</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                {{ custom_trans('Active', 'admin') }}</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                {{ custom_trans('Inactive', 'admin') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="search_filter" class="form-label">{{ custom_trans('Search', 'admin') }}</label>
                        <input type="text" class="form-control form-control-lg" id="search_filter" name="search"
                            value="{{ request('search') }}" placeholder="{{ custom_trans('Search by title, subtitle...', 'admin') }}"
                            autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label for="order_filter" class="form-label">{{ custom_trans('Order By', 'admin') }}</label>
                        <select class="form-select form-select-lg" id="order_filter" name="order_by">
                            <option value="order" {{ request('order_by', 'order') === 'order' ? 'selected' : '' }}>
                                {{ custom_trans('Order', 'admin') }}</option>
                            <option value="title" {{ request('order_by') === 'title' ? 'selected' : '' }}>
                                {{ custom_trans('Title', 'admin') }}</option>
                            <option value="subtitle" {{ request('order_by') === 'subtitle' ? 'selected' : '' }}>
                                {{ custom_trans('Subtitle', 'admin') }}</option>
                            <option value="created_at" {{ request('order_by') === 'created_at' ? 'selected' : '' }}>
                                {{ custom_trans('Created Date', 'admin') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-secondary btn-lg w-100" id="clear_filters">
                            <i class="fas fa-times me-2"></i>{{ custom_trans('Clear', 'admin') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="card table-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ custom_trans('Hero Features', 'admin') }}</h5>
                <div class="bulk-actions d-none-initially">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success btn-sm" id="bulk_activate">
                            <i class="fas fa-check me-1"></i>{{ custom_trans('Activate', 'admin') }}
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" id="bulk_deactivate">
                            <i class="fas fa-pause me-1"></i>{{ custom_trans('Deactivate', 'admin') }}
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="bulk_delete">
                            <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete', 'admin') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="hero-features-table">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                    </div>
                                </th>
                                <th width="80">{{ custom_trans('Order', 'admin') }}</th>
                                <th width="100">{{ custom_trans('Icon', 'admin') }}</th>
                                <th>{{ custom_trans('Title', 'admin') }}</th>
                                <th>{{ custom_trans('Title (AR)', 'admin') }}</th>
                                <th>{{ custom_trans('Subtitle', 'admin') }}</th>
                                <th>{{ custom_trans('Subtitle (AR)', 'admin') }}</th>
                                <th width="100">{{ custom_trans('Status', 'admin') }}</th>
                                <th width="120">{{ custom_trans('Actions', 'admin') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($heroFeatures as $heroFeature)
                                <tr data-id="{{ $heroFeature->id }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input feature-checkbox" type="checkbox"
                                                value="{{ $heroFeature->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $heroFeature->order }}</span>
                                    </td>
                                    <td>
                                        <i class="{{ $heroFeature->icon }} fa-2x text-primary"></i>
                                    </td>
                                    <td>{{ $heroFeature->title }}</td>
                                    <td>{{ $heroFeature->title_ar ?: '-' }}</td>
                                    <td>{{ Str::limit($heroFeature->subtitle, 50) }}</td>
                                    <td>{{ $heroFeature->subtitle_ar ? Str::limit($heroFeature->subtitle_ar, 50) : '-' }}
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                data-id="{{ $heroFeature->id }}"
                                                {{ $heroFeature->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info view-btn"
                                                data-hero-feature="{{ json_encode($heroFeature->toArray()) }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning edit-btn"
                                                data-hero-feature="{{ json_encode($heroFeature->toArray()) }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $heroFeature->id }}">
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
                                            <p>{{ custom_trans('No hero features found', 'admin') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $heroFeatures->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Hero Feature Modal -->
    <div class="modal fade" id="addHeroFeatureModal" tabindex="-1" aria-labelledby="addHeroFeatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addHeroFeatureModalLabel">{{ custom_trans('Add New Hero Feature', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addHeroFeatureForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title_ar" class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                    <input type="text" class="form-control" id="title_ar" name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subtitle" class="form-label">{{ custom_trans('Subtitle', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="subtitle" name="subtitle" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="subtitle_ar" class="form-label">{{ custom_trans('Subtitle (Arabic)', 'admin') }}</label>
                                    <input type="text" class="form-control" id="subtitle_ar" name="subtitle_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">{{ custom_trans('Icon Class', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="icon" name="icon"
                                        placeholder="fas fa-anchor" required>
                                    <small
                                        class="form-text text-muted">{{ custom_trans('Enter FontAwesome icon class (e.g., fas fa-anchor)', 'admin') }}</small>
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
                        <button type="submit" class="btn btn-primary">{{ custom_trans('Create Hero Feature', 'admin') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Hero Feature Modal -->
    <div class="modal fade" id="editHeroFeatureModal" tabindex="-1" aria-labelledby="editHeroFeatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHeroFeatureModalLabel">{{ custom_trans('Edit Hero Feature', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editHeroFeatureForm">
                    @csrf
                    <input type="hidden" id="edit_hero_feature_id" name="hero_feature_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title_ar" class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                    <input type="text" class="form-control" id="edit_title_ar" name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_subtitle" class="form-label">{{ custom_trans('Subtitle', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="edit_subtitle" name="subtitle"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_subtitle_ar"
                                        class="form-label">{{ custom_trans('Subtitle (Arabic)', 'admin') }}</label>
                                    <input type="text" class="form-control" id="edit_subtitle_ar" name="subtitle_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_icon" class="form-label">{{ custom_trans('Icon Class', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="edit_icon" name="icon" required>
                                    <small
                                        class="form-text text-muted">{{ custom_trans('Enter FontAwesome icon class (e.g., fas fa-anchor)', 'admin') }}</small>
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
                        <button type="submit" class="btn btn-warning">{{ custom_trans('Update Hero Feature', 'admin') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Hero Feature Modal -->
    <div class="modal fade" id="viewHeroFeatureModal" tabindex="-1" aria-labelledby="viewHeroFeatureModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewHeroFeatureModalLabel">{{ custom_trans('Hero Feature Details', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Title', 'admin') }}</label>
                                <p id="view_title" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                <p id="view_title_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Subtitle', 'admin') }}</label>
                                <p id="view_subtitle" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Subtitle (Arabic)', 'admin') }}</label>
                                <p id="view_subtitle_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Icon', 'admin') }}</label>
                                <div id="view_icon" class="text-center">
                                    <i class="fa-3x text-primary"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Order', 'admin') }}</label>
                                <p id="view_order" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                <p id="view_status" class="form-control-plaintext"></p>
                            </div>
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
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

                window.location.href = '{{ route('admin.settings.hero-features.index') }}?' + $.param({
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

            // Bulk actions
            $('#bulk_activate').on('click', function() {
                performBulkAction('activate');
            });

            $('#bulk_deactivate').on('click', function() {
                performBulkAction('deactivate');
            });

            $('#bulk_delete').on('click', function() {
                if (confirm('{{ custom_trans('Are you sure you want to delete the selected hero features?', 'admin') }}')) {
                    performBulkAction('delete');
                }
            });

            function performBulkAction(action) {
                const selectedIds = $('.feature-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    toastr.warning('{{ custom_trans('Please select at least one hero feature', 'admin') }}');
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.settings.hero-features.bulk-action') }}',
                    method: 'POST',
                    data: {
                        hero_feature_ids: selectedIds,
                        action: action,
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
                        toastr.error('{{ custom_trans('An error occurred while performing bulk action', 'admin') }}');
                    }
                });
            }

            // Status toggle
            $('.status-toggle').on('change', function() {
                const heroFeatureId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/settings/hero-feature/${heroFeatureId}/toggle-status`,
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
                            '{{ custom_trans('An error occurred while updating the hero feature status', 'admin') }}'
                            );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Add hero feature form
            $('#addHeroFeatureForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('admin.settings.hero-feature.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#addHeroFeatureModal').modal('hide');
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
                            '{{ custom_trans('An error occurred while creating the hero feature', 'admin') }}'
                            );
                    }
                });
            });

            // Edit hero feature form
            $('#editHeroFeatureForm').on('submit', function(e) {
                e.preventDefault();

                const heroFeatureId = $('#edit_hero_feature_id').val();

                $.ajax({
                    url: `/admin/settings/hero-feature/${heroFeatureId}`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editHeroFeatureModal').modal('hide');
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
                            '{{ custom_trans('An error occurred while updating the hero feature', 'admin') }}'
                            );
                    }
                });
            });

            // Edit button click
            $('.edit-btn').on('click', function() {
                const heroFeature = $(this).data('hero-feature');

                $('#edit_hero_feature_id').val(heroFeature.id);
                $('#edit_title').val(heroFeature.title);
                $('#edit_title_ar').val(heroFeature.title_ar);
                $('#edit_subtitle').val(heroFeature.subtitle);
                $('#edit_subtitle_ar').val(heroFeature.subtitle_ar);
                $('#edit_icon').val(heroFeature.icon);
                $('#edit_order').val(heroFeature.order);
                $('#edit_is_active').prop('checked', heroFeature.is_active);

                $('#editHeroFeatureModal').modal('show');
            });

            // View button click
            $('.view-btn').on('click', function() {
                const heroFeature = $(this).data('hero-feature');

                $('#view_title').text(heroFeature.title);
                $('#view_title_ar').text(heroFeature.title_ar || '-');
                $('#view_subtitle').text(heroFeature.subtitle);
                $('#view_subtitle_ar').text(heroFeature.subtitle_ar || '-');
                $('#view_icon i').attr('class', heroFeature.icon + ' fa-3x text-primary');
                $('#view_order').text(heroFeature.order);
                $('#view_status').text(heroFeature.is_active ? '{{ custom_trans('Active', 'admin') }}' :
                    '{{ custom_trans('Inactive', 'admin') }}');

                $('#viewHeroFeatureModal').modal('show');
            });

            // Delete button click
            $('.delete-btn').on('click', function() {
                const heroFeatureId = $(this).data('id');

                if (confirm('{{ custom_trans('Are you sure you want to delete this hero feature?', 'admin') }}')) {
                    $.ajax({
                        url: `/admin/settings/hero-feature/${heroFeatureId}`,
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
                                '{{ custom_trans('An error occurred while deleting the hero feature', 'admin') }}'
                                );
                        }
                    });
                }
            });

            // Make table rows sortable
            const tbody = document.querySelector('#hero-features-table tbody');
            new Sortable(tbody, {
                animation: 150,
                onEnd: function(evt) {
                    const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                    const orders = rows.map((row, index) => ({
                        id: row.dataset.id,
                        order: index + 1
                    }));

                    $.ajax({
                        url: '{{ route('admin.settings.hero-feature.order') }}',
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
                                '{{ custom_trans('An error occurred while updating the order', 'admin') }}'
                                );
                        }
                    });
                }
            });
        });
    </script>
@endpush

