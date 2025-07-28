@extends('layouts.app')

@section('title', 'Homework Assignments')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Homework Assignments</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($homeworks as $homework)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $homework->title }}</td>
                                <td>{{ $homework->course->name ?? '-' }}</td>
                                <td>{{ $homework->due_date ? $homework->due_date->format('M d, Y') : '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $homework->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($homework->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('homework.show', $homework->id) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No homework assignments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
