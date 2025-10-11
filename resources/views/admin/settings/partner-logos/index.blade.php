@extends('admin.layout')

@section('title', 'Partner Logos Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Partner Logos</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Partner Logos Management</h4>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="header-title">Partner Logos</h4>
                            <a href="{{ route('admin.partner-logos.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add New Logo
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>Link</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logos as $logo)
                                        <tr>
                                            <td>
                                                <img src="{{ $logo->logo_url }}" alt="{{ $logo->name }}"
                                                    style="max-height: 50px; max-width: 100px;">
                                            </td>
                                            <td>{{ $logo->name }}</td>
                                            <td>
                                                @if ($logo->link)
                                                    <a href="{{ $logo->link }}" target="_blank" class="text-primary">
                                                        <i class="fas fa-external-link-alt me-1"></i>Link
                                                    </a>
                                                @else
                                                    <span class="text-muted">No Link</span>
                                                @endif
                                            </td>
                                            <td>{{ $logo->order }}</td>
                                            <td>
                                                @if ($logo->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.partner-logos.edit', $logo) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.partner-logos.destroy', $logo) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="fas fa-image fa-3x mb-3 d-block"></i>
                                                No partner logos found. Add your first logo to get started!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
