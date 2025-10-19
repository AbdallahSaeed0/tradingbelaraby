@extends('admin.layout')

@section('title', 'Edit Blog Post')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .language-tabs {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .language-tab {
            background: none;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s;
        }

        .language-tab.active {
            border-bottom-color: #007bff;
            color: #007bff;
        }

        .language-content {
            display: none;
        }

        .language-content.active {
            display: block;
        }

        .seo-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .tags-input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px;
            min-height: 38px;
        }

        .tag-item {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px;
            cursor: pointer;
        }

        .tag-item:hover {
            background: #0056b3;
        }

        .image-preview {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 10px;
        }

        .ck-editor__editable {
            min-height: 300px;
            max-height: 500px;
            overflow-y: auto;
        }

        .ck-editor {
            max-width: 1000px;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Edit Blog Post</h1>
                <p class="text-muted">Update your blog post content and settings</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Blogs
                </a>
                <button type="submit" form="blogForm" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Blog
                </button>
            </div>
        </div>

        <form id="blogForm" action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8">
                    <!-- Language Tabs -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Blog Content</h6>
                        </div>
                        <div class="card-body">
                            <!-- Language Tabs -->
                            <div class="language-tabs">
                                <button type="button" class="language-tab active" data-lang="en">
                                    <i class="fas fa-globe me-1"></i> English
                                </button>
                                <button type="button" class="language-tab" data-lang="ar">
                                    <i class="fas fa-globe me-1"></i> العربية
                                </button>
                            </div>

                            <!-- English Content -->
                            <div id="content-en" class="language-content active">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Blog Title <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                id="title" name="title" value="{{ old('title', $blog->title) }}"
                                                placeholder="Enter blog title" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="custom_slug" class="form-label">Custom URL Slug</label>
                                            <input type="text"
                                                class="form-control @error('custom_slug') is-invalid @enderror"
                                                id="custom_slug" name="custom_slug"
                                                value="{{ old('custom_slug', $blog->custom_slug) }}"
                                                placeholder="custom-url-slug">
                                            @error('custom_slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Leave empty to auto-generate from title</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Excerpt</label>
                                    <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3"
                                        placeholder="Enter a brief excerpt for the blog post">{{ old('excerpt', $blog->excerpt) }}</textarea>
                                    @error('excerpt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">A short summary that will appear in blog listings</div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Content <span
                                            class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        placeholder="Enter blog content">{{ old('description', $blog->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Featured Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($blog->image_url)
                                        <img src="{{ $blog->image_url }}" alt="Current image" class="image-preview">
                                        <div class="form-text">Current image</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Arabic Content -->
                            <div id="content-ar" class="language-content">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title_ar" class="form-label">عنوان المدونة</label>
                                            <input type="text"
                                                class="form-control @error('title_ar') is-invalid @enderror"
                                                id="title_ar" name="title_ar"
                                                value="{{ old('title_ar', $blog->title_ar) }}"
                                                placeholder="أدخل عنوان المدونة">
                                            @error('title_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="slug_ar" class="form-label">رابط مخصص (عربي)</label>
                                            <input type="text"
                                                class="form-control @error('slug_ar') is-invalid @enderror" id="slug_ar"
                                                name="slug_ar" value="{{ old('slug_ar', $blog->slug_ar) }}"
                                                placeholder="رابط-مخصص">
                                            @error('slug_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt_ar" class="form-label">الملخص</label>
                                    <textarea class="form-control @error('excerpt_ar') is-invalid @enderror" id="excerpt_ar" name="excerpt_ar"
                                        rows="3" placeholder="أدخل ملخص قصير للمدونة">{{ old('excerpt_ar', $blog->excerpt_ar) }}</textarea>
                                    @error('excerpt_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description_ar" class="form-label">المحتوى</label>
                                    <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar"
                                        name="description_ar" placeholder="أدخل محتوى المدونة">{{ old('description_ar', $blog->description_ar) }}</textarea>
                                    @error('description_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="image_ar" class="form-label">الصورة المميزة (عربي)</label>
                                    <input type="file" class="form-control @error('image_ar') is-invalid @enderror"
                                        id="image_ar" name="image_ar" accept="image/*">
                                    @error('image_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($blog->image_ar_url)
                                        <img src="{{ $blog->image_ar_url }}" alt="Current Arabic image"
                                            class="image-preview">
                                        <div class="form-text">الصورة الحالية</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <div class="seo-section">
                        <h5 class="mb-3"><i class="fas fa-search me-2"></i>SEO Settings</h5>

                        <!-- Language Tabs for SEO -->
                        <div class="language-tabs">
                            <button type="button" class="language-tab active" data-seo-lang="en">
                                <i class="fas fa-globe me-1"></i> English SEO
                            </button>
                            <button type="button" class="language-tab" data-seo-lang="ar">
                                <i class="fas fa-globe me-1"></i> SEO عربي
                            </button>
                        </div>

                        <!-- English SEO -->
                        <div id="seo-en" class="language-content active">
                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                    id="meta_title" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}"
                                    placeholder="SEO title for search engines" maxlength="60">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Recommended: 50-60 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description"
                                    name="meta_description" rows="3" maxlength="160" placeholder="SEO description for search engines">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Recommended: 150-160 characters</div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_keywords_en" class="form-label">Meta Keywords</label>
                                <div id="meta_keywords_en" class="tags-input" data-field="meta_keywords">
                                    <input type="text" placeholder="Type keywords and press Enter"
                                        class="input-transparent w-200">
                                </div>
                                <input type="hidden" name="meta_keywords" id="meta_keywords_hidden"
                                    value="{{ $blog->meta_keywords ? json_encode($blog->meta_keywords) : (old('meta_keywords') ? json_encode(old('meta_keywords')) : '[]') }}">
                                <!-- Debug: Raw meta_keywords value -->
                                <script>
                                    console.log('Raw meta_keywords from database:', @json($blog->meta_keywords));
                                </script>
                                <div class="form-text">Press Enter to add keywords</div>
                            </div>
                        </div>

                        <!-- Arabic SEO -->
                        <div id="seo-ar" class="language-content">
                            <div class="mb-3">
                                <label for="meta_title_ar" class="form-label">عنوان SEO</label>
                                <input type="text" class="form-control @error('meta_title_ar') is-invalid @enderror"
                                    id="meta_title_ar" name="meta_title_ar"
                                    value="{{ old('meta_title_ar', $blog->meta_title_ar) }}"
                                    placeholder="عنوان SEO لمحركات البحث" maxlength="60">
                                @error('meta_title_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meta_description_ar" class="form-label">وصف SEO</label>
                                <textarea class="form-control @error('meta_description_ar') is-invalid @enderror" id="meta_description_ar"
                                    name="meta_description_ar" rows="3" maxlength="160" placeholder="وصف SEO لمحركات البحث">{{ old('meta_description_ar', $blog->meta_description_ar) }}</textarea>
                                @error('meta_description_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="meta_keywords_ar_field" class="form-label">كلمات مفتاحية SEO</label>
                                <div id="meta_keywords_ar_field" class="tags-input" data-field="meta_keywords_ar">
                                    <input type="text" placeholder="اكتب الكلمات المفتاحية واضغط Enter"
                                        class="input-transparent w-200">
                                </div>
                                <input type="hidden" name="meta_keywords_ar" id="meta_keywords_ar_hidden"
                                    value="{{ $blog->meta_keywords_ar ? json_encode($blog->meta_keywords_ar) : (old('meta_keywords_ar') ? json_encode(old('meta_keywords_ar')) : '[]') }}">
                                <!-- Debug: Raw meta_keywords_ar value -->
                                <script>
                                    console.log('Raw meta_keywords_ar from database:', @json($blog->meta_keywords_ar));
                                </script>
                                <div class="form-text">اضغط Enter لإضافة كلمات مفتاحية</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Blog Settings -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Blog Settings</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="author_id" class="form-label">Author</label>
                                <select class="form-select @error('author_id') is-invalid @enderror" id="author_id"
                                    name="author_id">
                                    <option value="">Select Author</option>
                                    @foreach ($authors as $author)
                                        <option value="{{ $author->id }}"
                                            {{ old('author_id', $blog->author_id) == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }} ({{ $author->type_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('author_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="">Select status</option>
                                    <option value="draft"
                                        {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published"
                                        {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published
                                    </option>
                                    <option value="archived"
                                        {{ old('status', $blog->status) == 'archived' ? 'selected' : '' }}>Archived
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <div id="tags" class="tags-input" data-field="tags">
                                    <input type="text" placeholder="Type tags and press Enter"
                                        class="input-transparent w-200">
                                </div>
                                <input type="hidden" name="tags" id="tags_hidden"
                                    value="{{ old('tags', $blog->tags ? json_encode($blog->tags) : '[]') }}">
                                <div class="form-text">Press Enter to add tags</div>
                            </div>


                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                    value="1" {{ old('is_featured', $blog->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Featured Blog
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Blog Statistics -->
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h4 class="text-primary">{{ $blog->views_count ?? 0 }}</h4>
                                        <small class="text-muted">Views</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success">{{ $blog->calculateReadingTime() }}</h4>
                                    <small class="text-muted">Min Read</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Language tab switching (only for content tabs)
            const contentLanguageTabs = document.querySelectorAll('[data-lang]');
            contentLanguageTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const lang = this.dataset.lang;

                    // Remove active class from content tabs and content only
                    document.querySelectorAll('[data-lang]').forEach(t => t.classList.remove(
                        'active'));
                    document.querySelectorAll('#content-en, #content-ar').forEach(c => c.classList
                        .remove('active'));

                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(`content-${lang}`).classList.add('active');
                });
            });

            // SEO language tab switching
            const seoTabs = document.querySelectorAll('[data-seo-lang]');
            seoTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const lang = this.dataset.seoLang;

                    // Remove active class from all SEO tabs and content
                    document.querySelectorAll('[data-seo-lang]').forEach(t => t.classList.remove(
                        'active'));
                    document.querySelectorAll('#seo-en, #seo-ar').forEach(c => c.classList.remove(
                        'active'));

                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(`seo-${lang}`).classList.add('active');
                });
            });

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
                    // Update textarea value when editor content changes
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#description').value = editor.getData();
                    });

                    // Hide the original textarea to prevent form validation issues
                    document.querySelector('#description').style.display = 'none';
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
                    }
                })
                .then(editor => {
                    // Update textarea value when editor content changes
                    editor.model.document.on('change:data', () => {
                        document.querySelector('#description_ar').value = editor.getData();
                    });

                    // Hide the original textarea to prevent form validation issues
                    document.querySelector('#description_ar').style.display = 'none';
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });

            // Tags input functionality
            function initializeTagsInput(containerId, hiddenInputId) {
                const container = document.getElementById(containerId);
                const hiddenInput = document.getElementById(hiddenInputId);
                const inputField = container.querySelector('input[type="text"]');

                // Debug: Log the hidden input value
                console.log(`Initializing ${containerId}:`, hiddenInput.value);
                console.log(`Hidden input element:`, hiddenInput);
                console.log(`Container element:`, container);

                // Safely parse existing tags with error handling
                let existingTags = [];
                if (hiddenInput.value) {
                    try {
                        // Handle double-encoded JSON strings
                        let valueToParse = hiddenInput.value;
                        if (valueToParse.startsWith('"') && valueToParse.endsWith('"')) {
                            // Remove outer quotes and unescape inner quotes
                            valueToParse = valueToParse.slice(1, -1).replace(/\\"/g, '"');
                        }
                        // Handle Unicode escape sequences
                        valueToParse = valueToParse.replace(/\\u([0-9a-fA-F]{4})/g, (match, code) => {
                            return String.fromCharCode(parseInt(code, 16));
                        });
                        const parsed = JSON.parse(valueToParse);
                        existingTags = Array.isArray(parsed) ? parsed : [];
                        console.log(`Parsed tags for ${containerId}:`, existingTags);
                        console.log(`Value to parse was:`, valueToParse);
                    } catch (e) {
                        console.warn('Error parsing existing tags:', e);
                        existingTags = [];
                    }
                }

                // Add existing tags
                existingTags.forEach(tag => {
                    if (tag && tag.trim()) {
                        console.log(`Adding tag to ${containerId}:`, tag);
                        addTag(container, tag.trim());
                    }
                });

                inputField.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const tag = e.target.value.trim();
                        if (tag) {
                            addTag(container, tag);
                            e.target.value = '';
                            updateHiddenInput();
                        }
                    }
                });
            }

            function addTag(container, tagText) {
                const tag = document.createElement('span');
                tag.className = 'tag-item';
                tag.textContent = tagText;
                tag.addEventListener('click', function() {
                    tag.remove();
                    updateHiddenInput();
                });
                container.appendChild(tag);
                updateHiddenInput(); // Update hidden input when tag is added
            }

            function updateHiddenInput() {
                const tags = Array.from(document.querySelectorAll('#tags .tag-item')).map(tag => tag.textContent);
                document.getElementById('tags_hidden').value = JSON.stringify(tags);
                console.log('Updated tags hidden input:', JSON.stringify(tags));

                // Update meta keywords
                const metaKeywordsEn = Array.from(document.querySelectorAll('#meta_keywords_en .tag-item')).map(
                    tag => tag.textContent);
                document.getElementById('meta_keywords_hidden').value = JSON.stringify(metaKeywordsEn);
                console.log('Updated meta keywords EN hidden input:', JSON.stringify(metaKeywordsEn));

                const metaKeywordsAr = Array.from(document.querySelectorAll('#meta_keywords_ar_field .tag-item'))
                    .map(tag => tag.textContent);
                document.getElementById('meta_keywords_ar_hidden').value = JSON.stringify(metaKeywordsAr);
                console.log('Updated meta keywords AR hidden input:', JSON.stringify(metaKeywordsAr));
            }

            // Initialize tags inputs
            initializeTagsInput('tags', 'tags_hidden');
            initializeTagsInput('meta_keywords_en', 'meta_keywords_hidden');
            initializeTagsInput('meta_keywords_ar_field', 'meta_keywords_ar_hidden');

            // Auto-generate slug from title
            document.getElementById('title').addEventListener('input', function() {
                if (!document.getElementById('custom_slug').value) {
                    const slug = this.value.toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim('-');
                    // You can display this in a preview field if needed
                }
            });

            // Form validation before submission
            document.getElementById('blogForm').addEventListener('submit', function(e) {
                const descriptionField = document.getElementById('description');
                if (!descriptionField.value.trim()) {
                    e.preventDefault();
                    alert('Please enter blog content before saving.');
                    // Show the editor container to focus on content
                    document.querySelector('.ck-editor').scrollIntoView({
                        behavior: 'smooth'
                    });
                    return false;
                }

                // Ensure all hidden inputs are updated before submission
                updateHiddenInput();

                // Debug: Log form data before submission
                console.log('Form submitting with:');
                console.log('Tags:', document.getElementById('tags_hidden').value);
                console.log('Meta keywords EN:', document.getElementById('meta_keywords_hidden').value);
                console.log('Meta keywords AR:', document.getElementById('meta_keywords_ar_hidden').value);
            });
        });
    </script>
@endpush
