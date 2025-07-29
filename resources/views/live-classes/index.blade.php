@extends('layouts.app')

@section('title', 'Live Classes')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Live Classes</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Scheduled At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($liveClasses as $liveClass)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $liveClass->title }}</td>
                                <td>{{ $liveClass->course->name ?? '-' }}</td>
                                <td>{{ $liveClass->scheduled_at ? $liveClass->scheduled_at->format('M d, Y H:i') : '-' }}
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $liveClass->status == 'scheduled' ? 'info' : ($liveClass->status == 'completed' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($liveClass->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('live-classes.show', $liveClass->id) }}"
                                        class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No live classes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
