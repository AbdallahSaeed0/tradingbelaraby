@extends('admin.layout')

@section('title', 'Edit Blog Category')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Edit Blog Category
                            </h4>
                            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.blog-categories.update', $category) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $category->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" rows="4"
                                            class="form-control @error('description') is-invalid @enderror" placeholder="Enter category description...">{{ old('description', $category->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_featured" id="is_featured"
                                                class="form-check-input" value="1"
                                                {{ old('is_featured', $category->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                <i class="fas fa-star me-1"></i>Featured Category
                                            </label>
                                        </div>
                                        <small class="text-muted">Featured categories will be highlighted</small>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="status" id="status" class="form-check-input"
                                                value="active"
                                                {{ old('status', $category->status) === 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">
                                                <i class="fas fa-check-circle me-1"></i>Active
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0">Category Statistics</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <h4 class="text-primary">{{ $category->blogs_count ?? 0 }}</h4>
                                                    <small class="text-muted">Blogs</small>
                                                </div>
                                                <div class="col-6">
                                                    <h4 class="text-success">{{ $category->created_at->format('M d') }}</h4>
                                                    <small class="text-muted">Created</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Category
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
