@extends('admin.layout')

@section('title', 'Terms and Conditions Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
                            <li class="breadcrumb-item active">Terms and Conditions</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Terms and Conditions</h4>
                    <p class="text-muted mb-0">Manage your website's terms and conditions in both English and Arabic.</p>
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
                            <i class="fas fa-file-contract me-2"></i>
                            Terms and Conditions Content
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.terms-conditions.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="id" value="{{ $termsConditions->id }}">

                            <!-- Status Toggle -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            {{ old('is_active', $termsConditions->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong> - Display this page on the website
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- English Content Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-language me-2"></i>
                                        English Content
                                    </h6>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title (English) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            id="title" name="title"
                                            value="{{ old('title', $termsConditions->title) }}"
                                            placeholder="Enter title in English" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">This title is for internal reference only, not displayed on
                                            the page</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description (English) <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="10" required>{{ old('description', $termsConditions->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Arabic Content Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-language me-2"></i>
                                        Arabic Content (المحتوى العربي)
                                    </h6>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="title_ar" class="form-label">Title (Arabic) <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title_ar') is-invalid @enderror"
                                            id="title_ar" name="title_ar"
                                            value="{{ old('title_ar', $termsConditions->title_ar) }}"
                                            placeholder="أدخل العنوان بالعربية" required dir="rtl">
                                        @error('title_ar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">هذا العنوان للمرجعية الداخلية فقط، لا يظهر في الصفحة</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description_ar" class="form-label">Description (Arabic) <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar" name="description_ar"
                                            rows="10" required dir="rtl">{{ old('description_ar', $termsConditions->description_ar) }}</textarea>
                                        @error('description_ar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                            <span class="input-group-text">{{ url('/') }}/terms-conditions/</span>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                                id="slug" name="slug"
                                                value="{{ old('slug', $termsConditions->slug ?? 'terms-and-conditions') }}"
                                                placeholder="terms-and-conditions" required>
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
                                            @if ($termsConditions->exists && $termsConditions->slug)
                                                <a href="{{ route('terms-conditions', $termsConditions->slug) }}"
                                                    target="_blank" class="btn btn-info me-2">
                                                    <i class="fas fa-eye me-2"></i>
                                                    Preview Page
                                                </a>
                                            @endif
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                Save Terms and Conditions
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
        });
    </script>
@endpush
