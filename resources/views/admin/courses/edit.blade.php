@extends('admin.layout')

@section('title', 'Edit Course - ' . $course->name)

@section('content')
    <!-- Define instructor functions BEFORE the dropdown HTML -->
    <script>
        window.selectInstructor = function(id, name, element) {
            try {
                const container = document.getElementById('selectedInstructors');
                if (!container) {
                    alert('Error: Container not found. ID: selectedInstructors');
                    return false;
                }
                
                // Check if already selected
                const existingTag = container.querySelector('[data-id="' + id + '"]');
                if (existingTag) {
                    return false; // Already selected
                }
                
                // Create tag
                const tag = document.createElement('div');
                tag.className = 'instructor-tag';
                tag.setAttribute('data-id', id);
                tag.innerHTML = '<span>' + name + '</span><i class="fa fa-times remove-instructor" onclick="removeInstructor(' + id + ')"></i><input type="hidden" name="instructor_ids[]" value="' + id + '">';
                container.appendChild(tag);
                
                // Mark as selected
                if (element) {
                    element.classList.add('selected');
                }
                
                // Show container if it has instructors
                if (container.children.length > 0) {
                    container.classList.add('has-instructors');
                }
                
                return false;
            } catch (e) {
                alert('Error in selectInstructor: ' + e.message);
                return false;
            }
        };
        
        window.removeInstructor = function(id) {
            try {
                const container = document.getElementById('selectedInstructors');
                if (!container) {
                    return;
                }
                
                const tag = container.querySelector('[data-id="' + id + '"]');
                if (tag) {
                    tag.remove();
                    
                    // Re-enable the option in dropdown
                    const option = document.querySelector('.instructor-option[data-id="' + id + '"]');
                    if (option) {
                        option.classList.remove('selected');
                    }
                    
                    // Hide container if no instructors
                    if (container.children.length === 0) {
                        container.classList.remove('has-instructors');
                    }
                }
            } catch (e) {
                alert('Error in removeInstructor: ' + e.message);
            }
        };
        
        // Define addSection function immediately at page load
        console.log('Defining addSection at top of page...');
        window.sectionCounter = window.sectionCounter || 0;
        
        window.addSection = function() {
            console.log('addSection function called');
            const template = document.getElementById('sectionTemplate');
            if (!template) {
                console.error('Section template not found');
                alert('Section template not found. Please refresh the page.');
                return;
            }
            
            const container = document.getElementById('courseSections');
            if (!container) {
                console.error('Course sections container not found');
                alert('Course sections container not found. Please refresh the page.');
                return;
            }
            
            const clone = template.content.cloneNode(true);
            const sectionElement = clone.querySelector('.section-item');
            
            if (!sectionElement) {
                console.error('Section element not found in template');
                return;
            }

            window.sectionCounter = (window.sectionCounter || 0) + 1;
            const sectionIndex = 'new_' + window.sectionCounter;
            sectionElement.dataset.sectionId = sectionIndex;

            // Set proper names for section inputs
            const titleInput = sectionElement.querySelector('.section-title');
            const titleArInput = sectionElement.querySelector('.section-title-ar');
            const descInput = sectionElement.querySelector('.section-description');
            const descArInput = sectionElement.querySelector('.section-description-ar');
            
            if (titleInput) titleInput.name = `sections[${sectionIndex}][title]`;
            if (titleArInput) titleArInput.name = `sections[${sectionIndex}][title_ar]`;
            if (descInput) descInput.name = `sections[${sectionIndex}][description]`;
            if (descArInput) descArInput.name = `sections[${sectionIndex}][description_ar]`;

            // Append to DOM
            container.appendChild(clone);
            
            // Get the actual element from DOM for event listeners
            const addedSection = container.querySelector(`[data-section-id="${sectionIndex}"]`);
            if (addedSection) {
                // Add event listeners
                const addLectureBtn = addedSection.querySelector('.add-lecture');
                const removeSectionBtn = addedSection.querySelector('.remove-section');
                
                if (addLectureBtn) {
                    addLectureBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (typeof window.addLecture === 'function') {
                            window.addLecture(addedSection);
                        } else {
                            console.error('addLecture function not found');
                        }
                    });
                }
                
                if (removeSectionBtn) {
                    removeSectionBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        addedSection.remove();
                    });
                }
            }
            
            console.log('Section added successfully!');
        };
        
        // Define addLearningObjective function globally
        window.learningObjectiveCounter = window.learningObjectiveCounter || {{ $course->what_to_learn ? count($course->what_to_learn) : 0 }};
        window.addLearningObjective = function() {
            const index = window.learningObjectiveCounter++;
            const container = document.getElementById('learningObjectives');
            if (!container) return;
            
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
            
            const removeBtn = objectiveDiv.querySelector('.remove-learning-objective');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    objectiveDiv.remove();
                });
            }
        };
        
        // Define addFaqItem function globally
        window.faqItemCounter = window.faqItemCounter || {{ $course->faq_course ? count($course->faq_course) : 0 }};
        window.addFaqItem = function() {
            const index = window.faqItemCounter++;
            const container = document.getElementById('faqItems');
            if (!container) return;
            
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
            
            const removeBtn = faqDiv.querySelector('.remove-faq-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    faqDiv.remove();
                });
            }
        };
        
        // Define addLecture function globally
        window.lectureCounter = window.lectureCounter || 0;
        window.addLecture = function(sectionElement) {
            console.log('addLecture function called');
            const template = document.getElementById('lectureTemplate');
            if (!template) {
                console.error('Lecture template not found');
                return;
            }
            
            const lecturesContainer = sectionElement.querySelector('.lectures-container');
            if (!lecturesContainer) {
                console.error('Lectures container not found in section');
                return;
            }
            
            const clone = template.content.cloneNode(true);
            const lectureElement = clone.querySelector('.lecture-item');
            if (!lectureElement) {
                console.error('Lecture element not found in template');
                return;
            }
            
            const lectureId = 'new_' + Date.now();
            lectureElement.dataset.lectureId = lectureId;
            
            // Update input names for new lecture
            const inputs = clone.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name && input.name.includes('[new]')) {
                    input.name = input.name.replace('[new]', `[${lectureId}]`);
                }
            });
            
            // Add hidden input to track which section this lecture belongs to
            const sectionId = sectionElement.dataset.sectionId;
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `lectures[${lectureId}][section_id]`;
            hiddenInput.value = sectionId;
            lectureElement.appendChild(hiddenInput);
            
            lecturesContainer.appendChild(clone);
            
            // Get the actual element from DOM
            const addedLecture = lecturesContainer.querySelector(`[data-lecture-id="${lectureId}"]`);
            if (addedLecture) {
                // Add remove button event
                const removeBtn = addedLecture.querySelector('.remove-lecture');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        addedLecture.remove();
                    });
                }
                
                // Handle lecture type switching
                const typeSelect = addedLecture.querySelector('.lecture-type');
                const urlInput = addedLecture.querySelector('.lecture-url-input');
                const uploadInput = addedLecture.querySelector('.lecture-upload-input');
                
                if (typeSelect && urlInput && uploadInput) {
                    typeSelect.addEventListener('change', function() {
                        if (this.value === 'url') {
                            urlInput.style.display = 'block';
                            uploadInput.style.display = 'none';
                            const urlField = addedLecture.querySelector('.lecture-link');
                            const fileField = addedLecture.querySelector('.lecture-file');
                            if (urlField) urlField.setAttribute('required', 'required');
                            if (fileField) fileField.removeAttribute('required');
                        } else {
                            urlInput.style.display = 'none';
                            uploadInput.style.display = 'block';
                            const urlField = addedLecture.querySelector('.lecture-link');
                            const fileField = addedLecture.querySelector('.lecture-file');
                            if (fileField) fileField.setAttribute('required', 'required');
                            if (urlField) urlField.removeAttribute('required');
                        }
                    });
                }
            }
            
            console.log('Lecture added successfully!');
        };
        
        // Set up event delegation for add-lecture buttons (works immediately)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-lecture') || e.target.closest('.add-lecture')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.classList.contains('add-lecture') ? e.target : e.target.closest('.add-lecture');
                const section = btn.closest('.section-item');
                
                if (section) {
                    if (typeof window.addLecture === 'function') {
                        window.addLecture(section);
                    } else {
                        console.error('addLecture function not found');
                        alert('Add lecture function not available. Please refresh the page.');
                    }
                }
            }
            
            // Handle remove-section buttons
            if (e.target.classList.contains('remove-section') || e.target.closest('.remove-section')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.classList.contains('remove-section') ? e.target : e.target.closest('.remove-section');
                const section = btn.closest('.section-item');
                
                if (section) {
                    // Check if this is an existing section (has numeric ID) or new section
                    const sectionId = section.dataset.sectionId;
                    
                    if (sectionId && !sectionId.toString().startsWith('new_')) {
                        // Existing section - track for deletion
                        // Create hidden input for deletion
                        let deletedContainer = document.getElementById('deletedItems');
                        if (!deletedContainer) {
                            deletedContainer = document.createElement('div');
                            deletedContainer.id = 'deletedItems';
                            deletedContainer.style.display = 'none';
                            const form = document.getElementById('courseForm') || document.querySelector('form');
                            if (form) {
                                form.appendChild(deletedContainer);
                            }
                        }
                        
                        // Check if already tracked
                        const existing = deletedContainer.querySelector(`input[name="deleted_sections[]"][value="${sectionId}"]`);
                        if (!existing) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'deleted_sections[]';
                            hiddenInput.value = sectionId;
                            deletedContainer.appendChild(hiddenInput);
                        }
                        
                        // Also track all lectures in this section for deletion
                        const lectures = section.querySelectorAll('.lecture-item');
                        lectures.forEach(lecture => {
                            const lectureId = lecture.dataset.lectureId;
                            if (lectureId && !lectureId.toString().startsWith('new_')) {
                                const existingLecture = deletedContainer.querySelector(`input[name="deleted_lectures[]"][value="${lectureId}"]`);
                                if (!existingLecture) {
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'deleted_lectures[]';
                                    hiddenInput.value = lectureId;
                                    deletedContainer.appendChild(hiddenInput);
                                }
                            }
                        });
                    }
                    
                    // Remove the section from DOM
                    section.remove();
                    console.log('Section removed');
                }
            }
            
            // Handle remove-lecture buttons
            if (e.target.classList.contains('remove-lecture') || e.target.closest('.remove-lecture')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.classList.contains('remove-lecture') ? e.target : e.target.closest('.remove-lecture');
                const lecture = btn.closest('.lecture-item');
                
                if (lecture) {
                    const lectureId = lecture.dataset.lectureId;
                    
                    // If it's an existing lecture (not new), track for deletion
                    if (lectureId && !lectureId.toString().startsWith('new_')) {
                        let deletedContainer = document.getElementById('deletedItems');
                        if (!deletedContainer) {
                            deletedContainer = document.createElement('div');
                            deletedContainer.id = 'deletedItems';
                            deletedContainer.style.display = 'none';
                            const form = document.getElementById('courseForm') || document.querySelector('form');
                            if (form) {
                                form.appendChild(deletedContainer);
                            }
                        }
                        
                        const existing = deletedContainer.querySelector(`input[name="deleted_lectures[]"][value="${lectureId}"]`);
                        if (!existing) {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'deleted_lectures[]';
                            hiddenInput.value = lectureId;
                            deletedContainer.appendChild(hiddenInput);
                        }
                    }
                    
                    lecture.remove();
                    console.log('Lecture removed');
                }
            }
            
            // Handle remove-learning-objective buttons
            if (e.target.classList.contains('remove-learning-objective') || e.target.closest('.remove-learning-objective')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.classList.contains('remove-learning-objective') ? e.target : e.target.closest('.remove-learning-objective');
                const objective = btn.closest('.learning-objective');
                
                if (objective) {
                    objective.remove();
                    console.log('Learning objective removed');
                }
            }
            
            // Handle remove-faq-item buttons
            if (e.target.classList.contains('remove-faq-item') || e.target.closest('.remove-faq-item')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.classList.contains('remove-faq-item') ? e.target : e.target.closest('.remove-faq-item');
                const faqItem = btn.closest('.faq-item');
                
                if (faqItem) {
                    faqItem.remove();
                    console.log('FAQ item removed');
                }
            }
        });
        
        console.log('All functions defined:', {
            addSection: typeof window.addSection,
            addLearningObjective: typeof window.addLearningObjective,
            addFaqItem: typeof window.addFaqItem,
            addLecture: typeof window.addLecture
        });
    </script>
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
                                        <ul class="dropdown-menu w-100 max-h-300 overflow-auto"
                                            aria-labelledby="instructorDropdown" id="instructorDropdownMenu">
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
                                                        href="javascript:void(0)" data-id="{{ $instructor->id }}"
                                                        data-name="{{ $instructor->name }}"
                                                        data-email="{{ $instructor->email ?? '' }}"
                                                        onclick="selectInstructor({{ $instructor->id }}, '{{ addslashes($instructor->name) }}', this); return false;">
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
                            <div class="col-md-4 mb-3">
                                <label for="original_price" class="form-label">Original Price (SAR)</label>
                                <input type="number" class="form-control" id="original_price" name="original_price" step="0.01"
                                    min="0" value="{{ old('original_price', $course->original_price) }}" placeholder="0.00">
                                <small class="text-muted">Main price before any discount</small>
                                @error('original_price')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Discount Price (SAR) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01"
                                    min="0" value="{{ old('price', $course->price) }}" required>
                                <small class="text-muted">Current selling price (set to 0 for free course)</small>
                                @error('price')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration"
                                    value="{{ old('duration', $course->duration) }}" placeholder="e.g., 8 weeks, 40 hours">
                                @error('duration')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3" id="discount_preview" style="display: none;">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Discount Preview:</strong>
                                    <span id="discount_preview_text"></span>
                                </div>
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
                        <script>
                            // Define function and attach event immediately after button
                            (function() {
                                const btn = document.getElementById('addSection');
                                if (btn) {
                                    btn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        
                                        if (typeof window.addSection === 'function') {
                                            window.addSection();
                                        } else {
                                            // Fallback: do it inline
                                            console.log('addSection not found, using inline code');
                                            const template = document.getElementById('sectionTemplate');
                                            const container = document.getElementById('courseSections');
                                            
                                            if (!template || !container) {
                                                alert('Required elements not found. Please refresh the page.');
                                                return;
                                            }
                                            
                                            const clone = template.content.cloneNode(true);
                                            const sectionElement = clone.querySelector('.section-item');
                                            if (!sectionElement) return;
                                            
                                            window.sectionCounter = (window.sectionCounter || 0) + 1;
                                            const idx = 'new_' + window.sectionCounter;
                                            sectionElement.dataset.sectionId = idx;
                                            
                                            const titleInput = sectionElement.querySelector('.section-title');
                                            const titleArInput = sectionElement.querySelector('.section-title-ar');
                                            const descInput = sectionElement.querySelector('.section-description');
                                            const descArInput = sectionElement.querySelector('.section-description-ar');
                                            
                                            if (titleInput) titleInput.name = `sections[${idx}][title]`;
                                            if (titleArInput) titleArInput.name = `sections[${idx}][title_ar]`;
                                            if (descInput) descInput.name = `sections[${idx}][description]`;
                                            if (descArInput) descArInput.name = `sections[${idx}][description_ar]`;
                                            
                                            container.appendChild(clone);
                                            
                                            const addedSection = container.querySelector(`[data-section-id="${idx}"]`);
                                            if (addedSection) {
                                                const removeBtn = addedSection.querySelector('.remove-section');
                                                if (removeBtn) {
                                                    removeBtn.addEventListener('click', function() {
                                                        addedSection.remove();
                                                    });
                                                }
                                            }
                                            
                                            console.log('Section added via inline code');
                                        }
                                    });
                                }
                            })();
                        </script>
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
                                                                            placeholder="Video URL (YouTube, Vimeo, Google Drive)"
                                                                            {{ $lecture->content_type == 'video' ? 'required' : '' }}>
                                                                        <small class="text-muted d-block mt-1">Supports YouTube, Vimeo, and Google Drive share links</small>
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
                        <script>
                            // Define function and attach event immediately after button
                            (function() {
                                const btn = document.getElementById('addLearningObjective');
                                if (btn) {
                                    btn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        
                                        if (typeof window.addLearningObjective === 'function') {
                                            window.addLearningObjective();
                                        } else {
                                            // Fallback: do it inline
                                            console.log('addLearningObjective not found, using inline code');
                                            const container = document.getElementById('learningObjectives');
                                            if (!container) {
                                                alert('Learning objectives container not found.');
                                                return;
                                            }
                                            
                                            window.learningObjectiveCounter = (window.learningObjectiveCounter || {{ $course->what_to_learn ? count($course->what_to_learn) : 0 }}) + 1;
                                            const index = window.learningObjectiveCounter - 1;
                                            
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
                                            
                                            // Remove learning objective event
                                            const removeBtn = objectiveDiv.querySelector('.remove-learning-objective');
                                            if (removeBtn) {
                                                removeBtn.addEventListener('click', function() {
                                                    objectiveDiv.remove();
                                                });
                                            }
                                            
                                            console.log('Learning objective added via inline code');
                                        }
                                    });
                                }
                            })();
                        </script>

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
                        <script>
                            // Define function and attach event immediately after button
                            (function() {
                                const btn = document.getElementById('addFaqItem');
                                if (btn) {
                                    btn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        
                                        if (typeof window.addFaqItem === 'function') {
                                            window.addFaqItem();
                                        } else {
                                            // Fallback: do it inline
                                            console.log('addFaqItem not found, using inline code');
                                            const container = document.getElementById('faqItems');
                                            if (!container) {
                                                alert('FAQ items container not found.');
                                                return;
                                            }
                                            
                                            window.faqItemCounter = (window.faqItemCounter || {{ $course->faq_course ? count($course->faq_course) : 0 }}) + 1;
                                            const index = window.faqItemCounter - 1;
                                            
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
                                            
                                            // Remove FAQ event
                                            const removeBtn = faqDiv.querySelector('.remove-faq-item');
                                            if (removeBtn) {
                                                removeBtn.addEventListener('click', function() {
                                                    faqDiv.remove();
                                                });
                                            }
                                            
                                            console.log('FAQ item added via inline code');
                                        }
                                    });
                                }
                            })();
                        </script>

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
                        <label for="courseImage" class="image-upload-area" id="imageUploadArea" style="display: block; cursor: pointer; user-select: none;">
                            @if ($course->image_url)
                                <img src="{{ $course->image_url }}" alt="Course Image"
                                    class="img-fluid rounded mb-3 max-h-200">
                            @else
                                <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Drag and drop an image here or click to select</p>
                                <p class="text-muted small">Recommended size: 800x600px</p>
                            @endif
                            <input type="file" class="d-none" id="courseImage" name="image" accept="image/*">
                        </label>
                        <div id="imagePreview" class="mt-3 d-none-initially">
                            <img id="previewImg" class="img-fluid rounded max-h-200">
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
                            @error('book')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @if ($course->book_url)
                                <div class="mt-2">
                                    <small class="text-muted d-block">Current book: {{ basename($course->book_url) }}</small>
                                    <a href="{{ $course->book_url }}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fa fa-download me-1"></i>Download current book
                                    </a>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="1" id="removeBook"
                                            name="remove_book">
                                        <label class="form-check-label" for="removeBook">
                                            Remove current book
                                        </label>
                                    </div>
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
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_certificate" name="enable_certificate"
                                    value="1" {{ old('enable_certificate', $course->enable_certificate) ? 'checked' : '' }}>
                                <label class="form-check-label" for="enable_certificate">
                                    Enable Certificate
                                </label>
                                <small class="form-text text-muted d-block">
                                    Allow students to receive a certificate upon course completion
                                </small>
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
                                name="lectures[new][video_url]" placeholder="Video URL (YouTube, Vimeo, Google Drive)" required>
                            <small class="text-muted d-block mt-1">Supports YouTube, Vimeo, and Google Drive share links</small>
                        </div>
                        <div class="lecture-upload-input d-none-initially">
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let sectionCounter = 0;
            let lectureCounter = 0;

            // Discount pricing preview functionality
            const originalPriceInput = document.getElementById('original_price');
            const priceInput = document.getElementById('price');
            const discountPreview = document.getElementById('discount_preview');
            const discountPreviewText = document.getElementById('discount_preview_text');

            function updateDiscountPreview() {
                const originalPrice = parseFloat(originalPriceInput.value) || 0;
                const discountPrice = parseFloat(priceInput.value) || 0;

                if (originalPrice > 0 && discountPrice > 0 && originalPrice > discountPrice) {
                    const discountPercent = Math.round(((originalPrice - discountPrice) / originalPrice) * 100);
                    const savings = (originalPrice - discountPrice).toFixed(2);
                    discountPreviewText.innerHTML = `<del class="text-muted">${originalPrice.toFixed(2)} SAR</del> → <strong class="text-success">${discountPrice.toFixed(2)} SAR</strong> (${discountPercent}% off - Save ${savings} SAR)`;
                    discountPreview.style.display = 'block';
                } else if (discountPrice === 0) {
                    discountPreviewText.innerHTML = '<strong class="text-success">Free Course</strong>';
                    discountPreview.style.display = 'block';
                } else {
                    discountPreview.style.display = 'none';
                }
            }

            originalPriceInput.addEventListener('input', updateDiscountPreview);
            priceInput.addEventListener('input', updateDiscountPreview);
            updateDiscountPreview(); // Initial check

            // Image upload functionality
            const imageUploadArea = document.getElementById('imageUploadArea');
            const courseImage = document.getElementById('courseImage');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImage');

            if (!imageUploadArea || !courseImage) {
                console.error('Image upload elements not found!', {
                    imageUploadArea: !!imageUploadArea,
                    courseImage: !!courseImage
                });
            } else {
                console.log('Image upload elements found successfully');
                
                // Label element will handle click automatically, but we still need drag and drop
                // Prevent label's default behavior if clicking on preview/remove
                imageUploadArea.addEventListener('click', function(e) {
                    // Don't trigger if clicking on preview or remove button
                    if (e.target.closest('#imagePreview') || e.target.id === 'removeImage') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            }

            if (imageUploadArea) {
                // Prevent default drag behavior on the label
                imageUploadArea.addEventListener('dragenter', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    imageUploadArea.classList.add('dragover');
                });

                imageUploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.dataTransfer.dropEffect = 'copy';
                    imageUploadArea.classList.add('dragover');
                });

                imageUploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Only remove dragover if we're actually leaving the upload area
                    if (!imageUploadArea.contains(e.relatedTarget)) {
                        imageUploadArea.classList.remove('dragover');
                    }
                });

                imageUploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    imageUploadArea.classList.remove('dragover');
                    
                    const files = e.dataTransfer.files;
                    console.log('Files dropped:', files.length);
                    
                    if (files.length > 0 && courseImage) {
                        const file = files[0];
                        // Validate it's an image
                        if (file.type.startsWith('image/')) {
                            // Create a new FileList using DataTransfer
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            courseImage.files = dataTransfer.files;
                            // Trigger change event manually
                            const changeEvent = new Event('change', { bubbles: true });
                            courseImage.dispatchEvent(changeEvent);
                            handleImagePreview(file);
                        } else {
                            alert('Please drop an image file');
                        }
                    }
                });
            }

            if (courseImage) {
                courseImage.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        handleImagePreview(file);
                    }
                });
            }

            function handleImagePreview(file) {
                if (!previewImg || !imagePreview || !imageUploadArea) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imageUploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function() {
                    if (courseImage) courseImage.value = '';
                    if (imagePreview) imagePreview.style.display = 'none';
                    if (imageUploadArea) imageUploadArea.style.display = 'block';
                });
            }

            // Section management
            const addSectionBtn = document.getElementById('addSection');
            const courseSections = document.getElementById('courseSections');
            const sectionTemplate = document.getElementById('sectionTemplate');

            if (addSectionBtn && courseSections && sectionTemplate) {
                addSectionBtn.addEventListener('click', function() {
                    const sectionClone = sectionTemplate.content.cloneNode(true);
                    const sectionElement = sectionClone.querySelector('.section-item');
                    
                    if (!sectionElement) {
                        console.error('Section element not found in template');
                        return;
                    }
                    
                    const sectionId = 'new_' + Date.now();
                    sectionElement.dataset.sectionId = sectionId;

                    // Update input names for new section
                    const titleInput = sectionElement.querySelector('.section-title');
                    const titleArInput = sectionElement.querySelector('.section-title-ar');
                    const descInput = sectionElement.querySelector('.section-description');
                    const descArInput = sectionElement.querySelector('.section-description-ar');
                    
                    if (titleInput) titleInput.name = `sections[${sectionId}][title]`;
                    if (titleArInput) titleArInput.name = `sections[${sectionId}][title_ar]`;
                    if (descInput) descInput.name = `sections[${sectionId}][description]`;
                    if (descArInput) descArInput.name = `sections[${sectionId}][description_ar]`;

                    // Append to DOM first
                    courseSections.appendChild(sectionClone);
                    
                    // Get the actual element from DOM for event listeners
                    const addedSection = courseSections.querySelector(`[data-section-id="${sectionId}"]`);
                    if (addedSection) {
                        // Add event listeners
                        const addLectureBtn = addedSection.querySelector('.add-lecture');
                        const removeSectionBtn = addedSection.querySelector('.remove-section');
                        
                        if (addLectureBtn) {
                            addLectureBtn.addEventListener('click', function() {
                                addLecture(addedSection);
                            });
                        }
                        
                        if (removeSectionBtn) {
                            removeSectionBtn.addEventListener('click', function() {
                                addedSection.remove();
                            });
                        }
                    }
                });
            } else {
                console.error('Section management elements not found:', {
                    addSectionBtn: !!addSectionBtn,
                    courseSections: !!courseSections,
                    sectionTemplate: !!sectionTemplate
                });
            }

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

            const addLearningObjectiveBtn = document.getElementById('addLearningObjective');
            if (addLearningObjectiveBtn) {
                addLearningObjectiveBtn.addEventListener('click', function() {
                    addLearningObjective();
                });
            } else {
                console.error('Add Learning Objective button not found');
            }

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

            const addFaqItemBtn = document.getElementById('addFaqItem');
            if (addFaqItemBtn) {
                addFaqItemBtn.addEventListener('click', function() {
                    addFaqItem();
                });
            } else {
                console.error('Add FAQ Item button not found');
            }

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
            let instructorDropdownInstance = null;

            // Initialize with existing instructors
            document.querySelectorAll('.instructor-tag').forEach(tag => {
                selectedInstructors.add(tag.dataset.id);
            });
            
            // selectInstructor is already defined above, outside DOMContentLoaded
            
            // Initialize Bootstrap dropdown instance
            const instructorDropdown = document.getElementById('instructorDropdown');
            const instructorDropdownMenu = document.getElementById('instructorDropdownMenu');
            
            // Let Bootstrap handle the dropdown naturally, but prevent closing on menu clicks
            // Don't interfere with Bootstrap's default behavior for opening

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

            // Additional event handlers as backup (inline onclick should work, but this is backup)
            document.addEventListener('click', function(e) {
                const option = e.target.closest('.instructor-option');
                if (option && option.dataset.id && option.dataset.name) {
                    const id = option.dataset.id;
                    const name = option.dataset.name;
                    
                    if (!selectedInstructors.has(id)) {
                        e.preventDefault();
                        e.stopPropagation();
                        selectedInstructors.add(id);
                        addInstructorTag(id, name);
                        option.classList.add('selected');
                        updateRequiredValidation();
                    }
                }
            }, true); // Use capture phase
            
            // Prevent Bootstrap dropdown from closing when clicking inside menu
            if (instructorDropdown) {
                instructorDropdown.addEventListener('hide.bs.dropdown', function(e) {
                    const clickEvent = e.clickEvent;
                    if (clickEvent) {
                        const target = clickEvent.target;
                        if (target && instructorDropdownMenu && instructorDropdownMenu.contains(target)) {
                            e.preventDefault();
                        }
                    }
                });
            }

            function addInstructorTag(id, name) {
                if (!selectedInstructorsContainer) {
                    return;
                }
                
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

            // removeInstructor is already defined above, outside DOMContentLoaded
            // But we can update the selectedInstructors Set here if needed
            const originalRemoveInstructor = window.removeInstructor;
            window.removeInstructor = function(id) {
                // Call the original function
                originalRemoveInstructor(id);
                
                // Update the Set
                if (selectedInstructors) {
                    selectedInstructors.delete(id.toString());
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

