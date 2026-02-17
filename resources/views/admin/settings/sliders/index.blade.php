@extends('admin.layout')

@section('title', custom_trans('Slider Management', 'admin'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ custom_trans('Sliders', 'admin') }}</li>
                        </ol>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="page-title mb-0">{{ custom_trans('Slider Management', 'admin') }}</h4>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addSliderModal">
                                <i class="fas fa-plus me-2"></i>{{ custom_trans('Add New Slider', 'admin') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.settings.sliders.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small mb-1">{{ custom_trans('Status', 'admin') }}</label>
                            <select class="form-select" name="status" id="status_filter">
                                <option value="">{{ custom_trans('All Status', 'admin') }}</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                    {{ custom_trans('Active', 'admin') }}</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                    {{ custom_trans('Inactive', 'admin') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">{{ custom_trans('Sort by', 'admin') }}</label>
                            <select class="form-select" name="order" id="order_filter">
                                <option value="order">{{ custom_trans('Order', 'admin') }}</option>
                                <option value="title">{{ custom_trans('Title', 'admin') }}</option>
                                <option value="created_at">{{ custom_trans('Created Date', 'admin') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-filter me-1"></i>{{ custom_trans('Filter', 'admin') }}
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary" id="clear_filters">
                                <i class="fa fa-refresh me-1"></i>{{ custom_trans('Clear', 'admin') }}
                            </button>
                        </div>
                    </div>
            </div>
            </form>
        </div>
    </div>

    <!-- Sliders Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="sliders-table">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select_all">
                                        </div>
                                    </th>
                                    <th width="80">{{ custom_trans('Order', 'admin') }}</th>
                                    <th width="100">{{ custom_trans('Image', 'admin') }}</th>
                                    <th>{{ custom_trans('Title', 'admin') }}</th>
                                    <th>{{ custom_trans('Title (AR)', 'admin') }}</th>
                                    <th>{{ custom_trans('Welcome Text', 'admin') }}</th>
                                    <th>{{ custom_trans('Welcome Text (AR)', 'admin') }}</th>
                                    <th>{{ custom_trans('Subtitle', 'admin') }}</th>
                                    <th>{{ custom_trans('Subtitle (AR)', 'admin') }}</th>
                                    <th width="100">{{ custom_trans('Status', 'admin') }}</th>
                                    <th width="120">{{ custom_trans('Actions', 'admin') }}</th>
                                </tr>
                            </thead>
                            <tbody id="sliders-tbody">
                                @forelse($sliders as $slider)
                                    <tr data-slider-id="{{ $slider->id }}">
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input slider-checkbox" type="checkbox"
                                                    value="{{ $slider->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $slider->order }}</span>
                                        </td>
                                        <td>
                                            <img src="{{ $slider->background_image_url }}" alt="{{ $slider->title }}"
                                                class="rounded slider-thumb">
                                        </td>
                                        <td>{{ $slider->title }}</td>
                                        <td>{{ $slider->title_ar ?: '-' }}</td>
                                        <td>{{ Str::limit($slider->welcome_text, 50) }}</td>
                                        <td>{{ $slider->welcome_text_ar ? Str::limit($slider->welcome_text_ar, 50) : '-' }}
                                        </td>
                                        <td>{{ Str::limit($slider->subtitle, 50) }}</td>
                                        <td>{{ $slider->subtitle_ar ?: '-' }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-status" type="checkbox"
                                                    data-slider-id="{{ $slider->id }}"
                                                    {{ $slider->is_active ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-slider"
                                                    data-slider="{{ json_encode($slider->toArray()) }}"
                                                    title="{{ custom_trans('Edit', 'admin') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info view-slider"
                                                    data-slider="{{ json_encode($slider->toArray()) }}"
                                                    title="{{ custom_trans('View', 'admin') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-slider"
                                                    data-slider-id="{{ $slider->id }}"
                                                    title="{{ custom_trans('Delete', 'admin') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            {{ custom_trans('No sliders found', 'admin') }}</td>
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

    <!-- Add Slider Modal -->
    <div class="modal fade" id="addSliderModal" tabindex="-1" aria-labelledby="addSliderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSliderModalLabel">{{ custom_trans('Add New Slider', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSliderForm" enctype="multipart/form-data">
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
                                <label for="title" class="form-label">{{ custom_trans('Title', 'admin') }}</label>
                                <input type="text" class="form-control" id="title" name="title">
                            </div>
                            <div class="mb-3">
                                <label for="welcome_text"
                                    class="form-label">{{ custom_trans('Welcome Text', 'admin') }}</label>
                                <input type="text" class="form-control" id="welcome_text" name="welcome_text">
                            </div>
                            <div class="mb-3">
                                <label for="subtitle" class="form-label">{{ custom_trans('Subtitle', 'admin') }}</label>
                                <input type="text" class="form-control" id="subtitle" name="subtitle">
                            </div>
                            <div class="mb-3">
                                <label for="button_text"
                                    class="form-label">{{ custom_trans('Button Text', 'admin') }}</label>
                                <input type="text" class="form-control" id="button_text" name="button_text"
                                    value="Search">
                            </div>
                            <div class="mb-3">
                                <label for="search_placeholder"
                                    class="form-label">{{ custom_trans('Search Placeholder', 'admin') }}</label>
                                <input type="text" class="form-control" id="search_placeholder"
                                    name="search_placeholder" value="Search Courses">
                            </div>
                        </div>

                        <!-- Arabic Content -->
                        <div id="add-content-ar" class="language-content">
                            <div class="mb-3">
                                <label for="title_ar"
                                    class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="title_ar" name="title_ar"
                                    dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="welcome_text_ar"
                                    class="form-label">{{ custom_trans('Welcome Text (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="welcome_text_ar" name="welcome_text_ar"
                                    dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="subtitle_ar"
                                    class="form-label">{{ custom_trans('Subtitle (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="subtitle_ar" name="subtitle_ar"
                                    dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="button_text_ar"
                                    class="form-label">{{ custom_trans('Button Text (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="button_text_ar" name="button_text_ar"
                                    dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="search_placeholder_ar"
                                    class="form-label">{{ custom_trans('Search Placeholder (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="search_placeholder_ar"
                                    name="search_placeholder_ar" dir="rtl">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="text_position"
                                        class="form-label">{{ custom_trans('Text Position', 'admin') }}</label>
                                    <select class="form-select" id="text_position" name="text_position">
                                        <option value="top-left">{{ custom_trans('Top Left', 'admin') }}</option>
                                        <option value="top-center">{{ custom_trans('Top Center', 'admin') }}</option>
                                        <option value="top-right">{{ custom_trans('Top Right', 'admin') }}</option>
                                        <option value="center-left" selected>{{ custom_trans('Center Left', 'admin') }}
                                        </option>
                                        <option value="center-center">{{ custom_trans('Center Center', 'admin') }}
                                        </option>
                                        <option value="center-right">{{ custom_trans('Center Right', 'admin') }}</option>
                                        <option value="bottom-left">{{ custom_trans('Bottom Left', 'admin') }}</option>
                                        <option value="bottom-center">{{ custom_trans('Bottom Center', 'admin') }}
                                        </option>
                                        <option value="bottom-right">{{ custom_trans('Bottom Right', 'admin') }}</option>
                                    </select>
                                    <small
                                        class="form-text text-muted">{{ custom_trans('Choose where the text will be positioned on the slider', 'admin') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="button_url" class="form-label">{{ custom_trans('Button URL', 'admin') }}</label>
                            <input type="url" class="form-control" id="button_url" name="button_url">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                    <input type="number" class="form-control" id="order" name="order"
                                        value="0" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="background_image"
                                        class="form-label">{{ custom_trans('Background Image', 'admin') }} *</label>
                                    <input type="file" class="form-control" id="background_image"
                                        name="background_image"
                                        accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" required>
                                    <small
                                        class="form-text text-muted d-block mt-1">{{ custom_trans('Max size: 5MB. Formats: JPG, JPEG, PNG, WEBP, GIF', 'admin') }}</small>
                                    <div id="background_image_error" class="invalid-feedback d-none"></div>
                                    <div id="background_image_preview" class="mt-2"></div>
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
                        <button type="submit"
                            class="btn btn-primary">{{ custom_trans('Create Slider', 'admin') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Slider Modal -->
    <div class="modal fade" id="editSliderModal" tabindex="-1" aria-labelledby="editSliderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSliderModalLabel">{{ custom_trans('Edit Slider', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSliderForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_slider_id" name="slider_id">
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
                                <label for="edit_title" class="form-label">{{ custom_trans('Title', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_title" name="title">
                            </div>
                            <div class="mb-3">
                                <label for="edit_welcome_text"
                                    class="form-label">{{ custom_trans('Welcome Text', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_welcome_text" name="welcome_text">
                            </div>
                            <div class="mb-3">
                                <label for="edit_subtitle"
                                    class="form-label">{{ custom_trans('Subtitle', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_subtitle" name="subtitle">
                            </div>
                            <div class="mb-3">
                                <label for="edit_button_text"
                                    class="form-label">{{ custom_trans('Button Text', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_button_text" name="button_text">
                            </div>
                            <div class="mb-3">
                                <label for="edit_search_placeholder"
                                    class="form-label">{{ custom_trans('Search Placeholder', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_search_placeholder"
                                    name="search_placeholder">
                            </div>
                        </div>

                        <!-- Arabic Content -->
                        <div id="edit-content-ar" class="language-content">
                            <div class="mb-3">
                                <label for="edit_title_ar"
                                    class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_title_ar" name="title_ar"
                                    dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="edit_welcome_text_ar"
                                    class="form-label">{{ custom_trans('Welcome Text (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_welcome_text_ar"
                                    name="welcome_text_ar" dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="edit_subtitle_ar"
                                    class="form-label">{{ custom_trans('Subtitle (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_subtitle_ar" name="subtitle_ar"
                                    dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="edit_button_text_ar"
                                    class="form-label">{{ custom_trans('Button Text (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_button_text_ar"
                                    name="button_text_ar" dir="rtl">
                            </div>
                            <div class="mb-3">
                                <label for="edit_search_placeholder_ar"
                                    class="form-label">{{ custom_trans('Search Placeholder (Arabic)', 'admin') }}</label>
                                <input type="text" class="form-control" id="edit_search_placeholder_ar"
                                    name="search_placeholder_ar" dir="rtl">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="edit_text_position"
                                        class="form-label">{{ custom_trans('Text Position', 'admin') }}</label>
                                    <select class="form-select" id="edit_text_position" name="text_position">
                                        <option value="top-left">{{ custom_trans('Top Left', 'admin') }}</option>
                                        <option value="top-center">{{ custom_trans('Top Center', 'admin') }}</option>
                                        <option value="top-right">{{ custom_trans('Top Right', 'admin') }}</option>
                                        <option value="center-left">{{ custom_trans('Center Left', 'admin') }}</option>
                                        <option value="center-center">{{ custom_trans('Center Center', 'admin') }}
                                        </option>
                                        <option value="center-right">{{ custom_trans('Center Right', 'admin') }}</option>
                                        <option value="bottom-left">{{ custom_trans('Bottom Left', 'admin') }}</option>
                                        <option value="bottom-center">{{ custom_trans('Bottom Center', 'admin') }}
                                        </option>
                                        <option value="bottom-right">{{ custom_trans('Bottom Right', 'admin') }}</option>
                                    </select>
                                    <small
                                        class="form-text text-muted">{{ custom_trans('Choose where the text will be positioned on the slider', 'admin') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_button_url"
                                class="form-label">{{ custom_trans('Button URL', 'admin') }}</label>
                            <input type="url" class="form-control" id="edit_button_url" name="button_url">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_order"
                                        class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                    <input type="number" class="form-control" id="edit_order" name="order"
                                        min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_background_image"
                                        class="form-label">{{ custom_trans('Background Image', 'admin') }}</label>
                                    <input type="file" class="form-control" id="edit_background_image"
                                        name="background_image"
                                        accept="image/jpeg,image/jpg,image/png,image/webp,image/gif">
                                    <small
                                        class="form-text text-muted d-block mt-1">{{ custom_trans('Max size: 5MB. Formats: JPG, JPEG, PNG, WEBP, GIF', 'admin') }}</small>
                                    <div id="edit_background_image_error" class="invalid-feedback d-none"></div>
                                    <div id="current_image_preview" class="mt-2"></div>
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
                        <button type="submit"
                            class="btn btn-primary">{{ custom_trans('Update Slider', 'admin') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Slider Modal -->
    <div class="modal fade" id="viewSliderModal" tabindex="-1" aria-labelledby="viewSliderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewSliderModalLabel">{{ custom_trans('Slider Details', 'admin') }}</h5>
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
                            <label class="form-label fw-bold">{{ custom_trans('Welcome Text', 'admin') }}</label>
                            <p id="view_welcome_text" class="form-control-plaintext"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Subtitle', 'admin') }}</label>
                            <p id="view_subtitle" class="form-control-plaintext"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Button Text', 'admin') }}</label>
                            <p id="view_button_text" class="form-control-plaintext"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Search Placeholder', 'admin') }}</label>
                            <p id="view_search_placeholder" class="form-control-plaintext"></p>
                        </div>
                    </div>

                    <!-- Arabic Content -->
                    <div id="view-content-ar" class="language-content">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                            <p id="view_title_ar" class="form-control-plaintext" dir="rtl"></p>
                        </div>
                        <div class="mb-3">
                            <label
                                class="form-label fw-bold">{{ custom_trans('Welcome Text (Arabic)', 'admin') }}</label>
                            <p id="view_welcome_text_ar" class="form-control-plaintext" dir="rtl"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Subtitle (Arabic)', 'admin') }}</label>
                            <p id="view_subtitle_ar" class="form-control-plaintext" dir="rtl"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Button Text (Arabic)', 'admin') }}</label>
                            <p id="view_button_text_ar" class="form-control-plaintext" dir="rtl"></p>
                        </div>
                        <div class="mb-3">
                            <label
                                class="form-label fw-bold">{{ custom_trans('Search Placeholder (Arabic)', 'admin') }}</label>
                            <p id="view_search_placeholder_ar" class="form-control-plaintext" dir="rtl"></p>
                        </div>
                    </div>

                    <!-- Common Fields (not language-specific) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ custom_trans('Button URL', 'admin') }}</label>
                        <p id="view_button_url" class="form-control-plaintext"></p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Order', 'admin') }}</label>
                                <p id="view_order" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                <p id="view_status" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ custom_trans('Background Image', 'admin') }}</label>
                        <div id="view_background_image" class="text-center">
                            <img src="" alt="Slider Image" class="img-fluid rounded img-modal-preview">
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
                    <p id="deleteConfirmMessage">
                        {{ custom_trans('Are you sure you want to delete this item? This action cannot be undone.', 'admin') }}
                    </p>
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
            $('#addSliderModal .language-tab').on('click', function() {
                const lang = $(this).data('lang');
                const modal = $(this).closest('.modal');

                // Remove active class from all tabs and content in this modal
                modal.find('.language-tab').removeClass('active');
                modal.find('.language-content').removeClass('active');

                // Add active class to clicked tab and corresponding content
                $(this).addClass('active');
                if (lang === 'en') {
                    modal.find('#add-content-en').addClass('active');
                } else if (lang === 'ar') {
                    modal.find('#add-content-ar').addClass('active');
                }
            });

            // Language tab switching for Edit Modal
            $('#editSliderModal .language-tab').on('click', function() {
                const lang = $(this).data('lang');
                const modal = $(this).closest('.modal');

                // Remove active class from all tabs and content in this modal
                modal.find('.language-tab').removeClass('active');
                modal.find('.language-content').removeClass('active');

                // Add active class to clicked tab and corresponding content
                $(this).addClass('active');
                if (lang === 'edit-en') {
                    modal.find('#edit-content-en').addClass('active');
                } else if (lang === 'edit-ar') {
                    modal.find('#edit-content-ar').addClass('active');
                }
            });

            // Language tab switching for View Modal
            $('#viewSliderModal .language-tab').on('click', function() {
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
                $('.slider-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActionButton();
            });

            // Individual Checkbox
            $('.slider-checkbox').on('change', function() {
                updateBulkActionButton();
                updateSelectAllCheckbox();
            });

            function updateBulkActionButton() {
                const checkedCount = $('.slider-checkbox:checked').length;
                $('#apply_bulk_action').prop('disabled', checkedCount === 0);
            }

            function updateSelectAllCheckbox() {
                const totalCheckboxes = $('.slider-checkbox').length;
                const checkedCheckboxes = $('.slider-checkbox:checked').length;
                $('#select_all').prop('checked', totalCheckboxes === checkedCheckboxes);
            }

            // Bulk Actions
            $('#apply_bulk_action').on('click', function() {
                const action = $('#bulk_action').val();
                const selectedIds = $('.slider-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (!action) {
                    toastr.warning('{{ custom_trans('Please select an action', 'admin') }}');
                    return;
                }

                if (selectedIds.length === 0) {
                    toastr.warning('{{ custom_trans('Please select at least one slider', 'admin') }}');
                    return;
                }

                let confirmMessage = '';
                switch (action) {
                    case 'activate':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to activate the selected sliders?', 'admin') }}';
                        break;
                    case 'deactivate':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to deactivate the selected sliders?', 'admin') }}';
                        break;
                    case 'delete':
                        confirmMessage =
                            '{{ custom_trans('Are you sure you want to delete the selected sliders? This action cannot be undone.', 'admin') }}';
                        break;
                }

                // Show delete confirmation modal for delete action
                if (action === 'delete') {
                    deleteSliderId = null; // Clear single delete
                    $('#deleteConfirmMessage').text(confirmMessage);
                    $('#deleteConfirmModal').modal('show');
                    // Use one-time handler for bulk delete
                    $('#confirmDeleteBtn').off('click.bulkDelete').on('click.bulkDelete', function() {
                        $.ajax({
                            url: '{{ route('admin.settings.sliders.bulk-action') }}',
                            type: 'POST',
                            data: {
                                action: action,
                                slider_ids: selectedIds,
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
                            url: '{{ route('admin.settings.sliders.bulk-action') }}',
                            type: 'POST',
                            data: {
                                action: action,
                                slider_ids: selectedIds,
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

            // Apply filters function
            function applyFilters() {
                const status = $('#status_filter').val();
                const orderBy = $('#order_filter').val();
                const params = {};
                if (status) params.status = status;
                if (orderBy && orderBy !== 'order') params.order_by = orderBy;
                window.location.href = '{{ route('admin.settings.sliders.index') }}' + (Object.keys(params)
                    .length ? '?' + $.param(params) : '');
            }

            // Automatic filter on change
            $('#status_filter, #order_filter').on('change', function() {
                applyFilters();
            });

            $('#clear_filters').on('click', function() {
                $('#status_filter').val('');
                $('#order_filter').val('order');
                applyFilters();
            });

            $('#addSliderModal').on('hidden.bs.modal', function() {
                $('#addSliderForm')[0].reset();
                $('#background_image_preview').empty();
                $('#background_image_error').addClass('d-none').text('');
                $('#background_image').removeClass('is-invalid');
            });

            // Add form: image preview and clear errors
            $('#background_image').on('change', function() {
                const err = $('#background_image_error');
                err.addClass('d-none').text('');
                $('#background_image').removeClass('is-invalid');
                const preview = $('#background_image_preview');
                preview.empty();
                const file = this.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        err.text('{{ custom_trans('The image may not be greater than 5MB.', 'admin') }}')
                            .removeClass('d-none');
                        $('#background_image').addClass('is-invalid');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.html('<img src="' + e.target.result +
                            '" alt="Preview" class="img-thumbnail" style="max-height:120px">');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Edit form: image preview and clear errors
            $('#edit_background_image').on('change', function() {
                const err = $('#edit_background_image_error');
                err.addClass('d-none').text('');
                $('#edit_background_image').removeClass('is-invalid');
                const file = this.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        err.text('{{ custom_trans('The image may not be greater than 5MB.', 'admin') }}')
                            .removeClass('d-none');
                        $('#edit_background_image').addClass('is-invalid');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#current_image_preview').html('<img src="' + e.target.result +
                            '" alt="Preview" class="img-thumbnail" style="max-height:120px">');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Add Slider Form Submission
            $('#addSliderForm').on('submit', function(e) {
                e.preventDefault();
                $('#background_image_error').addClass('d-none').text('');
                $('#background_image').removeClass('is-invalid');

                // Validate file size (5MB) before submit
                const addFileInput = document.getElementById('background_image');
                if (addFileInput && addFileInput.files && addFileInput.files[0]) {
                    if (addFileInput.files[0].size > 5 * 1024 * 1024) {
                        $('#background_image_error').text('{{ custom_trans('The image may not be greater than 5MB.', 'admin') }}')
                            .removeClass('d-none');
                        $('#background_image').addClass('is-invalid');
                        return;
                    }
                }

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('admin.settings.slider.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#addSliderModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.background_image) {
                                $('#background_image_error').text(errors.background_image[0])
                                    .removeClass('d-none');
                                $('#background_image').addClass('is-invalid');
                            }
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                '{{ custom_trans('An error occurred while creating the slider', 'admin') }}'
                                );
                        }
                    }
                });
            });

            // Edit Slider
            $('.edit-slider').on('click', function() {
                const slider = $(this).data('slider');

                $('#edit_slider_id').val(slider.id);
                $('#edit_title').val(slider.title);
                $('#edit_title_ar').val(slider.title_ar);
                $('#edit_welcome_text').val(slider.welcome_text);
                $('#edit_welcome_text_ar').val(slider.welcome_text_ar);
                $('#edit_subtitle').val(slider.subtitle);
                $('#edit_subtitle_ar').val(slider.subtitle_ar);
                $('#edit_text_position').val(slider.text_position || 'center-left');
                $('#edit_button_text').val(slider.button_text);
                $('#edit_button_text_ar').val(slider.button_text_ar);
                $('#edit_button_url').val(slider.button_url);
                $('#edit_search_placeholder').val(slider.search_placeholder);
                $('#edit_search_placeholder_ar').val(slider.search_placeholder_ar);
                $('#edit_order').val(slider.order);
                $('#edit_is_active').prop('checked', slider.is_active);

                // Reset file input and show current image preview
                $('#edit_background_image').val('');
                $('#edit_background_image_error').addClass('d-none').text('');
                $('#edit_background_image').removeClass('is-invalid');
                $('#current_image_preview').html(`
                    <img src="${slider.background_image_url}" alt="Current Image" class="img-thumbnail max-w-200">
                `);

                $('#editSliderModal').modal('show');
            });

            // View Slider
            $('.view-slider').on('click', function() {
                const slider = $(this).data('slider');

                $('#view_title').text(slider.title);
                $('#view_title_ar').text(slider.title_ar || '-');
                $('#view_welcome_text').text(slider.welcome_text);
                $('#view_welcome_text_ar').text(slider.welcome_text_ar || '-');
                $('#view_subtitle').text(slider.subtitle);
                $('#view_subtitle_ar').text(slider.subtitle_ar || '-');
                $('#view_button_text').text(slider.button_text || '-');
                $('#view_button_text_ar').text(slider.button_text_ar || '-');
                $('#view_button_url').text(slider.button_url || '-');
                $('#view_search_placeholder').text(slider.search_placeholder || '-');
                $('#view_search_placeholder_ar').text(slider.search_placeholder_ar || '-');
                $('#view_order').text(slider.order);
                $('#view_status').text(slider.is_active ? '{{ custom_trans('Active', 'admin') }}' :
                    '{{ custom_trans('Inactive', 'admin') }}');
                $('#view_background_image img').attr('src', slider.background_image_url);

                $('#viewSliderModal').modal('show');
            });

            // Edit Slider Form Submission
            $('#editSliderForm').on('submit', function(e) {
                e.preventDefault();
                $('#edit_background_image_error').addClass('d-none').text('');
                $('#edit_background_image').removeClass('is-invalid');

                // Validate file size (5MB) before submit - same as create
                const fileInput = document.getElementById('edit_background_image');
                if (fileInput && fileInput.files && fileInput.files[0]) {
                    if (fileInput.files[0].size > 5 * 1024 * 1024) {
                        $('#edit_background_image_error').text('{{ custom_trans('The image may not be greater than 5MB.', 'admin') }}')
                            .removeClass('d-none');
                        $('#edit_background_image').addClass('is-invalid');
                        return;
                    }
                }

                const sliderId = $('#edit_slider_id').val();
                const formData = new FormData(this);

                $.ajax({
                    url: `/admin/settings/slider/${sliderId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editSliderModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors.background_image) {
                                $('#edit_background_image_error').text(errors.background_image[
                                    0]).removeClass('d-none');
                                $('#edit_background_image').addClass('is-invalid');
                            }
                            Object.keys(errors).forEach(function(key) {
                                toastr.error(errors[key][0]);
                            });
                        } else {
                            toastr.error(
                                '{{ custom_trans('An error occurred while updating the slider', 'admin') }}'
                                );
                        }
                    }
                });
            });

            // Delete Slider
            let deleteSliderId = null;
            $('.delete-slider').on('click', function() {
                deleteSliderId = $(this).data('slider-id');
                $('#deleteConfirmMessage').text(
                    '{{ custom_trans('Are you sure you want to delete this slider? This action cannot be undone.', 'admin') }}'
                    );
                $('#deleteConfirmModal').modal('show');
            });

            // Single delete handler
            $(document).on('click', '#confirmDeleteBtn', function() {
                if (deleteSliderId) {
                    const sliderId = deleteSliderId;
                    deleteSliderId = null; // Reset
                    $.ajax({
                        url: `/admin/settings/slider/${sliderId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                                '{{ custom_trans('An error occurred while deleting the slider', 'admin') }}'
                                );
                        }
                    });
                }
            });

            // Toggle Slider Status
            $('.toggle-status').on('change', function() {
                const sliderId = $(this).data('slider-id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/settings/slider/${sliderId}/toggle-status`,
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
                            '{{ custom_trans('An error occurred while updating the slider status', 'admin') }}'
                        );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Sortable table for reordering
            const slidersTbody = document.getElementById('sliders-tbody');
            if (slidersTbody) {
                new Sortable(slidersTbody, {
                    handle: 'td:nth-child(2)',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        const sliders = [];
                        const rows = slidersTbody.querySelectorAll('tr[data-slider-id]');

                        rows.forEach((row, index) => {
                            const sliderId = row.getAttribute('data-slider-id');
                            if (sliderId) {
                                sliders.push({
                                    id: sliderId,
                                    order: index
                                });
                            }
                        });

                        $.ajax({
                            url: '{{ route('admin.settings.slider.order') }}',
                            type: 'POST',
                            data: {
                                sliders: sliders,
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
