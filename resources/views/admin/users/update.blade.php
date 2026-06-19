@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
    <div class="container-fluid py-4 admin-form-page" data-mobile-back-url="{{ route('admin.users.index') }}" data-mobile-back-label="Back to Users">
        @include('admin.partials.crud-form-shell', [
            'title' => 'Edit User',
            'subtitle' => $user->name,
            'backUrl' => route('admin.users.index'),
            'backLabel' => 'Back to Users',
            'formId' => 'userForm',
            'submitLabel' => 'Update User',
            'sections' => [
                ['id' => 'section-profile', 'label' => 'Profile', 'icon' => 'fa-user'],
            ],
        ])

        <div class="row justify-content-center admin-form-main-row">
            <div class="col-lg-6 admin-form-main">
                <div class="card shadow-sm" id="section-profile">
                    <div class="card-header fw-semibold">User Details</div>
                    <div class="card-body">
                        <form id="userForm" action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select name="country" class="form-select" required>
                                    <option value="">Select Country</option>
                                    @include('partials.countries', ['selected' => old('country', $user->country)])
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+1234567890" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password (leave blank to keep unchanged)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="activeUser" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activeUser">Active</label>
                            </div>
                            <div class="text-end d-none d-lg-block">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button class="btn btn-primary">Update User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
