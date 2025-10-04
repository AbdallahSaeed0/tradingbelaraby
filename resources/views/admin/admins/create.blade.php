@extends('admin.layout')

@section('title', 'Add Admin')

@push('styles')
    <style>
        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #dee2e6;
        }

        .avatar-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .avatar-upload-area:hover {
            border-color: #007bff;
        }

        .avatar-upload-area.dragover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        /* Cover Image Styles */
        .cover-preview {
            width: 100%;
            max-width: 300px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }

        .cover-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .cover-upload-area:hover {
            border-color: #007bff;
        }

        .cover-upload-area.dragover {
            border-color: #007bff;
            background-color: #f8f9fa;
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
                        <h1 class="h3 mb-0">Add New Admin</h1>
                        <p class="text-muted">Create a new administrator account</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Admins
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-user-plus me-2"></i>Admin Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.admins.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email Address <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" name="email" id="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" name="phone" id="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone') }}">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="admin_type_id" class="form-label">Admin Type <span
                                                    class="text-danger">*</span></label>
                                            <select name="admin_type_id" id="admin_type_id"
                                                class="form-select @error('admin_type_id') is-invalid @enderror" required>
                                                <option value="">Select Admin Type</option>
                                                @foreach ($adminTypes as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ old('admin_type_id') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->display_name }}
                                                        @if ($type->description)
                                                            - {{ $type->description }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('admin_type_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password" id="password"
                                                class="form-control @error('password') is-invalid @enderror" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Minimum 6 characters</div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                                value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                <strong>Active Account</strong>
                                            </label>
                                            <div class="form-text">Inactive accounts cannot log in to the system</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Avatar Upload -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Profile Avatar</label>
                                        <div class="avatar-upload-area" id="avatarUploadArea">
                                            <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-2">Drag and drop an image here or click to select</p>
                                            <p class="text-muted small">Recommended size: 200x200px</p>
                                            <input type="file" class="d-none" id="avatar" name="avatar"
                                                accept="image/*">
                                        </div>
                                        <div id="avatarPreview" class="mt-3 text-center" style="display: none;">
                                            <img id="previewImg" class="avatar-preview mb-2">
                                            <br>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                id="removeAvatar">
                                                <i class="fa fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                        @error('avatar')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Cover Image Upload -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Cover Image</label>
                                        <div class="cover-upload-area" id="coverUploadArea">
                                            <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-2">Drag and drop a cover image here or click to select
                                            </p>
                                            <p class="text-muted small">Recommended size: 800x400px</p>
                                            <input type="file" class="d-none" id="cover" name="cover"
                                                accept="image/*">
                                        </div>
                                        <div id="coverPreview" class="mt-3 text-center" style="display: none;">
                                            <img id="previewCoverImg" class="cover-preview mb-2">
                                            <br>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                id="removeCover">
                                                <i class="fa fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                        @error('cover')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="text-end">
                                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Create Admin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const avatarUploadArea = document.getElementById('avatarUploadArea');
            const avatarInput = document.getElementById('avatar');
            const avatarPreview = document.getElementById('avatarPreview');
            const previewImg = document.getElementById('previewImg');
            const removeAvatarBtn = document.getElementById('removeAvatar');

            // Avatar upload functionality
            avatarUploadArea.addEventListener('click', () => avatarInput.click());

            avatarUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                avatarUploadArea.classList.add('dragover');
            });

            avatarUploadArea.addEventListener('dragleave', () => {
                avatarUploadArea.classList.remove('dragover');
            });

            avatarUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                avatarUploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    avatarInput.files = files;
                    handleAvatarPreview(files[0]);
                }
            });

            avatarInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    handleAvatarPreview(file);
                }
            });

            function handleAvatarPreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    avatarPreview.style.display = 'block';
                    avatarUploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            removeAvatarBtn.addEventListener('click', function() {
                avatarInput.value = '';
                avatarPreview.style.display = 'none';
                avatarUploadArea.style.display = 'block';
            });

            // Password confirmation validation
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');

            function validatePassword() {
                if (password.value !== passwordConfirmation.value) {
                    passwordConfirmation.setCustomValidity('Passwords do not match');
                } else {
                    passwordConfirmation.setCustomValidity('');
                }
            }

            password.addEventListener('change', validatePassword);
            passwordConfirmation.addEventListener('keyup', validatePassword);

            // Cover image upload functionality
            const coverUploadArea = document.getElementById('coverUploadArea');
            const coverInput = document.getElementById('cover');
            const coverPreview = document.getElementById('coverPreview');
            const previewCoverImg = document.getElementById('previewCoverImg');
            const removeCoverBtn = document.getElementById('removeCover');

            coverUploadArea.addEventListener('click', () => coverInput.click());

            coverUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                coverUploadArea.classList.add('dragover');
            });

            coverUploadArea.addEventListener('dragleave', () => {
                coverUploadArea.classList.remove('dragover');
            });

            coverUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                coverUploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    coverInput.files = files;
                    handleCoverPreview(files[0]);
                }
            });

            coverInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    handleCoverPreview(file);
                }
            });

            function handleCoverPreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewCoverImg.src = e.target.result;
                    coverPreview.style.display = 'block';
                    coverUploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            removeCoverBtn.addEventListener('click', function() {
                coverInput.value = '';
                coverPreview.style.display = 'none';
                coverUploadArea.style.display = 'block';
            });
        });
    </script>
@endpush
