@extends('layouts.app')

@section('title', 'Questions & Answers')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4">Questions & Answers</h2>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $question)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $question->question }}</td>
                                <td>{{ $question->course->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $question->status == 'answered' ? 'success' : 'warning' }}">
                                        {{ ucfirst($question->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('qa.show', $question->id) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No questions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
