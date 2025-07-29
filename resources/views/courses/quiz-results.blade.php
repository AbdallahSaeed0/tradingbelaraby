@extends('layouts.app')

@section('title', 'Quiz Results - ' . $quiz->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/quiz.css') }}">
@endpush

@section('content')
    <!-- Banner Section -->
    <section class="quiz-banner position-relative d-flex align-items-center justify-content-center"
        style="min-height: 340px;">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="quiz-banner-bg position-absolute w-100 h-100 top-0 start-0" style="object-fit:cover; z-index:1;">
        <div class="quiz-banner-overlay position-absolute w-100 h-100 top-0 start-0"
            style="background:rgba(24,49,63,0.65); z-index:2;"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">Quiz Results</h1>
            <p class="text-white mb-3">{{ $quiz->name }}</p>
        </div>
    </section>

    <!-- Results Section -->
    <section class="results-section py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Results Summary Card -->
                    <div class="results-summary bg-white rounded-4 shadow-sm p-4 p-md-5 mb-4">
                        <div class="text-center mb-4">
                            @if ($attempt->is_passed)
                                <div class="mb-3">
                                    <i class="fa fa-trophy text-success" style="font-size: 4rem;"></i>
                                </div>
                                <h2 class="fw-bold text-success mb-2">Congratulations!</h2>
                                <p class="text-muted">You have successfully passed this quiz.</p>
                            @else
                                <div class="mb-3">
                                    <i class="fa fa-times-circle text-danger" style="font-size: 4rem;"></i>
                                </div>
                                <h2 class="fw-bold text-danger mb-2">Quiz Completed</h2>
                                <p class="text-muted">You need {{ $quiz->passing_score }}% to pass. Keep trying!</p>
                            @endif
                        </div>

                        <!-- Score Summary -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="score-card text-center p-3 bg-light rounded">
                                    <div class="score-value h3 fw-bold text-primary">
                                        {{ $attempt->score_earned }}/{{ $attempt->total_possible_score }}</div>
                                    <div class="score-label text-muted">Total Score</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="score-card text-center p-3 bg-light rounded">
                                    <div
                                        class="score-value h3 fw-bold {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($attempt->percentage_score, 1) }}%
                                    </div>
                                    <div class="score-label text-muted">Percentage</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="score-card text-center p-3 bg-light rounded">
                                    <div class="score-value h3 fw-bold text-info">{{ $attempt->time_taken_minutes ?? 0 }}m
                                    </div>
                                    <div class="score-label text-muted">Time Taken</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="score-card text-center p-3 bg-light rounded">
                                    <div class="score-value h3 fw-bold text-warning">{{ $attempt->attempt_number }}</div>
                                    <div class="score-label text-muted">Attempt #</div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress-section mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">Your Score</span>
                                <span class="text-muted">{{ number_format($attempt->percentage_score, 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}"
                                    role="progressbar" style="width: {{ $attempt->percentage_score }}%"
                                    aria-valuenow="{{ $attempt->percentage_score }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ number_format($attempt->percentage_score, 1) }}%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0%</small>
                                <small class="text-muted">Passing Score: {{ $quiz->passing_score }}%</small>
                                <small class="text-muted">100%</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('courses.learn', $quiz->course_id) }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-2"></i>Back to Course
                            </a>
                            @if ($quiz->allow_retake && $attempt->attempt_number < $quiz->max_attempts)
                                <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-primary">
                                    <i class="fa fa-redo me-2"></i>Retake Quiz
                                </a>
                            @endif
                            <a href="{{ route('quizzes.attempts', $quiz) }}" class="btn btn-outline-info">
                                <i class="fa fa-history me-2"></i>View All Attempts
                            </a>
                        </div>
                    </div>

                    <!-- Detailed Results -->
                    <div class="detailed-results bg-white rounded-4 shadow-sm p-4 p-md-5">
                        <h3 class="fw-bold mb-4">
                            <i class="fa fa-list-alt me-2 text-primary"></i>Detailed Results
                        </h3>

                        @foreach ($questions as $index => $question)
                            <div
                                class="question-result mb-4 p-4 border rounded {{ $question->is_correct ? 'border-success bg-light' : 'border-danger bg-light' }}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="fw-bold mb-0">
                                        Question {{ $index + 1 }}: {{ $question->question_text }}
                                    </h5>
                                    <div class="question-status">
                                        @if ($question->is_correct)
                                            <span class="badge bg-success">
                                                <i class="fa fa-check me-1"></i>Correct
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fa fa-times me-1"></i>Incorrect
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="question-meta text-muted mb-3">
                                    <small>
                                        <i class="fa fa-star me-1"></i>{{ $question->points }} points |
                                        <i
                                            class="fa fa-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                    </small>
                                </div>

                                <!-- User Answer -->
                                <div class="user-answer mb-3">
                                    <strong>Your Answer:</strong>
                                    <div class="mt-2">
                                        @if ($question->question_type === 'multiple_choice' && $question->options)
                                            @php
                                                $userAnswerIndex = $question->user_answer;
                                                $userAnswerText =
                                                    $question->options[$userAnswerIndex] ?? 'No answer provided';
                                            @endphp
                                            <span
                                                class="badge {{ $question->is_correct ? 'bg-success' : 'bg-danger' }} fs-6">
                                                {{ $userAnswerText }}
                                            </span>
                                        @elseif($question->question_type === 'true_false')
                                            <span
                                                class="badge {{ $question->is_correct ? 'bg-success' : 'bg-danger' }} fs-6">
                                                {{ $question->user_answer ? 'True' : 'False' }}
                                            </span>
                                        @elseif($question->question_type === 'fill_blank')
                                            <span
                                                class="badge {{ $question->is_correct ? 'bg-success' : 'bg-danger' }} fs-6">
                                                {{ $question->user_answer ?: 'No answer provided' }}
                                            </span>
                                        @else
                                            <span class="text-muted">No answer provided</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Correct Answer (if incorrect) -->
                                @if (!$question->is_correct)
                                    <div class="correct-answer mb-3">
                                        <strong class="text-success">Correct Answer:</strong>
                                        <div class="mt-2">
                                            @if ($question->question_type === 'multiple_choice' && $question->correct_answers)
                                                @foreach ($question->correct_answers as $correctIndex)
                                                    <span class="badge bg-success fs-6 me-2">
                                                        {{ $question->options[$correctIndex] ?? 'N/A' }}
                                                    </span>
                                                @endforeach
                                            @elseif($question->question_type === 'true_false')
                                                <span class="badge bg-success fs-6">
                                                    {{ $question->correct_answer_boolean ? 'True' : 'False' }}
                                                </span>
                                            @elseif($question->question_type === 'fill_blank' && $question->correct_answers_text)
                                                @foreach ($question->correct_answers_text as $correctAnswer)
                                                    <span class="badge bg-success fs-6 me-2">{{ $correctAnswer }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Explanation -->
                                @if ($question->explanation)
                                    <div class="explanation">
                                        <strong class="text-info">Explanation:</strong>
                                        <p class="mt-2 mb-0 text-muted">{{ $question->explanation }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Quiz Statistics -->
                    <div class="quiz-statistics bg-white rounded-4 shadow-sm p-4 p-md-5 mt-4">
                        <h3 class="fw-bold mb-4">
                            <i class="fa fa-chart-bar me-2 text-primary"></i>Quiz Statistics
                        </h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="stat-card p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stat-label text-muted">Total Questions</div>
                                            <div class="stat-value h4 fw-bold text-primary">{{ $questions->count() }}
                                            </div>
                                        </div>
                                        <i class="fa fa-question-circle text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stat-label text-muted">Correct Answers</div>
                                            <div class="stat-value h4 fw-bold text-success">
                                                {{ $questions->where('is_correct', true)->count() }}
                                            </div>
                                        </div>
                                        <i class="fa fa-check-circle text-success" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stat-label text-muted">Incorrect Answers</div>
                                            <div class="stat-value h4 fw-bold text-danger">
                                                {{ $questions->where('is_correct', false)->count() }}
                                            </div>
                                        </div>
                                        <i class="fa fa-times-circle text-danger" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="stat-label text-muted">Accuracy Rate</div>
                                            <div class="stat-value h4 fw-bold text-info">
                                                {{ $questions->count() > 0 ? number_format(($questions->where('is_correct', true)->count() / $questions->count()) * 100, 1) : 0 }}%
                                            </div>
                                        </div>
                                        <i class="fa fa-percentage text-info" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Add any additional JavaScript for results page
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll to detailed results
            const detailedResults = document.querySelector('.detailed-results');
            if (detailedResults) {
                detailedResults.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    </script>
@endpush
