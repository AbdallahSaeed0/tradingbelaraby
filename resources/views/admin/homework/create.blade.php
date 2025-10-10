@extends('admin.layout')

@section('title', 'Create Homework')

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

        .file-preview {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .file-preview .file-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-preview .file-icon {
            font-size: 2rem;
            margin-right: 15px;
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
                        <h1 class="h3 mb-0">Create Homework</h1>
                        <p class="text-muted">Add a new homework assignment to your course</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.homework.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Homework
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.homework.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Basic Information -->
                            <div class="form-section">
                                <h5><i class="fa fa-info-circle me-2"></i>Basic Information</h5>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            @include('admin.courses.partials.multilingual-fields', [
                                                'fieldName' => 'name',
                                                'label' => 'Homework Name',
                                                'type' => 'input',
                                                'required' => true,
                                                'placeholder' => 'Enter homework name',
                                                'value' => old('name'),
                                                'valueAr' => old('name_ar'),
                                            ])
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
                                                        {{ old('course_id') == $course->id ? 'selected' : '' }}>
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
                                                        {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
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
                                            <label for="due_date" class="form-label">Due Date <span
                                                    class="text-danger">*</span></label>
                                            <input type="datetime-local"
                                                class="form-control @error('due_date') is-invalid @enderror" id="due_date"
                                                name="due_date" value="{{ old('due_date') }}" required>
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    @include('admin.courses.partials.multilingual-fields', [
                                        'fieldName' => 'description',
                                        'label' => 'Description',
                                        'type' => 'textarea',
                                        'required' => true,
                                        'rows' => 4,
                                        'placeholder' => 'Enter homework description...',
                                        'value' => old('description'),
                                        'valueAr' => old('description_ar'),
                                    ])
                                </div>

                                <div class="form-group mb-3">
                                    @include('admin.courses.partials.multilingual-fields', [
                                        'fieldName' => 'instructions',
                                        'label' => 'Instructions',
                                        'type' => 'textarea',
                                        'required' => false,
                                        'rows' => 3,
                                        'placeholder' => 'Enter instructions for students...',
                                        'value' => old('instructions'),
                                        'valueAr' => old('instructions_ar'),
                                    ])
                                </div>
                            </div>

                            <!-- Scoring & Settings -->
                            <div class="form-section">
                                <h5><i class="fa fa-chart-line me-2"></i>Scoring & Settings</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="max_score" class="form-label">Maximum Score <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('max_score') is-invalid @enderror" id="max_score"
                                                name="max_score" value="{{ old('max_score', 100) }}" min="1"
                                                max="1000" required>
                                            @error('max_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="weight_percentage" class="form-label">Weight in Course (%) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('weight_percentage') is-invalid @enderror"
                                                id="weight_percentage" name="weight_percentage"
                                                value="{{ old('weight_percentage', 10) }}" min="1" max="100"
                                                required>
                                            @error('weight_percentage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="allow_late_submission"
                                                    name="allow_late_submission" value="1"
                                                    {{ old('allow_late_submission') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_late_submission">Allow Late
                                                    Submission</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="late_submission_until" class="form-label">Late Submission
                                                Until</label>
                                            <input type="datetime-local"
                                                class="form-control @error('late_submission_until') is-invalid @enderror"
                                                id="late_submission_until" name="late_submission_until"
                                                value="{{ old('late_submission_until') }}">
                                            @error('late_submission_until')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="require_file_upload"
                                                    name="require_file_upload" value="1"
                                                    {{ old('require_file_upload', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="require_file_upload">Require File
                                                    Upload</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="allow_text_submission" name="allow_text_submission"
                                                    value="1" {{ old('allow_text_submission') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_text_submission">Allow Text
                                                    Submission</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Files Section -->
                            <div class="form-section">
                                <h5><i class="fa fa-file-alt me-2"></i>Files & Materials</h5>

                                <div class="form-group mb-3">
                                    <label for="attachment_file" class="form-label">Main Homework File</label>
                                    <div class="file-upload-area" id="mainFileUpload">
                                        <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="mb-1">Drag and drop the main homework file here or click to browse</p>
                                        <input type="file" class="form-control" name="attachment_file"
                                            id="attachment_file" style="display: none;">
                                        <button type="button" class="btn btn-outline-primary btn-sm">Choose File</button>
                                    </div>
                                    <div id="mainFilePreview" class="file-preview" style="display: none;">
                                        <div class="file-info">
                                            <div class="d-flex align-items-center">
                                                <div class="file-icon text-primary">
                                                    <i class="fa fa-file"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold" id="mainFileName"></div>
                                                    <small class="text-muted" id="mainFileSize"></small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="removeMainFile()">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('attachment_file')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Additional Files</label>
                                    <div class="file-upload-area" id="additionalFilesUpload">
                                        <i class="fa fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="mb-1">Drag and drop additional files here or click to browse</p>
                                        <input type="file" class="form-control" name="additional_files[]"
                                            id="additional_files" multiple style="display: none;">
                                        <button type="button" class="btn btn-outline-primary btn-sm">Choose
                                            Files</button>
                                    </div>
                                    <div id="additionalFilesPreview"></div>
                                    @error('additional_files.*')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.homework.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Create Homework
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
            // Main file upload functionality
            document.getElementById('mainFileUpload').addEventListener('click', function() {
                document.getElementById('attachment_file').click();
            });

            document.getElementById('attachment_file').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    showMainFilePreview(file);
                }
            });

            // Additional files upload functionality
            document.getElementById('additionalFilesUpload').addEventListener('click', function() {
                document.getElementById('additional_files').click();
            });

            document.getElementById('additional_files').addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    showAdditionalFilePreview(file);
                });
            });

            // Drag and drop functionality
            function setupDragAndDrop(uploadArea, fileInput) {
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', function(e) {
                    uploadArea.classList.remove('dragover');
                });

                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    const files = e.dataTransfer.files;

                    if (fileInput.multiple) {
                        Array.from(files).forEach(file => {
                            if (fileInput.name.includes('additional_files')) {
                                showAdditionalFilePreview(file);
                            }
                        });
                    } else {
                        if (files.length > 0) {
                            fileInput.files = files;
                            showMainFilePreview(files[0]);
                        }
                    }
                });
            }

            // Setup drag and drop for both upload areas
            setupDragAndDrop(document.getElementById('mainFileUpload'), document.getElementById('attachment_file'));
            setupDragAndDrop(document.getElementById('additionalFilesUpload'), document.getElementById('additional_files'));

            function showMainFilePreview(file) {
                const preview = document.getElementById('mainFilePreview');
                const fileName = document.getElementById('mainFileName');
                const fileSize = document.getElementById('mainFileSize');

                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                preview.style.display = 'block';
            }

            function showAdditionalFilePreview(file) {
                const preview = document.getElementById('additionalFilesPreview');
                const fileDiv = document.createElement('div');
                fileDiv.className = 'file-preview';
                fileDiv.innerHTML = `
            <div class="file-info">
                <div class="d-flex align-items-center">
                    <div class="file-icon text-primary">
                        <i class="fa fa-file"></i>
                    </div>
                    <div>
                        <div class="fw-bold">${file.name}</div>
                        <small class="text-muted">${formatFileSize(file.size)}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.parentElement.remove()">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;
                preview.appendChild(fileDiv);
            }

            function removeMainFile() {
                document.getElementById('attachment_file').value = '';
                document.getElementById('mainFilePreview').style.display = 'none';
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Set minimum datetime to now
            document.getElementById('due_date').min = new Date().toISOString().slice(0, 16);
        </script>
    @endpush
@endsection
