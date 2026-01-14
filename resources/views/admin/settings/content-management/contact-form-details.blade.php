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
                                href="{{ route('admin.settings.content-management.index') }}">{{ custom_trans('Content Management', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-forms.index') }}">{{ custom_trans('Contact Forms', 'admin') }}</a></li>
                        <li class="breadcrumb-item active">{{ custom_trans('Details', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-forms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ custom_trans('Back to List', 'admin') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Contact Form Details -->
                <div class="card content-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-envelope text-primary me-2"></i>{{ custom_trans('Contact Form Submission', 'admin') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ custom_trans('Name', 'admin') }}</label>
                                    <p class="form-control-plaintext">{{ $contactForm->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ custom_trans('Email', 'admin') }}</label>
                                    <p class="form-control-plaintext">{{ $contactForm->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Phone', 'admin') }}</label>
                            <p class="form-control-plaintext">{{ $contactForm->phone ?: custom_trans('Not provided', 'admin') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ custom_trans('Message', 'admin') }}</label>
                            <div class="border rounded p-3 bg-light">
                                {{ $contactForm->message }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                    <p>
                                        <span
                                            class="badge bg-{{ $contactForm->status === 'new' ? 'danger' : ($contactForm->status === 'read' ? 'warning' : ($contactForm->status === 'replied' ? 'success' : 'secondary')) }}">
                                            {{ ucfirst($contactForm->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ custom_trans('Submitted', 'admin') }}</label>
                                    <p class="form-control-plaintext">
                                        {{ $contactForm->created_at->format('M d, Y H:i:s', 'admin') }}</p>
                                </div>
                            </div>
                        </div>
                        @if ($contactForm->read_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Read At', 'admin') }}</label>
                                <p class="form-control-plaintext">{{ $contactForm->read_at->format('M d, Y H:i:s', 'admin') }}</p>
                            </div>
                        @endif
                        @if ($contactForm->replied_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ custom_trans('Replied At', 'admin') }}</label>
                                <p class="form-control-plaintext">{{ $contactForm->replied_at->format('M d, Y H:i:s', 'admin') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Status Management -->
                <div class="card content-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs text-info me-2"></i>{{ custom_trans('Status Management', 'admin') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="statusForm">
                            @csrf
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">{{ custom_trans('Status', 'admin') }}</label>
                                <select class="form-select" id="status" name="status">
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
                            <div class="mb-3">
                                <label for="admin_notes" class="form-label fw-bold">{{ custom_trans('Admin Notes', 'admin') }}</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"
                                    placeholder="{{ custom_trans('Add notes about this submission...', 'admin') }}">{{ $contactForm->admin_notes }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>{{ custom_trans('Update Status', 'admin') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card content-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>{{ custom_trans('Quick Actions', 'admin') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success mark-read-btn"
                                {{ $contactForm->status === 'read' ? 'disabled' : '' }}>
                                <i class="fas fa-eye me-2"></i>{{ custom_trans('Mark as Read', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-info mark-replied-btn"
                                {{ $contactForm->status === 'replied' ? 'disabled' : '' }}>
                                <i class="fas fa-reply me-2"></i>{{ custom_trans('Mark as Replied', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary mark-closed-btn"
                                {{ $contactForm->status === 'closed' ? 'disabled' : '' }}>
                                <i class="fas fa-times me-2"></i>{{ custom_trans('Mark as Closed', 'admin') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger delete-form-btn">
                                <i class="fas fa-trash me-2"></i>{{ custom_trans('Delete Submission', 'admin') }}
                            </button>
                        </div>
                    </div>
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
            // Status Form Submission
            $('#statusForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('admin.settings.contact-forms.update-status', $contactForm) }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            // Reload page to show updated status
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
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
                        toastr.error(
                            '{{ custom_trans('An error occurred while updating the status', 'admin') }}');
                    }
                });
            });

            // Quick Action Buttons
            $('.mark-read-btn').on('click', function() {
                updateStatus('read');
            });

            $('.mark-replied-btn').on('click', function() {
                updateStatus('replied');
            });

            $('.mark-closed-btn').on('click', function() {
                updateStatus('closed');
            });

            function updateStatus(status) {
                $.ajax({
                    url: '{{ route('admin.settings.contact-forms.update-status', $contactForm) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('{{ custom_trans('An error occurred while updating the status', 'admin') }}');
                    }
                });
            }

            // Delete Form
            $('.delete-form-btn').on('click', function() {
                $('#deleteConfirmMessage').text('{{ custom_trans('Are you sure you want to delete this contact form submission? This action cannot be undone.', 'admin') }}');
                $('#deleteConfirmModal').modal('show');
            });

            $(document).on('click', '#confirmDeleteBtn', function() {
                $.ajax({
                    url: '{{ route('admin.settings.contact-forms.destroy', $contactForm) }}',
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                        success: function(response) {
                            if (response.success) {
                                $('#deleteConfirmModal').modal('hide');
                                toastr.success(response.message);
                                setTimeout(function() {
                                    window.location.href =
                                        '{{ route('admin.settings.contact-forms.index') }}';
                                }, 1500);
                            } else {
                                $('#deleteConfirmModal').modal('hide');
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            $('#deleteConfirmModal').modal('hide');
                            toastr.error(
                                '{{ custom_trans('An error occurred while deleting the submission', 'admin') }}'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush

