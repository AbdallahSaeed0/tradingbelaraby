@extends('admin.layout')

@section('title', 'Add Admin')

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
                                            <label for="name" class="form-label">Full Name (English) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="name_ar" class="form-label">Full Name (Arabic)</label>
                                            <input type="text" name="name_ar" id="name_ar"
                                                class="form-control @error('name_ar') is-invalid @enderror"
                                                value="{{ old('name_ar') }}" dir="rtl">
                                            @error('name_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
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
                                        <div class="avatar-upload-area border rounded p-4 text-center" id="avatarUploadArea">
                                            <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-2">Drag and drop an image here or click to select</p>
                                            <p class="text-muted small mb-0">Max 5MB. JPG, PNG, WEBP</p>
                                            <input type="file" class="d-none" id="avatar" name="avatar"
                                                accept="image/jpeg,image/jpg,image/png,image/webp">
                                        </div>
                                        <div id="avatarPreview" class="mt-3 text-center d-none-initially">
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
                                        <div class="cover-upload-area border rounded p-4 text-center" id="coverUploadArea">
                                            <i class="fa fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-2">Drag and drop a cover image here or click to select</p>
                                            <p class="text-muted small mb-0">Max 5MB. JPG, PNG, WEBP</p>
                                            <input type="file" class="d-none" id="cover" name="cover"
                                                accept="image/jpeg,image/jpg,image/png,image/webp">
                                        </div>
                                        <div id="coverPreview" class="mt-3 text-center d-none-initially">
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

                            <!-- About Me Section -->
                            <div class="row mt-3">
                                <div class="col-md-12 mb-3">
                                    <label for="about_me" class="form-label">About Me (English)</label>
                                    <textarea class="form-control @error('about_me') is-invalid @enderror" id="about_me" name="about_me"
                                        rows="6" placeholder="Enter about me information in English">{{ old('about_me') }}</textarea>
                                    @error('about_me')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="about_me_ar" class="form-label">About Me (Arabic)</label>
                                    <textarea class="form-control @error('about_me_ar') is-invalid @enderror" id="about_me_ar" name="about_me_ar"
                                        rows="6" placeholder="أدخل معلومات عني بالعربية" dir="rtl">{{ old('about_me_ar') }}</textarea>
                                    @error('about_me_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize CKEditor for About Me (English)
            ClassicEditor
                .create(document.querySelector('#about_me'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontColor', 'fontBackgroundColor', '|',
                        'link', '|',
                        'bulletedList', 'numberedList', '|',
                        'outdent', 'indent', '|',
                        'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ]
                })
                .catch(error => {
                    console.error('Error initializing CKEditor for about_me:', error);
                });

            // Initialize CKEditor for About Me (Arabic)
            ClassicEditor
                .create(document.querySelector('#about_me_ar'), {
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
                    language: 'ar'
                })
                .catch(error => {
                    console.error('Error initializing CKEditor for about_me_ar:', error);
                });
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

