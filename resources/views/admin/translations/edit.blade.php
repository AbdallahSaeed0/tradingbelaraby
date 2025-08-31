@extends('admin.layout')

@section('title', 'Edit Translation')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Edit Translation</h1>
                <a href="{{ route('admin.translations.index', ['language_id' => $translation->language_id]) }}"
                    class="btn btn-outline-secondary btn-sm">
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
                        <form action="{{ route('admin.translations.update', $translation) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Hidden fields for required validation -->
                            <input type="hidden" name="language_id" value="{{ $translation->language_id }}">
                            <input type="hidden" name="translation_key" value="{{ $translation->translation_key }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Language</label>
                                        <input type="text" class="form-control"
                                            value="{{ $translation->language->name }} ({{ $translation->language->native_name }})"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Translation Key</label>
                                        <input type="text" class="form-control"
                                            value="{{ $translation->translation_key }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="translation_value" class="form-label">Value <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('translation_value') is-invalid @enderror"
                                    id="translation_value" name="translation_value"
                                    value="{{ old('translation_value', $translation->translation_value) }}" required>
                                @error('translation_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="group" class="form-label">Group</label>
                                <select class="form-select @error('group') is-invalid @enderror" id="group"
                                    name="group" required>
                                    <option value="general" {{ $translation->group == 'general' ? 'selected' : '' }}>General
                                    </option>
                                    <option value="admin" {{ $translation->group == 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                    <option value="front" {{ $translation->group == 'front' ? 'selected' : '' }}>Front
                                    </option>
                                </select>
                                @error('group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.translations.index', ['language_id' => $translation->language_id]) }}"
                                    class="btn btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Update Translation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
