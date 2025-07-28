@extends('admin.layout')

@section('title', 'Add Language')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Add New Language</h1>
                <a href="{{ route('admin.languages.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i>Back to Languages
                </a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Language Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.languages.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Language Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="e.g., English, Arabic, French" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="native_name" class="form-label">Native Name *</label>
                                        <input type="text"
                                            class="form-control @error('native_name') is-invalid @enderror" id="native_name"
                                            name="native_name" value="{{ old('native_name') }}"
                                            placeholder="e.g., English, العربية, Français" required>
                                        @error('native_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Language Code *</label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                                            id="code" name="code" value="{{ old('code') }}"
                                            placeholder="e.g., en, ar, fr" maxlength="5" required>
                                        <div class="form-text">ISO 639-1 language code (2-5 characters)</div>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="direction" class="form-label">Text Direction *</label>
                                        <select class="form-select @error('direction') is-invalid @enderror" id="direction"
                                            name="direction" required>
                                            <option value="">Select direction</option>
                                            <option value="ltr" {{ old('direction') == 'ltr' ? 'selected' : '' }}>Left to
                                                Right (LTR)</option>
                                            <option value="rtl" {{ old('direction') == 'rtl' ? 'selected' : '' }}>Right
                                                to Left (RTL)</option>
                                        </select>
                                        @error('direction')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active
                                            </label>
                                        </div>
                                        <div class="form-text">Make this language available for users</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_default"
                                                name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_default">
                                                Set as Default
                                            </label>
                                        </div>
                                        <div class="form-text">Make this the default language for the application</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.languages.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Create Language
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
