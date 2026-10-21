@extends('admin.layout')

@section('title', 'Enrollment Progress')

@section('content')
    @php
        $statusClasses = [
            'active' => 'bg-primary',
            'enrolled' => 'bg-primary',
            'pending' => 'bg-warning text-dark',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
        ];
        $statusClass = $statusClasses[$enrollment->status] ?? 'bg-secondary';

        $instructorNames = $enrollment->course->instructors && $enrollment->course->instructors->count() > 0
            ? $enrollment->course->instructors->pluck('name')->join(', ')
            : ($enrollment->course->instructor->name ?? null);

        $watchHours = intdiv($progress['watch_seconds'], 3600);
        $watchMinutes = intdiv($progress['watch_seconds'] % 3600, 60);
        $watchTime = $watchHours > 0 ? "{$watchHours}h {$watchMinutes}m" : "{$watchMinutes}m";

        $typeIcons = [
            'video' => 'fa-play-circle',
            'document' => 'fa-file-pdf',
            'text' => 'fa-file-alt',
            'live' => 'fa-video',
        ];
    @endphp

    <div class="container-fluid py-4 enrollment-detail-page">
        <!-- Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h1 class="h3 mb-0">Enrollment Progress</h1>
                <p class="text-muted mb-0">Enrollment #{{ $enrollment->id }}</p>
            </div>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.enrollments.index') }}"
                class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i>Back
            </a>
        </div>

        <div class="row g-4">
            <!-- Left: overview + curriculum -->
            <div class="col-lg-8">
                <!-- Overview -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center gap-4">
                            <div class="progress-circle progress-circle--lg" style="--progress: {{ $progress['percent'] }}%;">
                                <span>{{ $progress['percent'] }}%</span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h2 class="h5 mb-1 text-break">{{ $enrollment->course->name }}</h2>
                                @if ($instructorNames)
                                    <div class="text-muted small mb-3"><i class="fa fa-chalkboard-teacher me-1"></i>{{ $instructorNames }}</div>
                                @endif
                                <div class="row g-3 enrollment-detail-stats">
                                    <div class="col-6 col-md-3">
                                        <div class="enrollment-detail-stat__label">Lectures</div>
                                        <div class="enrollment-detail-stat__value">{{ $progress['completed'] }} / {{ $progress['total'] }}</div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="enrollment-detail-stat__label">Watch Time</div>
                                        <div class="enrollment-detail-stat__value">{{ $watchTime }}</div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="enrollment-detail-stat__label">Enrolled</div>
                                        <div class="enrollment-detail-stat__value">{{ ($enrollment->enrolled_at ?? $enrollment->created_at)->format('M d, Y') }}</div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="enrollment-detail-stat__label">Last Activity</div>
                                        <div class="enrollment-detail-stat__value">
                                            {{ $progress['last_activity'] ? $progress['last_activity']->diffForHumans() : 'None' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Curriculum progress -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa fa-list-check me-2"></i>Lecture Progress</h5>
                        <span class="small text-muted">{{ $sections->count() }} {{ Str::plural('section', $sections->count()) }}</span>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($sections as $index => $row)
                            @php $sectionPercent = $row['total'] > 0 ? round($row['done'] / $row['total'] * 100) : 0; @endphp
                            <div class="enrollment-section">
                                <button class="enrollment-section__head {{ $index === 0 ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#section-{{ $row['section']->id }}"
                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                    <i class="fa fa-chevron-down enrollment-section__chevron"></i>
                                    <span class="flex-grow-1 text-start fw-semibold min-w-0 text-break">{{ $row['section']->title }}</span>
                                    <span class="enrollment-section__count">{{ $row['done'] }}/{{ $row['total'] }}</span>
                                    <span class="enrollment-section__bar">
                                        <span class="progress"><span class="progress-bar {{ $sectionPercent >= 100 ? 'bg-success' : '' }}" style="width: {{ $sectionPercent }}%"></span></span>
                                    </span>
                                </button>
                                <div class="collapse {{ $index === 0 ? 'show' : '' }}" id="section-{{ $row['section']->id }}">
                                    @foreach ($row['lectures'] as $item)
                                        @php
                                            $lecture = $item['lecture'];
                                            $completion = $item['completion'];
                                            $isDone = $completion?->is_completed;
                                            $started = $completion && !$isDone;
                                        @endphp
                                        <div class="enrollment-lecture">
                                            <span class="enrollment-lecture__state {{ $isDone ? 'is-done' : ($started ? 'is-started' : '') }}"
                                                title="{{ $isDone ? 'Completed' : ($started ? 'In progress' : 'Not started') }}">
                                                <i class="fa {{ $isDone ? 'fa-check' : ($started ? 'fa-hourglass-half' : 'fa-circle') }}"></i>
                                            </span>
                                            <i class="fa {{ $typeIcons[$lecture->content_type] ?? 'fa-file' }} text-muted"></i>
                                            <div class="flex-grow-1 min-w-0">
                                                <div class="text-break">{{ $lecture->title }}</div>
                                                @if ($completion)
                                                    <div class="small text-muted">
                                                        @if ($isDone && $completion->completed_at)
                                                            Completed {{ $completion->completed_at->format('M d, Y') }}
                                                        @elseif ($completion->last_accessed_at)
                                                            Last opened {{ $completion->last_accessed_at->diffForHumans() }}
                                                        @endif
                                                        @if ($completion->watch_time_seconds > 0)
                                                            · Watched {{ $completion->formatted_watch_time }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            @if ($started && $completion->progress_percentage > 0)
                                                <span class="badge bg-info">{{ round($completion->progress_percentage) }}%</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">This course has no published lectures yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right: student + enrollment info -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-user me-2"></i>Student</h5></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="user-avatar">
                                <i class="fa fa-user"></i>
                                @if ($enrollment->user->avatar)
                                    <img src="{{ asset('storage/' . $enrollment->user->avatar) }}" alt="{{ $enrollment->user->name }}"
                                        onerror="this.remove()">
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="fw-semibold text-break">{{ $enrollment->user->name }}</div>
                                <a href="mailto:{{ $enrollment->user->email }}" class="small text-muted text-break">{{ $enrollment->user->email }}</a>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.users.show', $enrollment->user) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="fa fa-eye me-1"></i>Profile
                            </a>
                            <a href="mailto:{{ $enrollment->user->email }}" class="btn btn-sm btn-outline-warning flex-fill">
                                <i class="fa fa-envelope me-1"></i>Email
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0"><i class="fa fa-id-card me-2"></i>Enrollment</h5></div>
                    <div class="card-body">
                        <dl class="enrollment-dl mb-0">
                            <dt>Status</dt>
                            <dd>
                                <span class="badge {{ $statusClass }} status-badge" style="cursor: pointer;"
                                    onclick="showStatusModal({{ $enrollment->id }}, '{{ $enrollment->status }}', [
                                        { value: 'active', label: 'Active' },
                                        { value: 'pending', label: 'Pending' },
                                        { value: 'completed', label: 'Completed' },
                                        { value: 'cancelled', label: 'Cancelled' }
                                    ], '{{ route('admin.enrollments.update_status', $enrollment->id) }}')">{{ ucfirst($enrollment->status) }}</span>
                            </dd>
                            <dt>Access</dt>
                            <dd>
                                @if ($enrollment->isSubscription())
                                    Subscription
                                    @if ($enrollment->expires_at)
                                        <div class="small {{ $enrollment->isExpired() ? 'text-danger' : 'text-muted' }}">
                                            {{ $enrollment->isExpired() ? 'Expired' : 'Expires' }} {{ $enrollment->expires_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                @else
                                    Lifetime
                                @endif
                            </dd>
                            <dt>Paid</dt>
                            <dd>{{ $enrollment->formatted_amount_paid }}</dd>
                            @if ($enrollment->payment_method)
                                <dt>Method</dt>
                                <dd>{{ ucfirst(str_replace('_', ' ', $enrollment->payment_method)) }}</dd>
                            @endif
                            @if ($enrollment->transaction_id)
                                <dt>Reference</dt>
                                <dd><code class="text-break">{{ $enrollment->transaction_id }}</code></dd>
                            @endif
                            @if ($enrollment->completed_at)
                                <dt>Completed</dt>
                                <dd>{{ $enrollment->completed_at->format('M d, Y') }}</dd>
                            @endif
                            @if ($enrollment->certificate_url)
                                <dt>Certificate</dt>
                                <dd><a href="{{ $enrollment->certificate_url }}" target="_blank" rel="noopener">View</a></dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body d-grid gap-2">
                        <a href="{{ route('admin.courses.show', $enrollment->course) }}" class="btn btn-outline-primary">
                            <i class="fa fa-book me-1"></i>View Course
                        </a>
                        <a href="{{ route('admin.courses.enrollments', $enrollment->course) }}" class="btn btn-outline-info">
                            <i class="fa fa-users me-1"></i>All Course Enrollments
                        </a>
                        <a href="{{ route('admin.users.enrollments-report', $enrollment->user) }}" class="btn btn-outline-secondary">
                            <i class="fa fa-chart-bar me-1"></i>Student's Enrollments Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .enrollment-detail-page .progress-circle {
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: conic-gradient(#0d6efd var(--progress, 0%), var(--admin-border, #e9ecef) 0);
        }

        .enrollment-detail-page .progress-circle--lg {
            width: 110px;
            height: 110px;
        }

        .enrollment-detail-page .progress-circle span {
            width: 80%;
            height: 80%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.4rem;
            background: var(--admin-card-bg, #fff);
            color: var(--admin-text, #212529);
        }

        .enrollment-detail-stat__label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--admin-text-muted, #6c757d);
        }

        .enrollment-detail-stat__value {
            font-weight: 600;
        }

        .enrollment-section + .enrollment-section {
            border-top: 1px solid var(--admin-border, #dee2e6);
        }

        .enrollment-section__head {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.9rem 1.25rem;
            border: 0;
            background: transparent;
            color: inherit;
        }

        .enrollment-section__head:hover {
            background: var(--admin-table-stripe, rgba(0, 0, 0, 0.03));
        }

        .enrollment-section__chevron {
            transition: transform 0.2s ease;
            color: var(--admin-text-muted, #6c757d);
        }

        .enrollment-section__head.collapsed .enrollment-section__chevron {
            transform: rotate(-90deg);
        }

        .enrollment-section__count {
            font-size: 0.85rem;
            color: var(--admin-text-muted, #6c757d);
            white-space: nowrap;
        }

        .enrollment-section__bar {
            width: 90px;
            flex-shrink: 0;
        }

        .enrollment-section__bar .progress {
            display: flex;
            height: 6px;
            background: var(--admin-border, #e9ecef);
        }

        .enrollment-lecture {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.25rem 0.6rem 2.75rem;
            border-top: 1px dashed var(--admin-border, #dee2e6);
        }

        .enrollment-lecture__state {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            border: 2px solid var(--admin-border, #dee2e6);
            color: transparent;
        }

        .enrollment-lecture__state.is-done {
            background: #198754;
            border-color: #198754;
            color: #fff;
        }

        .enrollment-lecture__state.is-started {
            border-color: #0dcaf0;
            color: #0dcaf0;
        }

        .enrollment-dl {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 0.7rem 1rem;
            align-items: start;
        }

        .enrollment-dl dt {
            font-weight: 400;
            color: var(--admin-text-muted, #6c757d);
            margin: 0;
        }

        .enrollment-dl dd {
            margin: 0;
            text-align: end;
            min-width: 0;
        }

        @media (max-width: 575.98px) {
            .enrollment-section__bar {
                display: none;
            }

            .enrollment-lecture {
                padding-left: 1.25rem;
            }
        }
    </style>
@endpush

@push('scripts')
    @include('admin.partials.status-modal')
@endpush
