@extends('admin.layout')

@section('title', 'Translation Details')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Translation Details</h1>
                <a href="{{ route('admin.translations.index', ['language_id' => $translation->language_id]) }}"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Translations
                </a>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.translations.edit', $translation) }}" class="btn btn-primary">
                    <i class="fa fa-edit me-2"></i>Edit Translation
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Translation Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Language</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">{{ $translation->language->name }}</span>
                                        ({{ $translation->language->native_name }})
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Group</label>
                                    <p class="mb-0">
                                        <span class="badge bg-secondary">{{ ucfirst($translation->group) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Translation Key</label>
                            <p class="mb-0">
                                <code>{{ $translation->translation_key }}</code>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Translation Value</label>
                            <div class="p-3 bg-light rounded">
                                {{ $translation->translation_value }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Created At</label>
                                    <p class="mb-0">{{ $translation->created_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Updated At</label>
                                    <p class="mb-0">{{ $translation->updated_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Usage Examples</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">General Usage</label>
                            <code class="d-block p-2 bg-light rounded">{{ __('key') }}</code>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Admin Only</label>
                            <code class="d-block p-2 bg-light rounded">@adminTrans('key')</code>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Front Only</label>
                            <code class="d-block p-2 bg-light rounded">@frontTrans('key')</code>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.translations.index', ['language_id' => $translation->language_id]) }}"
                                class="btn btn-outline-primary">
                                <i class="fa fa-language me-2"></i>View All Translations
                            </a>
                            <a href="{{ route('admin.translations.create') }}" class="btn btn-outline-success">
                                <i class="fa fa-plus me-2"></i>Add New Translation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
