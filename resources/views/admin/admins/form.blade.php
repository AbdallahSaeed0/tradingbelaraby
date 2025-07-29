@extends('admin.layout')

@section('title', $admin->exists ? 'Edit Admin' : 'Add Admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">
                        {{ $admin->exists ? 'Edit Admin' : 'Add Admin' }}
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ $admin->exists ? route('admin.admins.update', $admin) : route('admin.admins.store') }}"
                            method="POST">
                            @csrf
                            @if ($admin->exists)
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $admin->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $admin->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $admin->phone) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    @foreach (['admin' => 'Admin', 'instructor' => 'Instructor', 'employee' => 'Employee'] as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ old('type', $admin->type) == $key ? 'selected' : '' }}>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password
                                    {{ $admin->exists ? '(Leave blank to keep unchanged)' : '' }}</label>
                                <input type="password" name="password" class="form-control"
                                    {{ $admin->exists ? '' : 'required' }}>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button class="btn btn-primary">{{ $admin->exists ? 'Update' : 'Create' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
