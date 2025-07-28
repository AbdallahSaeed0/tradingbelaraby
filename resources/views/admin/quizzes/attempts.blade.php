@extends('admin.layout')

@section('title', 'Quiz Attempts - ' . $quiz->name)

@push('styles')
    <style>
        .attempt-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .attempt-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .attempt-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .attempt-content {
            padding: 1rem;
        }

        .score-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .progress-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
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
                        <h1 class="h3 mb-0">Quiz Attempts</h1>
                        <p class="text-muted">{{ $quiz->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Quiz
                        </a>
                        <button class="btn btn-primary" onclick="exportAttempts()">
                            <i class="fa fa-download me-2"></i>Export Results
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-users text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Attempts</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_attempts'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft">
                            <i class="fa fa-check-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Completed</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['completed_attempts'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Average Score</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['average_score'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-trophy text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Pass Rate</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['pass_rate'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search by student name..."
                                id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="abandoned">Abandoned</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="resultFilter">
                            <option value="">All Results</option>
                            <option value="passed">Passed</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="scoreFilter">
                            <option value="">All Scores</option>
                            <option value="90-100">90-100%</option>
                            <option value="80-89">80-89%</option>
                            <option value="70-79">70-79%</option>
                            <option value="60-69">60-69%</option>
                            <option value="0-59">Below 60%</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" id="clearFilters">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" id="gridView">
                                <i class="fa fa-th"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm active" id="listView">
                                <i class="fa fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attempts List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Attempts ({{ $attempts->count() }})</h5>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-2">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                            <i class="fa fa-sort me-1"></i>Sort By
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-sort="latest">Latest First</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="oldest">Oldest First</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="score-high">Score (High to Low)</a>
                            </li>
                            <li><a class="dropdown-item" href="#" data-sort="score-low">Score (Low to High)</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($attempts->count() > 0)
                    <div id="attemptsList">
                        @foreach ($attempts as $attempt)
                            <div class="attempt-card" data-status="{{ $attempt->status }}"
                                data-result="{{ $attempt->is_passed ? 'passed' : 'failed' }}"
                                data-score="{{ $attempt->percentage_score }}">
                                <div class="attempt-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="progress-circle bg-{{ $attempt->percentage_score >= 90 ? 'success' : ($attempt->percentage_score >= 70 ? 'warning' : 'danger') }} me-3">
                                            {{ number_format($attempt->percentage_score, 0) }}%
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $attempt->user->name }}</h6>
                                            <small class="text-muted">{{ $attempt->user->email }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge status-badge bg-{{ $attempt->status_color }}">
                                                {{ ucfirst($attempt->status) }}
                                            </span>
                                            <span
                                                class="badge status-badge bg-{{ $attempt->is_passed ? 'success' : 'danger' }}">
                                                {{ $attempt->is_passed ? 'Passed' : 'Failed' }}
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            Attempt {{ $attempt->attempt_number }} of
                                            {{ $quiz->max_attempts == 0 ? '∞' : $quiz->max_attempts }}
                                        </small>
                                    </div>
                                </div>
                                <div class="attempt-content">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <small class="text-muted">Score</small><br>
                                            <strong>{{ number_format($attempt->percentage_score, 1) }}%</strong>
                                            <small
                                                class="text-muted">({{ $attempt->correct_answers }}/{{ $attempt->total_questions }})</small>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Time Taken</small><br>
                                            <strong>{{ $attempt->formatted_duration }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Started</small><br>
                                            <strong>{{ $attempt->started_at->format('M d, Y H:i') }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted">Completed</small><br>
                                            <strong>{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y H:i') : 'Not completed' }}</strong>
                                        </div>
                                    </div>

                                    @if ($attempt->status === 'completed')
                                        <hr>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <small class="text-muted">Correct Answers</small><br>
                                                <span class="text-success">{{ $attempt->correct_answers }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Incorrect Answers</small><br>
                                                <span class="text-danger">{{ $attempt->incorrect_answers }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Skipped</small><br>
                                                <span class="text-warning">{{ $attempt->skipped_answers }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-outline-primary me-2"
                                            onclick="viewAttemptDetails({{ $attempt->id }})">
                                            <i class="fa fa-eye me-1"></i>View Details
                                        </button>
                                        @if ($attempt->status === 'completed')
                                            <button class="btn btn-sm btn-outline-info me-2"
                                                onclick="viewAnswers({{ $attempt->id }})">
                                                <i class="fa fa-list me-1"></i>View Answers
                                            </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteAttempt({{ $attempt->id }})">
                                            <i class="fa fa-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Attempts Yet</h5>
                        <p class="text-muted">Students haven't attempted this quiz yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attempt Details Modal -->
    <div class="modal fade" id="attemptDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attempt Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="attemptDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const attempts = document.querySelectorAll('.attempt-card');

            attempts.forEach(attempt => {
                const studentName = attempt.querySelector('h6').textContent.toLowerCase();
                const studentEmail = attempt.querySelector('small').textContent.toLowerCase();

                if (studentName.includes(searchTerm) || studentEmail.includes(searchTerm)) {
                    attempt.style.display = 'block';
                } else {
                    attempt.style.display = 'none';
                }
            });
        });

        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', filterAttempts);
        document.getElementById('resultFilter').addEventListener('change', filterAttempts);
        document.getElementById('scoreFilter').addEventListener('change', filterAttempts);

        function filterAttempts() {
            const statusFilter = document.getElementById('statusFilter').value;
            const resultFilter = document.getElementById('resultFilter').value;
            const scoreFilter = document.getElementById('scoreFilter').value;

            const attempts = document.querySelectorAll('.attempt-card');

            attempts.forEach(attempt => {
                const status = attempt.dataset.status;
                const result = attempt.dataset.result;
                const score = parseInt(attempt.dataset.score);

                let show = true;

                if (statusFilter && status !== statusFilter) show = false;
                if (resultFilter && result !== resultFilter) show = false;
                if (scoreFilter) {
                    const [min, max] = scoreFilter.split('-').map(Number);
                    if (score < min || score > max) show = false;
                }

                attempt.style.display = show ? 'block' : 'none';
            });
        }

        // Clear filters
        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('resultFilter').value = '';
            document.getElementById('scoreFilter').value = '';

            const attempts = document.querySelectorAll('.attempt-card');
            attempts.forEach(attempt => {
                attempt.style.display = 'block';
            });
        });

        // View attempt details
        function viewAttemptDetails(attemptId) {
            // Implement attempt details view
            alert('Attempt details functionality will be implemented');
        }

        // View answers
        function viewAnswers(attemptId) {
            // Implement answers view
            alert('View answers functionality will be implemented');
        }

        // Delete attempt
        function deleteAttempt(attemptId) {
            if (confirm('Are you sure you want to delete this attempt?')) {
                // Implement attempt deletion
                alert('Delete attempt functionality will be implemented');
            }
        }

        // Export attempts
        function exportAttempts() {
            // Implement export functionality
            alert('Export functionality will be implemented');
        }
    </script>
@endpush
