@extends('layouts.app')

@section('title', $quiz->localized_name . ' - Quiz')

@section('content')
    <!-- Hero Section -->
    <section class="quiz-hero py-5 position-relative">
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center text-white mb-5">
                        <div class="course-badge mb-3">
                            <i class="fas fa-graduation-cap me-2"></i>{{ $course->name }}
                        </div>
                        <h1 class="display-4 fw-bold mb-3">{{ $quiz->localized_name }}</h1>
                        <p class="lead mb-4 opacity-90">{{ $quiz->localized_description }}</p>

                        <!-- Quiz Stats -->
                        <div class="quiz-stats-card rounded-4 p-4 mb-4">
                            <div class="row g-3 justify-content-center">
                                <div class="col-auto">
                                    <div class="stat-pill">
                                        <i class="fas fa-question-circle"></i>
                                        {{ $quiz->questions->count() }} Questions
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="stat-pill">
                                        <i class="fas fa-clock"></i>
                                        {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' min' : 'No limit' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="stat-pill">
                                        <i class="fas fa-redo"></i>
                                        {{ $quiz->max_attempts ? $quiz->max_attempts . ' attempts' : 'Unlimited' }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="stat-pill">
                                        <i class="fas fa-trophy"></i>
                                        {{ $quiz->passing_score }}% to pass
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <!-- Quiz Instructions -->
                    <div class="quiz-instructions-card mb-5">
                        <div class="instructions-header" data-bs-toggle="collapse" data-bs-target="#instructionsCollapse"
                            aria-expanded="true">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span class="fw-bold">Quiz Instructions</span>
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                        <div class="collapse show" id="instructionsCollapse">
                            <div class="instructions-body">
                                <div class="instruction-item">
                                    <div class="instruction-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span>Read each question carefully before answering</span>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span>You can only select one answer per question</span>
                                </div>
                                @if ($quiz->time_limit_minutes)
                                    <div class="instruction-item">
                                        <div class="instruction-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <span>You have {{ $quiz->time_limit_minutes }} minutes to complete this quiz</span>
                                    </div>
                                @endif
                                @if ($quiz->max_attempts)
                                    <div class="instruction-item">
                                        <div class="instruction-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <span>You can attempt this quiz up to {{ $quiz->max_attempts }} times</span>
                                    </div>
                                @endif
                                <div class="instruction-item">
                                    <div class="instruction-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span>Your best score will be recorded</span>
                                </div>
                                <div class="instruction-item">
                                    <div class="instruction-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <span>You can review your answers before submitting</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Attempts -->
                    @if ($attempts->count() > 0)
                        <div class="mb-5">
                            <h4 class="fw-bold mb-4">
                                <i class="fas fa-history me-2 text-primary"></i>Previous Attempts
                            </h4>
                            <div class="attempts-table">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Score</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attempts as $attempt)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $attempt->created_at->format('M d, Y') }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ $attempt->created_at->format('g:i A') }}</small>
                                                </td>
                                                <td>
                                                    @if ($attempt->percentage_score !== null)
                                                        <span
                                                            class="score-badge {{ $attempt->percentage_score >= 80 ? 'score-excellent' : ($attempt->percentage_score >= 60 ? 'score-good' : 'score-poor') }}">
                                                            {{ number_format($attempt->percentage_score, 1) }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Not completed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="status-badge {{ $attempt->status == 'completed' ? 'status-completed' : 'status-in-progress' }}">
                                                        {{ ucfirst($attempt->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($attempt->status == 'completed')
                                                        <a href="{{ route('quizzes.results', ['quiz' => $quiz, 'attempt' => $attempt]) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>View Results
                                                        </a>
                                                    @else
                                                        <span class="text-muted">In Progress</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Start Quiz Section -->
                    <div class="start-quiz-section">
                        @if ($canTakeQuiz)
                            <div class="position-relative">
                                <h3 class="text-white fw-bold mb-3">Ready to Start?</h3>
                                <p class="text-white opacity-90 mb-4">Click the button below to begin your quiz. Good luck!
                                </p>
                                <button class="start-quiz-btn" onclick="startQuiz()">
                                    <i class="fas fa-play me-2"></i>Start Quiz
                                </button>
                            </div>
                        @else
                            <div class="position-relative">
                                <h3 class="text-white fw-bold mb-3">Quiz Not Available</h3>
                                <div class="warning-alert">
                                    <div class="alert-heading">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        You cannot take this quiz at this time.
                                    </div>
                                    <ul>
                                        @if (!$quiz->is_published)
                                            <li>This quiz is currently inactive</li>
                                        @endif
                                        @if ($quiz->required_lectures)
                                            <li>You need to complete {{ $quiz->required_lectures }} lectures first</li>
                                        @endif
                                        @if ($quiz->max_attempts && $attempts->count() >= $quiz->max_attempts)
                                            <li>You have reached the maximum number of attempts</li>
                                        @endif
                                        @if ($attempts->where('status', 'in_progress')->count() > 0)
                                            <li>You have an ongoing attempt. Please complete it first.</li>
                                        @endif
                                    </ul>
                                </div>
                                <a href="{{ route('courses.learn', $course->id) }}" class="back-btn">
                                    <i class="fas fa-arrow-left"></i>Back to Course
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Start Quiz Modal -->
    <div class="modal fade" id="startQuizModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient text-white bg-gradient-primary">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-play-circle me-2"></i>Confirm Quiz Start
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3">Are you sure you want to start the quiz
                        <strong>"{{ $quiz->localized_name }}"</strong>?
                    </p>
                    <div class="alert alert-info border-0 rounded-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important:</strong> Once you start, the timer will begin and you cannot pause the quiz.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary fw-bold" onclick="confirmStartQuiz()">
                        <i class="fas fa-play me-2"></i>Start Quiz
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function startQuiz() {
            const modal = new bootstrap.Modal(document.getElementById('startQuizModal'));
            modal.show();
        }

        function confirmStartQuiz() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('startQuizModal'));
            modal.hide();

            fetch('{{ route('quizzes.start', $quiz) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert('Error starting quiz: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error starting quiz. Please try again.');
                });
        }
    </script>
@endsection

