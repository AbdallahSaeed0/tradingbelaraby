@extends('layouts.app')

@section('title', $quiz->localized_name . ' - Quiz')

@push('styles')
    <style>
        .quiz-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .quiz-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .quiz-stats-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .quiz-stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-pill {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50px;
            padding: 8px 16px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 4px;
            transition: all 0.3s ease;
        }

        .stat-pill:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .quiz-instructions-card {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            overflow: hidden;
        }

        .instructions-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .instructions-header:hover {
            background: linear-gradient(135deg, #e685f0 0%, #e54b5f 100%);
        }

        .instructions-body {
            padding: 1.5rem;
            background: #f8f9fa;
        }

        .instruction-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .instruction-item:last-child {
            border-bottom: none;
        }

        .instruction-icon {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            flex-shrink: 0;
        }

        .attempts-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .attempts-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .attempts-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .attempts-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-in-progress {
            background: #fff3cd;
            color: #856404;
        }

        .score-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .score-excellent {
            background: #d4edda;
            color: #155724;
        }

        .score-good {
            background: #d1ecf1;
            color: #0c5460;
        }

        .score-poor {
            background: #f8d7da;
            color: #721c24;
        }

        .start-quiz-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .start-quiz-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        .start-quiz-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            border: none;
            padding: 1rem 3rem;
            font-size: 1.25rem;
            font-weight: 700;
            border-radius: 50px;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        .start-quiz-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .start-quiz-btn:hover::before {
            left: 100%;
        }

        .start-quiz-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 107, 107, 0.6);
        }

        .start-quiz-btn:active {
            transform: translateY(-1px);
        }

        .warning-alert {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border: none;
            border-radius: 16px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .warning-alert .alert-heading {
            color: #856404;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .warning-alert ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .warning-alert li {
            color: #856404;
            margin-bottom: 0.5rem;
            font-weight: 500;
            list-style: none;
        }

        .back-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .course-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .quiz-hero {
                padding: 2rem 1rem;
            }

            .start-quiz-section {
                padding: 2rem 1rem;
                margin: 1rem 0;
            }

            .start-quiz-btn {
                padding: 0.875rem 2rem;
                font-size: 1.125rem;
            }

            .stat-pill {
                font-size: 0.75rem;
                padding: 6px 12px;
            }
        }
    </style>
@endpush

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
                <div class="modal-header bg-gradient text-white"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-play-circle me-2"></i>Confirm Quiz Start
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-3">Are you sure you want to start the quiz
                        <strong>"{{ $quiz->localized_name }}"</strong>?</p>
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
