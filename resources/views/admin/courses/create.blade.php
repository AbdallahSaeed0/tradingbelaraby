@extends('admin.layout')

@section('title', 'Create New Course')

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
                tag.innerHTML = '<span>' + name +
                    '</span><i class="fa fa-times remove-instructor" onclick="removeInstructor(' + id +
                    ')"></i><input type="hidden" name="instructor_ids[]" value="' + id + '">';
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
        window.sectionCounter = 0;
        
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
            const sectionIndex = window.sectionCounter;
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
        
        console.log('addSection function defined:', typeof window.addSection);
        
        // Define addLearningObjective function globally
        window.learningObjectiveCounter = 0;
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
        window.faqItemCounter = 0;
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
        
        console.log('All functions defined:', {
            addSection: typeof window.addSection,
            addLearningObjective: typeof window.addLearningObjective,
            addFaqItem: typeof window.addFaqItem
        });
    </script>

    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Create New Course</h1>
                        <p class="text-muted">Build a comprehensive course with sections and lectures</p>
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

                            <!-- Meetings Requirements (Multilingual) -->
                            <div class="col-md-12 mb-3">
                                @include('admin.courses.partials.multilingual-fields', [
                                    'fieldName' => 'requirements',
                                    'label' => 'Meetings Requirements',
                                    'type' => 'textarea',
                                    'required' => false,
                                    'rows' => 3,
                                    'placeholder' => 'Enter course requirements',
                                    'value' => old('requirements'),
                                    'valueAr' => old('requirements_ar'),
                                ])
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="instructors" class="form-label">Instructors <span
                                        class="text-danger">*</span></label>
                                <div class="instructor-selector">
                                    <div class="selected-instructors mb-2" id="selectedInstructors">
                                        <!-- Selected instructors will appear here as tags -->
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
                                                    <a class="dropdown-item instructor-option" href="javascript:void(0)"
                                                        data-id="{{ $instructor->id }}" data-name="{{ $instructor->name }}"
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
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="original_price" class="form-label">Original Price (SAR)</label>
                                <input type="number" class="form-control" id="original_price" name="original_price"
                                    step="0.01" min="0" value="{{ old('original_price', 0) }}"
                                    placeholder="0.00">
                                <small class="text-muted">Main price before any discount</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Discount Price (SAR) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01"
                                    min="0" value="{{ old('price', 0) }}" required>
                                <small class="text-muted">Current selling price (set to 0 for free course)</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration"
                                    value="{{ old('duration') }}" placeholder="e.g., 8 weeks, 40 hours">
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
                                    value="{{ old('preview_video_url') }}"
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
                                                    const idx = window.sectionCounter;
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
                            </div>
                        </div>
                        <div id="courseSections">
                            <!-- Sections will be added here dynamically -->
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
                                            
                                            window.learningObjectiveCounter = (window.learningObjectiveCounter || 0) + 1;
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
                            <!-- Learning objectives will be added here -->
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
                                            
                                            window.faqItemCounter = (window.faqItemCounter || 0) + 1;
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
                            <!-- FAQ items will be added here -->
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
                            <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-2">Drag and drop an image here or click to select</p>
                            <p class="text-muted small">Recommended size: 800x600px</p>
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
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                    value="1">
                                <label class="form-check-label" for="is_featured">
                                    Featured Course
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free"
                                    value="1">
                                <label class="form-check-label" for="is_free">
                                    Free Course
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enable_certificate" name="enable_certificate"
                                    value="1">
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
                                    name="show_in_top_discounted" value="1">
                                <label class="form-check-label" for="show_in_top_discounted">
                                    Top Discounted Courses
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_subscription_bundles"
                                    name="show_in_subscription_bundles" value="1">
                                <label class="form-check-label" for="show_in_subscription_bundles">
                                    Subscription Bundles
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_live_meeting"
                                    name="show_in_live_meeting" value="1">
                                <label class="form-check-label" for="show_in_live_meeting">
                                    Live Meeting
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_recent_courses"
                                    name="show_in_recent_courses" value="1">
                                <label class="form-check-label" for="show_in_recent_courses">
                                    Recent Courses
                                </label>
                            </div>
                            <small class="text-muted">Select one or more sections to display this course on the
                                homepage</small>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    @include('admin.courses.partials.seo-fields')
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
                        <input type="text" class="form-control section-title" placeholder="Section Title (English)"
                            required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control section-title-ar" placeholder="عنوان القسم (العربية)"
                            dir="rtl">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Section Description</label>
                <div class="row">
                    <div class="col-md-6">
                        <textarea class="form-control section-description" placeholder="Section Description (English)" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <textarea class="form-control section-description-ar" placeholder="وصف القسم (العربية)" rows="2"
                            dir="rtl"></textarea>
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
                                    placeholder="Lecture Title (English)" required>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm lecture-title-ar mb-1"
                                    placeholder="عنوان المحاضرة (العربية)" dir="rtl">
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
                            <textarea class="form-control form-control-sm lecture-description" placeholder="Lecture Description (Optional)"
                                rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control form-control-sm lecture-description-ar" placeholder="وصف المحاضرة (اختياري)"
                                rows="2" dir="rtl"></textarea>
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
                                placeholder="Video URL (YouTube, Vimeo, Google Drive)" required>
                            <small class="text-muted d-block mt-1">Supports YouTube, Vimeo, and Google Drive share
                                links</small>
                        </div>
                        <div class="lecture-upload-input d-none-initially">
                            <input type="file" class="form-control form-control-sm lecture-file"
                                accept="video/*,audio/*,.pdf,.doc,.docx,.ppt,.pptx">
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <label class="form-label small text-muted">Lecture Book (Optional)</label>
                        <input type="file" class="form-control form-control-sm lecture-book" accept=".pdf">
                        <div class="form-text small">Upload a book/material specific to this lecture (PDF format)</div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Learn Item Template -->
    <template id="learnItemTemplate">
        <div class="d-flex align-items-center mb-2">
            <i class="fa fa-check-circle me-2 text-success"></i>
            <input type="text" class="form-control form-control-sm learn-item-title" placeholder="Learning Item Title"
                required>
            <button type="button" class="btn btn-sm btn-outline-danger remove-learn-item ms-2">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </template>
@endsection


@push('scripts')
    <script>
        console.log('Course form script loading...');
        
        // Define variables and functions in global scope so they're available immediately
        let sectionCounter = 0;
        let lectureCounter = 0;
        
        // Define addSection function globally BEFORE DOMContentLoaded
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
            
            console.log('Cloning template...');
            const clone = template.content.cloneNode(true);
            const sectionElement = clone.querySelector('.section-item');
            
            if (!sectionElement) {
                console.error('Section element not found in template');
                alert('Section element not found in template.');
                return;
            }

            sectionCounter++;
            const sectionIndex = sectionCounter;
            sectionElement.dataset.sectionId = sectionIndex;
            console.log('Created section with index:', sectionIndex);

            // Set proper names for section inputs
            const titleInput = sectionElement.querySelector('.section-title');
            const titleArInput = sectionElement.querySelector('.section-title-ar');
            const descInput = sectionElement.querySelector('.section-description');
            const descArInput = sectionElement.querySelector('.section-description-ar');
            
            if (titleInput) titleInput.name = `sections[${sectionIndex}][title]`;
            if (titleArInput) titleArInput.name = `sections[${sectionIndex}][title_ar]`;
            if (descInput) descInput.name = `sections[${sectionIndex}][description]`;
            if (descArInput) descArInput.name = `sections[${sectionIndex}][description_ar]`;

            // Append to DOM first
            console.log('Appending section to DOM...');
            container.appendChild(clone);
            
            // Now get the actual element from DOM for event listeners
            const addedSection = container.querySelector(`[data-section-id="${sectionIndex}"]`);
            if (!addedSection) {
                console.error('Added section not found in DOM');
                alert('Failed to add section. Please try again.');
                return;
            }

            console.log('Section added successfully, setting up event listeners...');
            // Add event listeners using the actual DOM element
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
            
            console.log('Section added successfully!');
        };
        
        console.log('addSection function defined:', typeof window.addSection);
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired');

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
                    discountPreviewText.innerHTML =
                        `<del class="text-muted">${originalPrice.toFixed(2)} SAR</del> → <strong class="text-success">${discountPrice.toFixed(2)} SAR</strong> (${discountPercent}% off - Save ${savings} SAR)`;
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
                    if (e.target.files.length > 0) {
                        handleImagePreview(e.target.files[0]);
                    }
                });
            }

            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', () => {
                    if (courseImage) courseImage.value = '';
                    if (imagePreview) imagePreview.style.display = 'none';
                    if (imageUploadArea) imageUploadArea.style.display = 'block';
                });
            }

            function handleImagePreview(file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    imageUploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            // Section management
            console.log('Setting up section management...');
            const addSectionBtn = document.getElementById('addSection');
            console.log('Add Section button:', addSectionBtn);
            
            if (addSectionBtn) {
                console.log('Adding click event listener to Add Section button');
                addSectionBtn.addEventListener('click', function(e) {
                    console.log('Add Section button clicked!', e);
                    e.preventDefault();
                    e.stopPropagation();
                    window.addSection();
                });
            } else {
                console.error('Add Section button not found');
            }

            // Make addLecture globally accessible
            window.addLecture = function(sectionElement) {
                const template = document.getElementById('lectureTemplate');
                const clone = template.content.cloneNode(true);
                const lectureElement = clone.querySelector('.lecture-item');

                lectureCounter++;
                lectureElement.dataset.lectureId = lectureCounter;

                // Add event listeners
                clone.querySelector('.remove-lecture').addEventListener('click', () => lectureElement.remove());

                // Handle lecture type switching
                const typeSelect = clone.querySelector('.lecture-type');
                const urlInput = clone.querySelector('.lecture-url-input');
                const uploadInput = clone.querySelector('.lecture-upload-input');

                typeSelect.addEventListener('change', function() {
                    if (this.value === 'url') {
                        urlInput.style.display = 'block';
                        uploadInput.style.display = 'none';
                        urlInput.querySelector('.lecture-link').required = true;
                        uploadInput.querySelector('.lecture-file').required = false;
                    } else {
                        urlInput.style.display = 'none';
                        uploadInput.style.display = 'block';
                        urlInput.querySelector('.lecture-link').required = false;
                        uploadInput.querySelector('.lecture-file').required = true;
                    }
                });

                sectionElement.querySelector('.lectures-container').appendChild(clone);
            }

            // Add initial section
            window.addSection();

            // Instructor Selector functionality
            const selectedInstructorsContainer = document.getElementById('selectedInstructors');
            const selectedInstructors = new Set();
            let instructorDropdownInstance = null;

            // Initialize selected instructors from existing tags
            document.querySelectorAll('#selectedInstructors .instructor-tag').forEach(tag => {
                const id = tag.dataset.id;
                if (id) {
                    selectedInstructors.add(id);
                }
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
            
            // Global event delegation for remove buttons
            document.addEventListener('click', function(e) {
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
            toggleSelectedContainer();

            // Free Course functionality
            const isFreeCheckbox = document.getElementById('is_free');
            const priceInput = document.getElementById('price');

            function togglePriceInput() {
                if (isFreeCheckbox.checked) {
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
            isFreeCheckbox.addEventListener('change', togglePriceInput);

            // Learning Objectives management
            let learningObjectiveCounter = 0;

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

                // Remove learning objective event
                objectiveDiv.querySelector('.remove-learning-objective').addEventListener('click', function() {
                    objectiveDiv.remove();
                });
            }

            // FAQ Items management
            let faqItemCounter = 0;

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

                // Remove FAQ event
                faqDiv.querySelector('.remove-faq-item').addEventListener('click', function() {
                    faqDiv.remove();
                });
            }

            // Form submission
            document.getElementById('courseForm').addEventListener('submit', function(e) {
                e.preventDefault();

                console.log('Form submission started');

                // Create FormData to handle file uploads
                const formData = new FormData(this);

                // Log form data for debugging
                console.log('Form data entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                // Log specific learning items (will be collected below)
                // console.log('Learning items array:', learnItems);
                // console.log('Learning items JSON:', JSON.stringify(learnItems));

                // Collect sections and lectures data
                const sections = [];
                let lectureFileIndex = 0;

                document.querySelectorAll('.section-item').forEach(sectionEl => {
                    const lectures = [];
                    sectionEl.querySelectorAll('.lecture-item').forEach(lectureEl => {
                        const lectureType = lectureEl.querySelector('.lecture-type').value;
                        const lectureData = {
                            title: lectureEl.querySelector('.lecture-title').value,
                            title_ar: lectureEl.querySelector('.lecture-title-ar')
                                .value,
                            description: lectureEl.querySelector('.lecture-description')
                                .value,
                            description_ar: lectureEl.querySelector(
                                '.lecture-description-ar').value,
                            type: lectureType
                        };

                        if (lectureType === 'url') {
                            lectureData.link = lectureEl.querySelector('.lecture-link')
                                .value;
                        } else {
                            // For file uploads, add file to FormData
                            const fileInput = lectureEl.querySelector('.lecture-file');
                            if (fileInput.files.length > 0) {
                                const fileKey = `lecture_file_${lectureFileIndex}`;
                                formData.append(fileKey, fileInput.files[0]);
                                lectureData.fileKey = fileKey;
                                lectureFileIndex++;
                            }
                        }

                        // Handle lecture book upload
                        const bookInput = lectureEl.querySelector('.lecture-book');
                        if (bookInput && bookInput.files.length > 0) {
                            const bookKey = `lecture_book_${lectureFileIndex}`;
                            formData.append(bookKey, bookInput.files[0]);
                            lectureData.bookKey = bookKey;
                            lectureFileIndex++;
                        }

                        lectures.push(lectureData);
                    });

                    sections.push({
                        title: sectionEl.querySelector('.section-title').value,
                        title_ar: sectionEl.querySelector('.section-title-ar').value,
                        description: sectionEl.querySelector('.section-description').value,
                        description_ar: sectionEl.querySelector('.section-description-ar')
                            .value,
                        lectures: lectures
                    });
                });

                // Collect learning objectives data
                const whatToLearn = [];
                const whatToLearnAr = [];

                document.querySelectorAll('input[name="what_to_learn[]"]').forEach((input, index) => {
                    if (input.value.trim()) {
                        whatToLearn.push(input.value.trim());
                    }
                });

                document.querySelectorAll('input[name="what_to_learn_ar[]"]').forEach((input, index) => {
                    if (input.value.trim()) {
                        whatToLearnAr.push(input.value.trim());
                    }
                });

                console.log('Learning objectives:', {
                    whatToLearn,
                    whatToLearnAr
                });


                // Add sections and learn items data to FormData
                formData.append('sections', JSON.stringify(sections));
                // Add learning objectives as arrays
                whatToLearn.forEach((item, index) => {
                    formData.append(`what_to_learn[${index}]`, item);
                });

                whatToLearnAr.forEach((item, index) => {
                    formData.append(`what_to_learn_ar[${index}]`, item);
                });

                // Debug: Log the data being sent
                console.log('Learning objectives being sent:', {
                    whatToLearn,
                    whatToLearnAr
                });
                console.log('Sections being sent:', sections);

                // Submit form via fetch
                fetch('{{ route('admin.courses.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            // Check if response is a redirect
                            if (response.redirected) {
                                showToast('Course created successfully!', 'success');
                                setTimeout(() => {
                                    window.location.href = response.url;
                                }, 1500);
                            } else {
                                showToast('Course created successfully!', 'success');
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('admin.courses.index') }}';
                                }, 1500);
                            }
                        } else {
                            return response.json().then(data => {
                                console.error('Server response:', data);
                                showToast(data.error ||
                                    'Error creating course. Please check the form and try again.',
                                    'error');
                            }).catch(() => {
                                showToast(
                                    'Error creating course. Please check the form and try again.',
                                    'error');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Network error. Please try again.', 'error');
                    });
            });
        });

        // Toast notification system
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    max-width: 350px;
                `;
                document.body.appendChild(toastContainer);
            }

            // Create toast element
            const toast = document.createElement('div');
            toast.className =
                `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 show`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.style.cssText = `
                margin-bottom: 10px;
                animation: slideIn 0.3s ease-out;
            `;

            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            toastContainer.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);

            // Add close button functionality
            const closeBtn = toast.querySelector('.btn-close');
            closeBtn.addEventListener('click', () => {
                toast.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            });
        }
    </script>
@endpush
