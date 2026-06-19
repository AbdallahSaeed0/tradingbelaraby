@extends('admin.layout')

@section('title', 'Admin Details')

@section('content')
    <div class="container-fluid py-4 admin-detail-page">
        @include('admin.partials.detail-page-header', [
            'title' => $admin->name,
            'subtitle' => 'Admin #' . $admin->id . ' · ' . ucfirst($admin->type),
            'backUrl' => route('admin.admins.index'),
            'backLabel' => 'Admins',
            'primaryUrl' => route('admin.admins.edit', $admin),
            'primaryLabel' => 'Edit Admin',
        ])

        <div class="row admin-detail-main-row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm" id="detail-section-info">
                    <div class="card-header fw-semibold">Profile</div>
                    <div class="card-body admin-detail-grid">
                        <div class="text-center mb-4">
                            <img src="{{ $admin->avatar_url }}" class="rounded-circle" width="100" height="100" alt="avatar">
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Name</strong>
                                <span class="admin-detail-value">{{ $admin->name }}</span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Email</strong>
                                <span class="admin-detail-value">{{ $admin->email }}</span>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-md-6 admin-detail-field">
                                <strong>Type</strong>
                                <span class="admin-detail-value">
                                    <span class="badge bg-{{ $admin->type == 'admin' ? 'danger' : ($admin->type == 'instructor' ? 'warning text-dark' : 'info') }}">
                                        {{ ucfirst($admin->type) }}
                                    </span>
                                </span>
                            </div>
                            <div class="col-md-6 admin-detail-field">
                                <strong>Status</strong>
                                <span class="admin-detail-value">
                                    <span class="badge bg-{{ $admin->is_active ? 'success' : 'secondary' }}">
                                        {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="admin-detail-field">
                            <strong>Joined</strong>
                            <span class="admin-detail-value">{{ $admin->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
