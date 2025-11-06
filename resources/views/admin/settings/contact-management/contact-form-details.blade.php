@extends('admin.layout')

@section('title', custom_trans('Contact Form Details', 'admin'))

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">{{ custom_trans('Contact Form Details', 'admin') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-management.index') }}">{{ custom_trans('Contact Management', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-management.contact-forms') }}">{{ custom_trans('Submissions', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Details', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-management.contact-forms') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ custom_trans('Back to List', 'admin') }}
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
                                    <i class="fas fa-envelope text-primary me-2"></i>{{ custom_trans('Submission Details', 'admin') }}
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
                                    <label class="form-label fw-bold text-muted">{{ custom_trans('Name', 'admin') }}</label>
                                    <div class="form-control-plaintext">{{ $contactForm->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ custom_trans('Email', 'admin') }}</label>
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
                                    <label class="form-label fw-bold text-muted">{{ custom_trans('Phone', 'admin') }}</label>
                                    <div class="form-control-plaintext">
                                        @if ($contactForm->phone)
                                            <a href="tel:{{ $contactForm->phone }}" class="text-decoration-none">
                                                {{ $contactForm->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ custom_trans('Not provided', 'admin') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ custom_trans('Subject', 'admin') }}</label>
                                    <div class="form-control-plaintext">{{ $contactForm->subject ?? custom_trans('No subject', 'admin') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">{{ custom_trans('Message', 'admin') }}</label>
                            <div class="form-control-plaintext min-h-150 ws-pre-wrap">
                                {{ $contactForm->message }}</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ custom_trans('Submitted', 'admin') }}</label>
                                    <div class="form-control-plaintext">
                                        {{ $contactForm->created_at->format('F d, Y \a\t H:i', 'admin') }}
                                        <br>
                                        <small class="text-muted">{{ $contactForm->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-muted">{{ custom_trans('Last Updated', 'admin') }}</label>
                                    <div class="form-control-plaintext">
                                        {{ $contactForm->updated_at->format('F d, Y \a\t H:i', 'admin') }}
                                        <br>
                                        <small class="text-muted">{{ $contactForm->updated_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($contactForm->read_at)
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">{{ custom_trans('Read At', 'admin') }}</label>
                                <div class="form-control-plaintext">
                                    {{ $contactForm->read_at->format('F d, Y \a\t H:i', 'admin') }}
                                </div>
                            </div>
                        @endif

                        @if ($contactForm->replied_at)
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted">{{ custom_trans('Replied At', 'admin') }}</label>
                                <div class="form-control-plaintext">
                                    {{ $contactForm->replied_at->format('F d, Y \a\t H:i', 'admin') }}
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
                            <i class="fas fa-cog text-primary me-2"></i>{{ custom_trans('Status Management', 'admin') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Current Status', 'admin') }}</label>
                            <div class="d-flex align-items-center">
                                <span
                                    class="badge bg-{{ $contactForm->status === 'new' ? 'warning' : ($contactForm->status === 'read' ? 'info' : ($contactForm->status === 'replied' ? 'success' : 'secondary')) }} fs-6 me-3">
                                    {{ ucfirst($contactForm->status) }}
                                </span>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="changeStatusBtn">
                                    <i class="fas fa-edit me-1"></i>{{ custom_trans('Change', 'admin') }}
                                </button>
                            </div>
                        </div>

                        <div id="statusForm" class="d-none-initially">
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ custom_trans('New Status', 'admin') }}</label>
                                <select class="form-select" id="status">
                                    <option value="new" {{ $contactForm->status === 'new' ? 'selected' : '' }}>
                                        {{ custom_trans('New', 'admin') }}</option>
                                    <option value="read" {{ $contactForm->status === 'read' ? 'selected' : '' }}>
                                        {{ custom_trans('Read', 'admin') }}</option>
                                    <option value="replied" {{ $contactForm->status === 'replied' ? 'selected' : '' }}>
                                        {{ custom_trans('Replied', 'admin') }}</option>
                                    <option value="closed" {{ $contactForm->status === 'closed' ? 'selected' : '' }}>
                                        {{ custom_trans('Closed', 'admin') }}</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm" id="saveStatusBtn">
                                    <i class="fas fa-save me-1"></i>{{ custom_trans('Save', 'admin') }}
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" id="cancelStatusBtn">
                                    <i class="fas fa-times me-1"></i>{{ custom_trans('Cancel', 'admin') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card content-card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>{{ custom_trans('Quick Actions', 'admin') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="mailto:{{ $contactForm->email }}?subject=Re: {{ $contactForm->subject ?? 'Contact Form Response' }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-reply me-2"></i>{{ custom_trans('Reply via Email', 'admin') }}
                            </a>

                            @if ($contactForm->phone)
                                <a href="tel:{{ $contactForm->phone }}" class="btn btn-outline-success">
                                    <i class="fas fa-phone me-2"></i>{{ custom_trans('Call', 'admin') }}
                                </a>
                            @endif

                            <button type="button" class="btn btn-outline-info" id="copyEmailBtn">
                                <i class="fas fa-copy me-2"></i>{{ custom_trans('Copy Email', 'admin') }}
                            </button>

                            <button type="button" class="btn btn-outline-danger" id="deleteBtn">
                                <i class="fas fa-trash me-2"></i>{{ custom_trans('Delete Submission', 'admin') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="card content-card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-sticky-note text-info me-2"></i>{{ custom_trans('Admin Notes', 'admin') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea class="form-control" id="adminNotes" rows="4"
                                placeholder="{{ custom_trans('Add internal notes about this submission...', 'admin') }}">{{ $contactForm->admin_notes }}</textarea>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" id="saveNotesBtn">
                            <i class="fas fa-save me-1"></i>{{ custom_trans('Save Notes', 'admin') }}
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
                    url: `{{ url('admin/settings/contact-management/contact-forms', 'admin') }}/${currentSubscriberId}/status`,
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

            // Save admin notes
            $('#saveNotesBtn').on('click', function() {
                const notes = $('#adminNotes').val();

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms', 'admin') }}/${currentSubscriberId}/notes`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        admin_notes: notes
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message || '{{ custom_trans('An error occurred.', 'admin') }}');
                        }
                    },
                    error: function() {
                        toastr.error('{{ custom_trans('An error occurred while saving notes.', 'admin') }}');
                    }
                });
            });

            // Copy email
            $('#copyEmailBtn').on('click', function() {
                const email = '{{ $contactForm->email }}';
                navigator.clipboard.writeText(email).then(function() {
                    toastr.success('{{ custom_trans('Email copied to clipboard!', 'admin') }}');
                }, function() {
                    toastr.error('{{ custom_trans('Failed to copy email.', 'admin') }}');
                });
            });

            // Delete submission
            $('#deleteBtn').on('click', function() {
                if (!confirm('{{ custom_trans('Are you sure you want to delete this submission?', 'admin') }}')) {
                    return;
                }

                $.ajax({
                    url: `{{ url('admin/settings/contact-management/contact-forms', 'admin') }}/${currentSubscriberId}`,
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
                            toastr.error(response.message || '{{ custom_trans('An error occurred.', 'admin') }}');
                        }
                    },
                    error: function() {
                        toastr.error(
                            '{{ custom_trans('An error occurred while deleting the submission.', 'admin') }}');
                    }
                });
            });
        });
    </script>
@endpush
