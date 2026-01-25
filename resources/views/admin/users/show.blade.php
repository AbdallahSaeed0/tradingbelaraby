@extends('admin.layout')

@section('title', 'User Details')

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">User #{{ $user->id }}</div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=100&background=007bff&color=fff' }}"
                                class="rounded-circle" width="100" height="100" alt="avatar">
                        </div>
                        <table class="table table-borderless">
                            <tr>
                                <th>Name:</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            @if($user->country)
                            <tr>
                                <th>Country:</th>
                                <td>{{ $user->country }}</td>
                            </tr>
                            @endif
                            @if($user->phone)
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $user->phone }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Joined:</th>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                        <div class="text-end">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
