@extends('layouts.app')

@section('title', $course->name . ' - Learn')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/course-content.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <style>
        .btn-orange {
            background-color: #FF6B35;
            border-color: #FF6B35;
            color: white;
        }

        .btn-orange:hover {
            background-color: #e55a2b;
            border-color: #e55a2b;
            color: white;
        }

        .completed-lecture {
            background-color: #e8f5e8 !important;
            border-left: 4px solid #28a745 !important;
        }

        .completed-lecture .class-title {
            color: #28a745;
        }
    </style>
@endpush

@section('content')
    <!-- Course Header -->
    @include('courses.partials.course-header', ['course' => $course])

    <!-- Language Switcher -->
    <div class="container mt-3">
        <div class="d-flex justify-content-end">
            @include('partials.language-switcher')
        </div>
    </div>

    <!-- Course Progress Hero -->
    @include('courses.partials.course-progress', [
        'course' => $course,
        'userEnrollment' => $enrollment,
    ])

    <!-- Tabs Section -->
    <section class="course-content-tabs-section py-4">
        <div class="container">
            <ul class="nav nav-tabs course-content-tabs mb-4" id="courseTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                        type="button" role="tab" aria-controls="overview"
                        aria-selected="true">{{ __('Overview') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button"
                        role="tab" aria-controls="content" aria-selected="false">{{ __('Course Content') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#live" type="button"
                        role="tab" aria-controls="live" aria-selected="false">{{ __('Live Class') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="qa-tab" data-bs-toggle="tab" data-bs-target="#qa" type="button"
                        role="tab" aria-controls="qa" aria-selected="false">{{ __('Q & A') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quiz-tab" data-bs-toggle="tab" data-bs-target="#quiz" type="button"
                        role="tab" aria-controls="quiz" aria-selected="false">{{ __('Quiz') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="homework-tab" data-bs-toggle="tab" data-bs-target="#homework"
                        type="button" role="tab" aria-controls="homework"
                        aria-selected="false">{{ __('Homework') }}</button>
                </li>
            </ul>

            <div class="tab-content" id="courseTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    @include('courses.partials.course-overview', ['course' => $course])



                </div>

                <!-- Course Content Tab -->
                <div class="tab-pane fade" id="content" role="tabpanel" aria-labelledby="content-tab">
                    @include('courses.partials.course-curriculum', ['course' => $course])
                </div>

                <!-- Live Class Tab -->
                <div class="tab-pane fade" id="live" role="tabpanel" aria-labelledby="live-tab">
                    <div class="live-class-section bg-light-blue-section py-5">
                        <div class="container">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="fw-bold">{{ __('Live Classes') }}</h3>
                            </div>

                            @if ($course->liveClasses && $course->liveClasses->count() > 0)
                                <div class="row g-4">
                                    @foreach ($course->liveClasses as $liveClass)
                                        @php
                                            $userRegistration = $liveClass->registrations->first();
                                            $isRegistered = $userRegistration !== null;
                                        @endphp
                                        <div class="col-lg-6">
                                            <div class="live-class-card bg-white p-4 rounded-4 shadow-sm h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="fw-bold mb-0">{{ $liveClass->title }}</h5>
                                                    <span
                                                        class="badge bg-{{ $liveClass->status === 'scheduled' ? 'primary' : ($liveClass->status === 'live' ? 'success' : 'secondary') }}">
                                                        {{ ucfirst($liveClass->status) }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mb-3">{{ $liveClass->description }}</p>

                                                @if ($isRegistered)
                                                    <div class="alert alert-success mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-check-circle me-2"></i>
                                                            <strong>{{ __('You are registered for this class!') }}</strong>
                                                        </div>
                                                        @if ($liveClass->link)
                                                            <div class="mt-2">
                                                                <a href="{{ $liveClass->link }}" target="_blank"
                                                                    class="btn btn-success btn-sm">
                                                                    <i
                                                                        class="fa fa-external-link-alt me-1"></i>{{ __('Join Meeting') }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="live-class-details mb-3">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Date') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $liveClass->scheduled_at ? $liveClass->scheduled_at->format('M d, Y') : __('TBD') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Time') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $liveClass->scheduled_at ? $liveClass->scheduled_at->format('g:i A') : __('TBD') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Duration') }}:</small>
                                                            <div class="fw-bold">{{ $liveClass->duration ?? '60' }}
                                                                {{ __('minutes') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Capacity') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $liveClass->max_participants ?? 'Unlimited' }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2">
                                                    @if (!$isRegistered)
                                                        <button class="btn btn-outline-primary flex-fill"
                                                            onclick="joinLiveClass({{ $liveClass->id }})">
                                                            <i class="fa fa-video me-2"></i>{{ __('Join Live Class') }}
                                                        </button>
                                                    @else
                                                        <div class="text-center flex-fill">
                                                            <span class="badge bg-success">
                                                                <i class="fa fa-check me-1"></i>{{ __('Registered') }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if ($liveClass->materials && count($liveClass->materials) > 0)
                                                        <button class="btn btn-outline-secondary"
                                                            onclick="downloadMaterials({{ $liveClass->id }})"
                                                            title="{{ __('Download Materials') }}">
                                                            <i class="fa fa-download"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fa fa-video fa-3x text-muted mb-3"></i>
                                    <h4 class="fw-bold mb-2">{{ __('No Live Classes Scheduled') }}</h4>
                                    <p class="text-muted mb-4">{{ __('Live classes will be scheduled and appear here.') }}
                                    </p>
                                    <button class="btn btn-orange" onclick="getNotified()">
                                        <i class="fa fa-bell me-2"></i>{{ __('Get Notified') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Q&A Tab -->
                <div class="tab-pane fade" id="qa" role="tabpanel" aria-labelledby="qa-tab">
                    @include('courses.partials.course-qa', ['course' => $course])
                </div>

                <!-- Quiz Tab -->
                <div class="tab-pane fade" id="quiz" role="tabpanel" aria-labelledby="quiz-tab">
                    @include('courses.partials.course-quiz', ['course' => $course])
                </div>

                <!-- Homework Tab -->
                <div class="tab-pane fade" id="homework" role="tabpanel" aria-labelledby="homework-tab">
                    <div class="homework-section bg-light-blue-section py-5">
                        <div class="container">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="fw-bold">{{ __('Homework Assignments') }}</h3>
                            </div>

                            @if ($course->homework && $course->homework->count() > 0)
                                <div class="row g-4">
                                    @foreach ($course->homework as $assignment)
                                        @php
                                            $userSubmission = $assignment->submissions->first();
                                            $hasSubmitted = $userSubmission !== null;
                                            $isGraded = $hasSubmitted && $userSubmission->is_graded;
                                        @endphp
                                        <div class="col-lg-6">
                                            <div class="homework-card bg-white p-4 rounded-4 shadow-sm h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="fw-bold mb-0">{{ $assignment->name }}</h5>
                                                    <span
                                                        class="badge bg-{{ $hasSubmitted ? ($isGraded ? 'success' : 'warning') : 'secondary' }}">
                                                        {{ $hasSubmitted ? ($isGraded ? __('Graded') : __('Submitted')) : __('Not Started') }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mb-3">{{ $assignment->description }}</p>
                                                <div class="homework-details mb-3">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Due Date') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : __('No due date') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Points') }}:</small>
                                                            <div class="fw-bold">{{ $assignment->max_score ?? '0' }}
                                                                {{ __('points') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Type') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ ucfirst($assignment->type ?? 'Assignment') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ __('Status') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $hasSubmitted ? ($isGraded ? __('Graded') : __('Submitted')) : __('Not Started') }}
                                                            </div>
                                                        </div>
                                                        @if ($isGraded)
                                                            <div class="col-12">
                                                                <div class="alert alert-success mb-0">
                                                                    <strong>{{ __('Your Grade:') }}</strong>
                                                                    {{ $userSubmission->score_earned }}/{{ $userSubmission->max_score }}
                                                                    ({{ number_format($userSubmission->percentage_score, 1) }}%)
                                                                    @if ($userSubmission->feedback)
                                                                        <br><small
                                                                            class="text-muted">{{ __('Feedback:') }}
                                                                            {{ $userSubmission->feedback }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-outline-primary flex-fill"
                                                        onclick="viewHomeworkDetails({{ $assignment->id }})">
                                                        <i class="fa fa-eye me-2"></i>{{ __('View Details') }}
                                                    </button>
                                                    @if (!$hasSubmitted)
                                                        <button class="btn btn-orange flex-fill"
                                                            onclick="submitHomeworkAssignment({{ $assignment->id }})">
                                                            <i class="fa fa-upload me-2"></i>{{ __('Submit') }}
                                                        </button>
                                                    @else
                                                        <button class="btn btn-secondary flex-fill" disabled>
                                                            <i class="fa fa-check me-2"></i>{{ __('Already Submitted') }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fa fa-book fa-3x text-muted mb-3"></i>
                                    <h4 class="fw-bold mb-2">{{ __('No Homework Assignments') }}</h4>
                                    <p class="text-muted mb-4">
                                        {{ __('Homework assignments will be posted here by your instructor.') }}</p>
                                    <button class="btn btn-orange" onclick="getHomeworkNotified()">
                                        <i class="fa fa-bell me-2"></i>{{ __('Get Notified') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalLabel">{{ __('Course Video') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Video Player Container -->
                    <div id="videoPlayerContainer">
                        <!-- HTML5 Video Player for direct video files -->
                        <div class="ratio ratio-16x9 d-none-initially" id="html5VideoContainer">
                            <video controls id="courseVideo">
                                <source src="" type="video/mp4">
                                {{ __('Your browser does not support the video tag.') }}
                            </video>
                        </div>

                        <!-- YouTube Embed -->
                        <div class="ratio ratio-16x9 d-none-initially" id="youtubePlayer">
                            <iframe id="youtubeIframe" frameborder="0" allowfullscreen></iframe>
                        </div>

                        <!-- Vimeo Embed -->
                        <div class="ratio ratio-16x9 d-none-initially" id="vimeoPlayer">
                            <iframe id="vimeoIframe" frameborder="0" allowfullscreen></iframe>
                        </div>

                        <!-- File Download for other file types -->
                        <div id="fileDownload" class="file-download-section d-none-initially">
                            <i class="fa fa-file fa-3x text-primary mb-3"></i>
                            <h5>{{ __('File Available for Download') }}</h5>
                            <p class="text-muted">{{ __('This content is available as a downloadable file.') }}</p>
                            <a id="downloadLink" href="" class="btn btn-primary" target="_blank">
                                <i class="fa fa-download me-2"></i>{{ __('Download File') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQuestionModalLabel">{{ __('Add New Question') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addQuestionForm">
                        <div class="mb-3">
                            <label for="questionTitle" class="form-label">{{ __('Question Title') }}</label>
                            <input type="text" class="form-control" id="questionTitle"
                                placeholder="{{ __('Enter your question title') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="questionContent" class="form-label">{{ __('Question Details') }}</label>
                            <div id="questionEditor"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-orange"
                        id="submitQuestion">{{ __('Submit Question') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Answer Modal -->
    <div class="modal fade" id="addAnswerModal" tabindex="-1" aria-labelledby="addAnswerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnswerModalLabel">{{ __('Add Answer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 class="text-muted">{{ __('Question:') }}</h6>
                        <p id="answerQuestionTitle" class="fw-bold"></p>
                    </div>
                    <form id="addAnswerForm">
                        <div class="mb-3">
                            <label for="answerContent" class="form-label">{{ __('Your Answer') }}</label>
                            <div id="answerEditor"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-orange" id="submitAnswer">{{ __('Submit Answer') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">{{ __('Report Content') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm">
                        <div class="mb-3">
                            <label for="reportTitle" class="form-label">{{ __('Report Title') }}</label>
                            <input type="text" class="form-control" id="reportTitle"
                                placeholder="{{ __('Brief description of the issue') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="reportEmail" class="form-label">{{ __('Your Email') }}</label>
                            <input type="email" class="form-control" id="reportEmail"
                                placeholder="your.email@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="reportDetails" class="form-label">{{ __('Details') }}</label>
                            <div id="reportEditor"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="submitReport">{{ __('Submit Report') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Live Class Modal -->
    <div class="modal fade" id="scheduleLiveClassModal" tabindex="-1" aria-labelledby="scheduleLiveClassModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleLiveClassModalLabel">{{ __('Schedule Live Class') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleLiveClassForm">
                        <div class="mb-3">
                            <label for="liveClassTitle" class="form-label">{{ __('Class Title') }}</label>
                            <input type="text" class="form-control" id="liveClassTitle" name="title"
                                placeholder="{{ __('Enter class title') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="liveClassDescription" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="liveClassDescription" name="description" rows="3"
                                placeholder="{{ __('Enter class description') }}"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassDate" class="form-label">{{ __('Date') }}</label>
                                    <input type="date" class="form-control" id="liveClassDate" name="scheduled_date"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassTime" class="form-label">{{ __('Time') }}</label>
                                    <input type="time" class="form-control" id="liveClassTime" name="scheduled_time"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassDuration"
                                        class="form-label">{{ __('Duration (minutes)') }}</label>
                                    <input type="number" class="form-control" id="liveClassDuration" name="duration"
                                        value="60" min="15" max="180">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassCapacity"
                                        class="form-label">{{ __('Max Participants') }}</label>
                                    <input type="number" class="form-control" id="liveClassCapacity"
                                        name="max_participants" value="50" min="1">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" form="scheduleLiveClassForm"
                        class="btn btn-orange">{{ __('Schedule Class') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Join Live Class Modal -->
    <div class="modal fade" id="joinLiveClassModal" tabindex="-1" aria-labelledby="joinLiveClassModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="joinLiveClassModalLabel">{{ __('Join Live Class') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="joinLiveClassForm">
                        <div class="mb-3">
                            <label for="joinReason" class="form-label">{{ __('Reason for joining') }}</label>
                            <textarea class="form-control" id="joinReason" name="reason" rows="3"
                                placeholder="{{ __('Why do you want to join this live class?') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" form="joinLiveClassForm"
                        class="btn btn-primary">{{ __('Join Class') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Homework Modal -->
    <div class="modal fade" id="submitHomeworkModal" tabindex="-1" aria-labelledby="submitHomeworkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitHomeworkModalLabel">{{ __('Submit Homework') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitHomeworkForm">
                        <div class="mb-3">
                            <label for="homeworkTitle" class="form-label">{{ __('Homework Title') }}</label>
                            <input type="text" class="form-control" id="homeworkTitle" name="title"
                                placeholder="{{ __('Enter homework title') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="homeworkDescription" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="homeworkDescription" name="description" rows="3"
                                placeholder="{{ __('Enter homework description') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="homeworkFile" class="form-label">{{ __('Upload File') }}</label>
                            <input type="file" class="form-control" id="homeworkFile" name="file"
                                accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                            <div class="form-text">{{ __('Accepted formats: PDF, DOC, DOCX, TXT, ZIP, RAR') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="homeworkContent" class="form-label">{{ __('Content') }}</label>
                            <textarea class="form-control" id="homeworkContent" name="content" rows="5"
                                placeholder="{{ __('Enter your homework content here...') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" form="submitHomeworkForm"
                        class="btn btn-orange">{{ __('Submit Homework') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Homework Details Modal -->
    <div class="modal fade" id="viewHomeworkDetailsModal" tabindex="-1" aria-labelledby="viewHomeworkDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewHomeworkDetailsModalLabel">{{ __('Homework Details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="homeworkDetailsId">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('Title') }}</h6>
                            <p id="homeworkDetailsTitle"></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('Status') }}</h6>
                            <p id="homeworkDetailsStatus"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('Due Date') }}</h6>
                            <p id="homeworkDetailsDueDate"></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('Points') }}</h6>
                            <p id="homeworkDetailsPoints"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ __('Type') }}</h6>
                            <p id="homeworkDetailsType"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold">{{ __('Description') }}</h6>
                            <div id="homeworkDetailsDescription"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-orange"
                        onclick="submitHomeworkAssignment(document.getElementById('homeworkDetailsId').value)">{{ __('Submit Assignment') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Homework Assignment Modal -->
    <div class="modal fade" id="submitHomeworkAssignmentModal" tabindex="-1"
        aria-labelledby="submitHomeworkAssignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitHomeworkAssignmentModalLabel">{{ __('Submit Assignment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitHomeworkAssignmentForm">
                        <div class="mb-3">
                            <label for="assignmentFile" class="form-label">{{ __('Upload Assignment File') }}</label>
                            <input type="file" class="form-control" id="assignmentFile" name="file"
                                accept=".pdf,.doc,.docx,.txt,.zip,.rar" required>
                            <div class="form-text">{{ __('Accepted formats: PDF, DOC, DOCX, TXT, ZIP, RAR') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="assignmentContent" class="form-label">{{ __('Assignment Content') }}</label>
                            <textarea class="form-control" id="assignmentContent" name="content" rows="5"
                                placeholder="{{ __('Enter your assignment content here...') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="assignmentNotes" class="form-label">{{ __('Notes (Optional)') }}</label>
                            <textarea class="form-control" id="assignmentNotes" name="notes" rows="3"
                                placeholder="{{ __('Any additional notes for your instructor...') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" form="submitHomeworkAssignmentForm"
                        class="btn btn-orange">{{ __('Submit Assignment') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Course Content Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const courseId = {{ $course->id }};
            const sectionSelectCheckboxes = document.querySelectorAll('.section-select-checkbox');
            const lectureSelectCheckboxes = document.querySelectorAll('.lecture-select-checkbox');
            const markCompleteBtn = document.getElementById('markCompleteBtn');
            const markIncompleteBtn = document.getElementById('markIncompleteBtn');
            const playVideoBtns = document.querySelectorAll('.play-video-btn');
            const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
            const courseVideo = document.getElementById('courseVideo');

            // Handle section checkbox changes
            function updateActionButtons() {
                const checkedSectionBoxes = document.querySelectorAll('.section-select-checkbox:checked');
                const checkedLectureBoxes = document.querySelectorAll('.lecture-select-checkbox:checked');
                const totalChecked = checkedSectionBoxes.length + checkedLectureBoxes.length;

                if (totalChecked > 0) {
                    markCompleteBtn.disabled = false;
                    markIncompleteBtn.disabled = false;
                    markCompleteBtn.innerHTML =
                        '<i class="fa fa-check me-2"></i>{{ __('Mark Selected as Complete') }} (' + totalChecked +
                        ')';
                    markIncompleteBtn.innerHTML =
                        '<i class="fa fa-times me-2"></i>{{ __('Mark Selected as Incomplete') }} (' +
                        totalChecked + ')';
                } else {
                    markCompleteBtn.disabled = true;
                    markIncompleteBtn.disabled = true;
                    markCompleteBtn.innerHTML =
                        '<i class="fa fa-check me-2"></i>{{ __('Mark Selected as Complete') }}';
                    markIncompleteBtn.innerHTML =
                        '<i class="fa fa-times me-2"></i>{{ __('Mark Selected as Incomplete') }}';
                }
            }

            // Add event listeners to checkboxes
            sectionSelectCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const sectionId = this.dataset.section;
                    const isChecked = this.checked;

                    // Check/uncheck all lectures in this section
                    const sectionLectures = document.querySelectorAll(
                        `.lecture-select-checkbox[data-section="${sectionId}"]`);
                    sectionLectures.forEach(lectureCheckbox => {
                        lectureCheckbox.checked = isChecked;
                    });

                    updateActionButtons();
                });
            });

            lectureSelectCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateActionButtons);
            });

            // Handle mark complete button click
            markCompleteBtn?.addEventListener('click', function() {
                const checkedSectionBoxes = document.querySelectorAll('.section-select-checkbox:checked');
                const checkedLectureBoxes = document.querySelectorAll('.lecture-select-checkbox:checked');
                const totalChecked = checkedSectionBoxes.length + checkedLectureBoxes.length;

                if (totalChecked > 0) {
                    // Process lecture completions
                    const lectureIds = Array.from(checkedLectureBoxes).map(checkbox => checkbox.dataset
                        .lecture);

                    // Send AJAX request to mark lectures as complete
                    if (lectureIds.length > 0) {
                        fetch(`/courses/${courseId}/complete-lectures`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    lecture_ids: lectureIds
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update progress bar
                                    updateProgressBar(data.progress);

                                    // Update visual state of completed lectures
                                    lectureIds.forEach(lectureId => {
                                        const lectureCard = document.querySelector(
                                            `.lecture-select-checkbox[data-lecture="${lectureId}"]`
                                        ).closest('.class-card');
                                        if (lectureCard) {
                                            lectureCard.classList.add('completed-lecture');

                                            // Update completion status indicator
                                            const statusIndicator = lectureCard.querySelector(
                                                '.fa-circle, .fa-check-circle');
                                            if (statusIndicator) {
                                                statusIndicator.className =
                                                    'fa fa-check-circle text-success';
                                                statusIndicator.title =
                                                    '{{ __('Completed') }}';
                                            }

                                            // Add completion badge if not exists
                                            const title = lectureCard.querySelector(
                                                '.class-title');
                                            if (title && !title.querySelector('.badge')) {
                                                title.innerHTML +=
                                                    '<span class="badge bg-success ms-2"><i class="fa fa-check"></i> {{ __('Completed') }}</span>';
                                            }
                                        }
                                    });
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }

                    // Show success message
                    showToast(`${totalChecked} {{ __('item(s) marked as complete!') }}`, 'success');

                    // Uncheck all checkboxes and update button
                    clearAllSelections();
                }
            });

            // Handle mark incomplete button click
            markIncompleteBtn?.addEventListener('click', function() {
                const checkedSectionBoxes = document.querySelectorAll('.section-select-checkbox:checked');
                const checkedLectureBoxes = document.querySelectorAll('.lecture-select-checkbox:checked');
                const totalChecked = checkedSectionBoxes.length + checkedLectureBoxes.length;

                if (totalChecked > 0) {
                    // Process lecture incompletions
                    const lectureIds = Array.from(checkedLectureBoxes).map(checkbox => checkbox.dataset
                        .lecture);

                    // Send AJAX request to mark lectures as incomplete
                    if (lectureIds.length > 0) {
                        fetch(`/courses/${courseId}/incomplete-lectures`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    lecture_ids: lectureIds
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update progress bar
                                    updateProgressBar(data.progress);

                                    // Update visual state of incomplete lectures
                                    lectureIds.forEach(lectureId => {
                                        const lectureCard = document.querySelector(
                                            `.lecture-select-checkbox[data-lecture="${lectureId}"]`
                                        ).closest('.class-card');
                                        if (lectureCard) {
                                            lectureCard.classList.remove('completed-lecture');

                                            // Update completion status indicator
                                            const statusIndicator = lectureCard.querySelector(
                                                '.fa-circle, .fa-check-circle');
                                            if (statusIndicator) {
                                                statusIndicator.className =
                                                    'fa fa-circle text-muted';
                                                statusIndicator.title =
                                                    '{{ __('Not Completed') }}';
                                            }

                                            // Remove completion badge
                                            const title = lectureCard.querySelector(
                                                '.class-title');
                                            const badge = title?.querySelector('.badge');
                                            if (badge) {
                                                badge.remove();
                                            }
                                        }
                                    });
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }

                    // Show success message
                    showToast(`${totalChecked} {{ __('item(s) marked as incomplete!') }}`, 'warning');

                    // Uncheck all checkboxes and update button
                    clearAllSelections();
                }
            });

            // Function to update progress bar
            function updateProgressBar(progress) {
                const progressBar = document.querySelector('.course-content-hero .progress-bar');
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                    progressBar.setAttribute('aria-valuenow', progress);
                    progressBar.textContent = progress + '%';
                }
            }

            // Function to show toast messages
            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                const bgClass = type === 'success' ? 'bg-success' : 'bg-warning';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';

                toast.className =
                    `toast align-items-center text-white ${bgClass} border-0 position-fixed top-0 end-0 m-3`;
                toast.style.zIndex = '9999';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fa ${icon} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);

                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();

                // Remove toast after it's hidden
                toast.addEventListener('hidden.bs.toast', () => {
                    document.body.removeChild(toast);
                });
            }

            // Function to clear all selections
            function clearAllSelections() {
                const checkedSectionBoxes = document.querySelectorAll('.section-select-checkbox:checked');
                const checkedLectureBoxes = document.querySelectorAll('.lecture-select-checkbox:checked');

                checkedSectionBoxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                checkedLectureBoxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                updateActionButtons();
            }

            // Handle video play buttons
            playVideoBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remove active class from all buttons
                    playVideoBtns.forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    const videoSrc = this.getAttribute('data-video');
                    const lectureTitle = this.closest('.class-card').querySelector('.class-title')
                        .textContent.trim();

                    if (videoSrc) {
                        // Hide all video players first
                        document.getElementById('html5VideoContainer').style.display = 'none';
                        document.getElementById('youtubePlayer').style.display = 'none';
                        document.getElementById('vimeoPlayer').style.display = 'none';
                        document.getElementById('fileDownload').style.display = 'none';

                        // Determine video type and show appropriate player
                        if (isYouTubeUrl(videoSrc)) {
                            showYouTubeVideo(videoSrc);
                        } else if (isVimeoUrl(videoSrc)) {
                            showVimeoVideo(videoSrc);
                        } else if (isVideoFile(videoSrc)) {
                            showHTML5Video(videoSrc);
                        } else {
                            showFileDownload(videoSrc);
                        }

                        // Update modal title
                        document.getElementById('videoModalLabel').textContent =
                            '{{ __('Playing:') }} ' + lectureTitle;

                        // Show the modal
                        videoModal.show();
                    } else {
                        // Show error message if no video URL
                        showToast('{{ __('No video available for this lecture') }}', 'warning');
                    }
                });
            });

            // Helper functions for video handling
            function isYouTubeUrl(url) {
                return url.includes('youtube.com') || url.includes('youtu.be');
            }

            function isVimeoUrl(url) {
                return url.includes('vimeo.com');
            }

            function isVideoFile(url) {
                const videoExtensions = ['.mp4', '.webm', '.ogg', '.mov', '.avi'];
                return videoExtensions.some(ext => url.toLowerCase().includes(ext));
            }

            function showYouTubeVideo(url) {
                const videoId = extractYouTubeId(url);
                const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;

                document.getElementById('youtubeIframe').src = embedUrl;
                document.getElementById('youtubePlayer').style.display = 'block';
            }

            function showVimeoVideo(url) {
                const videoId = extractVimeoId(url);
                const embedUrl = `https://player.vimeo.com/video/${videoId}?autoplay=1`;

                document.getElementById('vimeoIframe').src = embedUrl;
                document.getElementById('vimeoPlayer').style.display = 'block';
            }

            function showHTML5Video(url) {
                const video = document.getElementById('courseVideo');
                video.querySelector('source').src = url;
                video.load();

                document.getElementById('html5VideoContainer').style.display = 'block';

                // Get current lecture ID for auto-completion
                const currentLectureId = getCurrentLectureId();

                // Auto-play when modal opens
                videoModal._element.addEventListener('shown.bs.modal', function() {
                    video.play();
                }, {
                    once: true
                });

                // Track video progress for auto-completion
                video.addEventListener('timeupdate', function() {
                    if (video.duration > 0) {
                        const progress = (video.currentTime / video.duration) * 100;

                        // Auto-complete when 80% watched
                        if (progress >= 80 && currentLectureId) {
                            autoCompleteLecture(currentLectureId);
                        }
                    }
                });
            }

            function getCurrentLectureId() {
                // Get the lecture ID from the clicked button
                const activeButton = document.querySelector('.play-video-btn.active');
                if (activeButton) {
                    const lectureCard = activeButton.closest('.class-card');
                    const lectureCheckbox = lectureCard.querySelector('.lecture-select-checkbox');
                    return lectureCheckbox ? lectureCheckbox.dataset.lecture : null;
                }
                return null;
            }

            function autoCompleteLecture(lectureId) {
                // Check if already completed
                const lectureCard = document.querySelector(`.lecture-select-checkbox[data-lecture="${lectureId}"]`)
                    .closest('.class-card');
                if (lectureCard.classList.contains('completed-lecture')) {
                    return; // Already completed
                }

                // Mark as complete
                fetch(`/courses/${courseId}/complete-lectures`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            lecture_ids: [lectureId]
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update visual state
                            lectureCard.classList.add('completed-lecture');

                            // Update completion status indicator
                            const statusIndicator = lectureCard.querySelector('.fa-circle, .fa-check-circle');
                            if (statusIndicator) {
                                statusIndicator.className = 'fa fa-check-circle text-success';
                                statusIndicator.title = '{{ __('Completed') }}';
                            }

                            // Add completion badge if not exists
                            const title = lectureCard.querySelector('.class-title');
                            if (title && !title.querySelector('.badge')) {
                                title.innerHTML +=
                                    '<span class="badge bg-success ms-2"><i class="fa fa-check"></i> {{ __('Completed') }}</span>';
                            }

                            // Update progress bar
                            updateProgressBar(data.progress);

                            // Show success message
                            showToast('{{ __('Lecture completed automatically!') }}', 'success');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            function showFileDownload(url) {
                document.getElementById('downloadLink').href = url;
                document.getElementById('fileDownload').style.display = 'block';
            }

            function extractYouTubeId(url) {
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }

            function extractVimeoId(url) {
                const regExp = /vimeo\.com\/([0-9]+)/;
                const match = url.match(regExp);
                return match ? match[1] : null;
            }

            // Pause video when modal is closed
            document.getElementById('videoModal').addEventListener('hidden.bs.modal', function() {
                // Pause HTML5 video
                const video = document.getElementById('courseVideo');
                if (video) {
                    video.pause();
                }

                // Clear iframe sources to stop embedded videos
                document.getElementById('youtubeIframe').src = '';
                document.getElementById('vimeoIframe').src = '';

                // Hide all players
                document.getElementById('html5VideoContainer').style.display = 'none';
                document.getElementById('youtubePlayer').style.display = 'none';
                document.getElementById('vimeoPlayer').style.display = 'none';
                document.getElementById('fileDownload').style.display = 'none';
            });

            // Initialize button state
            updateActionButtons();
        });

        // Q&A Functionality
        let questionEditor, answerEditor, reportEditor;

        // Initialize CKEditor instances when modals are shown
        document.getElementById('addQuestionModal').addEventListener('shown.bs.modal', function() {
            if (!questionEditor) {
                ClassicEditor
                    .create(document.querySelector('#questionEditor'))
                    .then(editor => {
                        questionEditor = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });

        document.getElementById('addAnswerModal').addEventListener('shown.bs.modal', function() {
            if (!answerEditor) {
                ClassicEditor
                    .create(document.querySelector('#answerEditor'))
                    .then(editor => {
                        answerEditor = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });

        document.getElementById('reportModal').addEventListener('shown.bs.modal', function() {
            if (!reportEditor) {
                ClassicEditor
                    .create(document.querySelector('#reportEditor'))
                    .then(editor => {
                        reportEditor = editor;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        });

        // Handle Add Answer button clicks
        document.querySelectorAll('[data-bs-target="#addAnswerModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const questionText = this.getAttribute('data-question');
                document.getElementById('answerQuestionTitle').textContent = questionText;
            });
        });

        // Handle form submissions
        document.getElementById('submitQuestion')?.addEventListener('click', function() {
            const title = document.getElementById('questionTitle').value;
            const content = questionEditor ? questionEditor.getData() : '';

            if (title.trim() === '') {
                alert('{{ __('Please enter a question title.') }}');
                return;
            }

            // Show success message
            showToast('{{ __('Question submitted successfully!') }}', 'success');

            // Reset form
            document.getElementById('questionTitle').value = '';
            if (questionEditor) questionEditor.setData('');

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addQuestionModal')).hide();
        });

        document.getElementById('submitAnswer')?.addEventListener('click', function() {
            const content = answerEditor ? answerEditor.getData() : '';

            if (content.trim() === '') {
                alert('{{ __('Please enter your answer.') }}');
                return;
            }

            // Show success message
            showToast('{{ __('Answer submitted successfully!') }}', 'success');

            // Reset form
            if (answerEditor) answerEditor.setData('');

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addAnswerModal')).hide();
        });

        document.getElementById('submitReport')?.addEventListener('click', function() {
            const title = document.getElementById('reportTitle').value;
            const email = document.getElementById('reportEmail').value;
            const details = reportEditor ? reportEditor.getData() : '';

            if (title.trim() === '' || email.trim() === '') {
                alert('{{ __('Please fill in all required fields.') }}');
                return;
            }

            // Show success message
            showToast('{{ __('Report submitted successfully!') }}', 'success');

            // Reset form
            document.getElementById('reportTitle').value = '';
            document.getElementById('reportEmail').value = '';
            if (reportEditor) reportEditor.setData('');

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
        });

        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed top-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa fa-check-circle me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove toast after it's hidden
            toast.addEventListener('hidden.bs.toast', () => {
                document.body.removeChild(toast);
            });
        }

        // New functions for Live Class functionality

        function joinLiveClass(liveClassId) {
            const courseId = {{ $course->id }};
            const modal = new bootstrap.Modal(document.getElementById('joinLiveClassModal'));
            modal.show();

            document.getElementById('joinLiveClassForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('live_class_id', liveClassId);
                fetch(`/courses/${courseId}/join-live-class`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('{{ __('You have joined the live class!') }}', 'success');
                            modal.hide();
                            // Optionally, update the live class status or UI
                            location.reload(); // Simple refresh to show updated live class
                        } else {
                            showToast('{{ __('Failed to join live class.') }}', 'danger');
                            console.error(data.message);
                        }
                    })
                    .catch(error => {
                        showToast('{{ __('Error joining live class.') }}', 'danger');
                        console.error(error);
                    });
            });
        }

        function getNotified() {
            showToast('{{ __('You will be notified when a live class is scheduled.') }}', 'info');
        }

        function downloadMaterials(liveClassId) {
            const courseId = {{ $course->id }};

            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Downloading...';
            button.disabled = true;

            // Create a temporary form to download the materials
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/courses/${courseId}/live-classes/${liveClassId}/download-materials`;
            form.style.display = 'none';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            // Append form to body and submit
            document.body.appendChild(form);

            // Add error handling
            try {
                form.submit();
            } catch (error) {
                console.error('Download error:', error);
                showToast('{{ __('Error downloading materials.') }}', 'danger');
                button.innerHTML = originalText;
                button.disabled = false;
            }

            // Reset button after a short delay
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                if (document.body.contains(form)) {
                    document.body.removeChild(form);
                }
            }, 2000);
        }

        // New functions for Homework functionality

        function viewHomeworkDetails(assignmentId) {
            const courseId = {{ $course->id }};
            const modal = new bootstrap.Modal(document.getElementById('viewHomeworkDetailsModal'));
            modal.show();

            fetch(`/courses/${courseId}/homework/${assignmentId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('homeworkDetailsTitle').textContent = data.assignment.title;
                        document.getElementById('homeworkDetailsDescription').innerHTML = data.assignment.description;
                        document.getElementById('homeworkDetailsDueDate').textContent = data.assignment.due_date ? data
                            .assignment.due_date.format('M d, Y') : '{{ __('No due date') }}';
                        document.getElementById('homeworkDetailsPoints').textContent = data.assignment.max_score ?? '0';
                        document.getElementById('homeworkDetailsType').textContent = data.assignment.type ??
                            '{{ __('Assignment') }}';
                        document.getElementById('homeworkDetailsStatus').textContent = data.assignment.status ??
                            '{{ __('Not Started') }}';
                        document.getElementById('homeworkDetailsId').value = data.assignment.id;

                        // Show grade information if available
                        if (data.assignment.grade) {
                            const gradeInfo = `
                                <div class="alert alert-success mt-3">
                                    <strong>{{ __('Your Grade:') }}</strong>
                                    ${data.assignment.grade.score_earned}/${data.assignment.grade.max_score}
                                    (${data.assignment.grade.percentage_score}%)
                                    ${data.assignment.grade.feedback ? `<br><small class="text-muted">{{ __('Feedback:') }} ${data.assignment.grade.feedback}</small>` : ''}
                                </div>
                            `;
                            document.getElementById('homeworkDetailsDescription').innerHTML += gradeInfo;
                        }
                    } else {
                        showToast('{{ __('Failed to load homework details.') }}', 'danger');
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    showToast('{{ __('Error loading homework details.') }}', 'danger');
                    console.error(error);
                });
        }

        function submitHomeworkAssignment(assignmentId) {
            const courseId = {{ $course->id }};

            // Check if user has already submitted this assignment
            const assignmentCard = document.querySelector(`[onclick="submitHomeworkAssignment(${assignmentId})"]`).closest(
                '.homework-card');
            const submitButton = assignmentCard.querySelector(`[onclick="submitHomeworkAssignment(${assignmentId})"]`);
            const alreadySubmittedButton = assignmentCard.querySelector('.btn-secondary[disabled]');

            if (alreadySubmittedButton) {
                showToast('{{ __('You have already submitted this assignment.') }}', 'warning');
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('submitHomeworkAssignmentModal'));
            modal.show();

            document.getElementById('submitHomeworkAssignmentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('assignment_id', assignmentId);
                fetch(`/courses/${courseId}/submit-homework-assignment`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('{{ __('Homework submitted successfully!') }}', 'success');
                            modal.hide();
                            // Update the UI to show as submitted
                            submitButton.disabled = true;
                            submitButton.innerHTML =
                                '<i class="fa fa-check me-2"></i>{{ __('Already Submitted') }}';
                            submitButton.className = 'btn btn-secondary flex-fill';
                            // Optionally, refresh the page to show updated status
                            location.reload();
                        } else {
                            showToast('{{ __('Failed to submit homework.') }}', 'danger');
                            console.error(data.message);
                        }
                    })
                    .catch(error => {
                        showToast('{{ __('Error submitting homework.') }}', 'danger');
                        console.error(error);
                    });
            });
        }

        function getHomeworkNotified() {
            showToast('{{ __('You will be notified when a homework assignment is posted.') }}', 'info');
        }
    </script>
@endpush
