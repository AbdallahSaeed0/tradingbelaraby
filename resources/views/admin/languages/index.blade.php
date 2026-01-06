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
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
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
                                        <div class="form-check">
                                            <input class="form-check-input language-checkbox" type="checkbox" value="{{ $language->id }}">
                                        </div>
                                    </td>
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
                                    <td colspan="8" class="text-center py-4">
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

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center me-3">
                                <label class="form-label me-2 mb-0 small">Per page:</label>
                                <select class="form-select form-select-sm w-auto" id="perPageSelect" onchange="changePerPage(this.value)">
                                    @php
                                        $perPage = (int) request('per_page', 10);
                                    @endphp
                                    <option value="10" {{ $perPage === 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage === 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ $perPage === 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage === 100 ? 'selected' : '' }}>100</option>
                                    <option value="500" {{ $perPage === 500 ? 'selected' : '' }}>500</option>
                                    <option value="1000" {{ $perPage === 1000 ? 'selected' : '' }}>1000</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if ($languages->hasPages())
                            <div class="d-flex justify-content-end">
                                {{ $languages->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Change per page function
        function changePerPage(value) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }
    </script>
    @include('admin.partials.status-modal')
@endpush
