@extends('admin.layout')

@section('title', 'Edit Blog Post')

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
                        <form action="{{ route('admin.blogs.update', $blog) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

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
                                                    {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
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
                                            id="author" name="author" value="{{ old('author', $blog->author) }}"
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
                                            <option value="draft"
                                                {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft
                                            </option>
                                            <option value="published"
                                                {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>
                                                Published</option>
                                            <option value="archived"
                                                {{ old('status', $blog->status) == 'archived' ? 'selected' : '' }}>Archived
                                            </option>
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
                                    placeholder="Enter a brief excerpt for the blog post">{{ old('excerpt', $blog->excerpt) }}</textarea>
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
                                    rows="12" placeholder="Enter blog content" required>{{ old('description', $blog->description) }}</textarea>
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
                                                {{ old('is_featured', $blog->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                Featured Post
                                            </label>
                                        </div>
                                        <div class="form-text">Featured posts appear prominently on the website.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div id="imagePreview" class="{{ $blog->image ? '' : 'd-none' }}">
                                    <img id="previewImg" src="{{ $blog->image ? $blog->image_url : '' }}" alt="Preview"
                                        class="img-thumbnail" style="max-width: 300px;">
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeImage">
                                        <i class="fas fa-times me-1"></i> Remove
                                    </button>
                                </div>
                            </div>



                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Blog
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
                <!-- Blog Stats -->
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-bar me-1"></i> Blog Stats
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary mb-1">{{ $blog->views_count }}</h4>
                                    <small class="text-muted">Views</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success mb-1">{{ $blog->created_at->format('M d') }}</h4>
                                <small class="text-muted">Created</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h6 class="mb-1">{{ $blog->status }}</h6>
                                    <small class="text-muted">Status</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h6 class="mb-1">{{ $blog->is_featured ? 'Yes' : 'No' }}</h6>
                                <small class="text-muted">Featured</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Category -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tag me-1"></i> Current Category
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($blog->category)
                            <div class="d-flex align-items-center">
                                @if ($blog->category->image)
                                    <img src="{{ $blog->category->image_url }}" alt="{{ $blog->category->name }}"
                                        class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-tag text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $blog->category->name }}</h6>
                                    <small class="text-muted">{{ $blog->category->blogs_count }} blogs</small>
                                </div>
                            </div>
                            <p class="text-muted small mt-2">{{ $blog->category->description }}</p>
                        @else
                            <p class="text-muted mb-0">No category assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bolt me-1"></i> Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.blogs.toggle_status', $blog) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('POST')
                                <button type="submit" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-toggle-{{ $blog->status === 'published' ? 'on' : 'off' }} me-1"></i>
                                    {{ $blog->status === 'published' ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.blogs.toggle_featured', $blog) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('POST')
                                <button type="submit"
                                    class="btn btn-outline-{{ $blog->is_featured ? 'success' : 'secondary' }} w-100">
                                    <i class="fas fa-star me-1"></i>
                                    {{ $blog->is_featured ? 'Unfeature' : 'Feature' }}
                                </button>
                            </form>

                            <a href="{{ route('admin.blogs.show', $blog) }}" class="btn btn-outline-info">
                                <i class="fas fa-eye me-1"></i> View Post
                            </a>
                        </div>
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
