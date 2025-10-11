@extends('admin.layout')

@section('title', 'Edit Course - ' . $course->name)

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

        .image-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: border-color 0.3s ease;
            cursor: pointer;
        }

        .image-upload-area:hover {
            border-color: #007bff;
        }

        .image-upload-area.dragover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .section-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .lecture-item {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 0.25rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            margin-left: 1rem;
        }

        .drag-handle {
            cursor: move;
            color: #6c757d;
        }

        .content-builder {
            min-height: 400px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
        }

        .learn-item {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.25rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .lecture-item {
            cursor: move;
            transition: all 0.3s ease;
        }

        .lecture-item:hover {
            background-color: #f8f9fa;
        }

        .lecture-item.dragging {
            opacity: 0.5;
            transform: rotate(5deg);
        }

        .drag-handle {
            cursor: move;
            color: #6c757d;
        }

        .drag-handle:hover {
            color: #495057;
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

        /* Instructor Selector Styles */
        .instructor-selector .selected-instructors {
            display: none;
        }

        .instructor-selector .selected-instructors.has-instructors {
            display: block;
            margin-bottom: 0.5rem;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
        }

        .instructor-tag {
            display: inline-flex;
            align-items: center;
            background: #007bff;
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 1.5rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .instructor-tag:hover {
            background: #0056b3;
        }

        .instructor-tag .remove-instructor {
            margin-left: 0.5rem;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .instructor-tag .remove-instructor:hover {
            opacity: 1;
        }

        .instructor-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .instructor-option {
            cursor: pointer;
        }

        .instructor-option:hover {
            background-color: #f8f9fa;
        }

        .instructor-option.selected {
            background-color: #e3f2fd;
            pointer-events: none;
            opacity: 0.6;
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
                        <h1 class="h3 mb-0">Edit Course</h1>
                        <p class="text-muted">Update course information and content</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Courses
                        </a>
                        <button type="submit" form="courseForm" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Update Course
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="courseForm" action="{{ route('admin.courses.update', $course->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Hidden inputs to track deleted items -->
            <div id="deletedItems">
                <!-- Deleted sections and lectures will be tracked here -->
            </div>
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
                                    'value' => old('name', $course->name),
                                    'valueAr' => old('name_ar', $course->name_ar),
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
                                    'value' => old('description', $course->description),
                                    'valueAr' => old('description_ar', $course->description_ar),
                                ])
                            </div>

                            <!-- Meetings Requirements (Multilingual) -->
                            <div class="col-md-12 mb-3">
                                @include('admin.courses.partials.multilingual-fields', [
                                    'fieldName' => 'requirements',
                                    'label' => 'Meetings Requirements',
                                    'type' => 'textarea',
                                    'required' => false,
                                    'rows' => 3,
                                    'placeholder' => 'Enter course requirements',
                                    'value' => old('requirements', $course->requirements),
                                    'valueAr' => old('requirements_ar', $course->requirements_ar),
                                ])
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instructors" class="form-label">Instructors <span
                                        class="text-danger">*</span></label>
                                <div class="instructor-selector">
                                    <div class="selected-instructors {{ $course->instructors->count() > 0 ? 'has-instructors' : '' }}"
                                        id="selectedInstructors">
                                        <!-- Pre-populate with existing instructors -->
                                        @foreach ($course->instructors as $instructor)
                                            <div class="instructor-tag" data-id="{{ $instructor->id }}">
                                                <span>{{ $instructor->name }}</span>
                                                <i class="fa fa-times remove-instructor"
                                                    onclick="removeInstructor({{ $instructor->id }})"></i>
                                                <input type="hidden" name="instructor_ids[]"
                                                    value="{{ $instructor->id }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="dropdown w-100">
                                        <button class="btn btn-outline-secondary dropdown-toggle w-100 text-start"
                                            type="button" id="instructorDropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-plus me-2"></i>Add Instructor
                                        </button>
                                        <ul class="dropdown-menu w-100" aria-labelledby="instructorDropdown"
                                            style="max-height: 300px; overflow-y: auto;">
                                            <li class="px-3 py-2">
                                                <input type="text" class="form-control form-control-sm"
                                                    id="instructorSearch" placeholder="Search instructors..."
                                                    autocomplete="off">
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            @foreach ($instructors as $instructor)
                                                <li>
                                                    <a class="dropdown-item instructor-option {{ in_array($instructor->id, $course->instructors->pluck('id')->toArray()) ? 'selected' : '' }}"
                                                        href="#" data-id="{{ $instructor->id }}"
                                                        data-name="{{ $instructor->name }}"
                                                        data-email="{{ $instructor->email ?? '' }}">
                                                        <div class="d-flex align-items-center">
                                                            <div class="instructor-avatar me-2">
                                                                {{ strtoupper(substr($instructor->name, 0, 2)) }}
                                                            </div>
                                                            <div>
                                                                <div class="fw-medium">{{ $instructor->name }}</div>
                                                                @if ($instructor->email)
                                                                    <small
                                                                        class="text-muted">{{ $instructor->email }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <small class="text-muted">Click to add multiple instructors to this course</small>
                                </div>
                                @error('instructor_ids')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price (SAR)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01"
                                    min="0" value="{{ old('price', $course->price) }}" required>
                                @error('price')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration"
                                    value="{{ old('duration', $course->duration) }}"
                                    placeholder="e.g., 8 weeks, 40 hours">
                                @error('duration')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="preview_video_url" class="form-label">Preview Video URL
                                    (YouTube/Vimeo)</label>
                                <input type="url"
                                    class="form-control @error('preview_video_url') is-invalid @enderror"
                                    id="preview_video_url" name="preview_video_url"
                                    value="{{ old('preview_video_url', $course->preview_video_url) }}"
                                    placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                                @error('preview_video_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Add a YouTube or Vimeo video URL to show as course
                                    preview</small>
                            </div>

                        </div>
                    </div>

                    <!-- Course Content -->
                    <div class="form-section">
                        <div class="section-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5><i class="fa fa-list me-2"></i>Course Content</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addSection">
                                    <i class="fa fa-plus me-1"></i>Add Section
                                </button>
                            </div>
                        </div>
                        <div id="courseSections">
                            @if ($course->sections && $course->sections->count() > 0)
                                @foreach ($course->sections as $section)
                                    <div class="section-item" data-section-id="{{ $section->id }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-grip-vertical drag-handle me-2"></i>
                                                <h6 class="mb-0">Section Title</h6>
                                            </div>
                                            <div>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-primary add-lecture me-1">
                                                    <i class="fa fa-plus me-1"></i>Add Lecture
                                                </button>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-danger remove-section">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Section Title</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control section-title"
                                                        name="sections[{{ $section->id }}][title]"
                                                        value="{{ $section->title }}"
                                                        placeholder="Section Title (English)" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control section-title-ar"
                                                        name="sections[{{ $section->id }}][title_ar]"
                                                        value="{{ $section->title_ar ?? '' }}"
                                                        placeholder="عنوان القسم (العربية)" dir="rtl">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Section Description</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <textarea class="form-control section-description" name="sections[{{ $section->id }}][description]"
                                                        placeholder="Section Description (English)" rows="2">{{ $section->description ?? '' }}</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <textarea class="form-control section-description-ar" name="sections[{{ $section->id }}][description_ar]"
                                                        placeholder="وصف القسم (العربية)" rows="2" dir="rtl">{{ $section->description_ar ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lectures-container">
                                            @if ($section->lectures && $section->lectures->count() > 0)
                                                @foreach ($section->lectures as $lecture)
                                                    <div class="lecture-item" data-lecture-id="{{ $lecture->id }}">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <div class="d-flex align-items-center flex-grow-1">
                                                                <i class="fa fa-grip-vertical drag-handle me-2"></i>
                                                                <div class="flex-grow-1">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm lecture-title mb-1"
                                                                                name="lectures[{{ $lecture->id }}][title]"
                                                                                value="{{ $lecture->title }}"
                                                                                placeholder="Lecture Title (English)"
                                                                                required>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm lecture-title-ar mb-1"
                                                                                name="lectures[{{ $lecture->id }}][title_ar]"
                                                                                value="{{ $lecture->title_ar ?? '' }}"
                                                                                placeholder="عنوان المحاضرة (العربية)"
                                                                                dir="rtl">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="ms-2">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger remove-lecture">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="ms-4">
                                                            <div class="mb-2">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <textarea class="form-control form-control-sm lecture-description" name="lectures[{{ $lecture->id }}][description]"
                                                                            placeholder="Lecture Description (Optional)" rows="2">{{ $lecture->description }}</textarea>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <textarea class="form-control form-control-sm lecture-description-ar"
                                                                            name="lectures[{{ $lecture->id }}][description_ar]" placeholder="وصف المحاضرة (اختياري)" rows="2"
                                                                            dir="rtl">{{ $lecture->description_ar ?? '' }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-3">
                                                                    <select
                                                                        class="form-select form-select-sm lecture-type">
                                                                        <option value="url"
                                                                            {{ $lecture->content_type == 'video' ? 'selected' : '' }}>
                                                                            Video URL</option>
                                                                        <option value="upload"
                                                                            {{ $lecture->content_type == 'document' ? 'selected' : '' }}>
                                                                            Upload File</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <div class="lecture-url-input"
                                                                        style="display: {{ $lecture->content_type == 'video' ? 'block' : 'none' }};">
                                                                        <input type="url"
                                                                            class="form-control form-control-sm lecture-link"
                                                                            name="lectures[{{ $lecture->id }}][video_url]"
                                                                            value="{{ $lecture->video_url }}"
                                                                            placeholder="Video URL (YouTube, Vimeo, etc.)"
                                                                            {{ $lecture->content_type == 'video' ? 'required' : '' }}>
                                                                    </div>
                                                                    <div class="lecture-upload-input"
                                                                        style="display: {{ $lecture->content_type == 'document' ? 'block' : 'none' }};">
                                                                        @if ($lecture->document_file)
                                                                            <div class="mb-2">
                                                                                <small class="text-muted">Current
                                                                                    file:</small>
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="fa fa-file me-2"></i>
                                                                                    <span
                                                                                        class="text-truncate">{{ basename($lecture->document_file) }}</span>
                                                                                    <a href="{{ asset('storage/' . $lecture->document_file) }}"
                                                                                        target="_blank"
                                                                                        class="btn btn-sm btn-outline-primary ms-2">
                                                                                        <i class="fa fa-download"></i>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <input type="file"
                                                                            class="form-control form-control-sm lecture-file"
                                                                            name="lectures[{{ $lecture->id }}][file]"
                                                                            accept="video/*,audio/*,.pdf,.doc,.docx,.ppt,.pptx"
                                                                            {{ $lecture->content_type == 'document' && !$lecture->document_file ? 'required' : '' }}>
                                                                        <small class="form-text text-muted">
                                                                            {{ $lecture->document_file ? 'Upload a new file to replace the current one' : 'Upload a file for this lecture' }}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-md-12">
                                                                    <label class="form-label small text-muted">Lecture Book
                                                                        (Optional)
                                                                    </label>
                                                                    @if ($lecture->book)
                                                                        <div class="mb-2">
                                                                            <small class="text-muted">Current book:</small>
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="fa fa-book me-2"></i>
                                                                                <span
                                                                                    class="text-truncate">{{ basename($lecture->book) }}</span>
                                                                                <a href="{{ asset('storage/' . $lecture->book) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-sm btn-outline-primary ms-2">
                                                                                    <i class="fa fa-download"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                    <input type="file"
                                                                        class="form-control form-control-sm lecture-book"
                                                                        name="lectures[{{ $lecture->id }}][book]"
                                                                        accept=".pdf">
                                                                    <div class="form-text small">
                                                                        {{ $lecture->book ? 'Upload a new book to replace the current one' : 'Upload a book/material specific to this lecture (PDF format)' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- What You'll Learn (Multilingual) -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-graduation-cap me-2"></i>What You'll Learn</h5>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addLearningObjective">
                                <i class="fa fa-plus me-1"></i>Add Learning Objective
                            </button>
                        </div>

                        <div id="learningObjectives">
                            @if ($course->what_to_learn && count($course->what_to_learn) > 0)
                                @php
                                    $englishObjectives = $course->what_to_learn ?? [];
                                    $arabicObjectives = $course->what_to_learn_ar ?? [];
                                    $maxCount = max(count($englishObjectives), count($arabicObjectives));
                                @endphp
                                @for ($i = 0; $i < $maxCount; $i++)
                                    <div class="learning-objective mb-3 p-3 border rounded">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">English Learning Objective</label>
                                                <input type="text" class="form-control" name="what_to_learn[]"
                                                    value="{{ $englishObjectives[$i] ?? '' }}"
                                                    placeholder="Enter learning objective in English">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Arabic Learning Objective</label>
                                                <input type="text" class="form-control" name="what_to_learn_ar[]"
                                                    value="{{ $arabicObjectives[$i] ?? '' }}"
                                                    placeholder="أدخل هدف التعلم باللغة العربية" dir="rtl">
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger remove-learning-objective">
                                                <i class="fa fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>

                    <!-- Frequently Asked Questions (Multilingual) -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-question-circle me-2"></i>Frequently Asked Questions</h5>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addFaqItem">
                                <i class="fa fa-plus me-1"></i>Add FAQ Item
                            </button>
                        </div>

                        <div id="faqItems">
                            @if ($course->faq_course && count($course->faq_course) > 0)
                                @php
                                    $englishFaqs = $course->faq_course ?? [];
                                    $arabicFaqs = $course->faq_course_ar ?? [];
                                    $maxCount = max(count($englishFaqs), count($arabicFaqs));
                                @endphp
                                @for ($i = 0; $i < $maxCount; $i++)
                                    <div class="faq-item mb-4 p-3 border rounded bg-light">
                                        <h6 class="mb-3"><i class="fa fa-question-circle me-2"></i>FAQ Item
                                            #{{ $i + 1 }}</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Question (English)</label>
                                                <input type="text" class="form-control"
                                                    name="faq_course[{{ $i }}][question]"
                                                    value="{{ $englishFaqs[$i]['question'] ?? '' }}"
                                                    placeholder="Enter question in English">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Question (Arabic)</label>
                                                <input type="text" class="form-control"
                                                    name="faq_course_ar[{{ $i }}][question]"
                                                    value="{{ $arabicFaqs[$i]['question'] ?? '' }}"
                                                    placeholder="أدخل السؤال باللغة العربية" dir="rtl">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label">Answer (English)</label>
                                                <textarea class="form-control" name="faq_course[{{ $i }}][answer]" rows="3"
                                                    placeholder="Enter answer in English">{{ $englishFaqs[$i]['answer'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Answer (Arabic)</label>
                                                <textarea class="form-control" name="faq_course_ar[{{ $i }}][answer]" rows="3"
                                                    placeholder="أدخل الإجابة باللغة العربية" dir="rtl">{{ $arabicFaqs[$i]['answer'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-faq-item">
                                                <i class="fa fa-trash me-1"></i>Remove FAQ
                                            </button>
                                        </div>
                                    </div>
                                @endfor
                            @endif
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
                            @if ($course->image_url)
                                <img src="{{ $course->image_url }}" alt="Course Image" class="img-fluid rounded mb-3"
                                    style="max-height: 200px;">
                            @else
                                <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Drag and drop an image here or click to select</p>
                                <p class="text-muted small">Recommended size: 800x600px</p>
                            @endif
                            <input type="file" class="d-none" id="courseImage" name="image" accept="image/*">
                        </div>
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="previewImg" class="img-fluid rounded" style="max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="removeImage">
                                <i class="fa fa-trash me-1"></i>Remove
                            </button>
                        </div>
                    </div>

                    <!-- Course Book -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-book me-2"></i>Course Book</h5>
                        </div>
                        <div class="mb-3">
                            <label for="courseBook" class="form-label">Upload Course Book (PDF)</label>
                            <input type="file" class="form-control" id="courseBook" name="book" accept=".pdf">
                            <div class="form-text">Upload the main book/material for this course (PDF format only)</div>
                            @if ($course->book_url)
                                <div class="mt-2">
                                    <small class="text-muted">Current book: {{ basename($course->book_url) }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Course Settings -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-cog me-2"></i>Course Settings</h5>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>
                                    Draft</option>
                                <option value="published"
                                    {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Published
                                </option>
                                <option value="archived"
                                    {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured"
                                    value="1" {{ old('featured', $course->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">
                                    Featured Course
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free"
                                    value="1" {{ old('is_free', $course->is_free) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_free">
                                    Free Course
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Display in Homepage Sections:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_top_discounted"
                                    name="show_in_top_discounted" value="1"
                                    {{ old('show_in_top_discounted', $course->show_in_top_discounted) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_in_top_discounted">
                                    Top Discounted Courses
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_subscription_bundles"
                                    name="show_in_subscription_bundles" value="1"
                                    {{ old('show_in_subscription_bundles', $course->show_in_subscription_bundles) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_in_subscription_bundles">
                                    Subscription Bundles
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_live_meeting"
                                    name="show_in_live_meeting" value="1"
                                    {{ old('show_in_live_meeting', $course->show_in_live_meeting) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_in_live_meeting">
                                    Live Meeting
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_recent_courses"
                                    name="show_in_recent_courses" value="1"
                                    {{ old('show_in_recent_courses', $course->show_in_recent_courses) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_in_recent_courses">
                                    Recent Courses
                                </label>
                            </div>
                            <small class="text-muted">Select one or more sections to display this course on the
                                homepage</small>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    @include('admin.courses.partials.seo-fields', ['course' => $course])
                </div>
            </div>
        </form>
    </div>

    <!-- Section Template -->
    <template id="sectionTemplate">
        <div class="section-item" data-section-id="">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <i class="fa fa-grip-vertical drag-handle me-2"></i>
                    <h6 class="mb-0">Section Title</h6>
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-primary add-lecture">
                        <i class="fa fa-plus me-1"></i>Add Lecture
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-section">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control section-title" name="sections[new][title]"
                            placeholder="Section Title (English)" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control section-title-ar" name="sections[new][title_ar]"
                            placeholder="عنوان القسم (العربية)" dir="rtl">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Section Description</label>
                <div class="row">
                    <div class="col-md-6">
                        <textarea class="form-control section-description" name="sections[new][description]"
                            placeholder="Section Description (English)" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <textarea class="form-control section-description-ar" name="sections[new][description_ar]"
                            placeholder="وصف القسم (العربية)" rows="2" dir="rtl"></textarea>
                    </div>
                </div>
            </div>
            <div class="lectures-container">
                <!-- Lectures will be added here -->
            </div>
        </div>
    </template>

    <!-- Lecture Template -->
    <template id="lectureTemplate">
        <div class="lecture-item" data-lecture-id="">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fa fa-grip-vertical drag-handle me-2"></i>
                    <div class="flex-grow-1">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm lecture-title mb-1"
                                    name="lectures[new][title]" placeholder="Lecture Title (English)" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm lecture-title-ar mb-1"
                                    name="lectures[new][title_ar]" placeholder="عنوان المحاضرة (العربية)" dir="rtl">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ms-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-lecture">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="ms-4">
                <div class="mb-2">
                    <div class="row">
                        <div class="col-md-6">
                            <textarea class="form-control form-control-sm lecture-description" name="lectures[new][description]"
                                placeholder="Lecture Description (Optional)" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control form-control-sm lecture-description-ar" name="lectures[new][description_ar]"
                                placeholder="وصف المحاضرة (اختياري)" rows="2" dir="rtl"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm lecture-type">
                            <option value="url">Video URL</option>
                            <option value="upload">Upload File</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <div class="lecture-url-input">
                            <input type="url" class="form-control form-control-sm lecture-link"
                                name="lectures[new][video_url]" placeholder="Video URL (YouTube, Vimeo, etc.)" required>
                        </div>
                        <div class="lecture-upload-input" style="display: none;">
                            <input type="file" class="form-control form-control-sm lecture-file"
                                name="lectures[new][file]" accept="video/*,audio/*,.pdf,.doc,.docx,.ppt,.pptx">
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <label class="form-label small text-muted">Lecture Book (Optional)</label>
                        <input type="file" class="form-control form-control-sm lecture-book"
                            name="lectures[new][book]" accept=".pdf">
                        <div class="form-text small">Upload a book/material specific to this lecture (PDF format)</div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Learn Item Template -->
    <template id="learnItemTemplate">
        <div class="learn-item">
            <div class="d-flex align-items-center">
                <i class="fa fa-check-circle me-2 text-success"></i>
                <input type="text" class="form-control form-control-sm learn-item-title" name="learning_objectives[]"
                    placeholder="Learning Item Title" required>
                <button type="button" class="btn btn-sm btn-outline-danger remove-learn-item ms-2">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('styles')
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .toast {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let sectionCounter = 0;
            let lectureCounter = 0;

            // Image upload functionality
            const imageUploadArea = document.getElementById('imageUploadArea');
            const courseImage = document.getElementById('courseImage');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImage');

            imageUploadArea.addEventListener('click', () => courseImage.click());

            imageUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                imageUploadArea.classList.add('dragover');
            });

            imageUploadArea.addEventListener('dragleave', () => {
                imageUploadArea.classList.remove('dragover');
            });

            imageUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                imageUploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    courseImage.files = files;
                    handleImagePreview(files[0]);
                }
            });

            courseImage.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    handleImagePreview(file);
                }
            });

            function handleImagePreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imageUploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            removeImageBtn.addEventListener('click', function() {
                courseImage.value = '';
                imagePreview.style.display = 'none';
                imageUploadArea.style.display = 'block';
            });

            // Section management
            const addSectionBtn = document.getElementById('addSection');
            const courseSections = document.getElementById('courseSections');
            const sectionTemplate = document.getElementById('sectionTemplate');

            addSectionBtn.addEventListener('click', function() {
                const sectionClone = sectionTemplate.content.cloneNode(true);
                const sectionId = 'new_' + Date.now();
                sectionClone.querySelector('.section-item').dataset.sectionId = sectionId;

                // Update input names for new section
                const titleInput = sectionClone.querySelector('.section-title');
                const descInput = sectionClone.querySelector('.section-description');
                titleInput.name = `sections[${sectionId}][title]`;
                descInput.name = `sections[${sectionId}][description]`;

                courseSections.appendChild(sectionClone);
            });

            // Add lecture functionality
            courseSections.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-lecture')) {
                    const section = e.target.closest('.section-item');
                    const lecturesContainer = section.querySelector('.lectures-container');
                    const lectureTemplate = document.getElementById('lectureTemplate');
                    const lectureClone = lectureTemplate.content.cloneNode(true);
                    const lectureId = 'new_' + Date.now();
                    lectureClone.querySelector('.lecture-item').dataset.lectureId = lectureId;

                    // Update input names for new lecture
                    const inputs = lectureClone.querySelectorAll('input, select, textarea');
                    inputs.forEach(input => {
                        if (input.name) {
                            input.name = input.name.replace('[new]', `[${lectureId}]`);
                        }
                    });

                    // Add hidden input to track which section this lecture belongs to
                    const sectionId = section.dataset.sectionId;
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `lectures[${lectureId}][section_id]`;
                    hiddenInput.value = sectionId;
                    lectureClone.querySelector('.lecture-item').appendChild(hiddenInput);

                    lecturesContainer.appendChild(lectureClone);

                    // Update the order for all lectures in this section
                    updateLectureOrder();

                    // Don't scroll to the new lecture
                    e.preventDefault();
                }
            });

            // Remove section/lecture functionality
            courseSections.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-section')) {
                    const sectionItem = e.target.closest('.section-item');
                    const sectionId = sectionItem.dataset.sectionId;

                    // If it's an existing section (not a new one), track it for deletion
                    if (sectionId && !sectionId.startsWith('new_')) {
                        trackDeletedItem('deleted_sections[]', sectionId);
                    }

                    // Remove all lectures in this section from tracking
                    const lectures = sectionItem.querySelectorAll('.lecture-item');
                    lectures.forEach(lecture => {
                        const lectureId = lecture.dataset.lectureId;
                        if (lectureId && !lectureId.startsWith('new_')) {
                            trackDeletedItem('deleted_lectures[]', lectureId);
                        }
                    });

                    sectionItem.remove();
                }
                if (e.target.classList.contains('remove-lecture')) {
                    const lectureItem = e.target.closest('.lecture-item');
                    const lectureId = lectureItem.dataset.lectureId;

                    // If it's an existing lecture (not a new one), track it for deletion
                    if (lectureId && !lectureId.startsWith('new_')) {
                        trackDeletedItem('deleted_lectures[]', lectureId);
                    }

                    lectureItem.remove();
                }
            });

            // Lecture type toggle
            courseSections.addEventListener('change', function(e) {
                if (e.target.classList.contains('lecture-type')) {
                    const lectureItem = e.target.closest('.lecture-item');
                    const urlInput = lectureItem.querySelector('.lecture-url-input');
                    const uploadInput = lectureItem.querySelector('.lecture-upload-input');
                    const urlField = lectureItem.querySelector('.lecture-link');
                    const fileField = lectureItem.querySelector('.lecture-file');

                    if (e.target.value === 'url') {
                        urlInput.style.display = 'block';
                        uploadInput.style.display = 'none';
                        // Make URL field required and file field not required
                        if (urlField) urlField.setAttribute('required', 'required');
                        if (fileField) fileField.removeAttribute('required');
                    } else {
                        urlInput.style.display = 'none';
                        uploadInput.style.display = 'block';
                        // Make file field required and URL field not required
                        if (fileField) fileField.setAttribute('required', 'required');
                        if (urlField) urlField.removeAttribute('required');
                    }
                }
            });

            // Learning Objectives management
            let learningObjectiveCounter = {{ $course->what_to_learn ? count($course->what_to_learn) : 0 }};

            document.getElementById('addLearningObjective').addEventListener('click', function() {
                addLearningObjective();
            });

            function addLearningObjective() {
                const index = learningObjectiveCounter++;
                const container = document.getElementById('learningObjectives');

                const objectiveDiv = document.createElement('div');
                objectiveDiv.className = 'learning-objective mb-3 p-3 border rounded';
                objectiveDiv.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">English Learning Objective</label>
                            <input type="text" class="form-control" name="what_to_learn[]"
                                   placeholder="Enter learning objective in English">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Arabic Learning Objective</label>
                            <input type="text" class="form-control" name="what_to_learn_ar[]"
                                   placeholder="أدخل هدف التعلم باللغة العربية" dir="rtl">
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-learning-objective">
                            <i class="fa fa-trash me-1"></i>Remove
                        </button>
                    </div>
                `;

                container.appendChild(objectiveDiv);

                // Add remove functionality
                objectiveDiv.querySelector('.remove-learning-objective').addEventListener('click', function() {
                    objectiveDiv.remove();
                });
            }

            // Add event listeners to existing remove buttons
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-learning-objective')) {
                    e.target.closest('.learning-objective').remove();
                }
                if (e.target.classList.contains('remove-faq-item')) {
                    e.target.closest('.faq-item').remove();
                }
            });

            // FAQ Items management
            let faqItemCounter = {{ $course->faq_course ? count($course->faq_course) : 0 }};

            document.getElementById('addFaqItem').addEventListener('click', function() {
                addFaqItem();
            });

            function addFaqItem() {
                const index = faqItemCounter++;
                const container = document.getElementById('faqItems');

                const faqDiv = document.createElement('div');
                faqDiv.className = 'faq-item mb-4 p-3 border rounded bg-light';
                faqDiv.innerHTML = `
                    <h6 class="mb-3"><i class="fa fa-question-circle me-2"></i>FAQ Item #${index + 1}</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Question (English)</label>
                            <input type="text" class="form-control" name="faq_course[${index}][question]"
                                   placeholder="Enter question in English">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Question (Arabic)</label>
                            <input type="text" class="form-control" name="faq_course_ar[${index}][question]"
                                   placeholder="أدخل السؤال باللغة العربية" dir="rtl">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Answer (English)</label>
                            <textarea class="form-control" name="faq_course[${index}][answer]" rows="3"
                                      placeholder="Enter answer in English"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Answer (Arabic)</label>
                            <textarea class="form-control" name="faq_course_ar[${index}][answer]" rows="3"
                                      placeholder="أدخل الإجابة باللغة العربية" dir="rtl"></textarea>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-faq-item">
                            <i class="fa fa-trash me-1"></i>Remove FAQ
                        </button>
                    </div>
                `;

                container.appendChild(faqDiv);
            }

            // Lecture ordering functionality
            function initializeLectureOrdering() {
                const lectureItems = document.querySelectorAll('.lecture-item');
                lectureItems.forEach(lecture => {
                    lecture.draggable = true;

                    lecture.addEventListener('dragstart', function(e) {
                        e.dataTransfer.setData('text/plain', '');
                        this.classList.add('dragging');
                    });

                    lecture.addEventListener('dragend', function(e) {
                        this.classList.remove('dragging');
                        // Update order after drag ends
                        updateLectureOrder();
                    });
                });

                const lectureContainers = document.querySelectorAll('.lectures-container');
                lectureContainers.forEach(container => {
                    container.addEventListener('dragover', function(e) {
                        e.preventDefault();
                        const dragging = document.querySelector('.dragging');
                        if (!dragging) return;

                        const afterElement = getDragAfterElement(this, e.clientY);
                        if (afterElement == null) {
                            this.appendChild(dragging);
                        } else {
                            this.insertBefore(dragging, afterElement);
                        }
                    });
                });
            }

            // Update lecture order based on DOM position
            function updateLectureOrder() {
                const lectureContainers = document.querySelectorAll('.lectures-container');
                lectureContainers.forEach(container => {
                    const lectures = container.querySelectorAll('.lecture-item');
                    lectures.forEach((lecture, index) => {
                        // Add or update hidden order input
                        let orderInput = lecture.querySelector('input[name*="[order]"]');
                        if (!orderInput) {
                            orderInput = document.createElement('input');
                            orderInput.type = 'hidden';
                            orderInput.name = lecture.querySelector('input[name*="[title]"]').name
                                .replace('[title]', '[order]');
                            lecture.appendChild(orderInput);
                        }
                        orderInput.value = index + 1;
                    });
                });
            }

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.lecture-item:not(.dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;

                    if (offset < 0 && offset > closest.offset) {
                        return {
                            offset: offset,
                            element: child
                        };
                    } else {
                        return closest;
                    }
                }, {
                    offset: Number.NEGATIVE_INFINITY
                }).element;
            }

            // Function to track deleted items
            function trackDeletedItem(inputName, itemId) {
                // Check if this item is already tracked for deletion
                const existingInput = document.querySelector(`input[name="${inputName}"][value="${itemId}"]`);
                if (!existingInput) {
                    const deletedItemsContainer = document.getElementById('deletedItems');
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = inputName;
                    hiddenInput.value = itemId;
                    deletedItemsContainer.appendChild(hiddenInput);
                }
            }

            // Initialize lecture ordering when page loads
            initializeLectureOrdering();

            // Set initial order for existing lectures
            updateLectureOrder();

            // Re-initialize ordering when new lectures are added
            const originalAddLecture = courseSections.addEventListener;
            courseSections.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-lecture')) {
                    // Wait for the lecture to be added, then re-initialize ordering
                    setTimeout(() => {
                        initializeLectureOrdering();
                        updateLectureOrder();
                    }, 100);
                }
            });

            // Instructor Selector functionality
            const selectedInstructorsContainer = document.getElementById('selectedInstructors');
            const selectedInstructors = new Set();

            // Initialize with existing instructors
            document.querySelectorAll('.instructor-tag').forEach(tag => {
                selectedInstructors.add(tag.dataset.id);
            });

            // Search functionality
            const instructorSearch = document.getElementById('instructorSearch');
            if (instructorSearch) {
                instructorSearch.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const options = document.querySelectorAll('.instructor-option');

                    options.forEach(option => {
                        const name = option.dataset.name.toLowerCase();
                        const email = option.dataset.email.toLowerCase();
                        const listItem = option.closest('li');

                        if (name.includes(searchTerm) || email.includes(searchTerm)) {
                            listItem.style.display = '';
                        } else {
                            listItem.style.display = 'none';
                        }
                    });
                });

                // Prevent dropdown from closing when clicking search input
                instructorSearch.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Handle instructor selection
            document.querySelectorAll('.instructor-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const name = this.dataset.name;

                    if (!selectedInstructors.has(id)) {
                        selectedInstructors.add(id);
                        addInstructorTag(id, name);
                        this.classList.add('selected');
                        updateRequiredValidation();
                    }
                });
            });

            function addInstructorTag(id, name) {
                const tag = document.createElement('div');
                tag.className = 'instructor-tag';
                tag.dataset.id = id;
                tag.innerHTML = `
                    <span>${name}</span>
                    <i class="fa fa-times remove-instructor" onclick="removeInstructor(${id})"></i>
                    <input type="hidden" name="instructor_ids[]" value="${id}">
                `;
                selectedInstructorsContainer.appendChild(tag);
                toggleSelectedContainer();
            }

            window.removeInstructor = function(id) {
                const tag = selectedInstructorsContainer.querySelector(`[data-id="${id}"]`);
                if (tag) {
                    tag.remove();
                    selectedInstructors.delete(id.toString());

                    // Re-enable the option in dropdown
                    const option = document.querySelector(`.instructor-option[data-id="${id}"]`);
                    if (option) {
                        option.classList.remove('selected');
                    }

                    toggleSelectedContainer();
                    updateRequiredValidation();
                }
            };

            function updateRequiredValidation() {
                const hasInstructors = selectedInstructors.size > 0;
                const dropdownButton = document.getElementById('instructorDropdown');

                if (!hasInstructors) {
                    dropdownButton.classList.add('border-danger');
                } else {
                    dropdownButton.classList.remove('border-danger');
                }
            }

            function toggleSelectedContainer() {
                if (selectedInstructors.size > 0) {
                    selectedInstructorsContainer.classList.add('has-instructors');
                } else {
                    selectedInstructorsContainer.classList.remove('has-instructors');
                }
            }

            // Initialize validation state
            updateRequiredValidation();

            // Free Course functionality
            const freeCheckbox = document.getElementById('is_free');
            const priceInput = document.getElementById('price');

            function togglePriceInput() {
                if (freeCheckbox.checked) {
                    priceInput.value = '0';
                    priceInput.readOnly = true;
                    priceInput.style.backgroundColor = '#e9ecef';
                    priceInput.style.cursor = 'not-allowed';
                } else {
                    priceInput.readOnly = false;
                    priceInput.style.backgroundColor = '';
                    priceInput.style.cursor = '';
                }
            }

            // Initialize on page load
            togglePriceInput();

            // Add event listener for checkbox change
            freeCheckbox.addEventListener('change', togglePriceInput);
        });
    </script>
@endpush
