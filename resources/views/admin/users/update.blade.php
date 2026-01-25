@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Edit User</div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" required>
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
                                <input type="tel" name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}" placeholder="+1234567890" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password (leave blank to keep unchanged)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="activeUser"
                                    value="1" {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="activeUser">Active</label>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
