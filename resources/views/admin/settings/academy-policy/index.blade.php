@extends('admin.layout')

@section('title', 'Academy Policy Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                            <li class="breadcrumb-item active">Academy Policy</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Academy Policy</h4>
                    <p class="text-muted mb-0">Manage your website's academy policy page in both English and Arabic.</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-gavel me-2"></i>
                            Academy Policy Content
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.academy-policy.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" value="{{ $academyPolicy->id }}">
                            <input type="hidden" name="is_active" id="is_active_hidden" value="{{ old('is_active', $academyPolicy->is_active ?? true) ? '1' : '0' }}">

                            <!-- Status Toggle -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" 
                                            {{ old('is_active', $academyPolicy->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong> - Display this page on the website
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Section with Tabs -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-language me-2"></i>
                                        Content
                                    </h6>
                                    
                                    <!-- Language Tabs -->
                                    <ul class="nav nav-tabs" id="contentTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="content-en-tab" data-bs-toggle="tab"
                                                data-bs-target="#content-en" type="button" role="tab">
                                                <i class="fa fa-flag-usa me-1"></i>English
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="content-ar-tab" data-bs-toggle="tab"
                                                data-bs-target="#content-ar" type="button" role="tab">
                                                <i class="fa fa-flag me-1"></i>العربية
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Tab Content -->
                                    <div class="tab-content border border-top-0 p-3" id="contentTabContent">
                                        <!-- English Tab -->
                                        <div class="tab-pane fade show active" id="content-en" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Title (English) <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                    id="title" name="title"
                                                    value="{{ old('title', $academyPolicy->title) }}"
                                                    placeholder="Enter title in English" required>
                                                @error('title')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">This title will be displayed on the page</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description (English) <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                    rows="10">{{ old('description', $academyPolicy->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Arabic Tab -->
                                        <div class="tab-pane fade" id="content-ar" role="tabpanel">
                                            <div class="mb-3">
                                                <label for="title_ar" class="form-label">Title (Arabic) <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('title_ar') is-invalid @enderror"
                                                    id="title_ar" name="title_ar"
                                                    value="{{ old('title_ar', $academyPolicy->title_ar) }}"
                                                    placeholder="أدخل العنوان بالعربية" required dir="rtl">
                                                @error('title_ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">هذا العنوان سيظهر في الصفحة</div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description_ar" class="form-label">Description (Arabic) <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar" name="description_ar"
                                                    rows="10" dir="rtl">{{ old('description_ar', $academyPolicy->description_ar) }}</textarea>
                                                @error('description_ar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Slug Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-link me-2"></i>
                                        Page URL
                                    </h6>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ url('/') }}/academy-policy/</span>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                                id="slug" name="slug"
                                                value="{{ old('slug', $academyPolicy->slug ?? 'academy-policy') }}"
                                                placeholder="academy-policy" required>
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="generateSlugBtn">
                                                <i class="fas fa-sync-alt"></i> Generate
                                            </button>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-text">URL-friendly version of the title. Use lowercase letters,
                                            numbers, and hyphens only.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back to Settings
                                        </a>
                                        <div>
                                            @if ($academyPolicy->exists && $academyPolicy->slug)
                                                <a href="{{ route('academy-policy', $academyPolicy->slug) }}"
                                                    target="_blank" class="btn btn-info me-2">
                                                    <i class="fas fa-eye me-2"></i>
                                                    Preview Page
                                                </a>
                                            @endif
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                Save Academy Policy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize CKEditor for English content
            ClassicEditor
                .create(document.querySelector('#description'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontColor', 'fontBackgroundColor', '|',
                        'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ],
                    heading: {
                        options: [{
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3'
                            }
                        ]
                    }
                })
                .then(editor => {
                    window.editorEnglish = editor;
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });

            // Initialize CKEditor for Arabic content
            ClassicEditor
                .create(document.querySelector('#description_ar'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontColor', 'fontBackgroundColor', '|',
                        'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ],
                    heading: {
                        options: [{
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3'
                            }
                        ]
                    },
                    language: {
                        ui: 'en',
                        content: 'ar'
                    }
                })
                .then(editor => {
                    window.editorArabic = editor;
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });

            // Generate slug functionality
            document.getElementById('generateSlugBtn').addEventListener('click', function() {
                const title = document.getElementById('title').value;
                if (title) {
                    const slug = title.toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim();
                    document.getElementById('slug').value = slug;
                } else {
                    alert('Please enter a title first');
                }
            });

            // Update hidden is_active field when checkbox changes
            document.getElementById('is_active').addEventListener('change', function() {
                document.getElementById('is_active_hidden').value = this.checked ? '1' : '0';
            });

            // Form submission handler - sync CKEditor content and validate
            document.querySelector('form').addEventListener('submit', function(e) {
                // Update hidden is_active field
                const isActiveCheckbox = document.getElementById('is_active');
                document.getElementById('is_active_hidden').value = isActiveCheckbox.checked ? '1' : '0';

                // Sync CKEditor content to textareas before submission
                if (window.editorEnglish) {
                    const englishContent = window.editorEnglish.getData();
                    document.getElementById('description').value = englishContent;
                    
                    // Validate English content
                    if (!englishContent || englishContent.trim() === '' || englishContent === '<p></p>') {
                        e.preventDefault();
                        alert('Please enter a description in English.');
                        window.editorEnglish.focus();
                        return false;
                    }
                }

                if (window.editorArabic) {
                    const arabicContent = window.editorArabic.getData();
                    document.getElementById('description_ar').value = arabicContent;
                    
                    // Validate Arabic content
                    if (!arabicContent || arabicContent.trim() === '' || arabicContent === '<p></p>') {
                        e.preventDefault();
                        alert('Please enter a description in Arabic.');
                        window.editorArabic.focus();
                        return false;
                    }
                }

                return true;
            });
        });
    </script>
@endpush
