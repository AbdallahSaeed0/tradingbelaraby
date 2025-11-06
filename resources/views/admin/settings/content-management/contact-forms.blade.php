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
                                href="{{ route('admin.settings.content-management.index') }}">{{ custom_trans('Content Management', 'admin') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ custom_trans('Contact Forms', 'admin') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>{{ custom_trans('Export', 'admin') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.settings.contact-forms.export') }}?format=csv">{{ custom_trans('Export All (CSV)', 'admin') }}</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.settings.contact-forms.export') }}?format=csv&status=new">{{ custom_trans('Export New Only (CSV)', 'admin') }}</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ route('admin.settings.contact-forms.export') }}?format=csv&status=replied">{{ custom_trans('Export Replied Only (CSV)', 'admin') }}</a>
                                </li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.settings.content-management.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>{{ custom_trans('Back to Content Management', 'admin') }}
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
                        <p class="mb-0">{{ custom_trans('New', 'admin') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $contactForms->where('status', 'read')->count() }}</h3>
                        <p class="mb-0">{{ custom_trans('Read', 'admin') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $contactForms->where('status', 'replied')->count() }}</h3>
                        <p class="mb-0">{{ custom_trans('Replied', 'admin') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $contactForms->where('status', 'closed')->count() }}</h3>
                        <p class="mb-0">{{ custom_trans('Closed', 'admin') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Forms Table -->
        <div class="card content-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-envelope text-primary me-2"></i>{{ custom_trans('All Contact Form Submissions', 'admin') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ custom_trans('Name', 'admin') }}</th>
                                <th>{{ custom_trans('Email', 'admin') }}</th>
                                <th>{{ custom_trans('Phone', 'admin') }}</th>
                                <th>{{ custom_trans('Message', 'admin') }}</th>
                                <th>{{ custom_trans('Status', 'admin') }}</th>
                                <th>{{ custom_trans('Date', 'admin') }}</th>
                                <th>{{ custom_trans('Actions', 'admin') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contactForms as $form)
                                <tr class="{{ $form->status === 'new' ? 'table-danger' : '' }}">
                                    <td>
                                        <strong>{{ $form->name }}</strong>
                                        @if ($form->status === 'new')
                                            <span class="badge bg-danger ms-2">{{ custom_trans('NEW', 'admin') }}</span>
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
                                    <td>{{ $form->created_at->format('M d, Y H:i', 'admin') }}</td>
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
                                            <p>{{ custom_trans('No contact form submissions yet', 'admin') }}</p>
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
            // Delete Form
            $('.delete-form-btn').on('click', function() {
                const formId = $(this).data('id');

                if (confirm('{{ custom_trans('Are you sure you want to delete this contact form submission?', 'admin') }}')) {
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
                                '{{ custom_trans('An error occurred while deleting the submission', 'admin') }}'
                                );
                        }
                    });
                }
            });
        });
    </script>
@endpush

