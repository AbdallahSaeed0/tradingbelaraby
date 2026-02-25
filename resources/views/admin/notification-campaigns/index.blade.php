@extends('admin.layout')

@section('title', 'Send Notification')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Notification Campaigns</h3>
            <a href="{{ route('admin.notification-campaigns.create') }}" class="btn btn-primary"><i class="fa fa-paper-plane me-1"></i>Send Notification</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Audience</th>
                                <th>Title (EN)</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Sent at</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $c)
                                <tr>
                                    <td>{{ $c->id }}</td>
                                    <td>{{ $c->audience_type }}</td>
                                    <td>{{ Str::limit($c->title_en, 40) }}</td>
                                    <td>{{ $c->priority }}</td>
                                    <td><span class="badge bg-{{ $c->status === 'sent' ? 'success' : ($c->status === 'scheduled' ? 'info' : 'secondary') }}">{{ $c->status }}</span></td>
                                    <td>{{ $c->sent_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td>{{ $c->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted">No campaigns yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
@endsection
