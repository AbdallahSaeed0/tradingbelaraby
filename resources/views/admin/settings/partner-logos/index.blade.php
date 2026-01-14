@extends('admin.layout')

@section('title', 'Partner Logos Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Partner Logos</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Partner Logos Management</h4>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="header-title">Partner Logos</h4>
                            <a href="{{ route('admin.partner-logos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add New Logo
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>Link</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logos as $logo)
                                        <tr>
                                            <td>
                                                <img src="{{ $logo->logo_url }}" alt="{{ $logo->name }}"
                                                    class="logo-preview-max">
                                            </td>
                                            <td>{{ $logo->name }}</td>
                                            <td>
                                                @if ($logo->link)
                                                    <a href="{{ $logo->link }}" target="_blank" class="text-primary">
                                                        <i class="fas fa-external-link-alt me-1"></i>Link
                                                    </a>
                                                @else
                                                    <span class="text-muted">No Link</span>
                                                @endif
                                            </td>
                                            <td>{{ $logo->order }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $logo->is_active ? 'bg-success' : 'bg-secondary' }} status-badge"
                                                    style="cursor: pointer;"
                                                    onclick="showStatusModal({{ $logo->id }}, '{{ $logo->is_active ? 'active' : 'inactive' }}', [
                                                        { value: 'active', label: 'Active' },
                                                        { value: 'inactive', label: 'Inactive' }
                                                    ], '{{ route('admin.partner-logos.update_status', $logo->id) }}')">
                                                    {{ $logo->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.partner-logos.edit', $logo) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-partner-logo"
                                                    data-logo-id="{{ $logo->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-image fa-3x mb-3 d-block"></i>
                                                No partner logos found. Add your first logo to get started!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteConfirmMessage">Are you sure you want to delete this partner logo? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deletePartnerLogoForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('admin.partials.status-modal')
    <script>
        $(document).ready(function() {
            $('.delete-partner-logo').on('click', function() {
                const logoId = $(this).data('logo-id');
                const deleteUrl = '{{ route('admin.partner-logos.index') }}/' + logoId;
                $('#deletePartnerLogoForm').attr('action', deleteUrl);
                $('#deleteConfirmModal').modal('show');
            });
        });
    </script>
@endpush
