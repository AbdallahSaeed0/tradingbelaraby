@extends('admin.layout')

@section('title', custom_trans('Newsletter Subscriptions Management', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Newsletter Subscriptions Management', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Newsletter Subscriptions', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <button type="button" class="btn btn-success" id="exportSubscribers">
                            <i class="fas fa-download me-2"></i>{{ custom_trans('Export CSV', 'admin') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card content-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-primary-lighten text-primary rounded">
                                        <i class="fas fa-users fa-2x"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-1">{{ $totalSubscribers }}</h3>
                                <p class="text-muted mb-0">{{ custom_trans('Total Subscribers', 'admin') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card content-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-success-lighten text-success rounded">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-1">{{ $activeSubscribers }}</h3>
                                <p class="text-muted mb-0">{{ custom_trans('Active Subscribers', 'admin') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card content-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-warning-lighten text-warning rounded">
                                        <i class="fas fa-pause-circle fa-2x"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-1">{{ $inactiveSubscribers }}</h3>
                                <p class="text-muted mb-0">{{ custom_trans('Inactive Subscribers', 'admin') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card content-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <span class="avatar-title bg-info-lighten text-info rounded">
                                        <i class="fas fa-calendar-alt fa-2x"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h3 class="mb-1">{{ $thisMonthSubscribers }}</h3>
                                <p class="text-muted mb-0">{{ custom_trans('This Month', 'admin') }}</p>
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
                                placeholder="{{ custom_trans('Search by email...', 'admin') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ custom_trans('Status', 'admin') }}</label>
                            <select class="form-select" id="status">
                                <option value="">{{ custom_trans('All', 'admin') }}</option>
                                <option value="active">{{ custom_trans('Active', 'admin') }}</option>
                                <option value="inactive">{{ custom_trans('Inactive', 'admin') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sort" class="form-label">{{ custom_trans('Sort By', 'admin') }}</label>
                            <select class="form-select" id="sort">
                                <option value="subscribed_at">{{ custom_trans('Subscribed Date', 'admin') }}</option>
                                <option value="email">{{ custom_trans('Email', 'admin') }}</option>
                                <option value="status">{{ custom_trans('Status', 'admin') }}</option>
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

        <!-- Subscribers Table -->
        <div class="card content-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope text-primary me-2"></i>{{ custom_trans('All Subscribers', 'admin') }}
                        </h5>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success bulk-action-btn" data-action="activate"
                                disabled>
                                <i class="fas fa-check me-1"></i>{{ custom_trans('Activate', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-warning bulk-action-btn"
                                data-action="deactivate" disabled>
                                <i class="fas fa-pause me-1"></i>{{ custom_trans('Deactivate', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger bulk-action-btn" data-action="delete"
                                disabled>
                                <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete', 'admin') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="subscribers-table">
                        <thead>
                            <tr>
                                <th width="30">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                    </div>
                                </th>
                                <th>{{ custom_trans('Email', 'admin') }}</th>
                                <th width="100">{{ custom_trans('Status', 'admin') }}</th>
                                <th width="150">{{ custom_trans('Subscribed Date', 'admin') }}</th>
                                <th width="150">{{ custom_trans('Last Updated', 'admin') }}</th>
                                <th width="120">{{ custom_trans('Actions', 'admin') }}</th>
                            </tr>
                        </thead>
                        <tbody id="subscribers-tbody">
                            @forelse($newsletters as $subscriber)
                                <tr data-subscriber-id="{{ $subscriber->id }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input subscriber-checkbox" type="checkbox"
                                                value="{{ $subscriber->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light text-primary rounded-circle">
                                                    {{ strtoupper(substr($subscriber->email, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $subscriber->email }}</h6>
                                                <small class="text-muted">{{ custom_trans('ID:', 'admin') }}
                                                    {{ $subscriber->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $subscriber->status === 'active' ? 'bg-success' : 'bg-warning' }} status-badge"
                                            style="cursor: pointer;"
                                            onclick="showStatusModal({{ $subscriber->id }}, '{{ $subscriber->status }}', [
                                                { value: 'active', label: '{{ custom_trans('Active', 'admin') }}' },
                                                { value: 'inactive', label: '{{ custom_trans('Inactive', 'admin') }}' }
                                            ], '{{ route('admin.settings.newsletters.update-status', $subscriber->id) }}')">
                                            {{ $subscriber->status === 'active' ? custom_trans('Active', 'admin') : custom_trans('Inactive', 'admin') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $subscriber->subscribed_at ? $subscriber->subscribed_at->format('M d, Y') : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $subscriber->updated_at->format('M d, Y H:i', 'admin') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info view-subscriber"
                                                data-subscriber="{{ json_encode($subscriber->toArray()) }}"
                                                title="{{ custom_trans('View', 'admin') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if ($subscriber->status === 'active')
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-warning toggle-status"
                                                    data-subscriber-id="{{ $subscriber->id }}"
                                                    title="{{ custom_trans('Deactivate', 'admin') }}">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success toggle-status"
                                                    data-subscriber-id="{{ $subscriber->id }}"
                                                    title="{{ custom_trans('Activate', 'admin') }}">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-subscriber"
                                                data-subscriber-id="{{ $subscriber->id }}" title="{{ custom_trans('Delete', 'admin') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <h5>{{ custom_trans('No subscribers found', 'admin') }}</h5>
                                            <p>{{ custom_trans('No newsletter subscribers match your current filters.', 'admin') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($newsletters->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">
                                {{ custom_trans('Showing', 'admin') }} {{ $newsletters->firstItem() }} {{ custom_trans('to', 'admin') }}
                                {{ $newsletters->lastItem() }} {{ custom_trans('of', 'admin') }} {{ $newsletters->total() }}
                                {{ custom_trans('subscribers', 'admin') }}
                            </p>
                        </div>
                        <div>
                            {{ $newsletters->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- View Subscriber Modal -->
    <div class="modal fade" id="viewSubscriberModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ custom_trans('Subscriber Details', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Email Address', 'admin') }}</label>
                                <p class="form-control-plaintext" id="view_subscriber_email"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                <p class="form-control-plaintext" id="view_subscriber_status"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Subscribed Date', 'admin') }}</label>
                                <p class="form-control-plaintext" id="view_subscriber_subscribed_at"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Unsubscribed Date', 'admin') }}</label>
                                <p class="form-control-plaintext" id="view_subscriber_unsubscribed_at"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Duration', 'admin') }}</label>
                                <p class="form-control-plaintext" id="view_subscriber_duration"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Last Updated', 'admin') }}</label>
                                <p class="form-control-plaintext" id="view_subscriber_updated_at"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ custom_trans('Activity Timeline', 'admin') }}</label>
                        <div class="timeline" id="view_subscriber_timeline">
                            <!-- Timeline will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Close', 'admin') }}</button>
                    <button type="button" class="btn btn-primary"
                        id="view_subscriber_toggle_status">{{ custom_trans('Toggle Status', 'admin') }}</button>
                    <button type="button" class="btn btn-danger"
                        id="view_subscriber_delete">{{ custom_trans('Delete', 'admin') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteSubscriberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ custom_trans('Delete Subscriber', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ custom_trans('Are you sure you want to delete this subscriber? This action cannot be undone.', 'admin') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">{{ custom_trans('Delete', 'admin') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentSubscriberId = null;

            // Select all checkbox
            $('#select_all').on('change', function() {
                $('.subscriber-checkbox').prop('checked', $(this).is(':checked'));
                updateBulkActions();
            });

            // Individual checkboxes
            $(document).on('change', '.subscriber-checkbox', function() {
                updateBulkActions();
            });

            function updateBulkActions() {
                const checkedCount = $('.subscriber-checkbox:checked').length;
                $('.bulk-action-btn').prop('disabled', checkedCount === 0);
            }

            // Bulk actions
            $('.bulk-action-btn').on('click', function() {
                const action = $(this).data('action');
                const selectedSubscribers = $('.subscriber-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedSubscribers.length === 0) {
                    toastr.warning('{{ custom_trans('Please select at least one subscriber.', 'admin') }}');
                    return;
                }

                if (action === 'delete' && !confirm(
                        '{{ custom_trans('Are you sure you want to delete the selected subscribers?', 'admin') }}')) {
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.settings.newsletters.bulk-action') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        action: action,
                        subscribers: selectedSubscribers
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    },
                    error: function() {
                        toastr.error('{{ custom_trans('An error occurred. Please try again.', 'admin') }}');
                    }
                });
            });

            // Toggle status
            $(document).on('click', '.toggle-status', function() {
                const subscriberId = $(this).data('subscriber-id');
                const $btn = $(this);

                $.ajax({
                    url: `{{ url('admin/settings/newsletters', 'admin') }}/${subscriberId}/status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    },
                    error: function() {
                        toastr.error('{{ custom_trans('An error occurred. Please try again.', 'admin') }}');
                    }
                });
            });

            // View subscriber
            $(document).on('click', '.view-subscriber', function() {
                const subscriber = $(this).data('subscriber');
                currentSubscriberId = subscriber.id;

                $('#view_subscriber_email').text(subscriber.email);
                $('#view_subscriber_status').html(subscriber.status === 'active' ?
                    '<span class="badge bg-success">{{ custom_trans('Active', 'admin') }}</span>' :
                    '<span class="badge bg-warning">{{ custom_trans('Inactive', 'admin') }}</span>');
                $('#view_subscriber_subscribed_at').text(subscriber.subscribed_at ?
                    new Date(subscriber.subscribed_at).toLocaleDateString() : '-');
                $('#view_subscriber_unsubscribed_at').text(subscriber.unsubscribed_at ?
                    new Date(subscriber.unsubscribed_at).toLocaleDateString() : '-');
                $('#view_subscriber_updated_at').text(new Date(subscriber.updated_at).toLocaleString());

                // Calculate duration
                if (subscriber.subscribed_at) {
                    const subscribed = new Date(subscriber.subscribed_at);
                    const now = new Date();
                    const diffTime = Math.abs(now - subscribed);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    $('#view_subscriber_duration').text(`${diffDays} {{ custom_trans('days', 'admin') }}`);
                } else {
                    $('#view_subscriber_duration').text('-');
                }

                // Build timeline
                let timeline = '';
                if (subscriber.subscribed_at) {
                    timeline += `<div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ custom_trans('Subscribed', 'admin') }}</h6>
                            <small class="text-muted">${new Date(subscriber.subscribed_at).toLocaleString()}</small>
                        </div>
                    </div>`;
                }
                if (subscriber.unsubscribed_at) {
                    timeline += `<div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ custom_trans('Unsubscribed', 'admin') }}</h6>
                            <small class="text-muted">${new Date(subscriber.unsubscribed_at).toLocaleString()}</small>
                        </div>
                    </div>`;
                }
                $('#view_subscriber_timeline').html(timeline);

                $('#viewSubscriberModal').modal('show');
            });

            // Delete subscriber
            $(document).on('click', '.delete-subscriber', function() {
                currentSubscriberId = $(this).data('subscriber-id');
                $('#deleteSubscriberModal').modal('show');
            });

            $('#confirmDelete').on('click', function() {
                if (!currentSubscriberId) return;

                $.ajax({
                    url: `{{ url('admin/settings/newsletters', 'admin') }}/${currentSubscriberId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#deleteSubscriberModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function() {
                        toastr.error('{{ custom_trans('An error occurred. Please try again.', 'admin') }}');
                    }
                });
            });

            // Export subscribers
            $('#exportSubscribers').on('click', function() {
                const search = $('#search').val();
                const status = $('#status').val();
                const sort = $('#sort').val();

                const params = new URLSearchParams({
                    _token: '{{ csrf_token() }}',
                    search: search,
                    status: status,
                    sort: sort
                });

                window.location.href =
                    `{{ route('admin.settings.newsletters.export') }}?${params.toString()}`;
            });

            // Filters
            $('#search, #status, #sort').on('change keyup', function() {
                applyFilters();
            });

            $('#clear_filters').on('click', function() {
                $('#search').val('');
                $('#status').val('');
                $('#sort').val('subscribed_at');
                applyFilters();
            });

            function applyFilters() {
                const search = $('#search').val();
                const status = $('#status').val();
                const sort = $('#sort').val();

                $('#subscribers-tbody tr').each(function() {
                    const $row = $(this);
                    const email = $row.find('td:eq(1)').text().toLowerCase();
                    const statusText = $row.find('td:eq(2) .badge').text().toLowerCase();

                    let show = true;

                    // Search filter
                    if (search && !email.includes(search.toLowerCase())) {
                        show = false;
                    }

                    // Status filter
                    if (status === 'active' && !statusText.includes('active')) {
                        show = false;
                    } else if (status === 'inactive' && !statusText.includes('inactive')) {
                        show = false;
                    }

                    $row.toggle(show);
                });
            }
        });
    </script>
    @include('admin.partials.status-modal')
@endpush

