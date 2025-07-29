@extends('admin.layout')

@section('title', 'Language Details')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Language Details</h1>
                <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Languages
                </a>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.languages.edit', $language) }}" class="btn btn-primary">
                    <i class="fa fa-edit me-2"></i>Edit Language
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Language Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Language Name</label>
                                    <p class="mb-0">{{ $language->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Native Name</label>
                                    <p class="mb-0">{{ $language->native_name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Language Code</label>
                                    <p class="mb-0"><code>{{ $language->code }}</code></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Text Direction</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ strtoupper($language->direction) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <p class="mb-0">
                                        @if ($language->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Default Language</label>
                                    <p class="mb-0">
                                        @if ($language->is_default)
                                            <span class="badge bg-primary">Default</span>
                                        @else
                                            <span class="text-muted">No</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created At</label>
                                    <p class="mb-0">{{ $language->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Updated At</label>
                                    <p class="mb-0">{{ $language->updated_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.translations.index', ['language_id' => $language->id]) }}"
                                class="btn btn-outline-primary">
                                <i class="fa fa-language me-2"></i>View Translations
                            </a>
                            <a href="{{ route('admin.translations.create', ['language_id' => $language->id]) }}"
                                class="btn btn-outline-success">
                                <i class="fa fa-plus me-2"></i>Add Translation
                            </a>
                            @if (!$language->is_default)
                                <form action="{{ route('admin.languages.default', $language) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-warning w-100"
                                        onclick="return confirm('Set this language as default?')">
                                        <i class="fa fa-star me-2"></i>Set as Default
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
