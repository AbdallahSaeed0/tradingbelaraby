@extends('admin.layout')

@section('title', 'Languages Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Languages</h1>
                <p class="text-muted">Manage application languages</p>
            </div>
            <a href="{{ route('admin.languages.create') }}" class="btn btn-primary">
                <i class="fa fa-plus me-2"></i>Add Language
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Native Name</th>
                                <th>Code</th>
                                <th>Direction</th>
                                <th>Status</th>
                                <th>Default</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($languages as $language)
                                <tr>
                                    <td>
                                        <strong>{{ $language->name }}</strong>
                                    </td>
                                    <td>{{ $language->native_name }}</td>
                                    <td>
                                        <code>{{ $language->code }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ strtoupper($language->direction) }}</span>
                                    </td>
                                    <td>
                                        @if ($language->is_default)
                                            <span class="badge bg-success">Active</span>
                                            <small class="d-block text-muted">Default</small>
                                        @else
                                            <span
                                                class="badge {{ $language->is_active ? 'bg-success' : 'bg-secondary' }} status-badge"
                                                style="cursor: pointer;"
                                                onclick="showStatusModal({{ $language->id }}, '{{ $language->is_active ? 'active' : 'inactive' }}', [
                                                    { value: 'active', label: 'Active' },
                                                    { value: 'inactive', label: 'Inactive' }
                                                ], '{{ route('admin.languages.update_status', $language->id) }}')">
                                                {{ $language->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($language->is_default)
                                            <span class="badge bg-primary">Default</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.languages.show', $language) }}"
                                                class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.languages.edit', $language) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            @if (!$language->is_default)
                                                <form action="{{ route('admin.languages.default', $language) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                                        title="Set as Default"
                                                        onclick="return confirm('Set this language as default?')">
                                                        <i class="fa fa-star"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if (!$language->is_default)
                                                <form action="{{ route('admin.languages.toggle-status', $language) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-{{ $language->is_active ? 'warning' : 'success' }}"
                                                        title="{{ $language->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fa fa-{{ $language->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if (!$language->is_default)
                                                <form action="{{ route('admin.languages.destroy', $language) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this language?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">No languages found.</p>
                                        <a href="{{ route('admin.languages.create') }}" class="btn btn-primary mt-2">
                                            Add First Language
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($languages->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $languages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('admin.partials.status-modal')
@endpush
