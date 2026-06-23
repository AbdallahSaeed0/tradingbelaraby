@extends('admin.layout')

@section('title', custom_trans('Contact Form Submissions', 'admin'))

@section('content')
    <div class="container-fluid admin-settings-subpage py-3 py-lg-4"
        data-settings-back-url="{{ route('admin.settings.content-management.index') }}"
        data-settings-back-label="{{ custom_trans('Content Management', 'admin') }}">
        @include('admin.settings.partials.subpage-header', [
            'title' => custom_trans('Contact Form Submissions', 'admin'),
            'activeBreadcrumb' => custom_trans('Contact Forms', 'admin'),
            'actions' => '<a href="' . route('admin.settings.contact-forms.export') . '?format=csv" class="btn btn-success"><i class="fas fa-download me-2"></i>' . custom_trans('Export CSV', 'admin') . '</a>',
        ])

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
                    <table class="table table-hover table-striped">
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
            // Delete Form
            let deleteFormId = null;
            $('.delete-form-btn').on('click', function() {
                deleteFormId = $(this).data('id');
                $('#deleteConfirmMessage').text('{{ custom_trans('Are you sure you want to delete this contact form submission? This action cannot be undone.', 'admin') }}');
                $('#deleteConfirmModal').modal('show');
            });

            $(document).on('click', '#confirmDeleteBtn', function() {
                if (deleteFormId) {
                    const formId = deleteFormId;
                    deleteFormId = null;
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

