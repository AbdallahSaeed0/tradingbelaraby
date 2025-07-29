@extends('admin.layout')

@section('title', 'Create New Course')

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
    </style>
@endpush

@section('content')
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
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Course Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
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
                                <label for="instructor" class="form-label">Instructor <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="instructor" name="instructor_id" required>
                                    <option value="">Select Instructor</option>
                                    @foreach ($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01"
                                    min="0" value="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration"
                                    placeholder="e.g., 8 weeks, 40 hours">
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
                            <!-- Sections will be added here dynamically -->
                        </div>
                    </div>

                    <!-- What You'll Learn -->
                    <div class="form-section">
                        <div class="section-header">
                            <h5><i class="fa fa-graduation-cap me-2"></i>What You'll Learn</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary add-learn-item">
                                    <i class="fa fa-plus me-1"></i>Add Learning Item
                                </button>
                            </div>
                            <div id="learnItems">
                                <!-- Learning items will be added here -->
                            </div>
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
                                <input class="form-check-input" type="checkbox" id="featured" name="featured">
                                <label class="form-check-label" for="featured">
                                    Featured Course
                                </label>
                            </div>
                        </div>



                    </div>
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
                <input type="text" class="form-control section-title" placeholder="Section Title" required>
            </div>
            <div class="mb-3">
                <textarea class="form-control section-description" placeholder="Section Description" rows="2"></textarea>
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
                        <input type="text" class="form-control form-control-sm lecture-title mb-1"
                            placeholder="Lecture Title" required>
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
                    <textarea class="form-control form-control-sm lecture-description" placeholder="Lecture Description (Optional)"
                        rows="2"></textarea>
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
                                placeholder="Video URL (YouTube, Vimeo, etc.)" required>
                        </div>
                        <div class="lecture-upload-input" style="display: none;">
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
                if (e.target.files.length > 0) {
                    handleImagePreview(e.target.files[0]);
                }
            });

            removeImageBtn.addEventListener('click', () => {
                courseImage.value = '';
                imagePreview.style.display = 'none';
                imageUploadArea.style.display = 'block';
            });

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
            document.getElementById('addSection').addEventListener('click', addSection);

            function addSection() {
                const template = document.getElementById('sectionTemplate');
                const clone = template.content.cloneNode(true);
                const sectionElement = clone.querySelector('.section-item');

                sectionCounter++;
                sectionElement.dataset.sectionId = sectionCounter;

                // Add event listeners
                clone.querySelector('.add-lecture').addEventListener('click', () => addLecture(sectionElement));
                clone.querySelector('.remove-section').addEventListener('click', () => sectionElement.remove());

                document.getElementById('courseSections').appendChild(clone);
            }

            function addLecture(sectionElement) {
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
            addSection();

            // What You'll Learn management
            document.querySelector('.add-learn-item').addEventListener('click', addLearnItem);

            function addLearnItem() {
                const template = document.getElementById('learnItemTemplate');
                const clone = template.content.cloneNode(true);
                const learnItemContainer = clone.querySelector('.d-flex');

                // Add event listeners
                clone.querySelector('.remove-learn-item').addEventListener('click', () => {
                    learnItemContainer.remove();
                });

                document.getElementById('learnItems').appendChild(clone);
            }

            // Add initial learning item
            addLearnItem();

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

                // Log specific learning items
                console.log('Learning items array:', learnItems);
                console.log('Learning items JSON:', JSON.stringify(learnItems));

                // Collect sections and lectures data
                const sections = [];
                let lectureFileIndex = 0;

                document.querySelectorAll('.section-item').forEach(sectionEl => {
                    const lectures = [];
                    sectionEl.querySelectorAll('.lecture-item').forEach(lectureEl => {
                        const lectureType = lectureEl.querySelector('.lecture-type').value;
                        const lectureData = {
                            title: lectureEl.querySelector('.lecture-title').value,
                            description: lectureEl.querySelector('.lecture-description')
                                .value,
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
                        description: sectionEl.querySelector('.section-description').value,
                        lectures: lectures
                    });
                });

                // Collect learn items data
                const learnItems = [];
                const learnItemElements = document.querySelectorAll('.learn-item-title');
                console.log('Found learn item elements:', learnItemElements.length);

                learnItemElements.forEach((itemEl, index) => {
                    console.log(`Learn item ${index}:`, itemEl.value);
                    if (itemEl.value.trim()) {
                        learnItems.push(itemEl.value.trim());
                    }
                });

                console.log('Final learn items array:', learnItems);

                // Add sections and learn items data to FormData
                formData.append('sections', JSON.stringify(sections));
                formData.append('learn_items', JSON.stringify(learnItems));

                // Also add learning items as individual form fields
                learnItems.forEach((item, index) => {
                    formData.append(`learning_objectives[${index}]`, item);
                });

                // Debug: Log the data being sent
                console.log('Learning items being sent:', learnItems);
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
                                    window.location.href = '{{ route('admin.courses.index') }}';
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
