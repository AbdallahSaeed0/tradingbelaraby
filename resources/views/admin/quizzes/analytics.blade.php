@extends('admin.layout')

@section('title', 'Quiz Analytics - ' . $quiz->name)

@push('styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .analytics-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .metric-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .metric-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .metric-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
        }

        .question-analysis {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .difficulty-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .difficulty-easy {
            background-color: #28a745;
        }

        .difficulty-medium {
            background-color: #ffc107;
        }

        .difficulty-hard {
            background-color: #dc3545;
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
                        <h1 class="h3 mb-0">Quiz Analytics</h1>
                        <p class="text-muted">{{ $quiz->name }}</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary me-2" onclick="exportAnalytics()">
                            <i class="fa fa-download me-2"></i>Export Report
                        </button>
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Attempts</h6>
                            <h3 class="fw-bold mb-0">{{ $analytics['total_attempts'] }}</h3>
                        </div>
                        <i class="fa fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Completion Rate</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($analytics['completion_rate'], 1) }}%</h3>
                        </div>
                        <i class="fa fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Average Score</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($analytics['average_score'], 1) }}%</h3>
                        </div>
                        <i class="fa fa-chart-line fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Pass Rate</h6>
                            <h3 class="fw-bold mb-0">{{ number_format($analytics['pass_rate'], 1) }}%</h3>
                        </div>
                        <i class="fa fa-trophy fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Charts -->
            <div class="col-lg-8">
                <!-- Score Distribution -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Score Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="scoreDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Attempts Over Time -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-line me-2"></i>Attempts Over Time</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="attemptsOverTimeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Question Analysis -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-question-circle me-2"></i>Question Performance</h5>
                    </div>
                    <div class="card-body">
                        @if ($analytics['question_analysis'])
                            @foreach ($analytics['question_analysis'] as $question)
                                <div class="question-analysis">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Question {{ $loop->iteration }}</h6>
                                            <p class="mb-2 text-muted">{{ Str::limit($question['question_text'], 100) }}</p>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="difficulty-indicator difficulty-{{ $question['difficulty'] }}"></span>
                                                <small class="text-muted">{{ ucfirst($question['difficulty']) }}</small>
                                                <span
                                                    class="badge bg-secondary ms-2">{{ $question['question_type'] }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-0">{{ number_format($question['success_rate'], 1) }}%</h5>
                                            <small class="text-muted">Success Rate</small>
                                        </div>
                                    </div>
                                    <div class="progress mb-2 progress-h-8">
                                        <div class="progress-bar bg-success"
                                            style="width: {{ $question['success_rate'] }}%"></div>
                                    </div>
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Correct</small><br>
                                            <strong class="text-success">{{ $question['correct_answers'] }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Incorrect</small><br>
                                            <strong class="text-danger">{{ $question['incorrect_answers'] }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Skipped</small><br>
                                            <strong class="text-warning">{{ $question['skipped_answers'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No question data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Time Analysis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-clock me-2"></i>Time Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="fw-bold text-primary">{{ $analytics['average_time_minutes'] }}</h4>
                                    <small class="text-muted">Avg Time (min)</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="fw-bold text-success">{{ $analytics['fastest_time_minutes'] }}</h4>
                                    <small class="text-muted">Fastest (min)</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="fw-bold text-warning">{{ $analytics['slowest_time_minutes'] }}</h4>
                                    <small class="text-muted">Slowest (min)</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="fw-bold text-info">{{ $analytics['median_time_minutes'] }}</h4>
                                    <small class="text-muted">Median (min)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attempt Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-chart-pie me-2"></i>Attempt Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="attemptStatusChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Completed</span>
                                <span class="fw-bold">{{ $analytics['completed_attempts'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>In Progress</span>
                                <span class="fw-bold">{{ $analytics['in_progress_attempts'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Abandoned</span>
                                <span class="fw-bold">{{ $analytics['abandoned_attempts'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Performers -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-trophy me-2"></i>Top Performers</h5>
                    </div>
                    <div class="card-body">
                        @if ($analytics['top_performers'])
                            @foreach ($analytics['top_performers'] as $performer)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="bg-primary rounded-circle d-flex align-items-center justify-content-center w-40 h-40">
                                            <span
                                                class="text-white fw-bold">{{ strtoupper(substr($performer['name'], 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ $performer['name'] }}</h6>
                                        <small class="text-muted">{{ $performer['email'] }}</small>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="mb-0 text-success">{{ number_format($performer['score'], 1) }}%</h6>
                                        <small class="text-muted">{{ $performer['time_taken'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted">No performance data available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa fa-history me-2"></i>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        @if ($analytics['recent_activity'])
                            @foreach ($analytics['recent_activity'] as $activity)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fa fa-user-circle fa-2x text-muted"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">{{ $activity['student_name'] }}</h6>
                                        <small class="text-muted">{{ $activity['action'] }}</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $activity['time_ago'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted">No recent activity</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Score Distribution Chart
        const scoreCtx = document.getElementById('scoreDistributionChart').getContext('2d');
        new Chart(scoreCtx, {
            type: 'bar',
            data: {
                labels: ['0-10%', '11-20%', '21-30%', '31-40%', '41-50%', '51-60%', '61-70%', '71-80%', '81-90%',
                    '91-100%'
                ],
                datasets: [{
                    label: 'Number of Students',
                    data: {{ json_encode($analytics['score_distribution'] ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) }},
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Attempts Over Time Chart
        const timeCtx = document.getElementById('attemptsOverTimeChart').getContext('2d');
        new Chart(timeCtx, {
            type: 'line',
            data: {
                labels: {{ json_encode($analytics['time_labels'] ?? []) }},
                datasets: [{
                    label: 'Attempts',
                    data: {{ json_encode($analytics['attempts_over_time'] ?? []) }},
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Attempt Status Chart
        const statusCtx = document.getElementById('attemptStatusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Abandoned'],
                datasets: [{
                    data: [
                        {{ $analytics['completed_attempts'] }},
                        {{ $analytics['in_progress_attempts'] }},
                        {{ $analytics['abandoned_attempts'] }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Export analytics
        function exportAnalytics() {
            // Implement export functionality
            alert('Export analytics functionality will be implemented');
        }
    </script>
@endpush
