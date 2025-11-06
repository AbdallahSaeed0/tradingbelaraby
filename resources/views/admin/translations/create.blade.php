@extends('admin.layout')

@section('title', 'Add Translation')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Add New Translation</h1>
                <a href="{{ route('admin.translations.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Translations
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Translation Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.translations.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="language_id" class="form-label">Language *</label>
                                        <select class="form-select @error('language_id') is-invalid @enderror"
                                            id="language_id" name="language_id" required>
                                            <option value="">Select Language</option>
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->id }}"
                                                    {{ old('language_id') == $language->id ? 'selected' : '' }}>
                                                    {{ $language->name }} ({{ $language->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('language_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="group" class="form-label">Group *</label>
                                        <select class="form-select @error('group') is-invalid @enderror" id="group"
                                            name="group" required>
                                            <option value="">Select Group</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group }}"
                                                    {{ old('group') == $group ? 'selected' : '' }}>
                                                    {{ ucfirst($group) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">
                                            <strong>General:</strong> Used everywhere<br>
                                            <strong>Admin:</strong> Admin panel only<br>
                                            <strong>Front:</strong> Frontend only
                                        </div>
                                        @error('group')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="translation_key" class="form-label">Translation Key *</label>
                                <input type="text" class="form-control @error('translation_key') is-invalid @enderror"
                                    id="translation_key" name="translation_key" value="{{ old('translation_key', 'admin') }}"
                                    placeholder="e.g., courses, users, dashboard" required>
                                <div class="form-text">This is the key used in your code like: {{ custom_trans('key', 'admin') }}</div>
                                @error('translation_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="translation_value" class="form-label">Translation Value *</label>
                                <textarea class="form-control @error('translation_value') is-invalid @enderror" id="translation_value"
                                    name="translation_value" rows="3" placeholder="Enter the translated text" required>{{ old('translation_value', 'admin') }}</textarea>
                                @error('translation_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.translations.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Create Translation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Usage Examples -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Usage Examples</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>General Usage</h6>
                                <code>{{ custom_trans('key', 'admin') }}</code>
                                <p class="text-muted small">Works in any group</p>
                            </div>
                            <div class="col-md-4">
                                <h6>Admin Only</h6>
                                <code>@adminTrans('key')</code>
                                <p class="text-muted small">Only for admin group</p>
                            </div>
                            <div class="col-md-4">
                                <h6>Front Only</h6>
                                <code>@frontTrans('key')</code>
                                <p class="text-muted small">Only for front group</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
