@extends('admin.layout')

@section('title', 'Create Admin Type')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Create Admin Type</h1>
                        <p class="text-muted">Define a new administrator type with specific permissions</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.admin-types.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Admin Types
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-plus me-2"></i>Admin Type Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.admin-types.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Type Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This will be used as the display name (e.g., "Super Admin",
                                        "Course Manager")</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" name="sort_order" id="sort_order"
                                        class="form-control @error('sort_order') is-invalid @enderror"
                                        value="{{ old('sort_order', 0) }}" min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Lower numbers appear first in lists</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3" placeholder="Describe what this admin type can do...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional description of this admin type's role and responsibilities
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                                        value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active Type</strong>
                                    </label>
                                    <div class="form-text">Inactive types cannot be assigned to new admins</div>
                                </div>
                            </div>

                            <hr>

                            <!-- Permissions Section -->
                            <div class="mb-4">
                                <h6 class="mb-3"><i class="fa fa-shield-alt me-2"></i>Permissions</h6>
                                <p class="text-muted mb-3">Select the permissions that admins of this type will have:</p>

                                <!-- Permission Categories -->
                                <div class="row">
                                    <!-- Admin Management -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fa fa-user-shield me-2"></i>Admin Management
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_admins" value="manage_admins"
                                                        {{ in_array('manage_admins', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_admins">Manage
                                                        Administrators</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_users" value="manage_users"
                                                        {{ in_array('manage_users', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_users">Manage Users</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Course Management -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fa fa-graduation-cap me-2"></i>Course
                                                    Management</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_courses" value="manage_courses"
                                                        {{ in_array('manage_courses', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_courses">Manage
                                                        Courses</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_own_courses" value="manage_own_courses"
                                                        {{ in_array('manage_own_courses', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_own_courses">Manage Own
                                                        Courses</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_categories" value="manage_categories"
                                                        {{ in_array('manage_categories', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_categories">Manage
                                                        Categories</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_enrollments" value="manage_enrollments"
                                                        {{ in_array('manage_enrollments', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_enrollments">Manage
                                                        Enrollments</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Content Management -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fa fa-edit me-2"></i>Content Management</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_quizzes" value="manage_quizzes"
                                                        {{ in_array('manage_quizzes', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_quizzes">Manage
                                                        Quizzes</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_own_quizzes" value="manage_own_quizzes"
                                                        {{ in_array('manage_own_quizzes', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_own_quizzes">Manage Own
                                                        Quizzes</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_homework" value="manage_homework"
                                                        {{ in_array('manage_homework', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_homework">Manage
                                                        Homework</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_own_homework" value="manage_own_homework"
                                                        {{ in_array('manage_own_homework', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_own_homework">Manage Own
                                                        Homework</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_live_classes" value="manage_live_classes"
                                                        {{ in_array('manage_live_classes', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_live_classes">Manage Live
                                                        Classes</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_own_live_classes" value="manage_own_live_classes"
                                                        {{ in_array('manage_own_live_classes', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_own_live_classes">Manage
                                                        Own Live Classes</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_blogs" value="manage_blogs"
                                                        {{ in_array('manage_blogs', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_blogs">Manage
                                                        Blogs</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Support & Analytics -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fa fa-headset me-2"></i>Support & Analytics
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_questions_answers" value="manage_questions_answers"
                                                        {{ in_array('manage_questions_answers', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_questions_answers">Manage
                                                        Q&A</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_own_questions_answers"
                                                        value="manage_own_questions_answers"
                                                        {{ in_array('manage_own_questions_answers', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="manage_own_questions_answers">Manage Own Q&A</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="view_analytics" value="view_analytics"
                                                        {{ in_array('view_analytics', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="view_analytics">View
                                                        Analytics</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="view_own_analytics" value="view_own_analytics"
                                                        {{ in_array('view_own_analytics', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="view_own_analytics">View Own
                                                        Analytics</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- System Management -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fa fa-cog me-2"></i>System Management</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_translations" value="manage_translations"
                                                        {{ in_array('manage_translations', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_translations">Manage
                                                        Translations</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="manage_languages" value="manage_languages"
                                                        {{ in_array('manage_languages', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="manage_languages">Manage
                                                        Languages</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="export_data" value="export_data"
                                                        {{ in_array('export_data', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="export_data">Export Data</label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" name="permissions[]" class="form-check-input"
                                                        id="import_data" value="import_data"
                                                        {{ in_array('import_data', old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="import_data">Import Data</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Select All Permissions -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="select_all_permissions">
                                        <label class="form-check-label" for="select_all_permissions">
                                            <strong>Select All Permissions</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="text-end">
                                <a href="{{ route('admin.admin-types.index') }}"
                                    class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i>Create Admin Type
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
            const selectAllCheckbox = document.getElementById('select_all_permissions');
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Update select all when individual checkboxes change
            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(permissionCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(permissionCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                });
            });

            // Initialize select all state
            const allChecked = Array.from(permissionCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(permissionCheckboxes).some(cb => cb.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    </script>
@endpush
