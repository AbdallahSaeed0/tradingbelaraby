@extends('admin.layout')

@section('title', __('Contact Form Details'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ __('Contact Form Details') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-management.index') }}">{{ __('Contact Management') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-management.contact-forms') }}">{{ __('Submissions') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Details') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-management.contact-forms') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contact Form Details -->
            <div class="col-lg-8">
                <div class="card content-card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">
                                    <i class="fas fa-envelope text-primary me-2"></i>{{ __('Submission Details') }}
                                </h5>
                            </div>
                            <div class="col-auto">
                                <span
                                    class="badge bg-{{ $contactForm->status === 'new' ? 'warning' : ($contactForm->status === 'read' ? 'info' : ($contactForm->status === 'replied' ? 'success' : 'secondary')) }} fs-6">
                                    {{ ucfirst($contactForm->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ __('Name') }}</label>
                                    <div class="form-control-plaintext">{{ $contactForm->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ __('Email') }}</label>
                                    <div class="form-control-plaintext">
                                        <a href="mailto:{{ $contactForm->email }}" class="text-decoration-none">
                                            {{ $contactForm->email }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ __('Phone') }}</label>
                                    <div class="form-control-plaintext">
                                        @if ($contactForm->phone)
                                            <a href="tel:{{ $contactForm->phone }}" class="text-decoration-none">
                                                {{ $contactForm->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Not provided') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ __('Subject') }}</label>
                                    <div class="form-control-plaintext">{{ $contactForm->subject ?? __('No subject') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">{{ __('Message') }}</label>
                            <div class="form-control-plaintext" style="min-height: 150px; white-space: pre-wrap;">
                                {{ $contactForm->message }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ __('Submitted') }}</label>
                                    <div class="form-control-plaintext">
                                        {{ $contactForm->created_at->format('F d, Y \a\t H:i') }}
                                        <br>
                                        <small class="text-muted">{{ $contactForm->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ __('Last Updated') }}</label>
                                    <div class="form-control-plaintext">
                                        {{ $contactForm->updated_at->format('F d, Y \a\t H:i') }}
                                        <br>
                                        <small class="text-muted">{{ $contactForm->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($contactForm->read_at)
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">{{ __('Read At') }}</label>
                                <div class="form-control-plaintext">
                                    {{ $contactForm->read_at->format('F d, Y \a\t H:i') }}
                                </div>
                            </div>
                        @endif

                        @if ($contactForm->replied_at)
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">{{ __('Replied At') }}</label>
                                <div class="form-control-plaintext">
                                    {{ $contactForm->replied_at->format('F d, Y \a\t H:i') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="col-lg-4">
                <!-- Status Management -->
                <div class="card content-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-cog text-primary me-2"></i>{{ __('Status Management') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Current Status') }}</label>
                            <div class="d-flex align-items-center">
                                <span
                                    class="badge bg-{{ $contactForm->status === 'new' ? 'warning' : ($contactForm->status === 'read' ? 'info' : ($contactForm->status === 'replied' ? 'success' : 'secondary')) }} fs-6 me-3">
                                    {{ ucfirst($contactForm->status) }}
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="changeStatusBtn">
                                    <i class="fas fa-edit me-1"></i>{{ __('Change') }}
                                </button>
                            </div>
                        </div>

                        <div id="statusForm" style="display: none;">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('New Status') }}</label>
                                <select class="form-select" id="status">
                                    <option value="new" {{ $contactForm->status === 'new' ? 'selected' : '' }}>
                                        {{ __('New') }}</option>
                                    <option value="read" {{ $contactForm->status === 'read' ? 'selected' : '' }}>
                                        {{ __('Read') }}</option>
                                    <option value="replied" {{ $contactForm->status === 'replied' ? 'selected' : '' }}>
                                        {{ __('Replied') }}</option>
                                    <option value="closed" {{ $contactForm->status === 'closed' ? 'selected' : '' }}>
                                        {{ __('Closed') }}</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm" id="saveStatusBtn">
                                    <i class="fas fa-save me-1"></i>{{ __('Save') }}
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="cancelStatusBtn">
                                    <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card content-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>{{ __('Quick Actions') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="mailto:{{ $contactForm->email }}?subject=Re: {{ $contactForm->subject ?? 'Contact Form Response' }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-reply me-2"></i>{{ __('Reply via Email') }}
                            </a>

                            @if ($contactForm->phone)
                                <a href="tel:{{ $contactForm->phone }}" class="btn btn-outline-success">
                                    <i class="fas fa-phone me-2"></i>{{ __('Call') }}
                                </a>
                            @endif

                            <button type="button" class="btn btn-outline-info" id="copyEmailBtn">
                                <i class="fas fa-copy me-2"></i>{{ __('Copy Email') }}
                            </button>

                            <button type="button" class="btn btn-outline-danger" id="deleteBtn">
                                <i class="fas fa-trash me-2"></i>{{ __('Delete Submission') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="card content-card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note text-info me-2"></i>{{ __('Admin Notes') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea class="form-control" id="adminNotes" rows="4"
                                placeholder="{{ __('Add internal notes about this submission...') }}">{{ $contactForm->admin_notes }}</textarea>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="saveNotesBtn">
                            <i class="fas fa-save me-1"></i>{{ __('Save Notes') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const currentSubscriberId = {{ $contactForm->id }};

            // Status management
            $('#changeStatusBtn').on('click', function() {
                $('#statusForm').show();
                $(this).hide();
            });

            $('#cancelStatusBtn').on('click', function() {
                $('#statusForm').hide();
                $('#changeStatusBtn').show();
            });

            $('#saveStatusBtn').on('click', function() {
                const newStatus = $('#status').val();

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms') }}/${currentSubscriberId}/status`,
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

            // Save admin notes
            $('#saveNotesBtn').on('click', function() {
                const notes = $('#adminNotes').val();

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms') }}/${currentSubscriberId}/notes`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_notes: notes
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message || '{{ __('An error occurred.') }}');
                        }
                    },
                    error: function() {
                        toastr.error('{{ __('An error occurred while saving notes.') }}');
                    }
                });
            });

            // Copy email
            $('#copyEmailBtn').on('click', function() {
                const email = '{{ $contactForm->email }}';
                navigator.clipboard.writeText(email).then(function() {
                    toastr.success('{{ __('Email copied to clipboard!') }}');
                }, function() {
                    toastr.error('{{ __('Failed to copy email.') }}');
                });
            });

            // Delete submission
            $('#deleteBtn').on('click', function() {
                if (!confirm('{{ __('Are you sure you want to delete this submission?') }}')) {
                    return;
                }

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms') }}/${currentSubscriberId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                window.location.href =
                                    '{{ route('admin.settings.contact-management.contact-forms') }}';
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
        });
    </script>
@endpush
