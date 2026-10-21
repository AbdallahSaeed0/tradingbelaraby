@extends('admin.layout')

@section('title', 'User Details')

@section('content')
    <div class="container-fluid py-4 admin-detail-page">
        @include('admin.partials.detail-page-header', [
            'title' => $user->name,
            'subtitle' => 'User #' . $user->id,
            'backUrl' => route('admin.users.index'),
            'backLabel' => 'Users',
            'primaryUrl' => route('admin.users.edit', $user),
            'primaryLabel' => 'Edit User',
        ])

        <div class="row admin-detail-main-row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm" id="detail-section-info">
                    <div class="card-header fw-semibold">Profile</div>
                    <div class="card-body admin-detail-grid">
                        <div class="d-flex justify-content-center mb-4">
                            <div class="user-avatar user-avatar--lg">
                                <i class="fa fa-user"></i>
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                        onerror="this.remove()">
                                @endif
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Name</strong>
                                <span class="admin-detail-value">{{ $user->name }}</span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Email</strong>
                                <span class="admin-detail-value">{{ $user->email }}</span>
                            </div>
                        </div>
                        @if ($user->country || $user->phone)
                            <div class="row mb-0">
                                @if ($user->country)
                                    <div class="col-md-6 admin-detail-field">
                                        <strong>Country</strong>
                                        <span class="admin-detail-value">{{ $user->country }}</span>
                                    </div>
                                @endif
                                @if ($user->phone)
                                    <div class="col-md-6 admin-detail-field">
                                        <strong>Phone</strong>
                                        <span class="admin-detail-value">{{ $user->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="admin-detail-field">
                            <strong>Joined</strong>
                            <span class="admin-detail-value">{{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
