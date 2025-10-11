@extends('admin.layout')

@section('title', $quiz->name)

@push('styles')
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
        }

        .question-preview {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f8f9fa;
        }

        .option-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background-color: white;
        }

        .option-item.correct {
            border-color: #28a745;
            background-color: #d4edda;
        }

        .question-type-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .points-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
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
                        <h1 class="h3 mb-0">{{ $quiz->name }}</h1>
                        <p class="text-muted">{{ $quiz->course->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Quizzes
                        </a>
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-primary">
                            <i class="fa fa-edit me-2"></i>Edit Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quiz Details -->
            <div class="col-lg-8">
                <!-- Quiz Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Quiz Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name (English):</strong></p>
                                <p class="text-muted">{{ $quiz->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Name (Arabic):</strong></p>
                                <p class="text-muted" dir="rtl">{{ $quiz->name_ar ?: 'لا يوجد' }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Description (English):</strong></p>
                                <p class="text-muted">{{ $quiz->description ?: 'No description provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Description (Arabic):</strong></p>
                                <p class="text-muted" dir="rtl">{{ $quiz->description_ar ?: 'لا يوجد' }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Instructions (English):</strong></p>
                                <p class="text-muted">{{ $quiz->instructions ?: 'No instructions provided' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Instructions (Arabic):</strong></p>
                                <p class="text-muted" dir="rtl">{{ $quiz->instructions_ar ?: 'لا يوجد' }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Course:</strong></p>
                                <p class="text-muted">{{ $quiz->course->name }}</p>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <small class="text-muted">Time Limit</small><br>
                                <span
                                    class="text-muted">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' minutes' : 'No limit' }}</span>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Passing Score</small><br>
                                <span class="text-muted">{{ $quiz->passing_score }}%</span>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Total Questions</small><br>
                                <span class="text-muted">{{ $quiz->questions->count() }}</span>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Total Points</small><br>
                                <span class="text-muted">{{ $quiz->questions->sum('points') }}</span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <small class="text-muted">Available From</small><br>
                                <span
                                    class="text-muted">{{ $quiz->available_from ? $quiz->available_from->format('M d, Y H:i') : 'No start date' }}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Available Until</small><br>
                                <span
                                    class="text-muted">{{ $quiz->available_until ? $quiz->available_until->format('M d, Y H:i') : 'No end date' }}</span>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <small class="text-muted">Quiz Connection</small><br>
                                @php
                                    $connectionType = 'Course';
                                    $connectionDetail = '';
                                    if ($quiz->lecture_id && $quiz->lecture) {
                                        $connectionType = 'Lecture';
                                        $connectionDetail = ' - ' . $quiz->lecture->title;
                                    } elseif ($quiz->section_id && $quiz->section) {
                                        $connectionType = 'Section';
                                        $connectionDetail = ' - ' . $quiz->section->title;
                                    }
                                @endphp
                                <span class="badge bg-primary">
                                    <i class="fa fa-link me-1"></i>{{ $connectionType }}{{ $connectionDetail }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Settings</small><br>
                                <div class="d-flex gap-3">
                                    <span class="badge bg-{{ $quiz->is_published ? 'success' : 'warning' }}">
                                        <i class="fa fa-{{ $quiz->is_published ? 'check' : 'clock' }} me-1"></i>
                                        {{ $quiz->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                    @if ($quiz->is_randomized)
                                        <span class="badge bg-info">
                                            <i class="fa fa-random me-1"></i>Randomized
                                        </span>
                                    @endif
                                    @if ($quiz->show_results_immediately)
                                        <span class="badge bg-success">
                                            <i class="fa fa-eye me-1"></i>Show Results
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Preview -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-question-circle me-2"></i>Questions Preview
                            ({{ $quiz->questions->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @forelse($quiz->questions->take(5) as $question)
                            <div class="question-preview">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span
                                            class="badge question-type-badge bg-primary me-2">{{ $question->formatted_question_type }}</span>
                                        <span class="badge points-badge">{{ $question->points }} pts</span>
                                    </div>
                                </div>
                                <h6 class="mb-2">{{ Str::limit($question->question_text, 100) }}</h6>

                                @if ($question->question_type === 'multiple_choice' && $question->options)
                                    <div class="mt-2">
                                        @foreach (array_slice($question->options, 0, 3) as $index => $option)
                                            <div
                                                class="option-item {{ in_array($index, $question->correct_answers ?? []) ? 'correct' : '' }}">
                                                <i
                                                    class="fa fa-{{ in_array($index, $question->correct_answers ?? []) ? 'check-circle text-success' : 'circle text-muted' }} me-2"></i>
                                                {{ Str::limit($option, 50) }}
                                            </div>
                                        @endforeach
                                        @if (count($question->options) > 3)
                                            <small class="text-muted">+{{ count($question->options) - 3 }} more
                                                options</small>
                                        @endif
                                    </div>
                                @elseif($question->question_type === 'true_false')
                                    <div class="mt-2">
                                        <div class="option-item {{ $question->correct_answer_boolean ? 'correct' : '' }}">
                                            <i
                                                class="fa fa-{{ $question->correct_answer_boolean ? 'check-circle text-success' : 'circle text-muted' }} me-2"></i>
                                            True
                                        </div>
                                        <div class="option-item {{ !$question->correct_answer_boolean ? 'correct' : '' }}">
                                            <i
                                                class="fa fa-{{ !$question->correct_answer_boolean ? 'check-circle text-success' : 'circle text-muted' }} me-2"></i>
                                            False
                                        </div>
                                    </div>
                                @elseif($question->question_type === 'fill_blank' && $question->correct_answers_text)
                                    <div class="mt-2">
                                        <p class="mb-1"><strong>Correct Answers:</strong></p>
                                        @foreach (array_slice($question->correct_answers_text, 0, 2) as $answer)
                                            <span class="badge bg-success me-1">{{ $answer }}</span>
                                        @endforeach
                                        @if (count($question->correct_answers_text) > 2)
                                            <small class="text-muted">+{{ count($question->correct_answers_text) - 2 }}
                                                more</small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No questions added yet.</p>
                            </div>
                        @endforelse

                        @if ($quiz->questions->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-outline-primary">
                                    <i class="fa fa-edit me-2"></i>Edit Quiz to See All Questions
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="stats-card mb-3">
                            <h3>{{ $stats['total_questions'] }}</h3>
                            <p class="mb-0">Total Questions</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1">{{ $stats['total_attempts'] }}</h4>
                                    <small class="text-muted">Total Attempts</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1">{{ $stats['completed_attempts'] }}</h4>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1">{{ number_format($stats['average_score'], 1) }}%</h4>
                                    <small class="text-muted">Avg Score</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="mb-1">{{ number_format($stats['pass_rate'], 1) }}%</h4>
                                    <small class="text-muted">Pass Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.quizzes.attempts', $quiz) }}" class="btn btn-outline-primary">
                                <i class="fa fa-users me-2"></i>View Attempts
                            </a>
                            <a href="{{ route('admin.quizzes.analytics', $quiz) }}" class="btn btn-outline-info">
                                <i class="fa fa-chart-line me-2"></i>Analytics
                            </a>
                            <a href="{{ route('admin.quizzes.export', $quiz) }}" class="btn btn-outline-success">
                                <i class="fa fa-download me-2"></i>Export Results
                            </a>
                            <button class="btn btn-outline-warning" onclick="duplicateQuiz()">
                                <i class="fa fa-copy me-2"></i>Duplicate Quiz
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Total Marks -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-star me-2"></i>Total Marks</h5>
                    </div>
                    <div class="card-body">
                        <div class="stats-card">
                            <h3>{{ $quiz->questions->sum('points') }}</h3>
                            <p class="mb-0">Total Points</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function duplicateQuiz() {
            if (confirm('Are you sure you want to duplicate this quiz?')) {
                fetch('{{ route('admin.quizzes.duplicate', $quiz) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Error duplicating quiz: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error duplicating quiz');
                    });
            }
        }
    </script>
@endpush
