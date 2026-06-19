@extends('admin.layout')

@section('title', 'Question Details')

@section('content')
    <div class="container-fluid py-4 admin-detail-page">
        @php
            $replyAction = '';
            if ($questionsAnswer->status === 'pending') {
                $replyAction = '<a href="' . route('admin.questions-answers.reply', $questionsAnswer) . '" class="btn btn-primary ms-lg-2"><i class="fas fa-reply me-2"></i>Reply</a>';
            } elseif ($questionsAnswer->status === 'answered') {
                $replyAction = '<a href="' . route('admin.questions-answers.reply', $questionsAnswer) . '" class="btn btn-warning ms-lg-2"><i class="fas fa-edit me-2"></i>Edit Reply</a>';
            }
        @endphp

        @include('admin.partials.detail-page-header', [
            'title' => 'Question Details',
            'subtitle' => Str::limit($questionsAnswer->question_title, 80),
            'backUrl' => route('admin.questions-answers.index'),
            'backLabel' => 'Q&A List',
            'extraActions' => $replyAction,
        ])

        <nav class="admin-detail-section-nav d-lg-none" aria-label="Question sections">
            <a href="#detail-section-question" class="admin-detail-section-nav__link">Question</a>
            <a href="#detail-section-meta" class="admin-detail-section-nav__link">Details</a>
            @if ($questionsAnswer->answer_content)
                <a href="#detail-section-answer" class="admin-detail-section-nav__link">Answer</a>
            @endif
        </nav>

        <div class="row admin-detail-main-row">
            <!-- Question Details -->
            <div class="col-lg-8">
                <div class="card" id="detail-section-question">
                    <div class="card-header">
                        <div class="row align-items-center g-2">
                            <div class="col">
                                <h5 class="card-title mb-0">{{ $questionsAnswer->question_title }}</h5>
                            </div>
                            <div class="col-auto">
                                @switch($questionsAnswer->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @break

                                    @case('answered')
                                        <span class="badge bg-success">Answered</span>
                                    @break

                                    @case('closed')
                                        <span class="badge bg-secondary">Closed</span>
                                    @break

                                    @case('flagged')
                                        <span class="badge bg-danger">Flagged</span>
                                    @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Question Meta -->
                        <div class="question-meta mb-4 admin-detail-grid" id="detail-section-meta">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="meta-item">
                                        <strong>Asked by:</strong>
                                        @if ($questionsAnswer->is_anonymous)
                                            <span class="text-muted">Anonymous Student</span>
                                        @else
                                            <span>{{ $questionsAnswer->user->name ?? 'Unknown User' }}</span>
                                            <small
                                                class="text-muted d-block">{{ $questionsAnswer->user->email ?? '' }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="meta-item">
                                        <strong>Course:</strong>
                                        <span>{{ $questionsAnswer->course->name ?? 'N/A' }}</span>
                                        @if ($questionsAnswer->lecture)
                                            <small class="text-muted d-block">Lecture:
                                                {{ $questionsAnswer->lecture->title }}</small>
                                        @endif
                                        @if ($questionsAnswer->section)
                                            <small class="text-muted d-block">Section:
                                                {{ $questionsAnswer->section->title }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="meta-item">
                                        <strong>Question Type:</strong>
                                        <span
                                            class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $questionsAnswer->question_type)) }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="meta-item">
                                        <strong>Priority:</strong>
                                        @switch($questionsAnswer->priority)
                                            @case('urgent')
                                                <span class="badge bg-danger">Urgent</span>
                                            @break

                                            @case('high')
                                                <span class="badge bg-warning text-dark">High</span>
                                            @break

                                            @case('normal')
                                                <span class="badge bg-info">Normal</span>
                                            @break

                                            @case('low')
                                                <span class="badge bg-secondary">Low</span>
                                            @break
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="meta-item">
                                        <strong>Asked on:</strong>
                                        <span>{{ $questionsAnswer->formatted_question_date }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="meta-item">
                                        <strong>Views:</strong>
                                        <span>{{ $questionsAnswer->views_count }}</span>
                                        <strong class="ml-3">Votes:</strong>
                                        <span>{{ $questionsAnswer->helpful_votes }}/{{ $questionsAnswer->total_votes }}</span>
                                        @if ($questionsAnswer->total_votes > 0)
                                            <small class="text-muted">({{ $questionsAnswer->helpful_percentage }}%
                                                helpful)</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question Content -->
                        <div class="question-content mb-4">
                            <h6>Question:</h6>
                            <div class="content-box">
                                {!! nl2br(e($questionsAnswer->question_content)) !!}
                            </div>
                        </div>

                        <!-- Answer Content -->
                        @if ($questionsAnswer->answer_content)
                            <div class="answer-content" id="detail-section-answer">
                                <h6>Answer:</h6>
                                <div class="content-box answer-box">
                                    {!! nl2br(e($questionsAnswer->answer_content)) !!}

                                    <!-- Audio Answer -->
                                    @if ($questionsAnswer->hasAudio())
                                        <div class="audio-answer mt-3">
                                            <h6 class="mb-2">
                                                <i class="fas fa-microphone"></i> Audio Reply:
                                            </h6>
                                            <div class="audio-player-container">
                                                <audio controls style="width: 100%;">
                                                    <source src="{{ $questionsAnswer->audio_url }}" type="audio/webm">
                                                    <source src="{{ $questionsAnswer->audio_url }}" type="audio/mp3">
                                                    Your browser does not support the audio element.
                                                </audio>
                                                <div class="mt-2">
                                                    <a href="{{ $questionsAnswer->audio_url }}" download
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download Audio
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="answer-meta mt-3">
                                        <small class="text-muted">
                                            Answered by {{ $questionsAnswer->instructor->name ?? 'Unknown Instructor' }}
                                            on {{ $questionsAnswer->formatted_answer_date }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="no-answer">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    This question hasn't been answered yet.
                                    @if ($questionsAnswer->status === 'pending')
                                        <a href="{{ route('admin.questions-answers.reply', $questionsAnswer) }}"
                                            class="btn btn-sm btn-primary ml-2">
                                            Reply Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Moderation Notes -->
                        @if ($questionsAnswer->moderation_notes)
                            <div class="moderation-notes mt-4">
                                <h6>Moderation Notes:</h6>
                                <div class="content-box moderation-box">
                                    {!! nl2br(e($questionsAnswer->moderation_notes)) !!}
                                    @if ($questionsAnswer->moderator)
                                        <div class="moderation-meta mt-2">
                                            <small class="text-muted">
                                                Moderated by {{ $questionsAnswer->moderator->name }}
                                                @if ($questionsAnswer->moderated_at)
                                                    on {{ $questionsAnswer->moderated_at->format('M d, Y \a\t g:i A') }}
                                                @endif
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Questions -->
                @if ($relatedQuestions->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Related Questions</h5>
                        </div>
                        <div class="card-body">
                            <div class="related-questions">
                                @foreach ($relatedQuestions as $related)
                                    <div class="related-question mb-3">
                                        <h6>
                                            <a href="{{ route('admin.questions-answers.show', $related) }}"
                                                class="text-dark">
                                                {{ $related->question_title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ Str::limit(strip_tags($related->question_content), 100) }}
                                        </small>
                                        <div class="mt-1">
                                            <span class="badge badge-sm badge-success">{{ $related->status }}</span>
                                            @if ($related->created_at)
                                                <small
                                                    class="text-muted ml-2">{{ $related->created_at->format('M d, Y') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="col-lg-4 admin-detail-sidebar">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="action-buttons">
                            @if ($questionsAnswer->status === 'pending')
                                <form action="{{ route('admin.questions-answers.approve', $questionsAnswer) }}"
                                    method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-check"></i> Approve Question
                                    </button>
                                </form>
                            @endif

                            @if ($questionsAnswer->status !== 'closed')
                                <form action="{{ route('admin.questions-answers.close', $questionsAnswer) }}"
                                    method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-block">
                                        <i class="fas fa-lock"></i> Close Question
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.questions-answers.reopen', $questionsAnswer) }}"
                                    method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-block">
                                        <i class="fas fa-unlock"></i> Reopen Question
                                    </button>
                                </form>
                            @endif

                            <button type="button" class="btn btn-danger btn-block mb-2" data-toggle="modal"
                                data-target="#rejectModal">
                                <i class="fas fa-flag"></i> Flag/Reject
                            </button>

                            @if ($questionsAnswer->status === 'answered')
                                <form action="{{ route('admin.questions-answers.delete_reply', $questionsAnswer) }}"
                                    method="POST" class="mb-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-block"
                                        onclick="return confirm('Are you sure you want to delete this reply?')">
                                        <i class="fas fa-trash"></i> Delete Reply
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Priority Management -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Priority Management</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.questions-answers.update_priority', $questionsAnswer) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Current Priority:</label>
                                <select name="priority" class="form-control" onchange="this.form.submit()">
                                    <option value="low" {{ $questionsAnswer->priority === 'low' ? 'selected' : '' }}>
                                        Low
                                    </option>
                                    <option value="normal"
                                        {{ $questionsAnswer->priority === 'normal' ? 'selected' : '' }}>Normal
                                    </option>
                                    <option value="high" {{ $questionsAnswer->priority === 'high' ? 'selected' : '' }}>
                                        High
                                    </option>
                                    <option value="urgent"
                                        {{ $questionsAnswer->priority === 'urgent' ? 'selected' : '' }}>Urgent
                                    </option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Question Statistics -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="stat-item">
                            <strong>Views:</strong> {{ $questionsAnswer->views_count }}
                        </div>
                        <div class="stat-item">
                            <strong>Helpful Votes:</strong> {{ $questionsAnswer->helpful_votes }}
                        </div>
                        <div class="stat-item">
                            <strong>Total Votes:</strong> {{ $questionsAnswer->total_votes }}
                        </div>
                        @if ($questionsAnswer->total_votes > 0)
                            <div class="stat-item">
                                <strong>Helpful Percentage:</strong> {{ $questionsAnswer->helpful_percentage }}%
                            </div>
                        @endif
                        @if ($questionsAnswer->answered_at)
                            <div class="stat-item">
                                <strong>Response Time:</strong>
                                {{ $questionsAnswer->created_at->diffForHumans($questionsAnswer->answered_at, true) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Flag/Reject Question</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.questions-answers.reject', $questionsAnswer) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Rejection Reason:</label>
                            <textarea name="moderation_notes" class="form-control" rows="4" required
                                placeholder="Please provide a reason for flagging/rejecting this question..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Flag/Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
