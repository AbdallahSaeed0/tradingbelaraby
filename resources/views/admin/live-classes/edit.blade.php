@extends('admin.layout')

@section('title', 'Edit Live Class')

@push('styles')
    <style>
        .form-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .form-section h5 {
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .material-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .material-type-selector {
            margin-bottom: 10px;
        }

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: #007bff;
            background: #e3f2fd;
        }

        .file-upload-area.dragover {
            border-color: #007bff;
            background: #e3f2fd;
        }

        .status-display {
            background: #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
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
                        <h1 class="h3 mb-0">Edit Live Class</h1>
                        <p class="text-muted">Update live class information</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Live Classes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.live-classes.update', $liveClass) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Current Status Display -->
                            <div class="status-display">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Current Status:</strong>
                                        <span
                                            class="badge bg-{{ $liveClass->status == 'scheduled' ? 'primary' : ($liveClass->status == 'live' ? 'success' : ($liveClass->status == 'completed' ? 'secondary' : 'danger')) }}">
                                            {{ ucfirst($liveClass->status) }}
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Current Participants:</strong> {{ $liveClass->current_participants }}
                                        @if ($liveClass->max_participants)
                                            / {{ $liveClass->max_participants }}
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Created:</strong> {{ $liveClass->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Last Updated:</strong> {{ $liveClass->updated_at->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="form-section">
                                <h5><i class="fa fa-info-circle me-2"></i>Basic Information</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="name" class="form-label">Class Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $liveClass->name) }}"
                                                required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="course_id" class="form-label">Course <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('course_id') is-invalid @enderror"
                                                id="course_id" name="course_id" required>
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}"
                                                        {{ old('course_id', $liveClass->course_id) == $course->id ? 'selected' : '' }}>
                                                        {{ $course->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('course_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Enter class description...">{{ old('description', $liveClass->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="link" class="form-label">Meeting Link <span
                                            class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('link') is-invalid @enderror"
                                        id="link" name="link" value="{{ old('link', $liveClass->link) }}"
                                        placeholder="https://zoom.us/j/..." required>
                                    @error('link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="instructions" class="form-label">Pre-class Instructions</label>
                                    <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions"
                                        rows="3" placeholder="Enter instructions for students...">{{ old('instructions', $liveClass->instructions) }}</textarea>
                                    @error('instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="recording_url" class="form-label">Recording URL (after class)</label>
                                    <input type="url" class="form-control @error('recording_url') is-invalid @enderror"
                                        id="recording_url" name="recording_url"
                                        value="{{ old('recording_url', $liveClass->recording_url) }}"
                                        placeholder="https://...">
                                    @error('recording_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Scheduling & Settings -->
                            <div class="form-section">
                                <h5><i class="fa fa-calendar me-2"></i>Scheduling & Settings</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="instructor_id" class="form-label">Instructor <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('instructor_id') is-invalid @enderror"
                                                id="instructor_id" name="instructor_id" required>
                                                <option value="">Select Instructor</option>
                                                @foreach ($instructors as $instructor)
                                                    <option value="{{ $instructor->id }}"
                                                        {{ old('instructor_id', $liveClass->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                        {{ $instructor->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('instructor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="scheduled_at" class="form-label">Scheduled Date & Time <span
                                                    class="text-danger">*</span></label>
                                            <input type="datetime-local"
                                                class="form-control @error('scheduled_at') is-invalid @enderror"
                                                id="scheduled_at" name="scheduled_at"
                                                value="{{ old('scheduled_at', $liveClass->scheduled_at->format('Y-m-d\TH:i')) }}"
                                                required>
                                            @error('scheduled_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="duration_minutes" class="form-label">Duration (minutes) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('duration_minutes') is-invalid @enderror"
                                                id="duration_minutes" name="duration_minutes"
                                                value="{{ old('duration_minutes', $liveClass->duration_minutes) }}"
                                                min="15" max="480" required>
                                            @error('duration_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="max_participants" class="form-label">Maximum Participants</label>
                                            <input type="number"
                                                class="form-control @error('max_participants') is-invalid @enderror"
                                                id="max_participants" name="max_participants"
                                                value="{{ old('max_participants', $liveClass->max_participants) }}"
                                                min="1" placeholder="No limit">
                                            @error('max_participants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Materials Section -->
                            <div class="form-section">
                                <h5><i class="fa fa-file-alt me-2"></i>Materials</h5>
                                <div id="materials-container">
                                    @if ($liveClass->materials && count($liveClass->materials) > 0)
                                        @foreach ($liveClass->materials as $index => $material)
                                            @php
                                                $isFile =
                                                    is_array($material) &&
                                                    isset($material['type']) &&
                                                    $material['type'] === 'file';
                                                $isLink =
                                                    is_string($material) ||
                                                    (is_array($material) &&
                                                        (!isset($material['type']) || $material['type'] !== 'file'));
                                            @endphp
                                            <div class="material-item">
                                                <div class="material-type-selector">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input material-type" type="radio"
                                                            name="material_types[{{ $index }}]"
                                                            id="type_link_{{ $index }}" value="link"
                                                            {{ $isLink ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="type_link_{{ $index }}">Link</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input material-type" type="radio"
                                                            name="material_types[{{ $index }}]"
                                                            id="type_file_{{ $index }}" value="file"
                                                            {{ $isFile ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="type_file_{{ $index }}">File Upload</label>
                                                    </div>
                                                </div>

                                                <div class="material-link-input"
                                                    {{ $isFile ? 'style=display:none' : '' }}>
                                                    <input type="text" class="form-control" name="materials[]"
                                                        value="{{ $isLink ? (is_string($material) ? $material : '') : '' }}"
                                                        placeholder="Enter material URL or description">
                                                </div>

                                                <div class="material-file-input"
                                                    {{ $isLink ? 'style=display:none' : '' }}>
                                                    <div class="file-upload-area" data-index="{{ $index }}">
                                                        @if ($isFile && isset($material['name']))
                                                            <div class="existing-file mb-2">
                                                                <i class="fa fa-file fa-2x text-primary mb-2"></i>
                                                                <p class="mb-1"><strong>{{ $material['name'] }}</strong>
                                                                </p>
                                                                <small
                                                                    class="text-muted">{{ number_format($material['size'] / 1024, 2) }}
                                                                    KB</small>
                                                            </div>
                                                        @else
                                                            <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                            <p class="mb-1">Drag and drop files here or click to browse
                                                            </p>
                                                        @endif
                                                        <input type="file" class="form-control d-none"
                                                            name="material_files[]">
                                                        <button type="button"
                                                            class="btn btn-outline-primary btn-sm">{{ $isFile ? 'Change File' : 'Choose File' }}</button>
                                                    </div>
                                                    <!-- Hidden input for file materials to preserve data -->
                                                    <input type="hidden" name="materials[]" value="">
                                                </div>

                                                <div class="mt-2">
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm remove-material">
                                                        <i class="fa fa-trash me-1"></i>Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="material-item">
                                            <div class="material-type-selector">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input material-type" type="radio"
                                                        name="material_types[0]" id="type_link_0" value="link" checked>
                                                    <label class="form-check-label" for="type_link_0">Link</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input material-type" type="radio"
                                                        name="material_types[0]" id="type_file_0" value="file">
                                                    <label class="form-check-label" for="type_file_0">File Upload</label>
                                                </div>
                                            </div>

                                            <div class="material-link-input">
                                                <input type="text" class="form-control" name="materials[]"
                                                    placeholder="Enter material URL or description">
                                            </div>

                                            <div class="material-file-input d-none-initially">
                                                <div class="file-upload-area" data-index="0">
                                                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                    <p class="mb-1">Drag and drop files here or click to browse</p>
                                                    <input type="file" class="form-control d-none"
                                                        name="material_files[]">
                                                    <button type="button" class="btn btn-outline-primary btn-sm">Choose
                                                        File</button>
                                                </div>
                                            </div>

                                            <div class="mt-2">
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm remove-material">
                                                    <i class="fa fa-trash me-1"></i>Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-outline-primary" id="add-material">
                                    <i class="fa fa-plus me-2"></i>Add Material
                                </button>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.live-classes.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Update Live Class
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let materialIndex = {{ $liveClass->materials ? count($liveClass->materials) : 0 }};

            // Add material functionality
            document.getElementById('add-material').addEventListener('click', function() {
                const container = document.getElementById('materials-container');
                const materialItem = document.createElement('div');
                materialItem.className = 'material-item';
                materialItem.innerHTML = `
            <div class="material-type-selector">
                <div class="form-check form-check-inline">
                    <input class="form-check-input material-type" type="radio" name="material_types[${materialIndex}]" id="type_link_${materialIndex}" value="link" checked>
                    <label class="form-check-label" for="type_link_${materialIndex}">Link</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input material-type" type="radio" name="material_types[${materialIndex}]" id="type_file_${materialIndex}" value="file">
                    <label class="form-check-label" for="type_file_${materialIndex}">File Upload</label>
                </div>
            </div>

            <div class="material-link-input">
                <input type="text" class="form-control" name="materials[]" placeholder="Enter material URL or description">
            </div>

            <div class="material-file-input d-none-initially">
                <div class="file-upload-area" data-index="${materialIndex}">
                    <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                    <p class="mb-1">Drag and drop files here or click to browse</p>
                    <input type="file" class="form-control d-none" name="material_files[]">
                    <button type="button" class="btn btn-outline-primary btn-sm">Choose File</button>
                </div>
                <!-- Hidden input for file materials to preserve data -->
                <input type="hidden" name="materials[]" value="">
            </div>

            <div class="mt-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-material">
                    <i class="fa fa-trash me-1"></i>Remove
                </button>
            </div>
        `;
                container.appendChild(materialItem);
                materialIndex++;
            });

            // Remove material functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-material') || e.target.closest('.remove-material')) {
                    const materialItem = e.target.closest('.material-item');
                    if (materialItem) {
                        materialItem.remove();
                    }
                }
            });

            // Material type toggle functionality
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('material-type')) {
                    const materialItem = e.target.closest('.material-item');
                    const linkInput = materialItem.querySelector('.material-link-input');
                    const fileInput = materialItem.querySelector('.material-file-input');
                    const linkTextField = linkInput.querySelector('input[type="text"]');
                    const fileField = fileInput.querySelector('input[type="file"]');
                    const uploadArea = fileInput.querySelector('.file-upload-area');

                    if (e.target.value === 'link') {
                        linkInput.style.display = 'block';
                        fileInput.style.display = 'none';
                        // Clear file input when switching to link
                        if (fileField) {
                            fileField.value = '';
                        }
                        // Store existing file info for potential restoration
                        const existingFile = fileInput.querySelector('.existing-file');
                        if (existingFile) {
                            existingFile.setAttribute('data-preserved', 'true');
                            existingFile.style.display = 'none';
                        }
                        // Show default upload area
                        const defaultIcon = uploadArea.querySelector('i.fa-cloud-upload-alt');
                        if (defaultIcon) {
                            defaultIcon.style.display = 'block';
                        }
                        const defaultContent = uploadArea.querySelector('p');
                        if (defaultContent) {
                            defaultContent.textContent = 'Drag and drop files here or click to browse';
                            defaultContent.style.display = 'block';
                        }
                        // Update button text
                        const button = uploadArea.querySelector('button');
                        if (button) {
                            button.textContent = 'Choose File';
                        }
                    } else {
                        linkInput.style.display = 'none';
                        fileInput.style.display = 'block';
                        // Clear link input when switching to file
                        if (linkTextField) {
                            linkTextField.value = '';
                        }
                        // Restore existing file display if it was preserved
                        const preservedFile = fileInput.querySelector('.existing-file[data-preserved="true"]');
                        if (preservedFile) {
                            preservedFile.style.display = 'block';
                            preservedFile.removeAttribute('data-preserved');
                            // Hide default upload area
                            const defaultIcon = uploadArea.querySelector('i.fa-cloud-upload-alt');
                            if (defaultIcon) {
                                defaultIcon.style.display = 'none';
                            }
                            const defaultContent = uploadArea.querySelector('p');
                            if (defaultContent) {
                                defaultContent.style.display = 'none';
                            }
                            // Update button text
                            const button = uploadArea.querySelector('button');
                            if (button) {
                                button.textContent = 'Change File';
                            }
                        } else {
                            // Check if there's an existing file that wasn't preserved (initial load)
                            const existingFile = fileInput.querySelector('.existing-file');
                            if (existingFile && !existingFile.hasAttribute('data-preserved')) {
                                // This is an existing file from initial load, show it
                                existingFile.style.display = 'block';
                                // Hide default upload area
                                const defaultIcon = uploadArea.querySelector('i.fa-cloud-upload-alt');
                                if (defaultIcon) {
                                    defaultIcon.style.display = 'none';
                                }
                                const defaultContent = uploadArea.querySelector('p');
                                if (defaultContent) {
                                    defaultContent.style.display = 'none';
                                }
                                // Update button text
                                const button = uploadArea.querySelector('button');
                                if (button) {
                                    button.textContent = 'Change File';
                                }
                            } else {
                                // Show default upload area if no existing file
                                const defaultIcon = uploadArea.querySelector('i.fa-cloud-upload-alt');
                                if (defaultIcon) {
                                    defaultIcon.style.display = 'block';
                                }
                                const defaultContent = uploadArea.querySelector('p');
                                if (defaultContent) {
                                    defaultContent.textContent = 'Drag and drop files here or click to browse';
                                    defaultContent.style.display = 'block';
                                }
                                // Update button text
                                const button = uploadArea.querySelector('button');
                                if (button) {
                                    button.textContent = 'Choose File';
                                }
                            }
                        }

                        // Ensure proper toggling - if one is none, the other should be block
                        if (linkInput.style.display === 'none') {
                            fileInput.style.display = 'block';
                        }
                        if (fileInput.style.display === 'none') {
                            linkInput.style.display = 'block';
                        }
                    }
                }
            });

            // File upload functionality
            document.addEventListener('click', function(e) {
                if (e.target.closest('.file-upload-area') && e.target.tagName !== 'INPUT') {
                    const fileInput = e.target.closest('.file-upload-area').querySelector('input[type="file"]');
                    fileInput.click();
                }
            });

            // Drag and drop functionality
            document.addEventListener('dragover', function(e) {
                e.preventDefault();
                const uploadArea = e.target.closest('.file-upload-area');
                if (uploadArea) {
                    uploadArea.classList.add('dragover');
                }
            });

            document.addEventListener('dragleave', function(e) {
                const uploadArea = e.target.closest('.file-upload-area');
                if (uploadArea) {
                    uploadArea.classList.remove('dragover');
                }
            });

            document.addEventListener('drop', function(e) {
                e.preventDefault();
                const uploadArea = e.target.closest('.file-upload-area');
                if (uploadArea) {
                    uploadArea.classList.remove('dragover');
                    const fileInput = uploadArea.querySelector('input[type="file"]');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        // Update the display to show selected file
                        const fileName = files[0].name;
                        uploadArea.querySelector('p').textContent = `Selected: ${fileName}`;
                    }
                }
            });
        </script>
    @endpush
@endsection
