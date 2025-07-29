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
                                href="{{ route('admin.settings.content-management.index') }}">{{ __('Content Management') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.settings.contact-forms.index') }}">{{ __('Contact Forms') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Details') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <a href="{{ route('admin.settings.contact-forms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to List') }}
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
                            <i class="fas fa-envelope text-primary me-2"></i>{{ __('Contact Form Submission') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Name') }}</label>
                                    <p class="form-control-plaintext">{{ $contactForm->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Email') }}</label>
                                    <p class="form-control-plaintext">{{ $contactForm->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Phone') }}</label>
                            <p class="form-control-plaintext">{{ $contactForm->phone ?: __('Not provided') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Message') }}</label>
                            <div class="border rounded p-3 bg-light">
                                {{ $contactForm->message }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('Status') }}</label>
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
                                    <label class="form-label fw-bold">{{ __('Submitted') }}</label>
                                    <p class="form-control-plaintext">
                                        {{ $contactForm->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                        @if ($contactForm->read_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Read At') }}</label>
                                <p class="form-control-plaintext">{{ $contactForm->read_at->format('M d, Y H:i:s') }}</p>
                            </div>
                        @endif
                        @if ($contactForm->replied_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('Replied At') }}</label>
                                <p class="form-control-plaintext">{{ $contactForm->replied_at->format('M d, Y H:i:s') }}
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
                            <i class="fas fa-cogs text-info me-2"></i>{{ __('Status Management') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="statusForm">
                            @csrf
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold">{{ __('Status') }}</label>
                                <select class="form-select" id="status" name="status">
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
                            <div class="mb-3">
                                <label for="admin_notes" class="form-label fw-bold">{{ __('Admin Notes') }}</label>
                                <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"
                                    placeholder="{{ __('Add notes about this submission...') }}">{{ $contactForm->admin_notes }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>{{ __('Update Status') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card content-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt text-warning me-2"></i>{{ __('Quick Actions') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-success mark-read-btn"
                                {{ $contactForm->status === 'read' ? 'disabled' : '' }}>
                                <i class="fas fa-eye me-2"></i>{{ __('Mark as Read') }}
                            </button>
                            <button type="button" class="btn btn-outline-info mark-replied-btn"
                                {{ $contactForm->status === 'replied' ? 'disabled' : '' }}>
                                <i class="fas fa-reply me-2"></i>{{ __('Mark as Replied') }}
                            </button>
                            <button type="button" class="btn btn-outline-secondary mark-closed-btn"
                                {{ $contactForm->status === 'closed' ? 'disabled' : '' }}>
                                <i class="fas fa-times me-2"></i>{{ __('Mark as Closed') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger delete-form-btn">
                                <i class="fas fa-trash me-2"></i>{{ __('Delete Submission') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Enhanced Page Header */
        .page-title-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .page-title-box .page-title {
            color: white;
            margin-bottom: 0.5rem;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item.active {
            color: white;
        }

        /* Content Cards */
        .content-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .content-card .card-header {
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px 15px 0 0;
        }

        .content-card .card-body {
            background: white;
            border-radius: 0 0 15px 15px;
        }

        /* Form Styling */
        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Button Styling */
        .btn {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Badge Styling */
        .badge {
            font-size: 0.8rem;
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
        }
    </style>
@endpush

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
                            '{{ __('An error occurred while updating the status') }}');
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
                        toastr.error('{{ __('An error occurred while updating the status') }}');
                    }
                });
            }

            // Delete Form
            $('.delete-form-btn').on('click', function() {
                if (confirm('{{ __('Are you sure you want to delete this contact form submission?') }}')) {
                    $.ajax({
                        url: '{{ route('admin.settings.contact-forms.destroy', $contactForm) }}',
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    window.location.href =
                                        '{{ route('admin.settings.contact-forms.index') }}';
                                }, 1500);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error(
                                '{{ __('An error occurred while deleting the submission') }}'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush
