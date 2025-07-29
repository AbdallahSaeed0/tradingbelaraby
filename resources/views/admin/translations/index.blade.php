@extends('admin.layout')

@section('title', 'Translations Management')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Translations</h1>
                <p class="text-muted">Manage application translations</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.translations.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus me-2"></i>Add Translation
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fa fa-upload me-2"></i>Import
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                    data-bs-target="#exportModal">
                    <i class="fa fa-download me-2"></i>Export
                </button>
                <form action="{{ route('admin.translations.clear_cache') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning"
                        onclick="return confirm('Clear translation cache?')">
                        <i class="fa fa-refresh me-2"></i>Clear Cache
                    </button>
                </form>
            </div>
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

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.translations.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="language_id" class="form-label">Language</label>
                        <select class="form-select" id="language_id" name="language_id">
                            <option value="">All Languages</option>
                            @foreach ($languages as $language)
                                <option value="{{ $language->id }}"
                                    {{ request('language_id') == $language->id ? 'selected' : '' }}>
                                    {{ $language->name }} ({{ $language->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="group" class="form-label">Group</label>
                        <select class="form-select" id="group" name="group">
                            <option value="">All Groups</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>
                                    {{ ucfirst($group) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="key" class="form-label">Search Key</label>
                        <input type="text" class="form-control" id="key" name="key"
                            value="{{ request('key') }}" placeholder="Search translation keys...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.translations.index') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-times me-2"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Translations Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Translation Key</th>
                                <th>Translation Value</th>
                                <th>Language</th>
                                <th>Group</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($translations as $translation)
                                <tr>
                                    <td>
                                        <code>{{ $translation->translation_key }}</code>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;"
                                            title="{{ $translation->translation_value }}">
                                            {{ $translation->translation_value }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $translation->language->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $translation->group }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.translations.show', $translation) }}"
                                                class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.translations.edit', $translation) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.translations.destroy', $translation) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this translation?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p>No translations found for the selected filters.</p>
                                        <a href="{{ route('admin.translations.create') }}" class="btn btn-primary">
                                            Add First Translation
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($translations->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $translations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Translations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="importForm" action="{{ route('admin.translations.bulk_import') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="import_language_id" class="form-label">Language</label>
                            <select class="form-select" id="import_language_id" name="language_id" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->name }} ({{ $language->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="import_group" class="form-label">Group</label>
                            <select class="form-select" id="import_group" name="group" required>
                                <option value="">Select Group</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group }}">{{ ucfirst($group) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="import_file" class="form-label">JSON File</label>
                            <input type="file" class="form-control" id="import_file" accept=".json" required>
                            <div class="form-text">Upload a JSON file with key-value pairs</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="importTranslations()">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Translations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="exportForm" action="{{ route('admin.translations.export') }}" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="export_language_id" class="form-label">Language</label>
                            <select class="form-select" id="export_language_id" name="language_id" required>
                                <option value="">Select Language</option>
                                @foreach ($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->name }} ({{ $language->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="export_group" class="form-label">Group</label>
                            <select class="form-select" id="export_group" name="group" required>
                                <option value="">Select Group</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group }}">{{ ucfirst($group) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function importTranslations() {
            const fileInput = document.getElementById('import_file');
            const languageSelect = document.getElementById('import_language_id');
            const groupSelect = document.getElementById('import_group');

            if (!fileInput.files[0] || !languageSelect.value || !groupSelect.value) {
                alert('Please fill in all fields');
                return;
            }

            const file = fileInput.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                try {
                    const jsonData = JSON.parse(e.target.result);
                    const formData = new FormData();
                    formData.append('language_id', languageSelect.value);
                    formData.append('group', groupSelect.value);
                    formData.append('translations', JSON.stringify(jsonData));
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('admin.translations.bulk_import') }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Import failed: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Import failed');
                        });
                } catch (error) {
                    alert('Invalid JSON file');
                }
            };

            reader.readAsText(file);
        }
    </script>
@endpush
