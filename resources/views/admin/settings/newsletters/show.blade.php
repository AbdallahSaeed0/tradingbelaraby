@extends('admin.layout')

@section('title', __('Newsletter Subscriber Details'))

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
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.settings.newsletters.index') }}">{{ __('Newsletter Subscriptions') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Subscriber Details') }}</li>
                        </ol>
                    </div>
                    <h4 class="page-title">{{ __('Newsletter Subscriber Details') }}</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">
                            <i class="fas fa-user me-2"></i>{{ __('Subscriber Information') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Email Address') }}</label>
                                            <p class="form-control-plaintext">{{ $newsletter->email }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Status') }}</label>
                                            <div>
                                                @if ($newsletter->status === 'active')
                                                    <span class="badge bg-success fs-6">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge bg-warning fs-6">{{ __('Inactive') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Subscribed Date') }}</label>
                                            <p class="form-control-plaintext">
                                                @if ($newsletter->subscribed_at)
                                                    {{ $newsletter->subscribed_at->format('F d, Y \a\t g:i A') }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $newsletter->subscribed_at->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">{{ __('Not available') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Unsubscribed Date') }}</label>
                                            <p class="form-control-plaintext">
                                                @if ($newsletter->unsubscribed_at)
                                                    {{ $newsletter->unsubscribed_at->format('F d, Y \a\t g:i A') }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $newsletter->unsubscribed_at->diffForHumans() }}</small>
                                                @else
                                                    <span class="text-muted">{{ __('Not unsubscribed') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Subscription Duration') }}</label>
                                            <p class="form-control-plaintext">
                                                @if ($newsletter->subscribed_at)
                                                    @if ($newsletter->unsubscribed_at)
                                                        {{ $newsletter->subscribed_at->diffForHumans($newsletter->unsubscribed_at, ['parts' => 2]) }}
                                                    @else
                                                        {{ $newsletter->subscribed_at->diffForHumans() }}
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ __('Not available') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{ __('Last Updated') }}</label>
                                            <p class="form-control-plaintext">
                                                {{ $newsletter->updated_at->format('F d, Y \a\t g:i A') }}
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
                                        <h5 class="card-title">{{ __('Newsletter Subscriber') }}</h5>
                                        <p class="text-muted">{{ __('Email:') }} {{ $newsletter->email }}</p>

                                        <div class="d-grid gap-2">
                                            @if ($newsletter->status === 'active')
                                                <button type="button" class="btn btn-warning btn-sm status-toggle"
                                                    data-id="{{ $newsletter->id }}" data-status="1">
                                                    <i class="fas fa-pause me-1"></i>{{ __('Deactivate') }}
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success btn-sm status-toggle"
                                                    data-id="{{ $newsletter->id }}" data-status="0">
                                                    <i class="fas fa-check me-1"></i>{{ __('Activate') }}
                                                </button>
                                            @endif

                                            <button type="button" class="btn btn-danger btn-sm delete-subscriber"
                                                data-id="{{ $newsletter->id }}">
                                                <i class="fas fa-trash me-1"></i>{{ __('Delete Subscriber') }}
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
                            <i class="fas fa-history me-2"></i>{{ __('Activity Timeline') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline-alt pb-0">
                            <div class="timeline-item">
                                <i class="fas fa-user-plus bg-info-lighten text-info timeline-icon"></i>
                                <div class="timeline-item-info">
                                    <a href="#"
                                        class="text-info fw-bold mb-1 d-block">{{ __('Account Created') }}</a>
                                    <small>{{ $newsletter->created_at->format('F d, Y \a\t g:i A') }}</small>
                                    <p class="font-14 mt-1 mb-0">{{ __('Newsletter subscription account was created.') }}
                                    </p>
                                </div>
                            </div>

                            @if ($newsletter->subscribed_at)
                                <div class="timeline-item">
                                    <i class="fas fa-check-circle bg-success-lighten text-success timeline-icon"></i>
                                    <div class="timeline-item-info">
                                        <a href="#"
                                            class="text-success fw-bold mb-1 d-block">{{ __('Subscribed') }}</a>
                                        <small>{{ $newsletter->subscribed_at->format('F d, Y \a\t g:i A') }}</small>
                                        <p class="font-14 mt-1 mb-0">{{ __('Successfully subscribed to the newsletter.') }}
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if ($newsletter->unsubscribed_at)
                                <div class="timeline-item">
                                    <i class="fas fa-pause-circle bg-warning-lighten text-warning timeline-icon"></i>
                                    <div class="timeline-item-info">
                                        <a href="#"
                                            class="text-warning fw-bold mb-1 d-block">{{ __('Unsubscribed') }}</a>
                                        <small>{{ $newsletter->unsubscribed_at->format('F d, Y \a\t g:i A') }}</small>
                                        <p class="font-14 mt-1 mb-0">{{ __('Unsubscribed from the newsletter.') }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="timeline-item">
                                <i class="fas fa-edit bg-primary-lighten text-primary timeline-icon"></i>
                                <div class="timeline-item-info">
                                    <a href="#"
                                        class="text-primary fw-bold mb-1 d-block">{{ __('Last Updated') }}</a>
                                    <small>{{ $newsletter->updated_at->format('F d, Y \a\t g:i A') }}</small>
                                    <p class="font-14 mt-1 mb-0">{{ __('Account information was last updated.') }}</p>
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
                    <h5 class="modal-title">{{ __('Update Status') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="statusModalMessage">{{ __('Are you sure you want to update the status of this subscriber?') }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusToggle">{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Delete Subscriber') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete this subscriber? This action cannot be undone.') }}</p>
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
                    `{{ __('Are you sure you want to') }} ${action} {{ __('this subscriber?') }}`);

                $('#confirmStatusToggle').off('click').on('click', function() {
                    $.ajax({
                        url: `{{ url('admin/settings/newsletters') }}/${currentSubscriberId}/status`,
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
                                    '{{ __('An error occurred while updating status.') }}'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage =
                                '{{ __('An error occurred while updating status.') }}';
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
                        url: `{{ url('admin/settings/newsletters') }}/${currentSubscriberId}`,
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
                                    '{{ __('An error occurred while deleting subscriber.') }}'
                                );
                            }
                        },
                        error: function(xhr) {
                            let errorMessage =
                                '{{ __('An error occurred while deleting subscriber.') }}';
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
