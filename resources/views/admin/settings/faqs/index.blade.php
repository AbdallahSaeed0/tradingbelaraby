@extends('admin.layout')

@section('title', custom_trans('FAQ Management', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('FAQ Management', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('FAQs', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFAQModal">
                            <i class="fas fa-plus me-2"></i>{{ custom_trans('Add New FAQ', 'admin') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQs Table -->
        <div class="card faqs-card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ custom_trans('Frequently Asked Questions', 'admin') }}</h5>
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
                    <table class="table table-hover" id="faqs-table">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                    </div>
                                </th>
                                <th width="80">{{ custom_trans('Order', 'admin') }}</th>
                                <th>{{ custom_trans('Title', 'admin') }}</th>
                                <th>{{ custom_trans('Title (AR)', 'admin') }}</th>
                                <th>{{ custom_trans('Content', 'admin') }}</th>
                                <th>{{ custom_trans('Content (AR)', 'admin') }}</th>
                                <th width="120">{{ custom_trans('Status', 'admin') }}</th>
                                <th width="120">{{ custom_trans('Expanded', 'admin') }}</th>
                                <th width="120">{{ custom_trans('Actions', 'admin') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faqs as $faq)
                                <tr data-id="{{ $faq->id }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input faq-checkbox" type="checkbox"
                                                value="{{ $faq->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $faq->order }}</span>
                                    </td>
                                    <td>{{ $faq->title }}</td>
                                    <td>{{ $faq->title_ar ?: '-' }}</td>
                                    <td>{{ Str::limit($faq->content, 50) }}</td>
                                    <td>{{ $faq->content_ar ? Str::limit($faq->content_ar, 50) : '-' }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                data-id="{{ $faq->id }}" {{ $faq->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input expanded-toggle" type="checkbox"
                                                data-id="{{ $faq->id }}" {{ $faq->is_expanded ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info view-faq-btn"
                                                data-faq="{{ json_encode($faq->toArray()) }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning edit-faq-btn"
                                                data-faq="{{ json_encode($faq->toArray()) }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-faq-btn"
                                                data-id="{{ $faq->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-question-circle fa-3x mb-3"></i>
                                            <p>{{ custom_trans('No FAQs found', 'admin') }}</p>
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

    <!-- Add FAQ Modal -->
    <div class="modal fade" id="addFAQModal" tabindex="-1" aria-labelledby="addFAQModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFAQModalLabel">{{ custom_trans('Add New FAQ', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addFAQForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="faq_title" class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="faq_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="faq_title_ar" class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                    <input type="text" class="form-control" id="faq_title_ar" name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="faq_content" class="form-label">{{ custom_trans('Content', 'admin') }} *</label>
                                    <textarea class="form-control" id="faq_content" name="content" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="faq_content_ar" class="form-label">{{ custom_trans('Content (Arabic)', 'admin') }}</label>
                                    <textarea class="form-control" id="faq_content_ar" name="content_ar" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="faq_order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                    <input type="number" class="form-control" id="faq_order" name="order"
                                        value="0" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="faq_is_active"
                                            name="is_active" checked>
                                        <label class="form-check-label" for="faq_is_active">
                                            {{ custom_trans('Active', 'admin') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="faq_is_expanded"
                                            name="is_expanded">
                                        <label class="form-check-label" for="faq_is_expanded">
                                            {{ custom_trans('Expanded by Default', 'admin') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                        <button type="submit" class="btn btn-primary">{{ custom_trans('Create FAQ', 'admin') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit FAQ Modal -->
    <div class="modal fade" id="editFAQModal" tabindex="-1" aria-labelledby="editFAQModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFAQModalLabel">{{ custom_trans('Edit FAQ', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editFAQForm">
                    @csrf
                    <input type="hidden" id="edit_faq_id" name="faq_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_faq_title" class="form-label">{{ custom_trans('Title', 'admin') }} *</label>
                                    <input type="text" class="form-control" id="edit_faq_title" name="title"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_faq_title_ar" class="form-label">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                    <input type="text" class="form-control" id="edit_faq_title_ar" name="title_ar">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_faq_content" class="form-label">{{ custom_trans('Content', 'admin') }} *</label>
                                    <textarea class="form-control" id="edit_faq_content" name="content" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_faq_content_ar"
                                        class="form-label">{{ custom_trans('Content (Arabic)', 'admin') }}</label>
                                    <textarea class="form-control" id="edit_faq_content_ar" name="content_ar" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_faq_order" class="form-label">{{ custom_trans('Order', 'admin') }}</label>
                                    <input type="number" class="form-control" id="edit_faq_order" name="order"
                                        min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_faq_is_active"
                                            name="is_active">
                                        <label class="form-check-label" for="edit_faq_is_active">
                                            {{ custom_trans('Active', 'admin') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_faq_is_expanded"
                                            name="is_expanded">
                                        <label class="form-check-label" for="edit_faq_is_expanded">
                                            {{ custom_trans('Expanded by Default', 'admin') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                        <button type="submit" class="btn btn-warning">{{ custom_trans('Update FAQ', 'admin') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View FAQ Modal -->
    <div class="modal fade" id="viewFAQModal" tabindex="-1" aria-labelledby="viewFAQModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFAQModalLabel">{{ custom_trans('FAQ Details', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Title', 'admin') }}</label>
                                <p id="view_faq_title" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Title (Arabic)', 'admin') }}</label>
                                <p id="view_faq_title_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Content', 'admin') }}</label>
                                <p id="view_faq_content" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Content (Arabic)', 'admin') }}</label>
                                <p id="view_faq_content_ar" class="form-control-plaintext"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Order', 'admin') }}</label>
                                <p id="view_faq_order" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                <p id="view_faq_status" class="form-control-plaintext"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Expanded', 'admin') }}</label>
                                <p id="view_faq_expanded" class="form-control-plaintext"></p>
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
            // Select all functionality
            $('#select_all').on('change', function() {
                $('.faq-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActions();
            });

            $('.faq-checkbox').on('change', function() {
                updateBulkActions();
                updateSelectAll();
            });

            function updateBulkActions() {
                const checkedBoxes = $('.faq-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    $('.bulk-actions').show();
                } else {
                    $('.bulk-actions').hide();
                }
            }

            function updateSelectAll() {
                const totalCheckboxes = $('.faq-checkbox').length;
                const checkedCheckboxes = $('.faq-checkbox:checked').length;

                if (checkedCheckboxes === 0) {
                    $('#select_all').prop('indeterminate', false).prop('checked', false);
                } else if (checkedCheckboxes === totalCheckboxes) {
                    $('#select_all').prop('indeterminate', false).prop('checked', true);
                } else {
                    $('#select_all').prop('indeterminate', true);
                }
            }

            // FAQ Status Toggle
            $('.status-toggle').on('change', function() {
                const faqId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/settings/faq/${faqId}/toggle-status`,
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
                            '{{ custom_trans('An error occurred while updating the FAQ status', 'admin') }}');
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // FAQ Expanded Toggle
            $('.expanded-toggle').on('change', function() {
                const faqId = $(this).data('id');
                const isChecked = $(this).is(':checked');

                $.ajax({
                    url: `/admin/settings/faq/${faqId}/toggle-expanded`,
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
                            '{{ custom_trans('An error occurred while updating the FAQ expanded state', 'admin') }}'
                        );
                        // Revert the checkbox
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Add FAQ Form
            $('#addFAQForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('admin.settings.faq.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#addFAQModal').modal('hide');
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
                        toastr.error('{{ custom_trans('An error occurred while creating the FAQ', 'admin') }}');
                    }
                });
            });

            // Edit FAQ Form
            $('#editFAQForm').on('submit', function(e) {
                e.preventDefault();

                const faqId = $('#edit_faq_id').val();

                $.ajax({
                    url: `/admin/settings/faq/${faqId}`,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editFAQModal').modal('hide');
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
                        toastr.error('{{ custom_trans('An error occurred while updating the FAQ', 'admin') }}');
                    }
                });
            });

            // Edit FAQ Button
            $('.edit-faq-btn').on('click', function() {
                const faq = $(this).data('faq');

                $('#edit_faq_id').val(faq.id);
                $('#edit_faq_title').val(faq.title);
                $('#edit_faq_title_ar').val(faq.title_ar);
                $('#edit_faq_content').val(faq.content);
                $('#edit_faq_content_ar').val(faq.content_ar);
                $('#edit_faq_order').val(faq.order);
                $('#edit_faq_is_active').prop('checked', faq.is_active);
                $('#edit_faq_is_expanded').prop('checked', faq.is_expanded);

                $('#editFAQModal').modal('show');
            });

            // View FAQ Button
            $('.view-faq-btn').on('click', function() {
                const faq = $(this).data('faq');

                $('#view_faq_title').text(faq.title);
                $('#view_faq_title_ar').text(faq.title_ar || '-');
                $('#view_faq_content').text(faq.content);
                $('#view_faq_content_ar').text(faq.content_ar || '-');
                $('#view_faq_order').text(faq.order);
                $('#view_faq_status').text(faq.is_active ? '{{ custom_trans('Active', 'admin') }}' :
                    '{{ custom_trans('Inactive', 'admin') }}');
                $('#view_faq_expanded').text(faq.is_expanded ? '{{ custom_trans('Yes', 'admin') }}' :
                    '{{ custom_trans('No', 'admin') }}');

                $('#viewFAQModal').modal('show');
            });

            // Delete FAQ Button
            $('.delete-faq-btn').on('click', function() {
                const faqId = $(this).data('id');

                if (confirm('{{ custom_trans('Are you sure you want to delete this FAQ?', 'admin') }}')) {
                    $.ajax({
                        url: `/admin/settings/faq/${faqId}`,
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
                                '{{ custom_trans('An error occurred while deleting the FAQ', 'admin') }}');
                        }
                    });
                }
            });

            // Make table rows sortable
            const tbody = document.querySelector('#faqs-table tbody');
            new Sortable(tbody, {
                animation: 150,
                onEnd: function(evt) {
                    const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
                    const orders = rows.map((row, index) => ({
                        id: row.dataset.id,
                        order: index + 1
                    }));

                    $.ajax({
                        url: '{{ route('admin.settings.faqs.order') }}',
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

