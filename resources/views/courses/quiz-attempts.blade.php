@extends('layouts.app')

@section('title', 'Quiz Attempts - ' . $quiz->localized_name)

@section('content')
    <!-- Banner Section -->
    <section class="quiz-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="quiz-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="quiz-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">Quiz Attempts</h1>
            <p class="text-white mb-3">{{ $quiz->localized_name }}</p>
        </div>
    </section>

    <!-- Attempts Section -->
    <section class="attempts-section py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="fw-bold">
                            <i class="fa fa-history me-2 text-primary"></i>Your Quiz Attempts
                        </h2>
                        <div>
                            <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-primary">
                                <i class="fa fa-play me-2"></i>Take Quiz Again
                            </a>
                            <a href="{{ route('courses.learn', $quiz->course_id) }}" class="btn btn-outline-secondary">
                                <i class="fa fa-arrow-left me-2"></i>Back to Course
                            </a>
                        </div>
                    </div>

                    @if ($attempts->count() > 0)
                        <!-- Attempts List -->
                        <div class="attempts-list">
                            @foreach ($attempts as $attempt)
                                <div class="attempt-card bg-white rounded-4 shadow-sm p-4 mb-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <h5 class="fw-bold mb-0 me-3">Attempt #{{ $attempt->attempt_number }}</h5>
                                                @if ($attempt->is_passed)
                                                    <span class="badge bg-success">
                                                        <i class="fa fa-check me-1"></i>Passed
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fa fa-times me-1"></i>Failed
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="attempt-details">
                                                <div class="row g-3">
                                                    <div class="col-sm-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fa fa-calendar me-1"></i>
                                                            {{ $attempt->created_at->format('M d, Y g:i A') }}
                                                        </small>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <small class="text-muted d-block">
                                                            <i class="fa fa-clock me-1"></i>
                                                            {{ $attempt->time_taken_minutes ?? 0 }} minutes
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 text-end">
                                            <div class="score-display mb-2">
                                                <div
                                                    class="h4 fw-bold {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($attempt->percentage_score, 1) }}%
                                                </div>
                                                <small class="text-muted">
                                                    {{ $attempt->score_earned }}/{{ $attempt->total_possible_score }}
                                                    points
                                                </small>
                                            </div>

                                            <div class="attempt-actions">
                                                <a href="{{ route('quizzes.results', ['quiz' => $quiz, 'attempt' => $attempt]) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye me-1"></i>View Results
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Statistics Summary -->
                        <div class="statistics-summary bg-white rounded-4 shadow-sm p-4 mt-4">
                            <h4 class="fw-bold mb-3">
                                <i class="fa fa-chart-pie me-2 text-primary"></i>Attempts Summary
                            </h4>
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <div class="stat-item text-center">
                                        <div class="stat-value h3 fw-bold text-primary">{{ $attempts->count() }}</div>
                                        <div class="stat-label text-muted">Total Attempts</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item text-center">
                                        <div class="stat-value h3 fw-bold text-success">
                                            {{ $attempts->where('is_passed', true)->count() }}
                                        </div>
                                        <div class="stat-label text-muted">Passed</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item text-center">
                                        <div class="stat-value h3 fw-bold text-danger">
                                            {{ $attempts->where('is_passed', false)->count() }}
                                        </div>
                                        <div class="stat-label text-muted">Failed</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item text-center">
                                        <div class="stat-value h3 fw-bold text-info">
                                            {{ $attempts->count() > 0 ? number_format($attempts->avg('percentage_score'), 1) : 0 }}%
                                        </div>
                                        <div class="stat-label text-muted">Average Score</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- No Attempts -->
                        <div class="no-attempts text-center py-5">
                            <div class="mb-4">
                                <i class="fa fa-question-circle text-muted fs-4rem"></i>
                            </div>
                            <h3 class="fw-bold mb-3">No Quiz Attempts Yet</h3>
                            <p class="text-muted mb-4">You haven't taken this quiz yet. Click the button below to start your
                                first attempt.</p>
                            <a href="{{ route('quizzes.show', $quiz) }}" class="btn btn-primary btn-lg">
                                <i class="fa fa-play me-2"></i>Start Quiz
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

