@extends('admin.layout')

@section('title', custom_trans('Contact Form Submissions', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Contact Form Submissions', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-management.index') }}">{{ custom_trans('Contact Management', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Submissions', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-management.export') }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>{{ custom_trans('Export CSV', 'admin') }}
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
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $repliedSubmissions }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-reply fa-2x text-gray-300"></i>
                            </div>
                        </div>
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
                            <label for="search" class="form-label">{{ custom_trans('Search', 'admin') }}</label>
                            <input type="text" class="form-control" id="search"
                                placeholder="{{ custom_trans('Search by name, email, or subject...', 'admin') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ custom_trans('Status', 'admin') }}</label>
                            <select class="form-select" id="status">
                                <option value="">{{ custom_trans('All', 'admin') }}</option>
                                <option value="new">{{ custom_trans('New', 'admin') }}</option>
                                <option value="read">{{ custom_trans('Read', 'admin') }}</option>
                                <option value="replied">{{ custom_trans('Replied', 'admin') }}</option>
                                <option value="closed">{{ custom_trans('Closed', 'admin') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date_range" class="form-label">{{ custom_trans('Date Range', 'admin') }}</label>
                            <select class="form-select" id="date_range">
                                <option value="">{{ custom_trans('All Time', 'admin') }}</option>
                                <option value="today">{{ custom_trans('Today', 'admin') }}</option>
                                <option value="week">{{ custom_trans('This Week', 'admin') }}</option>
                                <option value="month">{{ custom_trans('This Month', 'admin') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-secondary w-100" id="clear_filters">
                                <i class="fas fa-times me-1"></i>{{ custom_trans('Clear', 'admin') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="card content-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-list text-primary me-2"></i>{{ custom_trans('Contact Form Submissions', 'admin') }}
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success bulk-action-btn"
                                data-action="mark_read" disabled>
                                <i class="fas fa-eye me-1"></i>{{ custom_trans('Mark Read', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-info bulk-action-btn"
                                data-action="mark_replied" disabled>
                                <i class="fas fa-reply me-1"></i>{{ custom_trans('Mark Replied', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger bulk-action-btn" data-action="delete"
                                disabled>
                                <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete', 'admin') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>{{ custom_trans('Name', 'admin') }}</th>
                                <th>{{ custom_trans('Email', 'admin') }}</th>
                                <th>{{ custom_trans('Subject', 'admin') }}</th>
                                <th>{{ custom_trans('Status', 'admin') }}</th>
                                <th>{{ custom_trans('Date', 'admin') }}</th>
                                <th>{{ custom_trans('Actions', 'admin') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contactForms as $form)
                                <tr class="submission-row"
                                    data-search="{{ strtolower($form->name . ' ' . $form->email . ' ' . ($form->subject ?? '')) }}"
                                    data-status="{{ $form->status }}"
                                    data-date="{{ $form->created_at->format('Y-m-d', 'admin') }}">
                                    <td>
                                        <input type="checkbox" class="submission-checkbox" value="{{ $form->id }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div
                                                    class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                                    <span
                                                        class="text-white fw-bold">{{ strtoupper(substr($form->name, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $form->name }}</h6>
                                                <small class="text-muted">{{ $form->phone ?? 'No phone' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $form->email }}" class="text-decoration-none">
                                            {{ $form->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block text-truncate-200"
                                            title="{{ $form->subject }}">
                                            {{ $form->subject ?? 'No subject' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $form->status === 'new' ? 'warning' : ($form->status === 'read' ? 'info' : ($form->status === 'replied' ? 'success' : 'secondary')) }}">
                                            {{ ucfirst($form->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $form->created_at->format('M d, Y', 'admin') }}</div>
                                            <small class="text-muted">{{ $form->created_at->format('H:i', 'admin') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.settings.contact-management.show', $form->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="{{ custom_trans('View Details', 'admin') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-success toggle-status-btn"
                                                data-id="{{ $form->id }}" data-status="{{ $form->status }}"
                                                title="{{ custom_trans('Toggle Status', 'admin') }}">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $form->id }}" title="{{ custom_trans('Delete', 'admin') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <h5>{{ custom_trans('No submissions found', 'admin') }}</h5>
                                            <p>{{ custom_trans('Contact form submissions will appear here.', 'admin') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($contactForms->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $contactForms->links() }}
                    </div>
                @endif
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
    <script>
        $(document).ready(function() {
            // Select all functionality
            $('#selectAll').on('change', function() {
                $('.submission-checkbox').prop('checked', this.checked);
                updateBulkActionButtons();
            });

            // Individual checkbox change
            $('.submission-checkbox').on('change', function() {
                updateBulkActionButtons();
                updateSelectAll();
            });

            // Update bulk action buttons
            function updateBulkActionButtons() {
                const checkedCount = $('.submission-checkbox:checked').length;
                $('.bulk-action-btn').prop('disabled', checkedCount === 0);
            }

            // Update select all checkbox
            function updateSelectAll() {
                const totalCheckboxes = $('.submission-checkbox').length;
                const checkedCheckboxes = $('.submission-checkbox:checked').length;
                $('#selectAll').prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
            }

            // Filter functionality
            $('#search, #status, #date_range').on('change keyup', function() {
                filterSubmissions();
            });

            function filterSubmissions() {
                const searchTerm = $('#search').val().toLowerCase();
                const statusFilter = $('#status').val();
                const dateFilter = $('#date_range').val();

                $('.submission-row').each(function() {
                    const row = $(this);
                    const searchData = row.data('search');
                    const status = row.data('status');
                    const date = row.data('date');
                    let show = true;

                    // Search filter
                    if (searchTerm && !searchData.includes(searchTerm)) {
                        show = false;
                    }

                    // Status filter
                    if (statusFilter && status !== statusFilter) {
                        show = false;
                    }

                    // Date filter
                    if (dateFilter) {
                        const today = new Date().toISOString().split('T')[0];
                        const rowDate = new Date(date);

                        switch (dateFilter) {
                            case 'today':
                                if (date !== today) show = false;
                                break;
                            case 'week':
                                const weekAgo = new Date();
                                weekAgo.setDate(weekAgo.getDate() - 7);
                                if (rowDate < weekAgo) show = false;
                                break;
                            case 'month':
                                const monthAgo = new Date();
                                monthAgo.setMonth(monthAgo.getMonth() - 1);
                                if (rowDate < monthAgo) show = false;
                                break;
                        }
                    }

                    row.toggle(show);
                });
            }

            // Clear filters
            $('#clear_filters').on('click', function() {
                $('#search, #status, #date_range').val('');
                $('.submission-row').show();
            });

            // Bulk actions
            $('.bulk-action-btn').on('click', function() {
                const action = $(this).data('action');
                const selectedIds = $('.submission-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    toastr.warning('{{ custom_trans('Please select at least one submission.', 'admin') }}');
                    return;
                }

                if (action === 'delete') {
                    $('#deleteConfirmMessage').text('{{ custom_trans('Are you sure you want to delete the selected submissions? This action cannot be undone.', 'admin') }}');
                    $('#deleteConfirmModal').modal('show');
                    $('#confirmDeleteBtn').off('click.bulkDelete').on('click.bulkDelete', function() {
                        $('#deleteConfirmModal').modal('hide');
                        $('#confirmDeleteBtn').off('click.bulkDelete');
                        performBulkDelete();
                    });
                        return;
                }
                
                function performBulkDelete() {

                $.ajax({
                    url: '{{ route('admin.settings.contact-management.bulk-action') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action,
                        contact_forms: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message || '{{ custom_trans('An error occurred.', 'admin') }}');
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ custom_trans('An error occurred while processing the request.', 'admin') }}');
                    }
                });
            });

            // Individual delete
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');

                if (!confirm('{{ custom_trans('Are you sure you want to delete this submission?', 'admin') }}')) {
                    return;
                }

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms', 'admin') }}/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                                $('#deleteConfirmModal').modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                                $('#deleteConfirmModal').modal('hide');
                            toastr.error(response.message || '{{ custom_trans('An error occurred.', 'admin') }}');
                        }
                    },
                    error: function() {
                            $('#deleteConfirmModal').modal('hide');
                        toastr.error(
                            '{{ custom_trans('An error occurred while deleting the submission.', 'admin') }}');
                    }
                });
                }
            });

            // Toggle status
            $('.toggle-status-btn').on('click', function() {
                const id = $(this).data('id');
                const currentStatus = $(this).data('status');
                const newStatus = currentStatus === 'new' ? 'read' : (currentStatus === 'read' ? 'replied' :
                    'new');

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms', 'admin') }}/${id}/status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message || '{{ custom_trans('An error occurred.', 'admin') }}');
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ custom_trans('An error occurred while updating the status.', 'admin') }}');
                    }
                });
            });
        });
    </script>
@endpush
