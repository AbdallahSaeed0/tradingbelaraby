@extends('admin.layout')

@section('title', 'Admin Details')

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Admins</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Admin #{{ $admin->id }}</div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="{{ $admin->avatar ? asset('storage/' . $admin->avatar) : 'https://i.pravatar.cc/100?u=' . $admin->id }}"
                                class="rounded-circle" width="100" height="100" alt="avatar">
                        </div>
                        <table class="table table-borderless">
                            <tr>
                                <th>Name:</th>
                                <td>{{ $admin->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $admin->email }}</td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>{{ ucfirst($admin->type) }}</td>
                            </tr>
                            <tr>
                                <th>Joined:</th>
                                <td>{{ $admin->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                        <div class="text-end">
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-outline-secondary">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
