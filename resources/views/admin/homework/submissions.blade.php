@extends('admin.layout')

@section('title', 'Homework Submissions')

@push('styles')
    <style>
        .submission-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.2s ease;
        }

        .submission-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .submission-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }

        .submission-body {
            padding: 15px;
        }

        .grade-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }

        .file-preview {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Homework Submissions</h1>
                        <p class="text-muted">Submissions for "{{ $homework->name }}"</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.homework.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Homework
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Homework Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title">{{ $homework->name }}</h5>
                        <p class="card-text text-muted">{{ $homework->description }}</p>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Course:</small><br>
                                <strong>{{ $homework->course->name }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Due Date:</small><br>
                                <strong>{{ $homework->due_date->format('M d, Y h:i A') }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Max Score:</small><br>
                                <strong>{{ $homework->max_score }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex flex-column align-items-end">
                            <span class="badge bg-{{ $homework->is_published ? 'success' : 'secondary' }} mb-2">
                                {{ $homework->is_published ? 'Published' : 'Draft' }}
                            </span>
                            <small class="text-muted">Submissions: {{ $submissions->total() }}</small>
                            <small class="text-muted">Graded: {{ $homework->graded_assignments }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.homework.submissions', $homework) }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted
                            </option>
                            <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned
                            </option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="late_only" class="form-label">Late Submissions</label>
                        <select class="form-select" id="late_only" name="late_only">
                            <option value="">All Submissions</option>
                            <option value="1" {{ request('late_only') == '1' ? 'selected' : '' }}>Late Only</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="graded_only" class="form-label">Graded Status</label>
                        <select class="form-select" id="graded_only" name="graded_only">
                            <option value="">All Submissions</option>
                            <option value="1" {{ request('graded_only') == '1' ? 'selected' : '' }}>Graded Only
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.homework.submissions', $homework) }}"
                                class="btn btn-outline-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submissions List -->
        @forelse($submissions as $submission)
            <div class="submission-card">
                <div class="submission-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fa fa-user-circle fa-2x text-muted"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $submission->user->name }}</h6>
                                    <small class="text-muted">{{ $submission->user->email }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex justify-content-end align-items-center gap-2">
                                <span
                                    class="badge status-badge bg-{{ $submission->status == 'graded' ? 'success' : ($submission->status == 'submitted' ? 'warning' : 'info') }}">
                                    {{ ucfirst($submission->status) }}
                                </span>
                                @if ($submission->is_late)
                                    <span class="badge status-badge bg-danger">{{ $submission->days_late }} days
                                        late</span>
                                @else
                                    <span class="badge status-badge bg-success">On time</span>
                                @endif
                                @if ($submission->is_graded)
                                    <span
                                        class="badge status-badge bg-primary">{{ number_format($submission->percentage_score, 1) }}%</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="submission-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Submission Details -->
                            <div class="mb-3">
                                <strong>Submitted:</strong> {{ $submission->submitted_at->format('M d, Y h:i A') }}
                                @if ($submission->is_late)
                                    <span class="text-danger">({{ $submission->submitted_at->diffForHumans() }})</span>
                                @endif
                            </div>

                            @if ($submission->submission_text)
                                <div class="mb-3">
                                    <strong>Text Submission:</strong>
                                    <div class="mt-2 p-3 bg-light rounded">
                                        {{ $submission->submission_text }}
                                    </div>
                                </div>
                            @endif

                            <!-- Files -->
                            @if ($submission->submission_file)
                                <div class="mb-3">
                                    <strong>Main File:</strong>
                                    <div class="file-preview">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-file me-2 text-primary"></i>
                                                <span>{{ basename($submission->submission_file) }}</span>
                                            </div>
                                            <a href="{{ asset('storage/' . $submission->submission_file) }}"
                                                class="btn btn-outline-primary btn-sm" target="_blank">
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($submission->additional_files && count($submission->additional_files) > 0)
                                <div class="mb-3">
                                    <strong>Additional Files:</strong>
                                    @foreach ($submission->additional_files as $file)
                                        <div class="file-preview">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-file me-2 text-primary"></i>
                                                    <span>{{ $file['name'] }}</span>
                                                </div>
                                                <a href="{{ asset('storage/' . $file['path']) }}"
                                                    class="btn btn-outline-primary btn-sm" target="_blank">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($submission->student_notes)
                                <div class="mb-3">
                                    <strong>Student Notes:</strong>
                                    <div class="mt-2 p-3 bg-light rounded">
                                        {{ $submission->student_notes }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Grading Section -->
                            @if ($submission->is_graded)
                                <div class="grade-form">
                                    <h6>Grading Results</h6>
                                    <div class="mb-2">
                                        <strong>Score:</strong>
                                        {{ $submission->score_earned }}/{{ $submission->max_score }}
                                    </div>
                                    <div class="mb-2">
                                        <strong>Percentage:</strong> {{ number_format($submission->percentage_score, 1) }}%
                                    </div>
                                    <div class="mb-2">
                                        <strong>Graded:</strong> {{ $submission->graded_at->format('M d, Y h:i A') }}
                                    </div>
                                    @if ($submission->feedback)
                                        <div class="mb-2">
                                            <strong>Feedback:</strong>
                                            <div class="mt-1 p-2 bg-white rounded border">
                                                {{ $submission->feedback }}
                                            </div>
                                        </div>
                                    @endif
                                    @if ($submission->instructor_notes)
                                        <div class="mb-2">
                                            <strong>Instructor Notes:</strong>
                                            <div class="mt-1 p-2 bg-white rounded border">
                                                {{ $submission->instructor_notes }}
                                            </div>
                                        </div>
                                    @endif
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="editGrade({{ $submission->id }})">
                                        <i class="fa fa-edit"></i> Edit Grade
                                    </button>
                                </div>
                            @else
                                <div class="grade-form">
                                    <h6>Grade Submission</h6>
                                    <form onsubmit="gradeSubmission(event, {{ $submission->id }})">
                                        <div class="mb-3">
                                            <label for="score_{{ $submission->id }}" class="form-label">Score</label>
                                            <input type="number" class="form-control" id="score_{{ $submission->id }}"
                                                name="score_earned" min="0" max="{{ $submission->max_score }}"
                                                required>
                                            <small class="text-muted">Max: {{ $submission->max_score }}</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="feedback_{{ $submission->id }}"
                                                class="form-label">Feedback</label>
                                            <textarea class="form-control" id="feedback_{{ $submission->id }}" name="feedback" rows="3"
                                                placeholder="Provide feedback to the student..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes_{{ $submission->id }}" class="form-label">Instructor
                                                Notes</label>
                                            <textarea class="form-control" id="notes_{{ $submission->id }}" name="instructor_notes" rows="2"
                                                placeholder="Private notes..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa fa-check"></i> Grade Submission
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fa fa-upload fa-3x text-muted mb-3"></i>
                    <h5>No submissions found</h5>
                    <p class="text-muted">No submissions match your current filters</p>
                </div>
            </div>
        @endforelse

        <!-- Pagination -->
        @if ($submissions->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function gradeSubmission(event, submissionId) {
                event.preventDefault();

                const form = event.target;
                const formData = new FormData(form);

                fetch(`/admin/homework/submissions/${submissionId}/grade`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error grading submission');
                    });
            }

            function editGrade(submissionId) {
                // This would open a modal or redirect to an edit page
                alert('Edit grade functionality would be implemented here');
            }
        </script>
    @endpush
@endsection
