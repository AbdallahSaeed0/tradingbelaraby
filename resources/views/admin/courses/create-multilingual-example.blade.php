@extends('admin.layout')

@section('title', 'Create New Course - Multilingual')

@push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .form-section {
            background: #ffffff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .section-header h5 {
            color: #495057;
            font-weight: 600;
        }

        .multilingual-field .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.375rem;
            border-top-right-radius: 0.375rem;
        }

        .multilingual-field .nav-tabs .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
        }

        .multilingual-field .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .multilingual-field .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            padding: 1rem;
        }

        .multilingual-field textarea[dir="rtl"] {
            text-align: right;
        }

        .multilingual-field input[dir="rtl"] {
            text-align: right;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Create New Course - Multilingual</h1>
                        <p class="text-muted">Build a comprehensive course with Arabic and English content</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Courses
                        </a>
                        <button type="submit" form="courseForm" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Create Course
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="courseForm" action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-info-circle me-2"></i>Basic Information</h5>
                        </div>
                        <div class="row">
                            <!-- Course Title (Multilingual) -->
                            <div class="col-md-12 mb-3">
                                @include('admin.courses.partials.multilingual-fields', [
                                    'fieldName' => 'name',
                                    'label' => 'Course Title',
                                    'type' => 'input',
                                    'required' => true,
                                    'placeholder' => 'Enter course title',
                                    'value' => old('name'),
                                    'valueAr' => old('name_ar'),
                                ])
                            </div>

                            <!-- Course Description (Multilingual) -->
                            <div class="col-md-12 mb-3">
                                @include('admin.courses.partials.multilingual-fields', [
                                    'fieldName' => 'description',
                                    'label' => 'Course Description',
                                    'type' => 'textarea',
                                    'required' => true,
                                    'rows' => 4,
                                    'placeholder' => 'Enter course description',
                                    'value' => old('description'),
                                    'valueAr' => old('description_ar'),
                                ])
                            </div>

                            <!-- Category and Instructor -->
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="instructor_id" class="form-label">Instructor <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('instructor_id') is-invalid @enderror" id="instructor_id"
                                    name="instructor_id" required>
                                    <option value="">Select Instructor</option>
                                    @foreach ($instructors as $instructor)
                                        <option value="{{ $instructor->id }}"
                                            {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('instructor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Price and Duration -->
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price (SAR)</label>
                                <input type="number" class="form-control @error('price') is-invalid @enderror"
                                    id="price" name="price" step="0.01" min="0"
                                    value="{{ old('price', 0) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control @error('duration') is-invalid @enderror"
                                    id="duration" name="duration" value="{{ old('duration') }}"
                                    placeholder="e.g., 8 weeks, 40 hours">
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- What You'll Learn (Multilingual) -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-graduation-cap me-2"></i>What You'll Learn</h5>
                        </div>

                        <!-- English Learning Objectives -->
                        <div class="mb-4">
                            <h6><i class="fa fa-flag-usa me-2"></i>English Learning Objectives</h6>
                            <div id="learnItemsEn">
                                <!-- Learning items will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-learn-item" data-lang="en">
                                <i class="fa fa-plus me-1"></i>Add Learning Item
                            </button>
                        </div>

                        <!-- Arabic Learning Objectives -->
                        <div class="mb-4">
                            <h6><i class="fa fa-flag me-2"></i>Arabic Learning Objectives</h6>
                            <div id="learnItemsAr">
                                <!-- Learning items will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-learn-item" data-lang="ar">
                                <i class="fa fa-plus me-1"></i>Add Learning Item
                            </button>
                        </div>
                    </div>

                    <!-- FAQ (Multilingual) -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-question-circle me-2"></i>Frequently Asked Questions</h5>
                        </div>

                        <!-- English FAQ -->
                        <div class="mb-4">
                            <h6><i class="fa fa-flag-usa me-2"></i>English FAQ</h6>
                            <div id="faqItemsEn">
                                <!-- FAQ items will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-faq-item" data-lang="en">
                                <i class="fa fa-plus me-1"></i>Add FAQ Item
                            </button>
                        </div>

                        <!-- Arabic FAQ -->
                        <div class="mb-4">
                            <h6><i class="fa fa-flag me-2"></i>Arabic FAQ</h6>
                            <div id="faqItemsAr">
                                <!-- FAQ items will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary add-faq-item" data-lang="ar">
                                <i class="fa fa-plus me-1"></i>Add FAQ Item
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Course Image -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-image me-2"></i>Course Image</h5>
                        </div>
                        <div class="image-upload-area" id="imageUploadArea">
                            <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-2">Drag and drop an image here or click to select</p>
                            <p class="text-muted small">Recommended size: 800x600px</p>
                            <input type="file" class="d-none" id="courseImage" name="image" accept="image/*">
                        </div>
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="previewImg" class="img-fluid rounded" style="max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeImage">
                                <i class="fa fa-trash me-1"></i>Remove
                            </button>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    @include('admin.courses.partials.seo-fields')

                    <!-- Course Settings -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-cog me-2"></i>Course Settings</h5>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Featured Course
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_free" name="is_free"
                                value="1" {{ old('is_free') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_free">
                                Free Course
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                name="status">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let learnItemCounter = {
                en: 0,
                ar: 0
            };
            let faqItemCounter = {
                en: 0,
                ar: 0
            };

            // Add learning item
            document.querySelectorAll('.add-learn-item').forEach(button => {
                button.addEventListener('click', function() {
                    const lang = this.dataset.lang;
                    addLearnItem(lang);
                });
            });

            // Add FAQ item
            document.querySelectorAll('.add-faq-item').forEach(button => {
                button.addEventListener('click', function() {
                    const lang = this.dataset.lang;
                    addFaqItem(lang);
                });
            });

            function addLearnItem(lang) {
                const container = document.getElementById(
                    `learnItems${lang.charAt(0).toUpperCase() + lang.slice(1)}`);
                const index = learnItemCounter[lang]++;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'learn-item mb-2';
                itemDiv.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="what_to_learn${lang === 'ar' ? '_ar' : ''}[]"
                           placeholder="Enter learning objective">
                    <button type="button" class="btn btn-outline-danger remove-learn-item">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            `;

                container.appendChild(itemDiv);

                // Add remove functionality
                itemDiv.querySelector('.remove-learn-item').addEventListener('click', function() {
                    itemDiv.remove();
                });
            }

            function addFaqItem(lang) {
                const container = document.getElementById(
                    `faqItems${lang.charAt(0).toUpperCase() + lang.slice(1)}`);
                const index = faqItemCounter[lang]++;

                const itemDiv = document.createElement('div');
                itemDiv.className = 'faq-item mb-3 p-3 border rounded';
                itemDiv.innerHTML = `
                <div class="mb-2">
                    <label class="form-label">Question</label>
                    <input type="text" class="form-control" name="faq_course${lang === 'ar' ? '_ar' : ''}[${index}][question]"
                           placeholder="Enter question">
                </div>
                <div class="mb-2">
                    <label class="form-label">Answer</label>
                    <textarea class="form-control" name="faq_course${lang === 'ar' ? '_ar' : ''}[${index}][answer]"
                              rows="3" placeholder="Enter answer"></textarea>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-faq-item">
                    <i class="fa fa-trash me-1"></i>Remove FAQ
                </button>
            `;

                container.appendChild(itemDiv);

                // Add remove functionality
                itemDiv.querySelector('.remove-faq-item').addEventListener('click', function() {
                    itemDiv.remove();
                });
            }

            // Image upload functionality
            const imageUploadArea = document.getElementById('imageUploadArea');
            const courseImage = document.getElementById('courseImage');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            imageUploadArea.addEventListener('click', () => courseImage.click());
            imageUploadArea.addEventListener('dragover', handleDragOver);
            imageUploadArea.addEventListener('dragleave', handleDragLeave);
            imageUploadArea.addEventListener('drop', handleDrop);

            courseImage.addEventListener('change', handleFileSelect);

            function handleDragOver(e) {
                e.preventDefault();
                imageUploadArea.classList.add('dragover');
            }

            function handleDragLeave(e) {
                e.preventDefault();
                imageUploadArea.classList.remove('dragover');
            }

            function handleDrop(e) {
                e.preventDefault();
                imageUploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    courseImage.files = files;
                    handleFileSelect(e);
                }
            }

            function handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                        imageUploadArea.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            }

            document.getElementById('removeImage').addEventListener('click', function() {
                courseImage.value = '';
                imagePreview.style.display = 'none';
                imageUploadArea.style.display = 'block';
            });
        });
    </script>
@endpush
