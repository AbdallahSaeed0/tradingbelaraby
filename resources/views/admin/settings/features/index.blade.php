@extends('admin.layout')

@section('title', __('Features Management'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Features') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Features Section Management') }}</h4>
                    <p class="text-muted mb-0">
                        {{ __('Manage the homepage features section with statistics and achievements') }}</p>
                </div>
            </div>
        </div>

        <!-- Filters and Bulk Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filter-card">
                    <div class="card-body p-4">
                        <div class="row align-items-end">
                            <!-- Filters -->
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="status_filter" class="form-label fw-semibold">
                                            <i class="fas fa-toggle-on me-1 text-primary"></i>{{ __('Status') }}
                                        </label>
                                        <select class="form-select form-select-lg" id="status_filter">
                                            <option value="">{{ __('All Status') }}</option>
                                            <option value="1">{{ __('Active') }}</option>
                                            <option value="0">{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="search_filter" class="form-label fw-semibold">
                                            <i class="fas fa-search me-1 text-info"></i>{{ __('Search') }}
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input type="text" class="form-control form-control-lg border-start-0"
                                                id="search_filter"
                                                placeholder="{{ __('Search by title or description...') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="order_filter" class="form-label fw-semibold">
                                            <i class="fas fa-sort me-1 text-warning"></i>{{ __('Sort By') }}
                                        </label>
                                        <select class="form-select form-select-lg" id="order_filter">
                                            <option value="order">{{ __('Order') }}</option>
                                            <option value="title">{{ __('Title') }}</option>
                                            <option value="created_at">{{ __('Created Date') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-secondary btn-lg w-100"
                                            id="clear_filters">
                                            <i class="fas fa-times me-1"></i>{{ __('Clear') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            <div class="col-lg-4">
                                <div class="d-flex justify-content-end align-items-end gap-3">
                                    <div class="flex-grow-1">
                                        <label for="bulk_action" class="form-label fw-semibold">
                                            <i class="fas fa-tasks me-1 text-success"></i>{{ __('Bulk Actions') }}
                                        </label>
                                        <select class="form-select form-select-lg" id="bulk_action">
                                            <option value="">{{ __('Select Action') }}</option>
                                            <option value="activate">{{ __('Activate') }}</option>
                                            <option value="deactivate">{{ __('Deactivate') }}</option>
                                            <option value="delete">{{ __('Delete') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-warning btn-lg" id="apply_bulk_action"
                                            disabled>
                                            <i class="fas fa-check me-1"></i>{{ __('Apply') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Table -->
        <div class="row">
            <div class="col-12">
                <div class="card table-card">
                    <div class="card-header d-flex justify-content-between align-items-center p-4">
                        <div>
                            <h4 class="header-title mb-1">{{ __('Features') }}</h4>
                            <p class="text-muted mb-0">{{ __('Manage your homepage features and statistics') }}</p>
                        </div>
                        <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal"
                            data-bs-target="#addFeatureModal">
                            <i class="fas fa-plus me-2"></i>{{ __('Add New Feature') }}
                        </button>
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
                                        <th width="100">{{ __('Number') }}</th>
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
                                                <span class="badge bg-secondary">{{ $feature->order }}</span>
                                            </td>
                                            <td>
                                                <img src="{{ $feature->icon_url }}" alt="{{ $feature->title }}"
                                                    class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
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
                                                        title="{{ __('Edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-info view-feature"
                                                        data-feature="{{ json_encode($feature->toArray()) }}"
                                                        title="{{ __('View') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-feature"
                                                        data-feature-id="{{ $feature->id }}"
                                                        title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">{{ __('No features found') }}</td>
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
                    <h5 class="modal-title" id="addFeatureModalLabel">{{ __('Add New Feature') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addFeatureForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">{{ __('Title') }} *</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title_ar" class="form-label">{{ __('Title (Arabic)') }}</label>
                                    <input type="text" class="form-control" id="title_ar" name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="number" class="form-label">{{ __('Number') }} *</label>
                                    <input type="number" class="form-control" id="number" name="number"
                                        min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order" class="form-label">{{ __('Order') }}</label>
                                    <input type="number" class="form-control" id="order" name="order"
                                        value="0" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">{{ __('Icon') }} *</label>
                                    <input type="file" class="form-control" id="icon" name="icon"
                                        accept="image/*" required>
                                    <small
                                        class="form-text text-muted">{{ __('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('Description') }} *</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description_ar"
                                        class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" id="description_ar" name="description_ar" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('Create Feature') }}</button>
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
                <form id="editFeatureForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_feature_id" name="feature_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">{{ __('Title') }} *</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title_ar" class="form-label">{{ __('Title (Arabic)') }}</label>
                                    <input type="text" class="form-control" id="edit_title_ar" name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_number" class="form-label">{{ __('Number') }} *</label>
                                    <input type="number" class="form-control" id="edit_number" name="number"
                                        min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_order" class="form-label">{{ __('Order') }}</label>
                                    <input type="number" class="form-control" id="edit_order" name="order"
                                        min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_order" class="form-label">{{ __('Order') }}</label>
                                    <input type="number" class="form-control" id="edit_order" name="order"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_icon" class="form-label">{{ __('Icon') }}</label>
                                    <input type="file" class="form-control" id="edit_icon" name="icon"
                                        accept="image/*">
                                    <small
                                        class="form-text text-muted">{{ __('Max size: 2MB. Formats: JPEG, PNG, JPG, GIF') }}</small>
                                    <div id="current_icon_preview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">{{ __('Description') }} *</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_description_ar"
                                        class="form-label">{{ __('Description (Arabic)') }}</label>
                                    <textarea class="form-control" id="edit_description_ar" name="description_ar" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                <label class="form-check-label" for="edit_is_active">
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
                                <p id="view_title" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Title (Arabic)') }}</label>
                                <p id="view_title_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Number') }}</label>
                                <p id="view_number" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Order') }}</label>
                                <p id="view_order" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Order') }}</label>
                                <p id="view_order" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Status') }}</label>
                                <p id="view_status" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Description') }}</label>
                                <p id="view_description" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Description (Arabic)') }}</label>
                                <p id="view_description_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Icon') }}</label>
                        <div id="view_icon" class="text-center">
                            <img src="" alt="Feature Icon" class="img-fluid rounded"
                                style="max-width: 200px; max-height: 200px;">
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

        .page-title-box .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item.active {
            color: white;
        }

        /* Filter Card */
        .filter-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .filter-card .card-body {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
        }

        /* Table Card */
        .table-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-card .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .table-card .header-title {
            color: #2c3e50;
            font-weight: 700;
        }

        /* Enhanced Table */
        #features-table {
            margin-bottom: 0;
        }

        #features-table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        #features-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        #features-table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: translateX(5px);
        }

        #features-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border: none;
        }

        /* Sortable Styles */
        .sortable-ghost {
            opacity: 0.5;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-radius: 10px;
        }

        .sortable-chosen {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        #features-tbody tr {
            cursor: move;
        }

        #features-tbody tr td:nth-child(2) {
            cursor: grab;
        }

        #features-tbody tr td:nth-child(2):active {
            cursor: grabbing;
        }

        /* Enhanced Buttons */
        .btn-group .btn {
            margin-right: 2px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Form Controls */
        .form-select-lg,
        .form-control-lg {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-select-lg:focus,
        .form-control-lg:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 2px solid #e9ecef;
            background: #f8f9fa;
        }

        /* Badge Enhancements */
        .badge {
            font-weight: 600;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }

        /* Image Enhancements */
        .table img {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .table img:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        /* Switch Enhancements */
        .form-check-input {
            width: 3rem;
            height: 1.5rem;
            border-radius: 1rem;
            border: 2px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        /* Loading Animation */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function() {
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
                    toastr.warning('{{ __('Please select an action') }}');
                    return;
                }

                if (selectedIds.length === 0) {
                    toastr.warning('{{ __('Please select at least one feature') }}');
                    return;
                }

                let confirmMessage = '';
                switch (action) {
                    case 'activate':
                        confirmMessage =
                            '{{ __('Are you sure you want to activate the selected features?') }}';
                        break;
                    case 'deactivate':
                        confirmMessage =
                            '{{ __('Are you sure you want to deactivate the selected features?') }}';
                        break;
                    case 'delete':
                        confirmMessage =
                            '{{ __('Are you sure you want to delete the selected features? This action cannot be undone.') }}';
                        break;
                }

                if (confirm(confirmMessage)) {
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
                                toastr.success(response.message);
                                location.reload();
                            }
                        },
                        error: function() {
                            toastr.error(
                                '{{ __('An error occurred while performing bulk action') }}'
                            );
                        }
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
                                '{{ __('An error occurred while creating the feature') }}'
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
                    <img src="${feature.icon_url}" alt="Current Icon" class="img-thumbnail" style="max-width: 100px;">
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
                $('#view_status').text(feature.is_active ? '{{ __('Active') }}' :
                    '{{ __('Inactive') }}');
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
                                '{{ __('An error occurred while updating the feature') }}'
                                );
                        }
                    }
                });
            });

            // Delete Feature
            $('.delete-feature').on('click', function() {
                const featureId = $(this).data('feature-id');

                if (confirm('{{ __('Are you sure you want to delete this feature?') }}')) {
                    $.ajax({
                        url: `/admin/settings/feature/${featureId}`,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                location.reload();
                            }
                        },
                        error: function() {
                            toastr.error(
                                '{{ __('An error occurred while deleting the feature') }}'
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
                            '{{ __('An error occurred while updating the feature status') }}'
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
                                    '{{ __('An error occurred while updating the order') }}'
                                );
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush
