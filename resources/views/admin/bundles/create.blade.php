@extends('admin.layout')

@section('title', 'Create New Bundle')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Create New Bundle</h1>
                        <p class="text-muted">Create a bundle of courses at a discounted price</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.bundles.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Bundles
                        </a>
                        <button type="submit" form="bundleForm" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Create Bundle
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fa fa-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="bundleForm" action="{{ route('admin.bundles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Bundle Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Bundle Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bundle Name (Arabic) -->
                                <div class="col-md-6 mb-3">
                                    <label for="name_ar" class="form-label">Bundle Name (Arabic)</label>
                                    <input type="text" class="form-control @error('name_ar') is-invalid @enderror" 
                                        id="name_ar" name="name_ar" value="{{ old('name_ar') }}" dir="rtl">
                                    @error('name_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description (Arabic) -->
                                <div class="col-md-12 mb-3">
                                    <label for="description_ar" class="form-label">Description (Arabic)</label>
                                    <textarea class="form-control @error('description_ar') is-invalid @enderror" 
                                        id="description_ar" name="description_ar" rows="4" dir="rtl">{{ old('description_ar') }}</textarea>
                                    @error('description_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Selection -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fa fa-book me-2"></i>Select Courses</h5>
                            <span class="badge bg-primary" id="selected-count">0 selected</span>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Select at least 2 courses to include in this bundle</p>
                            
                            <!-- Search Filter -->
                            <div class="mb-3">
                                <input type="text" class="form-control" id="course-search" 
                                    placeholder="Search courses by name...">
                            </div>

                            <!-- Select All / Deselect All -->
                            <div class="mb-3 d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-courses">
                                    <i class="fa fa-check-square me-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-courses">
                                    <i class="fa fa-square me-1"></i>Deselect All
                                </button>
                            </div>
                            
                            <div class="row" id="courses-container">
                                @foreach($courses as $course)
                                    <div class="col-md-6 mb-3 course-item" data-course-name="{{ strtolower($course->name) }}">
                                        <div class="card h-100 course-select-card {{ in_array($course->id, old('course_ids', [])) ? 'selected' : '' }}" 
                                            style="cursor: pointer; transition: all 0.3s ease;">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="form-check me-3 mt-1">
                                                        <input class="form-check-input course-checkbox" type="checkbox" 
                                                            name="course_ids[]" value="{{ $course->id }}" 
                                                            id="course_{{ $course->id }}"
                                                            {{ in_array($course->id, old('course_ids', [])) ? 'checked' : '' }}>
                                                    </div>
                                                    <img src="{{ $course->image_url }}" alt="{{ $course->name }}" 
                                                        class="me-3 rounded" 
                                                        style="width: 60px; height: 60px; object-fit: cover; flex-shrink: 0;">
                                                    <div class="flex-grow-1">
                                                        <label for="course_{{ $course->id }}" class="mb-0" style="cursor: pointer;">
                                                            <div class="fw-bold mb-1">{{ $course->name }}</div>
                                                            <div class="d-flex align-items-center gap-3">
                                                                <small class="text-muted">
                                                                    <i class="fa fa-tag me-1"></i>{{ $course->formatted_price }}
                                                                </small>
                                                                @if($course->instructor)
                                                                    <small class="text-muted">
                                                                        <i class="fa fa-user me-1"></i>{{ $course->instructor->name }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('course_ids')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Pricing -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-dollar-sign me-2"></i>Pricing</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="original_price" class="form-label">Original Price (SAR)</label>
                                <input type="number" class="form-control @error('original_price') is-invalid @enderror" 
                                    id="original_price" name="original_price" step="0.01" min="0" 
                                    value="{{ old('original_price') }}">
                                <small class="text-muted">Price before discount (optional)</small>
                                @error('original_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Bundle Price (SAR) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                    id="price" name="price" step="0.01" min="0" 
                                    value="{{ old('price') }}" required>
                                <small class="text-muted">Discounted bundle price</small>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bundle Image -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-image me-2"></i>Bundle Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="image" class="form-label">Bundle Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                    id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp,image/bmp,image/svg+xml">
                                <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WEBP, BMP, SVG. Max size: 2048 KB</small>
                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img src="" alt="Preview" class="img-fluid rounded">
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-cog me-2"></i>Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                    {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Featured Bundle
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
    <style>
        .course-select-card {
            border: 2px solid #e9ecef;
        }
        .course-select-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .course-select-card.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .course-item {
            display: block;
        }
        .course-item.hidden {
            display: none;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.querySelector('img').src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        // Course selection functionality
        document.addEventListener('DOMContentLoaded', function() {
            const courseCards = document.querySelectorAll('.course-select-card');
            const courseCheckboxes = document.querySelectorAll('.course-checkbox');
            const selectedCount = document.getElementById('selected-count');
            const courseSearch = document.getElementById('course-search');
            const selectAllBtn = document.getElementById('select-all-courses');
            const deselectAllBtn = document.getElementById('deselect-all-courses');

            // Update selected count
            function updateSelectedCount() {
                const checked = document.querySelectorAll('.course-checkbox:checked').length;
                selectedCount.textContent = checked + ' selected';
                selectedCount.className = checked >= 2 ? 'badge bg-success' : 'badge bg-warning';
            }

            // Toggle card selected state
            courseCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const card = this.closest('.course-select-card');
                    if (this.checked) {
                        card.classList.add('selected');
                    } else {
                        card.classList.remove('selected');
                    }
                    updateSelectedCount();
                });

                // Initialize selected state
                if (checkbox.checked) {
                    checkbox.closest('.course-select-card').classList.add('selected');
                }
            });

            // Click on card to toggle checkbox
            courseCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't toggle if clicking directly on checkbox
                    if (e.target.type !== 'checkbox') {
                        const checkbox = this.querySelector('.course-checkbox');
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change'));
                    }
                });
            });

            // Search functionality
            if (courseSearch) {
                courseSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    document.querySelectorAll('.course-item').forEach(item => {
                        const courseName = item.dataset.courseName;
                        if (courseName.includes(searchTerm)) {
                            item.classList.remove('hidden');
                        } else {
                            item.classList.add('hidden');
                        }
                    });
                });
            }

            // Select all
            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    document.querySelectorAll('.course-checkbox').forEach(checkbox => {
                        if (!checkbox.checked) {
                            checkbox.checked = true;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                });
            }

            // Deselect all
            if (deselectAllBtn) {
                deselectAllBtn.addEventListener('click', function() {
                    document.querySelectorAll('.course-checkbox').forEach(checkbox => {
                        if (checkbox.checked) {
                            checkbox.checked = false;
                            checkbox.dispatchEvent(new Event('change'));
                        }
                    });
                });
            }

            // Initial count
            updateSelectedCount();

            // Form validation before submit
            const bundleForm = document.getElementById('bundleForm');
            if (bundleForm) {
                bundleForm.addEventListener('submit', function(e) {
                    const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
                    if (checkedBoxes.length < 2) {
                        e.preventDefault();
                        alert('Please select at least 2 courses to create a bundle.');
                        return false;
                    }
                });
            }
        });
    </script>
    @endpush
@endsection

