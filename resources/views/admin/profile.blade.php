@extends('admin.layout')

@section('title', 'My Profile')

@section('content')
    <div class="container-fluid">
        <h3 class="mb-4">My Profile</h3>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $admin->name) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $admin->email) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $admin->phone) }}">
                            </div>
                            <div class="text-end">
                                <button class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
