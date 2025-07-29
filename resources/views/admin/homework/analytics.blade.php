@extends('admin.layout')

@section('title', 'Homework Analytics')

@push('styles')
    <style>
        .analytics-card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .analytics-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-info-soft {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-danger-soft {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .score-distribution-bar {
            height: 30px;
            border-radius: 15px;
            margin-bottom: 10px;
            position: relative;
            overflow: hidden;
        }

        .score-distribution-bar .bar-fill {
            height: 100%;
            border-radius: 15px;
            transition: width 0.3s ease;
        }

        .score-distribution-bar .bar-label {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-weight: bold;
            font-size: 0.875rem;
        }

        .score-distribution-bar .bar-count {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-weight: bold;
            font-size: 0.875rem;
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
                        <h1 class="h3 mb-0">Homework Analytics</h1>
                        <p class="text-muted">Detailed analysis for "{{ $homework->name }}"</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.homework.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Homework
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Homework Info Card -->
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
                                <small class="text-muted">Instructor:</small><br>
                                <strong>{{ $homework->instructor->name }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Due Date:</small><br>
                                <strong>{{ $homework->due_date->format('M d, Y h:i A') }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex flex-column align-items-end">
                            <span class="badge bg-{{ $homework->is_published ? 'success' : 'secondary' }} mb-2">
                                {{ $homework->is_published ? 'Published' : 'Draft' }}
                            </span>
                            <small class="text-muted">Max Score: {{ $homework->max_score }}</small>
                            <small class="text-muted">Weight: {{ $homework->weight_percentage }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="card analytics-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft">
                            <i class="fa fa-upload text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Total Submissions</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['total_submissions'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card analytics-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft">
                            <i class="fa fa-clock text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Submitted</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['submitted'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card analytics-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft">
                            <i class="fa fa-check-circle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Graded</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['graded'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card analytics-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft">
                            <i class="fa fa-undo text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Returned</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['returned'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card analytics-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-danger-soft">
                            <i class="fa fa-exclamation-triangle text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Late</h6>
                            <h4 class="fw-bold mb-0">{{ $stats['late_submissions'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card analytics-card">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft">
                            <i class="fa fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Avg Score</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($stats['average_score'], 1) }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Completion Rate</h6>
                        <h3 class="fw-bold text-primary">{{ number_format($stats['completion_rate'], 1) }}%</h3>
                        <small class="text-muted">{{ $stats['total_submissions'] }} of
                            {{ $homework->total_assignments ?: 'N/A' }} students</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Highest Score</h6>
                        <h3 class="fw-bold text-success">{{ number_format($stats['highest_score'], 1) }}%</h3>
                        <small class="text-muted">Best performance</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Lowest Score</h6>
                        <h3 class="fw-bold text-danger">{{ number_format($stats['lowest_score'], 1) }}%</h3>
                        <small class="text-muted">Needs improvement</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card analytics-card">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">On Time Rate</h6>
                        <h3 class="fw-bold text-info">
                            {{ $stats['total_submissions'] > 0 ? number_format(($stats['on_time_submissions'] / $stats['total_submissions']) * 100, 1) : 0 }}%
                        </h3>
                        <small class="text-muted">{{ $stats['on_time_submissions'] }} on time</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4">
            <!-- Score Distribution -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">Score Distribution</h5>
                    @if ($stats['graded'] > 0)
                        @foreach ($scoreDistribution as $range => $count)
                            @php
                                $percentage = ($count / $stats['graded']) * 100;
                                $color = match ($range) {
                                    '90-100' => 'bg-success',
                                    '80-89' => 'bg-info',
                                    '70-79' => 'bg-warning',
                                    '60-69' => 'bg-orange',
                                    '50-59' => 'bg-warning',
                                    '0-49' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <div class="score-distribution-bar">
                                <div class="bar-fill {{ $color }}" style="width: {{ $percentage }}%"></div>
                                <span class="bar-label">{{ $range }}%</span>
                                <span class="bar-count">{{ $count }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-chart-bar fa-3x mb-3"></i>
                            <p>No graded submissions yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submission Timeline -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">Submission Timeline</h5>
                    @if (count($submissionTimeline) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Submissions</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($submissionTimeline->take(10) as $date => $count)
                                        @php
                                            $percentage = ($count / $stats['total_submissions']) * 100;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</td>
                                            <td>{{ $count }}</td>
                                            <td>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-calendar fa-3x mb-3"></i>
                            <p>No submissions yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Submissions</h5>
                    </div>
                    <div class="card-body">
                        @if ($stats['total_submissions'] > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Submitted At</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Is Late</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($homework->submissions()->with('user')->latest('submitted_at')->take(10)->get() as $submission)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <i class="fa fa-user-circle fa-lg text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">{{ $submission->user->name }}</div>
                                                            <small
                                                                class="text-muted">{{ $submission->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>{{ $submission->submitted_at->format('M d, Y') }}</div>
                                                    <small
                                                        class="text-muted">{{ $submission->submitted_at->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $submission->status == 'graded' ? 'success' : ($submission->status == 'submitted' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($submission->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($submission->is_graded)
                                                        <span
                                                            class="fw-bold">{{ $submission->score_earned }}/{{ $submission->max_score }}</span>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ number_format($submission->percentage_score, 1) }}%</small>
                                                    @else
                                                        <span class="text-muted">Not graded</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($submission->is_late)
                                                        <span class="badge bg-danger">{{ $submission->days_late }} days
                                                            late</span>
                                                    @else
                                                        <span class="badge bg-success">On time</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.homework.submissions', $homework) }}"
                                                        class="btn btn-outline-primary btn-sm">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.homework.submissions', $homework) }}"
                                    class="btn btn-outline-primary">
                                    View All Submissions
                                </a>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fa fa-upload fa-3x mb-3"></i>
                                <h5>No submissions yet</h5>
                                <p>Students haven't submitted this homework yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
