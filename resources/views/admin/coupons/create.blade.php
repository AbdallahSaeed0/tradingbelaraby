@extends('admin.layout')

@section('title', 'Create New Coupon')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Create New Coupon</h1>
                        <p class="text-muted">Create a discount coupon</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Coupons
                        </a>
                        <button type="submit" form="couponForm" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Create Coupon
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h5 class="alert-heading"><i class="fa fa-exclamation-triangle me-2"></i>Please fix the following errors:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="couponForm" action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                        id="code" name="code" value="{{ old('code') }}" required style="text-transform: uppercase;">
                                    <small class="text-muted">Will be converted to uppercase</small>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Coupon Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discount Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-percent me-2"></i>Discount Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="discount_type" class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('discount_type') is-invalid @enderror" 
                                        id="discount_type" name="discount_type" required>
                                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                    @error('discount_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="discount_value" class="form-label">Discount Value <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('discount_value') is-invalid @enderror" 
                                        id="discount_value" name="discount_value" step="0.01" min="0" 
                                        value="{{ old('discount_value') }}" required>
                                    <small class="text-muted" id="discount_hint">Enter percentage (0-100) or fixed amount</small>
                                    @error('discount_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scope Settings -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-bullseye me-2"></i>Scope Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="scope" class="form-label">Course Scope <span class="text-danger">*</span></label>
                                    <select class="form-select @error('scope') is-invalid @enderror" 
                                        id="scope" name="scope" required>
                                        <option value="all_courses" {{ old('scope') == 'all_courses' ? 'selected' : '' }}>All Courses</option>
                                        <option value="specific_course" {{ old('scope') == 'specific_course' ? 'selected' : '' }}>Specific Course</option>
                                    </select>
                                    @error('scope')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3" id="course_select_wrapper" style="display: none;">
                                    <label for="course_id" class="form-label">Select Course</label>
                                    <select class="form-select @error('course_id') is-invalid @enderror" 
                                        id="course_id" name="course_id">
                                        <option value="">Select a course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="user_scope" class="form-label">User Scope <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_scope') is-invalid @enderror" 
                                        id="user_scope" name="user_scope" required>
                                        <option value="all_users" {{ old('user_scope') == 'all_users' ? 'selected' : '' }}>All Users</option>
                                        <option value="specific_user" {{ old('user_scope') == 'specific_user' ? 'selected' : '' }}>Specific User</option>
                                    </select>
                                    @error('user_scope')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3" id="user_select_wrapper" style="display: none;">
                                    <label for="user_id" class="form-label">Select User</label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id">
                                        <option value="">Select a user</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Validity Period -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-calendar me-2"></i>Validity Period</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                    id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                    id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Usage Limits -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-chart-line me-2"></i>Usage Limits</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">Global Usage Limit</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                    id="usage_limit" name="usage_limit" min="1" value="{{ old('usage_limit') }}">
                                <small class="text-muted">Leave empty for unlimited</small>
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="per_user_limit" class="form-label">Per User Limit <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('per_user_limit') is-invalid @enderror" 
                                    id="per_user_limit" name="per_user_limit" min="1" value="{{ old('per_user_limit', 1) }}" required>
                                @error('per_user_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-toggle-on me-2"></i>Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        // Show/hide course select based on scope
        document.getElementById('scope').addEventListener('change', function() {
            const courseWrapper = document.getElementById('course_select_wrapper');
            if (this.value === 'specific_course') {
                courseWrapper.style.display = 'block';
                document.getElementById('course_id').required = true;
            } else {
                courseWrapper.style.display = 'none';
                document.getElementById('course_id').required = false;
            }
        });

        // Show/hide user select based on user scope
        document.getElementById('user_scope').addEventListener('change', function() {
            const userWrapper = document.getElementById('user_select_wrapper');
            if (this.value === 'specific_user') {
                userWrapper.style.display = 'block';
                document.getElementById('user_id').required = true;
            } else {
                userWrapper.style.display = 'none';
                document.getElementById('user_id').required = false;
            }
        });

        // Update discount hint based on type
        document.getElementById('discount_type').addEventListener('change', function() {
            const hint = document.getElementById('discount_hint');
            if (this.value === 'percentage') {
                hint.textContent = 'Enter percentage (0-100)';
            } else {
                hint.textContent = 'Enter fixed amount in SAR';
            }
        });

        // Trigger on page load
        document.getElementById('scope').dispatchEvent(new Event('change'));
        document.getElementById('user_scope').dispatchEvent(new Event('change'));
    </script>
    @endpush
@endsection

