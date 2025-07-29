@extends('admin.layout')

@section('title', 'Course Analytics - ' . $course->name)

@push('styles')
    <style>
        .analytics-card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .analytics-card:hover {
            transform: translateY(-2px);
        }

        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }

        .progress-bar-custom {
            height: 8px;
            border-radius: 4px;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .completion-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .trend-up {
            color: #28a745;
        }

        .trend-down {
            color: #dc3545;
        }

        .trend-neutral {
            color: #6c757d;
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
                        <h1 class="h3 mb-0">Course Analytics</h1>
                        <p class="text-muted">{{ $course->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-arrow-left me-2"></i>Back to Course
                        </a>
                        <button class="btn btn-primary" onclick="exportReport()">
                            <i class="fa fa-download me-2"></i>Export Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">{{ $analytics['total_enrollments'] ?? 0 }}</div>
                    <div class="metric-label">Total Enrollments</div>
                    <div class="mt-2">
                        @php
                            $lastMonthEnrollments = $course
                                ->enrollments()
                                ->where('created_at', '>=', now()->subMonth()->startOfMonth())
                                ->where('created_at', '<', now()->subMonth()->endOfMonth())
                                ->count();
                            $currentMonthEnrollments = $course
                                ->enrollments()
                                ->where('created_at', '>=', now()->startOfMonth())
                                ->count();
                            $enrollmentGrowth =
                                $lastMonthEnrollments > 0
                                    ? round(
                                        (($currentMonthEnrollments - $lastMonthEnrollments) / $lastMonthEnrollments) *
                                            100,
                                        1,
                                    )
                                    : 0;
                        @endphp
                        @if ($enrollmentGrowth > 0)
                            <i class="fa fa-arrow-up trend-up me-1"></i>
                            <small>+{{ $enrollmentGrowth }}% this month</small>
                        @elseif($enrollmentGrowth < 0)
                            <i class="fa fa-arrow-down trend-down me-1"></i>
                            <small>{{ $enrollmentGrowth }}% this month</small>
                        @else
                            <i class="fa fa-minus trend-neutral me-1"></i>
                            <small>No change</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">{{ $analytics['completion_rate'] ?? 0 }}%</div>
                    <div class="metric-label">Completion Rate</div>
                    <div class="mt-2">
                        @php
                            $lastMonthCompletionRate = $course
                                ->enrollments()
                                ->where('created_at', '>=', now()->subMonth()->startOfMonth())
                                ->where('created_at', '<', now()->subMonth()->endOfMonth())
                                ->where('status', 'completed')
                                ->count();
                            $lastMonthTotal = $course
                                ->enrollments()
                                ->where('created_at', '>=', now()->subMonth()->startOfMonth())
                                ->where('created_at', '<', now()->subMonth()->endOfMonth())
                                ->count();
                            $lastMonthRate =
                                $lastMonthTotal > 0 ? ($lastMonthCompletionRate / $lastMonthTotal) * 100 : 0;
                            $completionGrowth =
                                $lastMonthRate > 0
                                    ? round(
                                        (($analytics['completion_rate'] - $lastMonthRate) / $lastMonthRate) * 100,
                                        1,
                                    )
                                    : 0;
                        @endphp
                        @if ($completionGrowth > 0)
                            <i class="fa fa-arrow-up trend-up me-1"></i>
                            <small>+{{ $completionGrowth }}% this month</small>
                        @elseif($completionGrowth < 0)
                            <i class="fa fa-arrow-down trend-down me-1"></i>
                            <small>{{ $completionGrowth }}% this month</small>
                        @else
                            <i class="fa fa-minus trend-neutral me-1"></i>
                            <small>No change</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">{{ $analytics['avg_progress'] ?? 0 }}%</div>
                    <div class="metric-label">Average Progress</div>
                    <div class="mt-2">
                        @php
                            $lastWeekProgress =
                                $course
                                    ->enrollments()
                                    ->where('last_accessed_at', '>=', now()->subWeeks(2))
                                    ->where('last_accessed_at', '<', now()->subWeek())
                                    ->avg('progress_percentage') ?? 0;
                            $currentWeekProgress =
                                $course
                                    ->enrollments()
                                    ->where('last_accessed_at', '>=', now()->subWeek())
                                    ->avg('progress_percentage') ?? 0;
                            $progressGrowth =
                                $lastWeekProgress > 0
                                    ? round((($currentWeekProgress - $lastWeekProgress) / $lastWeekProgress) * 100, 1)
                                    : 0;
                        @endphp
                        @if ($progressGrowth > 0)
                            <i class="fa fa-arrow-up trend-up me-1"></i>
                            <small>+{{ $progressGrowth }}% this week</small>
                        @elseif($progressGrowth < 0)
                            <i class="fa fa-arrow-down trend-down me-1"></i>
                            <small>{{ $progressGrowth }}% this week</small>
                        @else
                            <i class="fa fa-minus trend-neutral me-1"></i>
                            <small>No change</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card text-center">
                    <div class="metric-value">{{ $analytics['avg_rating'] ?? 0 }}</div>
                    <div class="metric-label">Average Rating</div>
                    <div class="mt-2">
                        <i class="fa fa-star text-warning me-1"></i>
                        <small>{{ $analytics['total_ratings'] ?? 0 }} reviews</small>
                        @if ($analytics['total_ratings'] > 0)
                            <br><small class="text-muted">Last review:
                                {{ $course->ratings()->latest()->first()?->created_at->diffForHumans() ?? 'N/A' }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Enrollment Chart -->
            <div class="col-lg-8">
                <div class="analytics-card p-4 mb-4">
                    <h5 class="mb-3"><i class="fa fa-chart-line me-2"></i>Enrollment Trends</h5>
                    <div class="chart-container">
                        <canvas id="enrollmentChart"></canvas>
                    </div>
                </div>

                <!-- Progress Distribution -->
                <div class="analytics-card p-4 mb-4">
                    <h5 class="mb-3"><i class="fa fa-chart-pie me-2"></i>Progress Distribution</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-container">
                                <canvas id="progressChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Not Started</span>
                                    <span class="badge bg-secondary">{{ $analytics['not_started'] ?? 0 }}</span>
                                </div>
                                <div class="progress progress-bar-custom mb-3">
                                    <div class="progress-bar bg-secondary"
                                        style="width: {{ (($analytics['not_started'] ?? 0) / max(1, $analytics['total_enrollments'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>In Progress</span>
                                    <span class="badge bg-warning">{{ $analytics['in_progress'] ?? 0 }}</span>
                                </div>
                                <div class="progress progress-bar-custom mb-3">
                                    <div class="progress-bar bg-warning"
                                        style="width: {{ (($analytics['in_progress'] ?? 0) / max(1, $analytics['total_enrollments'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Completed</span>
                                    <span class="badge bg-success">{{ $analytics['completed'] ?? 0 }}</span>
                                </div>
                                <div class="progress progress-bar-custom mb-3">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ (($analytics['completed'] ?? 0) / max(1, $analytics['total_enrollments'] ?? 1)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="analytics-card p-4">
                    <h5 class="mb-3"><i class="fa fa-clock me-2"></i>Recent Activity</h5>
                    <div class="list-group list-group-flush">
                        @php
                            $recentActivities = collect();

                            // Add recent enrollments
                            foreach ($analytics['recent_enrollments'] as $enrollment) {
                                $recentActivities->push([
                                    'type' => 'enrollment',
                                    'user' => $enrollment->user,
                                    'time' => $enrollment->created_at,
                                    'message' => 'enrolled in the course',
                                    'badge' => 'New',
                                    'badge_color' => 'bg-primary',
                                    'icon' => 'fa-user',
                                    'icon_bg' => 'bg-primary',
                                ]);
                            }

                            // Add recent completions
                            foreach ($analytics['recent_completions'] as $completion) {
                                $recentActivities->push([
                                    'type' => 'completion',
                                    'user' => $completion->user,
                                    'time' => $completion->completed_at,
                                    'message' => 'completed the course',
                                    'badge' => 'Completed',
                                    'badge_color' => 'bg-success',
                                    'icon' => 'fa-check',
                                    'icon_bg' => 'bg-success',
                                ]);
                            }

                            // Add recent ratings
                            foreach ($analytics['recent_ratings'] as $rating) {
                                $recentActivities->push([
                                    'type' => 'rating',
                                    'user' => $rating->user,
                                    'time' => $rating->created_at,
                                    'message' => "left a {$rating->rating}-star review",
                                    'badge' => 'Review',
                                    'badge_color' => 'bg-warning',
                                    'icon' => 'fa-star',
                                    'icon_bg' => 'bg-warning',
                                ]);
                            }

                            // Sort by time and take top 5
                            $recentActivities = $recentActivities->sortByDesc('time')->take(5);
                        @endphp

                        @forelse($recentActivities as $activity)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="student-avatar {{ $activity['icon_bg'] }} text-white d-flex align-items-center justify-content-center me-3">
                                        <i class="fa {{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium">{{ $activity['user']->name ?? 'Unknown User' }}
                                            {{ $activity['message'] }}</div>
                                        <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                    </div>
                                    <span class="badge {{ $activity['badge_color'] }}">{{ $activity['badge'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fa fa-clock fa-2x mb-2"></i>
                                <p>No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Top Performing Lectures -->
                <div class="analytics-card p-4 mb-4">
                    <h5 class="mb-3"><i class="fa fa-trophy me-2"></i>Top Performing Lectures</h5>
                    <div class="list-group list-group-flush">
                        @forelse($analytics['top_lectures'] as $lectureData)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-medium">{{ Str::limit($lectureData['lecture']->title, 30) }}</div>
                                        <small class="text-muted">{{ $lectureData['section']->title }}</small>
                                        <div class="small text-muted">
                                            {{ $lectureData['total_views'] }} views •
                                            {{ round($lectureData['avg_watch_time'] / 60, 1) }}m avg
                                        </div>
                                    </div>
                                    <span
                                        class="badge bg-success completion-badge">{{ $lectureData['completion_rate'] }}%</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fa fa-play-circle fa-2x mb-2"></i>
                                <p>No lectures available</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Student Engagement -->
                <div class="analytics-card p-4 mb-4">
                    <h5 class="mb-3"><i class="fa fa-users me-2"></i>Student Engagement</h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-value h4 text-primary">{{ $analytics['active_students'] ?? 0 }}</div>
                            <div class="metric-label small">Active This Week</div>
                        </div>
                        <div class="col-6">
                            <div class="metric-value h4 text-success">{{ $analytics['avg_time_spent'] ?? 0 }}h</div>
                            <div class="metric-label small">Avg. Time Spent</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-value h4 text-warning">{{ $analytics['quiz_attempts'] ?? 0 }}</div>
                            <div class="metric-label small">Quiz Attempts</div>
                            @if ($analytics['quiz_attempts'] > 0)
                                <div class="small text-muted">{{ $analytics['quiz_pass_rate'] ?? 0 }}% pass rate</div>
                            @endif
                        </div>
                        <div class="col-6">
                            <div class="metric-value h4 text-info">{{ $analytics['homework_submissions'] ?? 0 }}</div>
                            <div class="metric-label small">Homework Submitted</div>
                            @if ($analytics['homework_submissions'] > 0)
                                <div class="small text-muted">{{ $analytics['homework_pass_rate'] ?? 0 }}% pass rate</div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-value h5 text-secondary">{{ $analytics['avg_session_duration'] ?? 0 }}m
                            </div>
                            <div class="metric-label small">Avg. Session</div>
                        </div>
                        <div class="col-6">
                            <div class="metric-value h5 text-secondary">{{ $analytics['total_watch_time'] ?? 0 }}h</div>
                            <div class="metric-label small">Total Watch Time</div>
                        </div>
                    </div>
                </div>

                <!-- Course Content Stats -->
                <div class="analytics-card p-4 mb-4">
                    <h5 class="mb-3"><i class="fa fa-book me-2"></i>Course Content</h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-value h4 text-primary">{{ $analytics['total_sections'] ?? 0 }}</div>
                            <div class="metric-label small">Sections</div>
                        </div>
                        <div class="col-6">
                            <div class="metric-value h4 text-success">{{ $analytics['total_lectures'] ?? 0 }}</div>
                            <div class="metric-label small">Lectures</div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-value h4 text-warning">{{ $analytics['total_quizzes'] ?? 0 }}</div>
                            <div class="metric-label small">Quizzes</div>
                        </div>
                        <div class="col-6">
                            <div class="metric-value h4 text-info">{{ $analytics['total_homework'] ?? 0 }}</div>
                            <div class="metric-label small">Homework</div>
                        </div>
                    </div>
                    @if ($analytics['total_lectures'] > 0)
                        <hr>
                        <div class="text-center">
                            <div class="metric-value h5 text-secondary">
                                {{ $analytics['avg_lecture_completion_rate'] ?? 0 }}%</div>
                            <div class="metric-label small">Avg. Lecture Completion Rate</div>
                        </div>
                    @endif
                </div>

                <!-- Revenue Analytics -->
                <div class="analytics-card p-4 mb-4">
                    <h5 class="mb-3"><i class="fa fa-dollar-sign me-2"></i>Revenue Analytics</h5>
                    <div class="text-center">
                        <div class="metric-value h3 text-success">
                            ${{ number_format($analytics['total_revenue'] ?? 0, 2) }}</div>
                        <div class="metric-label">Total Revenue</div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-value h5">${{ number_format($analytics['monthly_revenue'] ?? 0, 2) }}</div>
                            <div class="metric-label small">This Month</div>
                        </div>
                        <div class="col-6">
                            <div class="metric-value h5">
                                ${{ number_format($analytics['avg_revenue_per_student'] ?? 0, 2) }}</div>
                            <div class="metric-label small">Per Student</div>
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
            // Enrollment Chart
            const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
            new Chart(enrollmentCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    datasets: [{
                        label: 'Enrollments',
                        data: {!! json_encode($analytics['enrollment_trends'] ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]) !!},
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Progress Chart
            const progressCtx = document.getElementById('progressChart').getContext('2d');
            new Chart(progressCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Not Started', 'In Progress', 'Completed'],
                    datasets: [{
                        data: [{{ $analytics['not_started'] ?? 0 }},
                            {{ $analytics['in_progress'] ?? 0 }},
                            {{ $analytics['completed'] ?? 0 }}
                        ],
                        backgroundColor: ['#6c757d', '#ffc107', '#28a745'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
