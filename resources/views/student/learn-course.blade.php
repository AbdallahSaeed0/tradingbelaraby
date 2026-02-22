@extends('layouts.app')

@section('title', $course->localized_name . ' - Learn')

@push('styles')
    @if (\App\Helpers\TranslationHelper::getCurrentLanguage()->direction == 'rtl')
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/course-content.css') }}">
        <link rel="stylesheet" href="{{ asset('css/rtl/pages/student-dashboard.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/pages/course-content.css') }}">
        <link rel="stylesheet" href="{{ asset('css/pages/student-dashboard.css') }}">
    @endif
@endpush

@section('content')
    <!-- Course Header -->
    @include('courses.partials.course-header', ['course' => $course])



    <!-- Course Progress Hero -->
    @include('courses.partials.course-progress', [
        'course' => $course,
        'userEnrollment' => $enrollment,
        'resumeLecture' => $resumeLecture ?? null,
    ])

    <!-- Tabs Section -->
    <section class="course-content-tabs-section py-4">
        <div class="container">
            <ul class="nav nav-tabs course-content-tabs mb-4" id="courseTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                        type="button" role="tab" aria-controls="overview"
                        aria-selected="true">{{ custom_trans('Overview', 'front') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button"
                        role="tab" aria-controls="content" aria-selected="false">{{ custom_trans('Course Content', 'front') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#live" type="button"
                        role="tab" aria-controls="live" aria-selected="false">{{ custom_trans('Live Class', 'front') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="qa-tab" data-bs-toggle="tab" data-bs-target="#qa" type="button"
                        role="tab" aria-controls="qa" aria-selected="false">{{ custom_trans('Q & A', 'front') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="quiz-tab" data-bs-toggle="tab" data-bs-target="#quiz" type="button"
                        role="tab" aria-controls="quiz" aria-selected="false">{{ custom_trans('Quiz', 'front') }}</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="homework-tab" data-bs-toggle="tab" data-bs-target="#homework"
                        type="button" role="tab" aria-controls="homework"
                        aria-selected="false">{{ custom_trans('Homework', 'front') }}</button>
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
                                <h3 class="fw-bold">{{ custom_trans('Live Classes', 'front') }}</h3>
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
                                                    <h5 class="fw-bold mb-0" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>{{ $liveClass->localized_name }}</h5>
                                                    <span
                                                        class="badge bg-{{ $liveClass->status === 'scheduled' ? 'primary' : ($liveClass->status === 'live' ? 'success' : 'secondary') }}">
                                                        {{ ucfirst($liveClass->status) }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mb-3" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>{{ $liveClass->localized_description ?? '' }}</p>

                                                @if ($isRegistered)
                                                    <div class="alert alert-success mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-check-circle me-2"></i>
                                                            <strong>{{ custom_trans('You are registered for this class!', 'front') }}</strong>
                                                        </div>
                                                        @if ($liveClass->link)
                                                            <div class="mt-2">
                                                                <a href="{{ $liveClass->link }}" target="_blank"
                                                                    class="btn btn-success btn-sm">
                                                                    <i
                                                                        class="fa fa-external-link-alt me-1"></i>{{ custom_trans('Join Meeting', 'front') }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="live-class-details mb-3">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ custom_trans('Date', 'front') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $liveClass->scheduled_at ? $liveClass->scheduled_at->format('M d, Y') : custom_trans('TBD', 'front') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ custom_trans('Time', 'front') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $liveClass->scheduled_at ? $liveClass->scheduled_at->format('g:i A') : custom_trans('TBD', 'front') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ custom_trans('Duration', 'front') }}:</small>
                                                            <div class="fw-bold">{{ $liveClass->duration_minutes ?? '60' }}
                                                                {{ custom_trans('minutes', 'front') }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2">
                                                    @if (!$isRegistered)
                                                        <button class="btn btn-outline-primary flex-fill"
                                                            onclick="joinLiveClass({{ $liveClass->id }}, {{ json_encode($liveClass->link ?? '') }})">
                                                            <i class="fa fa-video me-2"></i>{{ custom_trans('Join Live Class', 'front') }}
                                                        </button>
                                                    @else
                                                        <div class="text-center flex-fill">
                                                            <span class="badge bg-success">
                                                                <i class="fa fa-check me-1"></i>{{ custom_trans('Registered', 'front') }}
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if ($liveClass->materials && count($liveClass->materials) > 0)
                                                        <button class="btn btn-outline-secondary"
                                                            onclick="downloadMaterials({{ $liveClass->id }})"
                                                            title="{{ custom_trans('Download Materials', 'front') }}">
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
                                    <h4 class="fw-bold mb-2">{{ custom_trans('No Live Classes Scheduled', 'front') }}</h4>
                                    <p class="text-muted mb-4">{{ custom_trans('Live classes will be scheduled and appear here.', 'front') }}
                                    </p>
                                    <button class="btn btn-orange" onclick="getNotified()">
                                        <i class="fa fa-bell me-2"></i>{{ custom_trans('Get Notified', 'front') }}
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
                                <h3 class="fw-bold">{{ custom_trans('Homework Assignments', 'front') }}</h3>
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
                                                        {{ $hasSubmitted ? ($isGraded ? custom_trans('Graded', 'front') : custom_trans('Submitted', 'front')) : custom_trans('Not Started', 'front') }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mb-3">{{ $assignment->description }}</p>
                                                <div class="homework-details mb-3">
                                                    <div class="row g-2">
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ custom_trans('Due Date', 'front') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $assignment->due_date ? $assignment->due_date->format('M d, Y') : custom_trans('No due date', 'front') }}
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ custom_trans('Points', 'front') }}:</small>
                                                            <div class="fw-bold">{{ $assignment->max_score ?? '0' }}
                                                                {{ custom_trans('points', 'front') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ custom_trans('Type', 'front') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ ucfirst($assignment->type ?? 'Assignment') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <small class="text-muted">{{ custom_trans('Status', 'front') }}:</small>
                                                            <div class="fw-bold">
                                                                {{ $hasSubmitted ? ($isGraded ? custom_trans('Graded', 'front') : custom_trans('Submitted', 'front')) : custom_trans('Not Started', 'front') }}
                                                            </div>
                                                        </div>
                                                        @if ($isGraded)
                                                            <div class="col-12">
                                                                <div class="alert alert-success mb-0">
                                                                    <strong>{{ custom_trans('Your Grade:', 'front') }}</strong>
                                                                    {{ $userSubmission->score_earned }}/{{ $userSubmission->max_score }}
                                                                    ({{ number_format($userSubmission->percentage_score, 1) }}%)
                                                                    @if ($userSubmission->feedback)
                                                                        <br><small
                                                                            class="text-muted">{{ custom_trans('Feedback:', 'front') }}
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
                                                        <i class="fa fa-eye me-2"></i>{{ custom_trans('View Details', 'front') }}
                                                    </button>
                                                    @if (!$hasSubmitted)
                                                        <button class="btn btn-orange flex-fill"
                                                            onclick="submitHomeworkAssignment({{ $assignment->id }})">
                                                            <i class="fa fa-upload me-2"></i>{{ custom_trans('Submit', 'front') }}
                                                        </button>
                                                    @else
                                                        <button class="btn btn-secondary flex-fill" disabled>
                                                            <i class="fa fa-check me-2"></i>{{ custom_trans('Already Submitted', 'front') }}
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
                                    <h4 class="fw-bold mb-2">{{ custom_trans('No Homework Assignments', 'front') }}</h4>
                                    <p class="text-muted mb-4">
                                        {{ custom_trans('Homework assignments will be posted here by your instructor.', 'front') }}</p>
                                    <button class="btn btn-orange" onclick="getHomeworkNotified()">
                                        <i class="fa fa-bell me-2"></i>{{ custom_trans('Get Notified', 'front') }}
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
                    <h5 class="modal-title" id="videoModalLabel">{{ custom_trans('Course Video', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Video Player Container -->
                    <div id="videoPlayerContainer">
                        <!-- HTML5 Video Player for direct video files -->
                        <div class="ratio ratio-16x9 d-none-initially" id="html5VideoContainer">
                            <video controls id="courseVideo">
                                <source src="" type="video/mp4">
                                {{ custom_trans('Your browser does not support the video tag.', 'front') }}
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

                        <!-- Google Drive Embed -->
                        <div class="ratio ratio-16x9 d-none-initially" id="googleDrivePlayer">
                            <iframe id="googleDriveIframe" frameborder="0" allowfullscreen allow="autoplay"></iframe>
                        </div>

                        <!-- File Download for other file types -->
                        <div id="fileDownload" class="file-download-section d-none-initially">
                            <i class="fa fa-file fa-3x text-primary mb-3"></i>
                            <h5>{{ custom_trans('File Available for Download', 'front') }}</h5>
                            <p class="text-muted">{{ custom_trans('This content is available as a downloadable file.', 'front') }}</p>
                            <a id="downloadLink" href="" class="btn btn-primary" target="_blank">
                                <i class="fa fa-download me-2"></i>{{ custom_trans('Download File', 'front') }}
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
                    <h5 class="modal-title" id="addQuestionModalLabel">{{ custom_trans('Add New Question', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addQuestionForm">
                        <div class="mb-3">
                            <label for="questionTitle" class="form-label">{{ custom_trans('Question Title', 'front') }}</label>
                            <input type="text" class="form-control" id="questionTitle"
                                placeholder="{{ custom_trans('Enter your question title', 'front') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="questionContent" class="form-label">{{ custom_trans('Question Details', 'front') }}</label>
                            <div id="questionEditor"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'front') }}</button>
                    <button type="button" class="btn btn-orange"
                        id="submitQuestion">{{ custom_trans('Submit Question', 'front') }}</button>
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
                    <h5 class="modal-title" id="addAnswerModalLabel">{{ custom_trans('Add Answer', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 class="text-muted">{{ custom_trans('Question:', 'front') }}</h6>
                        <p id="answerQuestionTitle" class="fw-bold"></p>
                    </div>
                    <form id="addAnswerForm">
                        <div class="mb-3">
                            <label for="answerContent" class="form-label">{{ custom_trans('Your Answer', 'front') }}</label>
                            <div id="answerEditor"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'front') }}</button>
                    <button type="button" class="btn btn-orange" id="submitAnswer">{{ custom_trans('Submit Answer', 'front') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">{{ custom_trans('Report Content', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm">
                        <div class="mb-3">
                            <label for="reportTitle" class="form-label">{{ custom_trans('Report Title', 'front') }}</label>
                            <input type="text" class="form-control" id="reportTitle"
                                placeholder="{{ custom_trans('Brief description of the issue', 'front') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="reportEmail" class="form-label">{{ custom_trans('Your Email', 'front') }}</label>
                            <input type="email" class="form-control" id="reportEmail"
                                placeholder="your.email@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="reportDetails" class="form-label">{{ custom_trans('Details', 'front') }}</label>
                            <div id="reportEditor"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'front') }}</button>
                    <button type="button" class="btn btn-danger" id="submitReport">{{ custom_trans('Submit Report', 'front') }}</button>
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
                    <h5 class="modal-title" id="scheduleLiveClassModalLabel">{{ custom_trans('Schedule Live Class', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleLiveClassForm">
                        <div class="mb-3">
                            <label for="liveClassTitle" class="form-label">{{ custom_trans('Class Title', 'front') }}</label>
                            <input type="text" class="form-control" id="liveClassTitle" name="title"
                                placeholder="{{ custom_trans('Enter class title', 'front') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="liveClassDescription" class="form-label">{{ custom_trans('Description', 'front') }}</label>
                            <textarea class="form-control" id="liveClassDescription" name="description" rows="3"
                                placeholder="{{ custom_trans('Enter class description', 'front') }}"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassDate" class="form-label">{{ custom_trans('Date', 'front') }}</label>
                                    <input type="date" class="form-control" id="liveClassDate" name="scheduled_date"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassTime" class="form-label">{{ custom_trans('Time', 'front') }}</label>
                                    <input type="time" class="form-control" id="liveClassTime" name="scheduled_time"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassDuration"
                                        class="form-label">{{ custom_trans('Duration (minutes)', 'front') }}</label>
                                    <input type="number" class="form-control" id="liveClassDuration" name="duration"
                                        value="60" min="15" max="180">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="liveClassCapacity"
                                        class="form-label">{{ custom_trans('Max Participants', 'front') }}</label>
                                    <input type="number" class="form-control" id="liveClassCapacity"
                                        name="max_participants" value="50" min="1">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'front') }}</button>
                    <button type="submit" form="scheduleLiveClassForm"
                        class="btn btn-orange">{{ custom_trans('Schedule Class', 'front') }}</button>
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
                    <h5 class="modal-title" id="submitHomeworkModalLabel">{{ custom_trans('Submit Homework', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitHomeworkForm">
                        <div class="mb-3">
                            <label for="homeworkTitle" class="form-label">{{ custom_trans('Homework Title', 'front') }}</label>
                            <input type="text" class="form-control" id="homeworkTitle" name="title"
                                placeholder="{{ custom_trans('Enter homework title', 'front') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="homeworkDescription" class="form-label">{{ custom_trans('Description', 'front') }}</label>
                            <textarea class="form-control" id="homeworkDescription" name="description" rows="3"
                                placeholder="{{ custom_trans('Enter homework description', 'front') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="homeworkFile" class="form-label">{{ custom_trans('Upload File', 'front') }}</label>
                            <input type="file" class="form-control" id="homeworkFile" name="file"
                                accept=".pdf,.doc,.docx,.txt,.zip,.rar">
                            <div class="form-text">{{ custom_trans('Accepted formats: PDF, DOC, DOCX, TXT, ZIP, RAR', 'front') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="homeworkContent" class="form-label">{{ custom_trans('Content', 'front') }}</label>
                            <textarea class="form-control" id="homeworkContent" name="content" rows="5"
                                placeholder="{{ custom_trans('Enter your homework content here...', 'front') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'front') }}</button>
                    <button type="submit" form="submitHomeworkForm"
                        class="btn btn-orange">{{ custom_trans('Submit Homework', 'front') }}</button>
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
                    <h5 class="modal-title" id="viewHomeworkDetailsModalLabel">{{ custom_trans('Homework Details', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="homeworkDetailsId">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ custom_trans('Title', 'front') }}</h6>
                            <p id="homeworkDetailsTitle"></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ custom_trans('Status', 'front') }}</h6>
                            <p id="homeworkDetailsStatus"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ custom_trans('Due Date', 'front') }}</h6>
                            <p id="homeworkDetailsDueDate"></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ custom_trans('Points', 'front') }}</h6>
                            <p id="homeworkDetailsPoints"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">{{ custom_trans('Type', 'front') }}</h6>
                            <p id="homeworkDetailsType"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold">{{ custom_trans('Description', 'front') }}</h6>
                            <div id="homeworkDetailsDescription"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Close', 'front') }}</button>
                    <button type="button" class="btn btn-orange"
                        onclick="submitHomeworkAssignment(document.getElementById('homeworkDetailsId').value)">{{ custom_trans('Submit Assignment', 'front') }}</button>
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
                    <h5 class="modal-title" id="submitHomeworkAssignmentModalLabel">{{ custom_trans('Submit Assignment', 'front') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="submitHomeworkAssignmentForm">
                        <div class="mb-3">
                            <label for="assignmentFile" class="form-label">{{ custom_trans('Upload Assignment File', 'front') }}</label>
                            <input type="file" class="form-control" id="assignmentFile" name="file"
                                accept=".pdf,.doc,.docx,.txt,.zip,.rar" required>
                            <div class="form-text">{{ custom_trans('Accepted formats: PDF, DOC, DOCX, TXT, ZIP, RAR', 'front') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="assignmentContent" class="form-label">{{ custom_trans('Assignment Content', 'front') }}</label>
                            <textarea class="form-control" id="assignmentContent" name="content" rows="5"
                                placeholder="{{ custom_trans('Enter your assignment content here...', 'front') }}"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="assignmentNotes" class="form-label">{{ custom_trans('Notes (Optional)', 'front') }}</label>
                            <textarea class="form-control" id="assignmentNotes" name="notes" rows="3"
                                placeholder="{{ custom_trans('Any additional notes for your instructor...', 'front') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ custom_trans('Cancel', 'front') }}</button>
                    <button type="submit" form="submitHomeworkAssignmentForm"
                        class="btn btn-orange">{{ custom_trans('Submit Assignment', 'front') }}</button>
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
            const isRtl = document.documentElement.getAttribute('dir') === 'rtl';

            // Continue to Lecture modal
            const continueBtn = document.querySelector('.continue-to-lecture-btn');
            const continueModalEl = document.getElementById('continueToLectureModal');
            if (continueBtn && continueModalEl) {
                const data = continueBtn.dataset.resumeLecture;
                if (data) {
                    try {
                        const lecture = JSON.parse(data);
                        continueBtn.addEventListener('click', function() {
                            const titleEl = document.getElementById('continueToLectureTitle');
                            const lang = isRtl ? 'ar' : 'en';
                            titleEl.textContent = (lang === 'ar' ? lecture.title_ar || lecture.title : lecture.title || lecture.title_ar) || '';
                            if (titleEl.getAttribute('dir') !== (lang === 'ar' ? 'rtl' : 'ltr')) {
                                titleEl.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
                            }
                            const modal = new bootstrap.Modal(continueModalEl);
                            modal.show();
                            document.getElementById('continueToLectureGoBtn').onclick = function() {
                                modal.hide();
                                const contentTab = document.getElementById('content-tab');
                                const sectionCollapse = document.getElementById('section' + lecture.section_id);
                                if (contentTab && sectionCollapse) {
                                    bootstrap.Tab.getOrCreateInstance(contentTab).show();
                                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(sectionCollapse);
                                    bsCollapse.show();
                                    setTimeout(function() {
                                        const lectureCard = document.querySelector('[data-lecture="' + lecture.id + '"]');
                                        if (lectureCard) {
                                            lectureCard.closest('.class-card').scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        }
                                    }, 350);
                                }
                            };
                        });
                    } catch (e) {}
                }
            }
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
                        '<i class="fa fa-check me-2"></i>{{ custom_trans('Mark Selected as Complete', 'front') }} (' + totalChecked +
                        ')';
                    markIncompleteBtn.innerHTML =
                        '<i class="fa fa-times me-2"></i>{{ custom_trans('Mark Selected as Incomplete', 'front') }} (' +
                        totalChecked + ')';
                } else {
                    markCompleteBtn.disabled = true;
                    markIncompleteBtn.disabled = true;
                    markCompleteBtn.innerHTML =
                        '<i class="fa fa-check me-2"></i>{{ custom_trans('Mark Selected as Complete', 'front') }}';
                    markIncompleteBtn.innerHTML =
                        '<i class="fa fa-times me-2"></i>{{ custom_trans('Mark Selected as Incomplete', 'front') }}';
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
                                                    '{{ custom_trans('Completed', 'front') }}';
                                            }

                                            // Add completion badge if not exists
                                            const title = lectureCard.querySelector(
                                                '.class-title');
                                            if (title && !title.querySelector('.badge')) {
                                                title.innerHTML +=
                                                    '<span class="badge bg-success ms-2"><i class="fa fa-check"></i> {{ custom_trans('Completed', 'front') }}</span>';
                                            }
                                        }
                                    });
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }

                    // Show success message
                    showToast(`${totalChecked} {{ custom_trans('item(s) marked as complete!', 'front') }}`, 'success');

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
                                                    '{{ custom_trans('Not Completed', 'front') }}';
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
                    showToast(`${totalChecked} {{ custom_trans('item(s) marked as incomplete!', 'front') }}`, 'warning');

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
                        document.getElementById('googleDrivePlayer').style.display = 'none';
                        document.getElementById('fileDownload').style.display = 'none';

                        // Determine video type and show appropriate player
                        if (isYouTubeUrl(videoSrc)) {
                            showYouTubeVideo(videoSrc);
                        } else if (isVimeoUrl(videoSrc)) {
                            showVimeoVideo(videoSrc);
                        } else if (isGoogleDriveUrl(videoSrc)) {
                            showGoogleDriveVideo(videoSrc);
                        } else if (isVideoFile(videoSrc)) {
                            showHTML5Video(videoSrc);
                        } else {
                            showFileDownload(videoSrc);
                        }

                        // Update modal title
                        document.getElementById('videoModalLabel').textContent =
                            '{{ custom_trans('Playing:', 'front') }} ' + lectureTitle;

                        // Show the modal
                        videoModal.show();
                    } else {
                        // Show error message if no video URL
                        showToast('{{ custom_trans('No video available for this lecture', 'front') }}', 'warning');
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

            function isGoogleDriveUrl(url) {
                return url.includes('drive.google.com');
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

            function showGoogleDriveVideo(url) {
                const embedUrl = convertGoogleDriveUrl(url);

                document.getElementById('googleDriveIframe').src = embedUrl;
                document.getElementById('googleDrivePlayer').style.display = 'block';
            }

            function convertGoogleDriveUrl(url) {
                // If already in preview format, return as is
                if (url.includes('/preview')) {
                    return url;
                }

                let fileId = null;

                // Pattern 1: /file/d/{FILE_ID}/
                const pattern1 = /\/file\/d\/([a-zA-Z0-9_-]+)/;
                const match1 = url.match(pattern1);
                if (match1) {
                    fileId = match1[1];
                }

                // Pattern 2: ?id={FILE_ID} or &id={FILE_ID}
                if (!fileId) {
                    const pattern2 = /[?&]id=([a-zA-Z0-9_-]+)/;
                    const match2 = url.match(pattern2);
                    if (match2) {
                        fileId = match2[1];
                    }
                }

                if (fileId) {
                    return `https://drive.google.com/file/d/${fileId}/preview`;
                }

                // Return original URL if pattern not recognized
                return url;
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
                                statusIndicator.title = '{{ custom_trans('Completed', 'front') }}';
                            }

                            // Add completion badge if not exists
                            const title = lectureCard.querySelector('.class-title');
                            if (title && !title.querySelector('.badge')) {
                                title.innerHTML +=
                                    '<span class="badge bg-success ms-2"><i class="fa fa-check"></i> {{ custom_trans('Completed', 'front') }}</span>';
                            }

                            // Update progress bar
                            updateProgressBar(data.progress);

                            // Show success message
                            showToast('{{ custom_trans('Lecture completed automatically!', 'front') }}', 'success');
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
                document.getElementById('googleDriveIframe').src = '';

                // Hide all players
                document.getElementById('html5VideoContainer').style.display = 'none';
                document.getElementById('youtubePlayer').style.display = 'none';
                document.getElementById('vimeoPlayer').style.display = 'none';
                document.getElementById('googleDrivePlayer').style.display = 'none';
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
                alert('{{ custom_trans('Please enter a question title.', 'front') }}');
                return;
            }

            // Show success message
            showToast('{{ custom_trans('Question submitted successfully!', 'front') }}', 'success');

            // Reset form
            document.getElementById('questionTitle').value = '';
            if (questionEditor) questionEditor.setData('');

            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('addQuestionModal')).hide();
        });

        document.getElementById('submitAnswer')?.addEventListener('click', function() {
            const content = answerEditor ? answerEditor.getData() : '';

            if (content.trim() === '') {
                alert('{{ custom_trans('Please enter your answer.', 'front') }}');
                return;
            }

            // Show success message
            showToast('{{ custom_trans('Answer submitted successfully!', 'front') }}', 'success');

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
                alert('{{ custom_trans('Please fill in all required fields.', 'front') }}');
                return;
            }

            // Show success message
            showToast('{{ custom_trans('Report submitted successfully!', 'front') }}', 'success');

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

        function joinLiveClass(liveClassId, meetingLink) {
            const courseId = {{ $course->id }};
            const formData = new FormData();
            formData.append('live_class_id', liveClassId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/courses/${courseId}/join-live-class`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('{{ custom_trans('You have joined the live class!', 'front') }}', 'success');
                    if (meetingLink && meetingLink.trim() !== '') {
                        window.open(meetingLink, '_blank');
                    }
                    location.reload();
                } else {
                    showToast(data.message || '{{ custom_trans('Failed to join live class.', 'front') }}', 'danger');
                    console.error(data.message);
                }
            })
            .catch(error => {
                showToast('{{ custom_trans('Error joining live class.', 'front') }}', 'danger');
                console.error(error);
            });
        }

        function getNotified() {
            showToast('{{ custom_trans('You will be notified when a live class is scheduled.', 'front') }}', 'info');
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
                showToast('{{ custom_trans('Error downloading materials.', 'front') }}', 'danger');
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
                            .assignment.due_date.format('M d, Y') : '{{ custom_trans('No due date', 'front') }}';
                        document.getElementById('homeworkDetailsPoints').textContent = data.assignment.max_score ?? '0';
                        document.getElementById('homeworkDetailsType').textContent = data.assignment.type ??
                            '{{ custom_trans('Assignment', 'front') }}';
                        document.getElementById('homeworkDetailsStatus').textContent = data.assignment.status ??
                            '{{ custom_trans('Not Started', 'front') }}';
                        document.getElementById('homeworkDetailsId').value = data.assignment.id;

                        // Show grade information if available
                        if (data.assignment.grade) {
                            const gradeInfo = `
                                <div class="alert alert-success mt-3">
                                    <strong>{{ custom_trans('Your Grade:', 'front') }}</strong>
                                    ${data.assignment.grade.score_earned}/${data.assignment.grade.max_score}
                                    (${data.assignment.grade.percentage_score}%)
                                    ${data.assignment.grade.feedback ? `<br><small class="text-muted">{{ custom_trans('Feedback:', 'front') }} ${data.assignment.grade.feedback}</small>` : ''}
                                </div>
                            `;
                            document.getElementById('homeworkDetailsDescription').innerHTML += gradeInfo;
                        }
                    } else {
                        showToast('{{ custom_trans('Failed to load homework details.', 'front') }}', 'danger');
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    showToast('{{ custom_trans('Error loading homework details.', 'front') }}', 'danger');
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
                showToast('{{ custom_trans('You have already submitted this assignment.', 'front') }}', 'warning');
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
                            showToast('{{ custom_trans('Homework submitted successfully!', 'front') }}', 'success');
                            modal.hide();
                            // Update the UI to show as submitted
                            submitButton.disabled = true;
                            submitButton.innerHTML =
                                '<i class="fa fa-check me-2"></i>{{ custom_trans('Already Submitted', 'front') }}';
                            submitButton.className = 'btn btn-secondary flex-fill';
                            // Optionally, refresh the page to show updated status
                            location.reload();
                        } else {
                            showToast('{{ custom_trans('Failed to submit homework.', 'front') }}', 'danger');
                            console.error(data.message);
                        }
                    })
                    .catch(error => {
                        showToast('{{ custom_trans('Error submitting homework.', 'front') }}', 'danger');
                        console.error(error);
                    });
            });
        }

        function getHomeworkNotified() {
            showToast('{{ custom_trans('You will be notified when a homework assignment is posted.', 'front') }}', 'info');
        }
    </script>
@endpush
