@extends('admin.layout')

@section('title', custom_trans('Newsletter Subscriber Details', 'admin'))

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
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.settings.newsletters.index') }}">{{ custom_trans('Newsletter Subscriptions', 'admin') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ custom_trans('Subscriber Details', 'admin') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ custom_trans('Newsletter Subscriber Details', 'admin') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">
                            <i class="fas fa-user me-2"></i>{{ custom_trans('Subscriber Information', 'admin') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ custom_trans('Email Address', 'admin') }}</label>
                                            <p class="form-control-plaintext">{{ $newsletter->email }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                            <div>
                                                @if ($newsletter->status === 'active')
                                                    <span class="badge bg-success fs-6">{{ custom_trans('Active', 'admin') }}</span>
                                                @else
                                                    <span class="badge bg-warning fs-6">{{ custom_trans('Inactive', 'admin') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ custom_trans('Subscribed Date', 'admin') }}</label>
                                            <p class="form-control-plaintext">
                                                @if ($newsletter->subscribed_at)
                                                    {{ $newsletter->subscribed_at->format('F d, Y \a\t g:i A', 'admin') }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $newsletter->subscribed_at->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">{{ custom_trans('Not available', 'admin') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ custom_trans('Unsubscribed Date', 'admin') }}</label>
                                            <p class="form-control-plaintext">
                                                @if ($newsletter->unsubscribed_at)
                                                    {{ $newsletter->unsubscribed_at->format('F d, Y \a\t g:i A', 'admin') }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $newsletter->unsubscribed_at->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">{{ custom_trans('Not unsubscribed', 'admin') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ custom_trans('Subscription Duration', 'admin') }}</label>
                                            <p class="form-control-plaintext">
                                                @if ($newsletter->subscribed_at)
                                                    @if ($newsletter->unsubscribed_at)
                                                        {{ $newsletter->subscribed_at->diffForHumans($newsletter->unsubscribed_at, ['parts' => 2]) }}
                                                    @else
                                                        {{ $newsletter->subscribed_at->diffForHumans() }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ custom_trans('Not available', 'admin') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ custom_trans('Last Updated', 'admin') }}</label>
                                            <p class="form-control-plaintext">
                                                {{ $newsletter->updated_at->format('F d, Y \a\t g:i A', 'admin') }}
                                                <br>
                                                <small
                                                    class="text-muted">{{ $newsletter->updated_at->diffForHumans() }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <i class="fas fa-envelope fa-3x"></i>
                                            </div>
                                        </div>
                                        <h5 class="card-title">{{ custom_trans('Newsletter Subscriber', 'admin') }}</h5>
                                        <p class="text-muted">{{ custom_trans('Email:', 'admin') }} {{ $newsletter->email }}</p>

                                        <div class="d-grid gap-2">
                                            @if ($newsletter->status === 'active')
                                                <button type="button" class="btn btn-warning btn-sm status-toggle"
                                                    data-id="{{ $newsletter->id }}" data-status="1">
                                                    <i class="fas fa-pause me-1"></i>{{ custom_trans('Deactivate', 'admin') }}
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm status-toggle"
                                                    data-id="{{ $newsletter->id }}" data-status="0">
                                                    <i class="fas fa-check me-1"></i>{{ custom_trans('Activate', 'admin') }}
                                                </button>
                                            @endif

                                            <button type="button" class="btn btn-danger btn-sm delete-subscriber"
                                                data-id="{{ $newsletter->id }}">
                                                <i class="fas fa-trash me-1"></i>{{ custom_trans('Delete Subscriber', 'admin') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">
                            <i class="fas fa-history me-2"></i>{{ custom_trans('Activity Timeline', 'admin') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline-alt pb-0">
                            <div class="timeline-item">
                                <i class="fas fa-user-plus bg-info-lighten text-info timeline-icon"></i>
                                <div class="timeline-item-info">
                                    <a href="#"
                                        class="text-info fw-bold mb-1 d-block">{{ custom_trans('Account Created', 'admin') }}</a>
                                    <small>{{ $newsletter->created_at->format('F d, Y \a\t g:i A', 'admin') }}</small>
                                    <p class="font-14 mt-1 mb-0">{{ custom_trans('Newsletter subscription account was created.', 'admin') }}
                                    </p>
                                </div>
                            </div>

                            @if ($newsletter->subscribed_at)
                                <div class="timeline-item">
                                    <i class="fas fa-check-circle bg-success-lighten text-success timeline-icon"></i>
                                    <div class="timeline-item-info">
                                        <a href="#"
                                            class="text-success fw-bold mb-1 d-block">{{ custom_trans('Subscribed', 'admin') }}</a>
                                        <small>{{ $newsletter->subscribed_at->format('F d, Y \a\t g:i A', 'admin') }}</small>
                                        <p class="font-14 mt-1 mb-0">{{ custom_trans('Successfully subscribed to the newsletter.', 'admin') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if ($newsletter->unsubscribed_at)
                                <div class="timeline-item">
                                    <i class="fas fa-pause-circle bg-warning-lighten text-warning timeline-icon"></i>
                                    <div class="timeline-item-info">
                                        <a href="#"
                                            class="text-warning fw-bold mb-1 d-block">{{ custom_trans('Unsubscribed', 'admin') }}</a>
                                        <small>{{ $newsletter->unsubscribed_at->format('F d, Y \a\t g:i A', 'admin') }}</small>
                                        <p class="font-14 mt-1 mb-0">{{ custom_trans('Unsubscribed from the newsletter.', 'admin') }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="timeline-item">
                                <i class="fas fa-edit bg-primary-lighten text-primary timeline-icon"></i>
                                <div class="timeline-item-info">
                                    <a href="#"
                                        class="text-primary fw-bold mb-1 d-block">{{ custom_trans('Last Updated', 'admin') }}</a>
                                    <small>{{ $newsletter->updated_at->format('F d, Y \a\t g:i A', 'admin') }}</small>
                                    <p class="font-14 mt-1 mb-0">{{ custom_trans('Account information was last updated.', 'admin') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Toggle Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ custom_trans('Update Status', 'admin') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="statusModalMessage">{{ custom_trans('Are you sure you want to update the status of this subscriber?', 'admin') }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'admin') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusToggle">{{ custom_trans('Confirm', 'admin') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
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

            // Handle status toggle
            $('.status-toggle').on('click', function(e) {
                e.preventDefault();

                currentSubscriberId = $(this).data('id');
                const currentStatus = $(this).data('status');
                const newStatus = currentStatus ? 0 : 1;
                const action = newStatus ? 'activate' : 'deactivate';

                $('#statusModalMessage').text(
                    `{{ custom_trans('Are you sure you want to', 'admin') }} ${action} {{ custom_trans('this subscriber?', 'admin') }}`);

                $('#confirmStatusToggle').off('click').on('click', function() {
                    $.ajax({
                        url: `{{ url('admin/settings/newsletters', 'admin') }}/${currentSubscriberId}/status`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: newStatus ? 'active' : 'inactive'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#statusModal').modal('hide');
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                toastr.error(response.message ||
                                    '{{ custom_trans('An error occurred while updating status.', 'admin') }}'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage =
                                '{{ custom_trans('An error occurred while updating status.', 'admin') }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                        }
                    });
                });

                $('#statusModal').modal('show');
            });

            // Handle delete
            $('.delete-subscriber').on('click', function(e) {
                e.preventDefault();

                currentSubscriberId = $(this).data('id');

                $('#confirmDelete').off('click').on('click', function() {
                    $.ajax({
                        url: `{{ url('admin/settings/newsletters', 'admin') }}/${currentSubscriberId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#deleteModal').modal('hide');
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('admin.settings.newsletters.index') }}';
                                }, 1500);
                            } else {
                                toastr.error(response.message ||
                                    '{{ custom_trans('An error occurred while deleting subscriber.', 'admin') }}'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage =
                                '{{ custom_trans('An error occurred while deleting subscriber.', 'admin') }}';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                        }
                    });
                });

                $('#deleteModal').modal('show');
            });
        });
    </script>
@endpush
