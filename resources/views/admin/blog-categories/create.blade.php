@extends('admin.layout')

@section('title', 'Create Blog Category')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Create Blog Category</h1>
                <p class="text-muted">Add a new blog category to organize your content</p>
            </div>
            <div>
                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Categories
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Category Form -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Category Information</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blog-categories.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Basic Information Section -->
                            <div class="mb-4">
                                <h6 class="mb-3 text-primary">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>

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
                                            <div class="form-text">This will be used to generate the URL slug automatically.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="order" class="form-label">Display Order</label>
                                            <input type="number" class="form-control @error('order') is-invalid @enderror"
                                                id="order" name="order" value="{{ old('order', 0) }}" placeholder="0"
                                                min="0">
                                            @error('order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Lower numbers appear first.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
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
                                            <div class="form-text">A brief description of what this category is about.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Settings Section -->
                            <div class="mb-4">
                                <h6 class="mb-3 text-primary">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                                name="status" required>
                                                <option value="">Select status</option>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="inactive"
                                                    {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Image Section -->
                            <div class="mb-4">
                                <h6 class="mb-3 text-primary">
                                    <i class="fas fa-image me-2"></i>Category Image
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Upload Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                                id="image" name="image" accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Recommended size: 400x300px. Max size: 2MB.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div id="imagePreview" class="d-none">
                                        <img id="previewImg" src="" alt="Preview" class="img-thumbnail max-w-200">
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeImage">
                                            <i class="fas fa-times me-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Form Actions -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Create Category
                                </button>
                                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-1"></i> Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Name:</strong> Choose a clear, descriptive name
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Description:</strong> Help users understand the category
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Order:</strong> Lower numbers appear first in lists
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Image:</strong> Optional but recommended for visual appeal
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Status:</strong> Inactive categories won't show to users
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Recent Categories -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-1"></i> Recent Categories
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $recentCategories = \App\Models\BlogCategory::latest()->limit(5)->get();
                        @endphp

                        @if ($recentCategories->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentCategories as $recentCategory)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $recentCategory->name }}</h6>
                                                <small class="text-muted">{{ $recentCategory->blogs_count }} blogs</small>
                                            </div>
                                            <span
                                                class="badge bg-{{ $recentCategory->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($recentCategory->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No categories created yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImage');

            // Image preview functionality
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Remove image
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreview.classList.add('d-none');
                previewImg.src = '';
            });

            // Auto-generate slug from name
            const nameInput = document.getElementById('name');
            nameInput.addEventListener('input', function() {
                const name = this.value;
                // You can add slug generation here if needed
            });
        });
    </script>
@endpush
