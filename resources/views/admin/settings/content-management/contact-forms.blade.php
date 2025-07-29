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
                                href="{{ route('admin.settings.content-management.index') }}">{{ __('Content Management') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('Contact Forms') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>{{ __('Export') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.settings.contact-forms.export') }}?format=csv">{{ __('Export All (CSV)') }}</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.settings.contact-forms.export') }}?format=csv&status=new">{{ __('Export New Only (CSV)') }}</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.settings.contact-forms.export') }}?format=csv&status=replied">{{ __('Export Replied Only (CSV)') }}</a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.settings.content-management.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>{{ __('Back to Content Management') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $contactForms->where('status', 'new')->count() }}</h3>
                        <p class="mb-0">{{ __('New') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $contactForms->where('status', 'read')->count() }}</h3>
                        <p class="mb-0">{{ __('Read') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $contactForms->where('status', 'replied')->count() }}</h3>
                        <p class="mb-0">{{ __('Replied') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $contactForms->where('status', 'closed')->count() }}</h3>
                        <p class="mb-0">{{ __('Closed') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Forms Table -->
        <div class="card content-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-envelope text-primary me-2"></i>{{ __('All Contact Form Submissions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Message') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contactForms as $form)
                                <tr class="{{ $form->status === 'new' ? 'table-danger' : '' }}">
                                    <td>
                                        <strong>{{ $form->name }}</strong>
                                        @if ($form->status === 'new')
                                            <span class="badge bg-danger ms-2">{{ __('NEW') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $form->email }}</td>
                                    <td>{{ $form->phone ?: '-' }}</td>
                                    <td>{{ Str::limit($form->message, 50) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $form->status === 'new' ? 'danger' : ($form->status === 'read' ? 'warning' : ($form->status === 'replied' ? 'success' : 'secondary')) }}">
                                            {{ ucfirst($form->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $form->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.settings.contact-forms.show', $form) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-form-btn"
                                                data-id="{{ $form->id }}">
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
                                            <p>{{ __('No contact form submissions yet') }}</p>
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

        /* Stats Cards */
        .stats-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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

        /* Table Styling */
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.1);
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

        /* Pagination Styling */
        .pagination {
            border-radius: 10px;
            overflow: hidden;
        }

        .page-link {
            border: none;
            color: #667eea;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: #667eea;
            color: white;
        }

        .page-item.active .page-link {
            background: #667eea;
            border-color: #667eea;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Delete Form
            $('.delete-form-btn').on('click', function() {
                const formId = $(this).data('id');

                if (confirm('{{ __('Are you sure you want to delete this contact form submission?') }}')) {
                    $.ajax({
                        url: `/admin/settings/contact-forms/${formId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
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
