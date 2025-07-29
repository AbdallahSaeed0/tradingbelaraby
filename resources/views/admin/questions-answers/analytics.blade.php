@extends('admin.layout')

@section('title', 'Q&A Analytics')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Q&A Analytics</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.questions-answers.index') }}">Q&A Management</a>
                        </li>
                        <li class="breadcrumb-item active">Analytics</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.questions-answers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Q&A
                    </a>
                    <a href="{{ route('admin.questions-answers.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Export Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Statistics -->
        <div class="row">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary">
                                <i class="fas fa-question-circle"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $stats['total_questions'] }}</h3>
                            </div>
                        </div>
                        <p class="text-muted">Total Questions</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $stats['answered_questions'] }}</h3>
                            </div>
                        </div>
                        <p class="text-muted">Answered Questions</p>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ $stats['total_questions'] > 0 ? round(($stats['answered_questions'] / $stats['total_questions']) * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-warning">
                                <i class="fas fa-clock"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $stats['pending_questions'] }}</h3>
                            </div>
                        </div>
                        <p class="text-muted">Pending Questions</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-info">
                                <i class="fas fa-chart-line"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $stats['avg_response_time'] }}h</h3>
                            </div>
                        </div>
                        <p class="text-muted">Avg Response Time</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Questions by Status -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Questions by Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Questions by Priority -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Questions by Priority</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="priorityChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- More Charts -->
        <div class="row">
            <!-- Questions by Type -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Questions by Type</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="typeChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Monthly Question Trends</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Courses and Recent Activity -->
        <div class="row">
            <!-- Top Courses by Questions -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Top Courses by Questions</h5>
                    </div>
                    <div class="card-body">
                        <div class="top-courses">
                            @forelse($topCourses as $courseData)
                                <div class="course-item d-flex justify-content-between align-items-center mb-3">
                                    <div class="course-info">
                                        <h6 class="mb-1">{{ $courseData->course->name ?? 'Unknown Course' }}</h6>
                                        <small class="text-muted">{{ $courseData->count }} questions</small>
                                    </div>
                                    <div class="course-stats">
                                        <span class="badge badge-primary">{{ $courseData->count }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-chart-bar fa-2x mb-3"></i>
                                    <p>No course data available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="recent-activity">
                            @forelse($recentActivity as $activity)
                                <div class="activity-item d-flex align-items-start mb-3">
                                    <div class="activity-icon mr-3">
                                        @switch($activity->status)
                                            @case('pending')
                                                <i class="fas fa-clock text-warning"></i>
                                            @break

                                            @case('answered')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @break

                                            @case('closed')
                                                <i class="fas fa-lock text-secondary"></i>
                                            @break

                                            @case('flagged')
                                                <i class="fas fa-flag text-danger"></i>
                                            @break
                                        @endswitch
                                    </div>
                                    <div class="activity-content flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.questions-answers.show', $activity) }}"
                                                class="text-dark">
                                                {{ Str::limit($activity->question_title, 50) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $activity->user->name ?? 'Anonymous' }} •
                                            {{ $activity->course->name ?? 'Unknown Course' }} •
                                            {{ $activity->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                @empty
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-activity fa-2x mb-3"></i>
                                        <p>No recent activity</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Performance Metrics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value">{{ $stats['total_views'] }}</div>
                                        <div class="metric-label">Total Views</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value">{{ $stats['total_votes'] }}</div>
                                        <div class="metric-label">Total Votes</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value">{{ $stats['urgent_questions'] }}</div>
                                        <div class="metric-label">Urgent Questions</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-item text-center">
                                        <div class="metric-value">
                                            {{ $stats['total_questions'] > 0 ? round(($stats['answered_questions'] / $stats['total_questions']) * 100) : 0 }}%
                                        </div>
                                        <div class="metric-label">Answer Rate</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Questions by Status Chart
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pending', 'Answered', 'Closed', 'Flagged'],
                        datasets: [{
                            data: [
                                {{ $questionsByStatus['pending'] ?? 0 }},
                                {{ $questionsByStatus['answered'] ?? 0 }},
                                {{ $questionsByStatus['closed'] ?? 0 }},
                                {{ $questionsByStatus['flagged'] ?? 0 }}
                            ],
                            backgroundColor: ['#ffc107', '#28a745', '#6c757d', '#dc3545'],
                            borderWidth: 2,
                            borderColor: '#fff'
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

                // Questions by Priority Chart
                const priorityCtx = document.getElementById('priorityChart').getContext('2d');
                new Chart(priorityCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Low', 'Normal', 'High', 'Urgent'],
                        datasets: [{
                            label: 'Questions',
                            data: [
                                {{ $questionsByPriority['low'] ?? 0 }},
                                {{ $questionsByPriority['normal'] ?? 0 }},
                                {{ $questionsByPriority['high'] ?? 0 }},
                                {{ $questionsByPriority['urgent'] ?? 0 }}
                            ],
                            backgroundColor: ['#6c757d', '#17a2b8', '#ffc107', '#dc3545'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                // Questions by Type Chart
                const typeCtx = document.getElementById('typeChart').getContext('2d');
                new Chart(typeCtx, {
                    type: 'pie',
                    data: {
                        labels: ['General', 'Lecture Specific', 'Technical', 'Clarification'],
                        datasets: [{
                            data: [
                                {{ $questionsByType['general'] ?? 0 }},
                                {{ $questionsByType['lecture_specific'] ?? 0 }},
                                {{ $questionsByType['technical'] ?? 0 }},
                                {{ $questionsByType['clarification'] ?? 0 }}
                            ],
                            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8'],
                            borderWidth: 2,
                            borderColor: '#fff'
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

                // Monthly Trends Chart
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($monthlyTrends->pluck('month')) !!},
                        datasets: [{
                            label: 'Questions',
                            data: {!! json_encode($monthlyTrends->pluck('count')) !!},
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .dash-widget-header {
                display: flex;
                align-items: center;
                margin-bottom: 15px;
            }

            .dash-widget-icon {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                margin-right: 15px;
            }

            .dash-count h3 {
                margin: 0;
                font-size: 28px;
                font-weight: 600;
            }

            .course-item {
                padding: 10px 0;
                border-bottom: 1px solid #f1f1f1;
            }

            .course-item:last-child {
                border-bottom: none;
            }

            .activity-item {
                padding: 10px 0;
                border-bottom: 1px solid #f1f1f1;
            }

            .activity-item:last-child {
                border-bottom: none;
            }

            .activity-icon {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                background-color: #f8f9fa;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
            }

            .metric-item {
                padding: 20px;
            }

            .metric-value {
                font-size: 32px;
                font-weight: 600;
                color: #007bff;
                margin-bottom: 5px;
            }

            .metric-label {
                font-size: 14px;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .progress {
                margin-top: 10px;
            }

            .card {
                margin-bottom: 30px;
            }

            .card-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid #dee2e6;
            }

            .top-courses,
            .recent-activity {
                max-height: 400px;
                overflow-y: auto;
            }
        </style>
    @endpush
