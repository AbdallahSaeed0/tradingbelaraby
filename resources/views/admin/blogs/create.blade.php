@extends('admin.layout')

@section('title', 'Create Blog Post')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Create Blog Post</h1>
                <p class="text-muted">Add a new blog post to your website</p>
            </div>
            <div>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Blogs
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Blog Form -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Blog Information</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Blog Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                                            id="title" name="title" value="{{ old('title') }}"
                                            placeholder="Enter blog title" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">This will be used to generate the URL slug automatically.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id">
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
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="author" class="form-label">Author</label>
                                        <input type="text" class="form-control @error('author') is-invalid @enderror"
                                            id="author" name="author" value="{{ old('author') }}"
                                            placeholder="Enter author name">
                                        @error('author')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="">Select status</option>
                                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft
                                            </option>
                                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>
                                                Published</option>
                                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>
                                                Archived</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Excerpt</label>
                                <textarea class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" name="excerpt" rows="3"
                                    placeholder="Enter a brief excerpt for the blog post">{{ old('excerpt') }}</textarea>
                                @error('excerpt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">A short summary that will appear in blog listings. Leave empty to
                                    auto-generate from content.</div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Content <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="12" placeholder="Enter blog content" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Featured Image</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            id="image" name="image" accept="image/*">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Recommended size: 1200x630px. Max size: 2MB.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured"
                                                name="is_featured" value="1"
                                                {{ old('is_featured') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                Featured Post
                                            </label>
                                        </div>
                                        <div class="form-text">Featured posts appear prominently on the website.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div id="imagePreview" class="d-none">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail"
                                        style="max-width: 300px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeImage">
                                        <i class="fas fa-times me-1"></i> Remove
                                    </button>
                                </div>
                            </div>



                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Create Blog
                                </button>
                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle me-1"></i> Writing Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Title:</strong> Make it compelling and SEO-friendly
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Content:</strong> Write engaging, valuable content
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Category:</strong> Choose the most relevant category
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Image:</strong> Use high-quality, relevant images
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <strong>Tags:</strong> Add relevant tags for better SEO
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Recent Blogs -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock me-1"></i> Recent Blogs
                        </h6>
                    </div>
                    <div class="card-body">
                        @php
                            $recentBlogs = \App\Models\Blog::with('category')->latest()->limit(5)->get();
                        @endphp

                        @if ($recentBlogs->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($recentBlogs as $recentBlog)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($recentBlog->title, 40) }}</h6>
                                                <small class="text-muted">
                                                    @if ($recentBlog->category)
                                                        {{ $recentBlog->category->name }} •
                                                    @endif
                                                    {{ $recentBlog->created_at->format('M d, Y') }}
                                                </small>
                                            </div>
                                            <span
                                                class="badge bg-{{ $recentBlog->status === 'published' ? 'success' : 'warning' }}">
                                                {{ ucfirst($recentBlog->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No blogs created yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Category Stats -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie me-1"></i> Category Stats
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($categories->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($categories->take(5) as $category)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $category->name }}</h6>
                                                <small class="text-muted">{{ $category->blogs_count }} blogs</small>
                                            </div>
                                            <span
                                                class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($category->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No categories available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImage');

            // Image preview functionality
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Remove image
            removeImageBtn.addEventListener('click', function() {
                imageInput.value = '';
                imagePreview.classList.add('d-none');
                previewImg.src = '';
            });

            // Auto-generate excerpt from content
            const descriptionInput = document.getElementById('description');
            const excerptInput = document.getElementById('excerpt');

            descriptionInput.addEventListener('input', function() {
                if (!excerptInput.value) {
                    const content = this.value;
                    const excerpt = content.substring(0, 150);
                    if (excerpt.length === 150) {
                        excerptInput.value = excerpt + '...';
                    } else {
                        excerptInput.value = excerpt;
                    }
                }
            });
        });
    </script>
@endpush
