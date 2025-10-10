@extends('admin.layout')

@section('title', 'Add Category')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">
                        <i class="fas fa-plus-circle me-2"></i>Add Category
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        @include('admin.courses.partials.multilingual-fields', [
                                            'fieldName' => 'name',
                                            'label' => 'Category Name',
                                            'type' => 'input',
                                            'required' => true,
                                            'placeholder' => 'Enter category name',
                                            'value' => old('name'),
                                            'valueAr' => old('name_ar'),
                                        ])
                                    </div>

                                    <div class="mb-3">
                                        @include('admin.courses.partials.multilingual-fields', [
                                            'fieldName' => 'description',
                                            'label' => 'Description',
                                            'type' => 'textarea',
                                            'required' => false,
                                            'rows' => 4,
                                            'placeholder' => 'Enter category description',
                                            'value' => old('description'),
                                            'valueAr' => old('description_ar'),
                                        ])
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_featured" class="form-check-input"
                                                id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                <i class="fas fa-star me-1"></i>Featured Category
                                            </label>
                                        </div>
                                        <small class="text-muted">Featured categories will be highlighted on the
                                            homepage</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Category Image</label>
                                        <div class="image-upload-container">
                                            <div class="image-preview mb-2" id="imagePreview">
                                                <img src="{{ asset('images/placeholder-image.png') }}" alt="Preview"
                                                    class="img-fluid rounded" id="previewImg">
                                            </div>
                                            <input type="file" name="image"
                                                class="form-control @error('image') is-invalid @enderror" id="imageInput"
                                                accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Recommended size: 400x300px. Max size: 2MB</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Create Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .image-upload-container {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .image-upload-container:hover {
            border-color: #007bff;
            background: #f0f8ff;
        }

        .image-preview {
            max-width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 8px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Image preview functionality
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('previewImg');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
