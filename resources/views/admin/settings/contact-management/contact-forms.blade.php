@extends('admin.layout')

@section('title', __('Contact Form Submissions'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('Contact Form Submissions') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-management.index') }}">{{ __('Contact Management') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Submissions') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-management.export') }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>{{ __('Export CSV') }}
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
                            <label for="search" class="form-label">{{ __('Search') }}</label>
                            <input type="text" class="form-control" id="search"
                                placeholder="{{ __('Search by name, email, or subject...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Status') }}</label>
                            <select class="form-select" id="status">
                                <option value="">{{ __('All') }}</option>
                                <option value="new">{{ __('New') }}</option>
                                <option value="read">{{ __('Read') }}</option>
                                <option value="replied">{{ __('Replied') }}</option>
                                <option value="closed">{{ __('Closed') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date_range" class="form-label">{{ __('Date Range') }}</label>
                            <select class="form-select" id="date_range">
                                <option value="">{{ __('All Time') }}</option>
                                <option value="today">{{ __('Today') }}</option>
                                <option value="week">{{ __('This Week') }}</option>
                                <option value="month">{{ __('This Month') }}</option>
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

        <!-- Submissions Table -->
        <div class="card content-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-list text-primary me-2"></i>{{ __('Contact Form Submissions') }}
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success bulk-action-btn"
                                data-action="mark_read" disabled>
                                <i class="fas fa-eye me-1"></i>{{ __('Mark Read') }}
                            </button>
                            <button type="button" class="btn btn-outline-info bulk-action-btn"
                                data-action="mark_replied" disabled>
                                <i class="fas fa-reply me-1"></i>{{ __('Mark Replied') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger bulk-action-btn" data-action="delete"
                                disabled>
                                <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contactForms as $form)
                                <tr class="submission-row"
                                    data-search="{{ strtolower($form->name . ' ' . $form->email . ' ' . ($form->subject ?? '')) }}"
                                    data-status="{{ $form->status }}"
                                    data-date="{{ $form->created_at->format('Y-m-d') }}">
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
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;"
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
                                            <div class="fw-semibold">{{ $form->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $form->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.settings.contact-management.show', $form->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="{{ __('View Details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-success toggle-status-btn"
                                                data-id="{{ $form->id }}" data-status="{{ $form->status }}"
                                                title="{{ __('Toggle Status') }}">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="{{ $form->id }}" title="{{ __('Delete') }}">
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
                                            <h5>{{ __('No submissions found') }}</h5>
                                            <p>{{ __('Contact form submissions will appear here.') }}</p>
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
                    toastr.warning('{{ __('Please select at least one submission.') }}');
                    return;
                }

                if (action === 'delete') {
                    if (!confirm(
                            '{{ __('Are you sure you want to delete the selected submissions?') }}')) {
                        return;
                    }
                }

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
                            toastr.error(response.message || '{{ __('An error occurred.') }}');
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while processing the request.') }}');
                    }
                });
            });

            // Individual delete
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');

                if (!confirm('{{ __('Are you sure you want to delete this submission?') }}')) {
                    return;
                }

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms') }}/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message || '{{ __('An error occurred.') }}');
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while deleting the submission.') }}');
                    }
                });
            });

            // Toggle status
            $('.toggle-status-btn').on('click', function() {
                const id = $(this).data('id');
                const currentStatus = $(this).data('status');
                const newStatus = currentStatus === 'new' ? 'read' : (currentStatus === 'read' ? 'replied' :
                    'new');

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms') }}/${id}/status`,
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
                            toastr.error(response.message || '{{ __('An error occurred.') }}');
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ __('An error occurred while updating the status.') }}');
                    }
                });
            });
        });
    </script>
@endpush
