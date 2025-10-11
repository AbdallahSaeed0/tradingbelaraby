@extends('admin.layout')

@section('title', 'Edit Quiz - ' . $quiz->name)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            color: #6c757d;
            font-weight: 500;
            padding: 1rem 1.5rem;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color: #007bff;
            color: #007bff;
            background: none;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #007bff;
        }

        .tab-content {
            padding: 2rem 0;
        }

        .question-preview {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .option-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background-color: #f8f9fa;
        }

        .option-item.correct {
            border-color: #28a745;
            background-color: #d4edda;
        }

        .question-type-card {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .question-type-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .question-type-card.selected {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .connection-type-selector {
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .connection-type-selector:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .connection-type-selector.selected {
            border-color: #007bff;
            background-color: #e7f3ff;
        }

        .connection-type-selector input[type="radio"] {
            margin-right: 0.5rem;
        }

        .total-marks-display {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .question-counter {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        .question-form {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .question-type-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .type-card {
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .type-card:hover {
            border-color: #007bff;
            background-color: #f8f9ff;
        }

        .type-card.selected {
            border-color: #007bff;
            background-color: #e7f3ff;
        }

        .type-card i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #6c757d;
        }

        .type-card.selected i {
            color: #007bff;
        }

        .question-card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .question-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .question-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .question-body {
            padding: 1rem;
        }

        .question-type-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .points-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
        }

        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
        }

        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
            margin-top: 0.25rem;
        }

        .form-switch .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .form-switch .form-check-input:focus {
            border-color: rgba(40, 167, 69, 0.25);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
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
                        <h1 class="h3 mb-0">Edit Quiz</h1>
                        <p class="text-muted">{{ $quiz->name }}</p>
                    </div>
                    <div>
                        <button type="submit" form="quizForm" class="btn btn-primary me-2"
                            onclick="console.log('Update Quiz button clicked')">
                            <i class="fa fa-save me-2"></i>Update Quiz
                        </button>
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-outline-secondary me-2">
                            <i class="fa fa-eye me-2"></i>View Quiz
                        </a>
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Quizzes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" id="quizForm">
            @csrf
            @method('PUT')
            <input type="hidden" id="is_published" name="is_published" value="{{ $quiz->is_published ? '1' : '0' }}">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="quizTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="quiz-info-tab" data-bs-toggle="tab" data-bs-target="#quiz-info"
                        type="button" role="tab">
                        <i class="fa fa-info-circle me-2"></i>Quiz Info
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions"
                        type="button" role="tab">
                        <i class="fa fa-question-circle me-2"></i>Questions & Statistics
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="quizTabsContent">
                <!-- Quiz Info Tab -->
                <div class="tab-pane fade show active" id="quiz-info" role="tabpanel">
                    <div class="row">
                        <!-- Quiz Details -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa fa-info-circle me-2"></i>Quiz Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Quiz Name (English) *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $quiz->name) }}"
                                                required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="name_ar" class="form-label">Quiz Name (Arabic)</label>
                                            <input type="text"
                                                class="form-control @error('name_ar') is-invalid @enderror" id="name_ar"
                                                name="name_ar" value="{{ old('name_ar', $quiz->name_ar) }}" dir="rtl">
                                            @error('name_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-12">
                                            <label for="course_id" class="form-label">Course *</label>
                                            <select class="form-select @error('course_id') is-invalid @enderror"
                                                id="course_id" name="course_id" required>
                                                <option value="">Select Course</option>
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->id }}"
                                                        {{ old('course_id', $quiz->course_id) == $course->id ? 'selected' : '' }}>
                                                        {{ $course->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('course_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="description" class="form-label">Description (English)</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3">{{ old('description', $quiz->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="description_ar" class="form-label">Description (Arabic)</label>
                                            <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar"
                                                name="description_ar" rows="3" dir="rtl">{{ old('description_ar', $quiz->description_ar) }}</textarea>
                                            @error('description_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="instructions" class="form-label">Instructions (English)</label>
                                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions"
                                                rows="3">{{ old('instructions', $quiz->instructions) }}</textarea>
                                            @error('instructions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="instructions_ar" class="form-label">Instructions (Arabic)</label>
                                            <textarea class="form-control @error('instructions_ar') is-invalid @enderror" id="instructions_ar"
                                                name="instructions_ar" rows="3" dir="rtl">{{ old('instructions_ar', $quiz->instructions_ar) }}</textarea>
                                            @error('instructions_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Connection -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa fa-link me-2"></i>Quiz Connection</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-3">Choose how this quiz connects to your course content:</p>

                                    @php
                                        $connectionType = 'course';
                                        if ($quiz->section_id) {
                                            $connectionType = 'section';
                                        } elseif ($quiz->lecture_id) {
                                            $connectionType = 'lecture';
                                        }
                                    @endphp

                                    <div class="connection-type-selector" onclick="selectConnectionType('course', event)">
                                        <input type="radio" name="connection_type" id="connection_course"
                                            value="course" {{ $connectionType === 'course' ? 'checked' : '' }}>
                                        <label for="connection_course" class="fw-bold">Course Level</label>
                                        <p class="text-muted mb-0">Quiz available for the entire course</p>
                                    </div>

                                    <div class="connection-type-selector"
                                        onclick="selectConnectionType('section', event)">
                                        <input type="radio" name="connection_type" id="connection_section"
                                            value="section" {{ $connectionType === 'section' ? 'checked' : '' }}>
                                        <label for="connection_section" class="fw-bold">Section Level</label>
                                        <p class="text-muted mb-0">Quiz available after completing a specific section</p>
                                        <div id="section_selection" class="mt-3"
                                            style="display: {{ $connectionType === 'section' ? 'block' : 'none' }};"
                                            onclick="event.stopPropagation()">
                                            <select class="form-select" id="section_id" name="section_id">
                                                <option value="">Select Section</option>
                                                @if ($quiz->section)
                                                    <option value="{{ $quiz->section->id }}" selected>
                                                        {{ $quiz->section->title }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="connection-type-selector"
                                        onclick="selectConnectionType('lecture', event)">
                                        <input type="radio" name="connection_type" id="connection_lecture"
                                            value="lecture" {{ $connectionType === 'lecture' ? 'checked' : '' }}>
                                        <label for="connection_lecture" class="fw-bold">Lecture Level</label>
                                        <p class="text-muted mb-0">Quiz available after completing a specific lecture</p>
                                        <div id="lecture_selection" class="mt-3"
                                            style="display: {{ $connectionType === 'lecture' ? 'block' : 'none' }};"
                                            onclick="event.stopPropagation()">
                                            <select class="form-select" id="lecture_id" name="lecture_id">
                                                <option value="">Select Lecture</option>
                                                @if ($quiz->lecture)
                                                    <option value="{{ $quiz->lecture->id }}" selected>
                                                        {{ $quiz->lecture->section->title }} - {{ $quiz->lecture->title }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa fa-cog me-2"></i>Quiz Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="time_limit_minutes" class="form-label">Time Limit
                                                (minutes)</label>
                                            <input type="number"
                                                class="form-control @error('time_limit_minutes') is-invalid @enderror"
                                                id="time_limit_minutes" name="time_limit_minutes"
                                                value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}"
                                                min="1" placeholder="No limit">
                                            @error('time_limit_minutes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Leave empty for no time limit</small>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="passing_score" class="form-label">Passing Score (%) *</label>
                                            <input type="number"
                                                class="form-control @error('passing_score') is-invalid @enderror"
                                                id="passing_score" name="passing_score"
                                                value="{{ old('passing_score', $quiz->passing_score) }}" min="0"
                                                max="100" required>
                                            @error('passing_score')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="available_from" class="form-label">Available From</label>
                                            <input type="datetime-local"
                                                class="form-control @error('available_from') is-invalid @enderror"
                                                id="available_from" name="available_from"
                                                value="{{ old('available_from', $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}">
                                            @error('available_from')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="available_until" class="form-label">Available Until</label>
                                            <input type="datetime-local"
                                                class="form-control @error('available_until') is-invalid @enderror"
                                                id="available_until" name="available_until"
                                                value="{{ old('available_until', $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}">
                                            @error('available_until')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_randomized"
                                                    name="is_randomized" value="1"
                                                    {{ old('is_randomized', $quiz->is_randomized) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_randomized">
                                                    Randomize Questions
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="show_results_immediately" name="show_results_immediately"
                                                    value="1"
                                                    {{ old('show_results_immediately', $quiz->show_results_immediately) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="show_results_immediately">
                                                    Show Results Immediately
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="publish_toggle"
                                                    {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}
                                                    onchange="togglePublishStatus()">
                                                <label class="form-check-label" for="publish_toggle">
                                                    <strong>Publish Quiz</strong>
                                                </label>
                                                <small class="form-text text-muted d-block">
                                                    {{ $quiz->is_published ? 'Currently published' : 'Currently in draft mode' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Quiz Preview -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa fa-eye me-2"></i>Quiz Preview</h5>
                                </div>
                                <div class="card-body">
                                    <div id="quizPreview">
                                        <div class="quiz-preview-content">
                                            <h6 class="fw-bold">{{ $quiz->name }}</h6>
                                            @if ($quiz->description)
                                                <p class="text-muted small">{{ $quiz->description }}</p>
                                            @endif
                                            <div class="row g-2 mt-3">
                                                <div class="col-6">
                                                    <small class="text-muted">Status:</small><br>
                                                    <span
                                                        class="badge bg-{{ $quiz->is_published ? 'success' : 'warning' }}">
                                                        <i
                                                            class="fa fa-{{ $quiz->is_published ? 'check' : 'clock' }} me-1"></i>
                                                        {{ $quiz->is_published ? 'Published' : 'Draft' }}
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Time Limit:</small><br>
                                                    <span
                                                        class="text-muted">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' min' : 'No limit' }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Passing Score:</small><br>
                                                    <span class="text-muted">{{ $quiz->passing_score }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Questions Tab -->
                <div class="tab-pane fade" id="questions" role="tabpanel">
                    <div class="row">
                        <!-- Questions Management -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fa fa-question-circle me-2"></i>Questions Management</h5>
                                    <button type="button" class="btn btn-primary btn-sm" onclick="showQuestionForm()">
                                        <i class="fa fa-plus me-2"></i>Add Question
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Question Form Container -->
                                    <div id="questionFormContainer" class="question-form" style="display: none;">
                                        <h6 class="mb-3">Add New Question</h6>

                                        <!-- Question Type Selection -->
                                        <div class="question-type-selector">
                                            <div class="type-card" onclick="selectQuestionType('multiple_choice', event)">
                                                <i class="fa fa-list-ul"></i>
                                                <div><strong>Multiple Choice</strong></div>
                                                <small class="text-muted">Choose one correct answer</small>
                                            </div>
                                            <div class="type-card" onclick="selectQuestionType('true_false', event)">
                                                <i class="fa fa-toggle-on"></i>
                                                <div><strong>True/False</strong></div>
                                                <small class="text-muted">True or false statement</small>
                                            </div>
                                            <div class="type-card" onclick="selectQuestionType('fill_blank', event)">
                                                <i class="fa fa-pencil"></i>
                                                <div><strong>Fill in the Blank</strong></div>
                                                <small class="text-muted">Text input answer</small>
                                            </div>
                                            <div class="type-card" onclick="selectQuestionType('essay', event)">
                                                <i class="fa fa-file-text"></i>
                                                <div><strong>Essay</strong></div>
                                                <small class="text-muted">Long text answer</small>
                                            </div>
                                        </div>

                                        <!-- Question form will be shown here -->
                                        <div id="questionFormContent"></div>
                                    </div>

                                    <!-- Questions List -->
                                    <div id="questionsList">
                                        @forelse($quiz->questions as $question)
                                            <div class="question-card" data-question-id="{{ $question->id }}"
                                                data-question-type="{{ $question->question_type }}">
                                                <div
                                                    class="question-header d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span
                                                            class="badge question-type-badge bg-primary me-2">{{ $question->formatted_question_type }}</span>
                                                        <span class="badge points-badge">{{ $question->points }}
                                                            pts</span>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="editQuestion({{ $question->id }})">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="deleteQuestion({{ $question->id }})">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="question-body">
                                                    <h6>{{ $question->question_text }}</h6>

                                                    @if ($question->question_type === 'multiple_choice' && $question->options)
                                                        <div class="mt-3">
                                                            @foreach ($question->options as $index => $option)
                                                                <div
                                                                    class="option-item {{ in_array($index, $question->correct_answers ?? []) ? 'correct' : '' }}">
                                                                    <i
                                                                        class="fa fa-{{ in_array($index, $question->correct_answers ?? []) ? 'check-circle text-success' : 'circle text-muted' }} me-2"></i>
                                                                    {{ $option }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @elseif($question->question_type === 'true_false')
                                                        <div class="mt-3">
                                                            <div
                                                                class="option-item {{ $question->correct_answer_boolean ? 'correct' : '' }}">
                                                                <i
                                                                    class="fa fa-{{ $question->correct_answer_boolean ? 'check-circle text-success' : 'circle text-muted' }} me-2"></i>
                                                                True
                                                            </div>
                                                            <div
                                                                class="option-item {{ !$question->correct_answer_boolean ? 'correct' : '' }}">
                                                                <i
                                                                    class="fa fa-{{ !$question->correct_answer_boolean ? 'check-circle text-success' : 'circle text-muted' }} me-2"></i>
                                                                False
                                                            </div>
                                                        </div>
                                                    @elseif($question->question_type === 'fill_blank' && $question->correct_answers_text)
                                                        <div class="mt-3">
                                                            <p><strong>Correct Answers:</strong></p>
                                                            @foreach ($question->correct_answers_text as $answer)
                                                                <span
                                                                    class="badge bg-success me-1">{{ $answer }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    @if ($question->explanation)
                                                        <div class="mt-3">
                                                            <small class="text-muted"><strong>Explanation:</strong>
                                                                {{ $question->explanation }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4">
                                                <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No questions added yet. Click "Add Question" to get
                                                    started.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Statistics -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa fa-chart-bar me-2"></i>Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="stats-card mb-3">
                                        <h3>{{ $quiz->questions->count() }}</h3>
                                        <p class="mb-0">Total Questions</p>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" data-stat-type="multiple_choice">
                                                    {{ $quiz->questions->where('question_type', 'multiple_choice')->count() }}
                                                </h4>
                                                <small class="text-muted">Multiple Choice</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" data-stat-type="true_false">
                                                    {{ $quiz->questions->where('question_type', 'true_false')->count() }}
                                                </h4>
                                                <small class="text-muted">True/False</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" data-stat-type="fill_blank">
                                                    {{ $quiz->questions->where('question_type', 'fill_blank')->count() }}
                                                </h4>
                                                <small class="text-muted">Fill in Blank</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" data-stat-type="essay">
                                                    {{ $quiz->questions->where('question_type', 'essay')->count() }}</h4>
                                                <small class="text-muted">Essay</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Marks -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa fa-star me-2"></i>Total Marks</h5>
                                </div>
                                <div class="card-body">
                                    <div class="total-marks-display">
                                        <h3>{{ $quiz->questions->sum('points') }}</h3>
                                        <p class="mb-0">Total Points</p>
                                    </div>
                                    <div class="question-counter mt-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Questions:</span>
                                            <span>{{ $quiz->questions->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Question Form (Outside main form to prevent conflicts) -->
        <form id="addQuestionForm" onsubmit="return false;" style="display: none;">
            <input type="hidden" id="questionType" name="question_type">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="questionText" class="form-label">Question Text (English) *</label>
                    <textarea class="form-control" id="questionText" name="question_text" rows="3"></textarea>
                </div>

                <div class="col-md-6">
                    <label for="questionTextAr" class="form-label">Question Text (Arabic)</label>
                    <textarea class="form-control" id="questionTextAr" name="question_text_ar" rows="3" dir="rtl"></textarea>
                </div>

                <div class="col-md-6">
                    <label for="questionPoints" class="form-label">Points *</label>
                    <input type="number" class="form-control" id="questionPoints" name="points" min="1"
                        value="1">
                </div>

                <div class="col-md-6">
                    <label for="questionOrder" class="form-label">Order</label>
                    <input type="number" class="form-control" id="questionOrder" name="order" min="1"
                        value="{{ $quiz->questions->count() + 1 }}">
                </div>

                <!-- Multiple Choice Options -->
                <div id="multipleChoiceOptions" class="col-12" style="display: none;">
                    <label class="form-label">Options *</label>
                    <div id="optionsContainer">
                        <div class="option-item mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <input type="radio" name="correct_answers[]" value="0" class="me-2">
                                <label class="form-label mb-0 me-2"><strong>Option 1</strong></label>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="options[]"
                                        placeholder="Option 1 (English)">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="options_ar[]"
                                        placeholder="الخيار 1 (عربي)" dir="rtl">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100"
                                        onclick="removeOption(this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="option-item mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <input type="radio" name="correct_answers[]" value="1" class="me-2">
                                <label class="form-label mb-0 me-2"><strong>Option 2</strong></label>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="options[]"
                                        placeholder="Option 2 (English)">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="options_ar[]"
                                        placeholder="الخيار 2 (عربي)" dir="rtl">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100"
                                        onclick="removeOption(this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addOption()">
                        <i class="fa fa-plus me-1"></i>Add Option
                    </button>
                </div>

                <!-- True/False Options -->
                <div id="trueFalseOptions" class="col-12" style="display: none;">
                    <label class="form-label">Correct Answer *</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="correct_answer_boolean" id="true"
                            value="1">
                        <label class="form-check-label" for="true">True</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="correct_answer_boolean" id="false"
                            value="0">
                        <label class="form-check-label" for="false">False</label>
                    </div>
                </div>

                <!-- Fill in the Blank Options -->
                <div id="fillBlankOptions" class="col-12" style="display: none;">
                    <label class="form-label">Correct Answers *</label>
                    <div id="fillBlankContainer">
                        <div class="fill-blank-answer mb-3">
                            <label class="form-label mb-2"><strong>Answer 1</strong></label>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="correct_answers_text[]"
                                        placeholder="Correct answer 1 (English)">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="correct_answers_text_ar[]"
                                        placeholder="الإجابة الصحيحة 1 (عربي)" dir="rtl">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100"
                                        onclick="removeFillBlank(this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addFillBlank()">
                        <i class="fa fa-plus me-1"></i>Add Answer
                    </button>
                </div>

                <div class="col-md-6">
                    <label for="explanation" class="form-label">Explanation (English)</label>
                    <textarea class="form-control" id="explanation" name="explanation" rows="2"></textarea>
                </div>

                <div class="col-md-6">
                    <label for="explanationAr" class="form-label">Explanation (Arabic)</label>
                    <textarea class="form-control" id="explanationAr" name="explanation_ar" rows="2" dir="rtl"></textarea>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="submitQuestion()">
                            <i class="fa fa-save me-2"></i>Add Question
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="hideQuestionForm()">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let optionCount = 2;
        let fillBlankCount = 1;

        // Initialize date pickers
        flatpickr("#available_from", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        flatpickr("#available_until", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });

        // Connection type selection
        function selectConnectionType(type, event = null) {
            // Prevent default action and stop propagation
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Remove selected class from all selectors
            document.querySelectorAll('.connection-type-selector').forEach(selector => {
                selector.classList.remove('selected');
            });

            // Add selected class to clicked selector (only if event exists)
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('selected');
            } else {
                // Find the selector for this type and add selected class
                const radioButton = document.getElementById('connection_' + type);
                if (radioButton) {
                    const selector = radioButton.closest('.connection-type-selector');
                    if (selector) {
                        selector.classList.add('selected');
                    }
                }
            }

            // Check the radio button
            document.getElementById('connection_' + type).checked = true;

            // Show/hide selection dropdowns
            document.getElementById('section_selection').style.display = type === 'section' ? 'block' : 'none';
            document.getElementById('lecture_selection').style.display = type === 'lecture' ? 'block' : 'none';

            // Load sections/lectures if course is selected
            const courseId = document.getElementById('course_id').value;
            if (courseId) {
                if (type === 'section') {
                    loadSections(courseId);
                } else if (type === 'lecture') {
                    loadLectures(courseId);
                }
            }
        }

        // Load sections for selected course
        function loadSections(courseId) {
            const select = document.getElementById('section_id');
            const currentValue = select.value; // Save current selection

            fetch(`/admin/quizzes/get-sections/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">Select Section</option>';

                    data.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.title;
                        // Restore previous selection if it exists
                        if (section.id == currentValue) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading sections:', error);
                });
        }

        // Load lectures for selected course
        function loadLectures(courseId) {
            const select = document.getElementById('lecture_id');
            const currentValue = select.value; // Save current selection

            fetch(`/admin/quizzes/get-lectures/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">Select Lecture</option>';

                    data.forEach(lecture => {
                        const option = document.createElement('option');
                        option.value = lecture.id;
                        option.textContent = `${lecture.section_title} - ${lecture.title}`;
                        // Restore previous selection if it exists
                        if (lecture.id == currentValue) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading lectures:', error);
                });
        }

        // Course change handler
        document.getElementById('course_id').addEventListener('change', function() {
            const courseId = this.value;
            const connectionType = document.querySelector('input[name="connection_type"]:checked').value;

            if (courseId) {
                if (connectionType === 'section') {
                    loadSections(courseId);
                } else if (connectionType === 'lecture') {
                    loadLectures(courseId);
                }
            }
        });

        // Quiz preview functionality
        function updateQuizPreview() {
            const name = document.getElementById('name').value;
            const description = document.getElementById('description').value;
            const timeLimit = document.getElementById('time_limit_minutes').value;
            const passingScore = document.getElementById('passing_score').value;
            const isPublished = document.getElementById('publish_toggle').checked;

            const preview = document.getElementById('quizPreview');

            if (name) {
                preview.innerHTML = `
                    <div class="quiz-preview-content">
                        <h6 class="fw-bold">${name}</h6>
                        ${description ? `<p class="text-muted small">${description}</p>` : ''}
                        <div class="row g-2 mt-3">
                            <div class="col-6">
                                <small class="text-muted">Status:</small><br>
                                <span class="badge bg-${isPublished ? 'success' : 'warning'}">
                                    <i class="fa fa-${isPublished ? 'check' : 'clock'} me-1"></i>
                                    ${isPublished ? 'Published' : 'Draft'}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Time Limit:</small><br>
                                <span class="text-muted">${timeLimit ? timeLimit + ' min' : 'No limit'}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Passing Score:</small><br>
                                <span class="text-muted">${passingScore}%</span>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        // Save as draft functionality
        function saveAsDraft() {
            document.getElementById('is_published').value = '0';
            document.getElementById('quizForm').submit();
        }

        // Toggle publish status
        function togglePublishStatus() {
            const toggle = document.getElementById('publish_toggle');
            const hiddenField = document.getElementById('is_published');
            const statusText = toggle.parentElement.querySelector('small');

            if (toggle.checked) {
                hiddenField.value = '1';
                statusText.textContent = 'Currently published';
            } else {
                hiddenField.value = '0';
                statusText.textContent = 'Currently in draft mode';
            }

            // Update the preview
            updateQuizPreview();
        }

        // Question management functions
        function showQuestionForm() {
            console.log('showQuestionForm called');

            const container = document.getElementById('questionFormContainer');
            const content = document.getElementById('questionFormContent');
            const form = document.getElementById('addQuestionForm');

            console.log('Elements found:', {
                container: !!container,
                content: !!content,
                form: !!form
            });

            if (!container || !content || !form) {
                console.error('Required elements not found');
                return;
            }

            // Show the original form instead of cloning
            form.style.display = 'block';
            form.id = 'addQuestionFormClone'; // Temporarily change ID for consistency

            // Clear the content area and add the form
            content.innerHTML = '';
            content.appendChild(form);

            // Verify the form has the required elements
            console.log('Form elements:', {
                questionText: !!form.querySelector('#questionText'),
                questionPoints: !!form.querySelector('#questionPoints'),
                questionOrder: !!form.querySelector('#questionOrder'),
                explanation: !!form.querySelector('#explanation'),
                questionType: !!form.querySelector('#questionType')
            });

            container.style.display = 'block';
            container.scrollIntoView({
                behavior: 'smooth'
            });

            console.log('Question form shown successfully');
        }

        function hideQuestionForm() {
            const container = document.getElementById('questionFormContainer');
            const content = document.getElementById('questionFormContent');
            const form = document.getElementById('addQuestionFormClone');

            // Hide the form and restore original ID
            if (form) {
                form.style.display = 'none';
                form.id = 'addQuestionForm';
            }

            content.innerHTML = '';
            container.style.display = 'none';
            resetQuestionForm();
        }

        function updateQuestionInList(question) {
            // Find the existing question card
            const existingCard = document.querySelector(`[data-question-id="${question.id}"]`);
            if (existingCard) {
                // Remove the existing card
                existingCard.remove();
            }

            // Add the updated question to the list
            addQuestionToList(question);
        }

        function selectQuestionType(type, event = null) {
            // Remove selected class from all type cards
            document.querySelectorAll('.type-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card (only if event exists)
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('selected');
            } else {
                // Find the card for this type and add selected class
                const cards = document.querySelectorAll('.type-card');
                cards.forEach(card => {
                    if (card.textContent.toLowerCase().includes(type.replace('_', ' '))) {
                        card.classList.add('selected');
                    }
                });
            }

            // Try to get the cloned form first, fallback to original
            let form = document.getElementById('addQuestionFormClone');
            if (!form) {
                form = document.getElementById('addQuestionForm');
            }

            // Set the question type
            form.querySelector('#questionType').value = type;

            // Hide all option sections
            form.querySelector('#multipleChoiceOptions').style.display = 'none';
            form.querySelector('#trueFalseOptions').style.display = 'none';
            form.querySelector('#fillBlankOptions').style.display = 'none';

            // Show relevant option section
            if (type === 'multiple_choice') {
                form.querySelector('#multipleChoiceOptions').style.display = 'block';
            } else if (type === 'true_false') {
                form.querySelector('#trueFalseOptions').style.display = 'block';
            } else if (type === 'fill_blank') {
                form.querySelector('#fillBlankOptions').style.display = 'block';
            }
        }

        function addOption() {
            // Try to get the cloned form first, fallback to original
            let form = document.getElementById('addQuestionFormClone');
            if (!form) {
                form = document.getElementById('addQuestionForm');
            }

            const container = form.querySelector('#optionsContainer');
            const newOption = document.createElement('div');
            newOption.className = 'option-item mb-3';
            newOption.innerHTML = `
                <div class="d-flex align-items-center mb-2">
                    <input type="radio" name="correct_answers[]" value="${optionCount}" class="me-2">
                    <label class="form-label mb-0 me-2"><strong>Option ${optionCount + 1}</strong></label>
                </div>
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount + 1} (English)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="options_ar[]" placeholder="الخيار ${optionCount + 1} (عربي)" dir="rtl">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeOption(this)">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newOption);
            optionCount++;
        }

        function removeOption(button) {
            if (document.querySelectorAll('.option-item').length > 2) {
                button.closest('.option-item').remove();
            }
        }

        function addFillBlank() {
            // Try to get the cloned form first, fallback to original
            let form = document.getElementById('addQuestionFormClone');
            if (!form) {
                form = document.getElementById('addQuestionForm');
            }

            const container = form.querySelector('#fillBlankContainer');
            const newAnswer = document.createElement('div');
            newAnswer.className = 'fill-blank-answer mb-3';
            newAnswer.innerHTML = `
                <label class="form-label mb-2"><strong>Answer ${fillBlankCount + 1}</strong></label>
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="correct_answers_text[]" placeholder="Correct answer ${fillBlankCount + 1} (English)" required>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="correct_answers_text_ar[]" placeholder="الإجابة الصحيحة ${fillBlankCount + 1} (عربي)" dir="rtl">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeFillBlank(this)">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newAnswer);
            fillBlankCount++;
        }

        function removeFillBlank(button) {
            if (document.querySelectorAll('#fillBlankContainer .fill-blank-answer').length > 1) {
                button.closest('.fill-blank-answer').remove();
            }
        }

        function resetQuestionForm() {
            // Try to get the cloned form first, fallback to original
            let form = document.getElementById('addQuestionFormClone');
            if (!form) {
                form = document.getElementById('addQuestionForm');
            }

            if (form) {
                form.reset();
                // Clear edit state
                form.removeAttribute('data-edit-question-id');
            }

            // Remove selected class from type cards
            document.querySelectorAll('.type-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Reset counters
            optionCount = 2;
            fillBlankCount = 1;
        }

        // Handle question form submission
        function submitQuestion() {
            console.log('submitQuestion called');

            // Try to get the cloned form first, fallback to original
            let form = document.getElementById('addQuestionFormClone');
            if (!form) {
                form = document.getElementById('addQuestionForm');
            }

            const formData = new FormData(form);
            formData.append('quiz_id', {{ $quiz->id }});

            // Debug: Log form data
            console.log('Form data prepared');
            console.log('Form elements before submission:');
            console.log('Question text:', form.querySelector('#questionText')?.value);
            console.log('Question type:', form.querySelector('#questionType')?.value);
            console.log('Points:', form.querySelector('#questionPoints')?.value);
            console.log('Order:', form.querySelector('#questionOrder')?.value);
            console.log('Explanation:', form.querySelector('#explanation')?.value);

            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            // Check if this is an edit or new question
            const editQuestionId = form.getAttribute('data-edit-question-id');
            const isEdit = editQuestionId !== null;

            // Validate required fields - use the form we found
            const questionText = form.querySelector('#questionText').value.trim();
            const questionType = form.querySelector('#questionType').value;
            const points = form.querySelector('#questionPoints').value;

            console.log('Form values:', {
                questionText,
                questionType,
                points,
                isEdit,
                editQuestionId
            });

            if (!questionText) {
                alert('Please enter question text');
                return;
            }

            if (!questionType) {
                alert('Please select a question type');
                return;
            }

            if (!points || points < 1) {
                alert('Please enter valid points');
                return;
            }

            // Validate question type specific fields
            if (questionType === 'multiple_choice') {
                const options = formData.getAll('options[]');
                const correctAnswers = formData.getAll('correct_answers[]');

                if (options.length < 2) {
                    alert('Please add at least 2 options');
                    return;
                }

                if (options.some(option => !option.trim())) {
                    alert('Please fill in all options');
                    return;
                }

                if (correctAnswers.length === 0) {
                    alert('Please select a correct answer');
                    return;
                }
            } else if (questionType === 'true_false') {
                const correctAnswer = formData.get('correct_answer_boolean');
                if (correctAnswer === null) {
                    alert('Please select a correct answer');
                    return;
                }
            } else if (questionType === 'fill_blank') {
                const answers = formData.getAll('correct_answers_text[]');
                if (answers.length === 0 || answers.some(answer => !answer.trim())) {
                    alert('Please add at least one correct answer');
                    return;
                }
            } else if (questionType === 'essay') {
                // Essay questions don't need additional validation for correct answers
                // They are manually graded
            }

            // Show loading state - find the submit button in the form we're using
            const submitBtn = form.querySelector('button[type="button"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `<i class="fa fa-spinner fa-spin me-2"></i>${isEdit ? 'Updating...' : 'Adding...'}`;
            submitBtn.disabled = true;

            // Determine the endpoint and method
            const url = isEdit ?
                `{{ route('admin.quizzes.questions.update', [$quiz, ':id']) }}`.replace(':id', editQuestionId) :
                '{{ route('admin.quizzes.questions.store', $quiz) }}';

            const method = isEdit ? 'PUT' : 'POST';

            fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Server error');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Hide the question form
                        hideQuestionForm();

                        if (isEdit) {
                            // Update existing question in the list
                            updateQuestionInList(data.question);
                        } else {
                            // Add the new question to the list dynamically
                            addQuestionToList(data.question);
                        }

                        // Update statistics
                        updateStatistics();

                        // Show success message
                        toastr.success(`Question ${isEdit ? 'updated' : 'added'} successfully!`);
                    } else {
                        toastr.error(`Error ${isEdit ? 'updating' : 'adding'} question: ` + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error adding question: ' + error.message);
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        function deleteQuestion(questionId) {
            if (confirm('Are you sure you want to delete this question?')) {
                fetch(`{{ route('admin.quizzes.questions.destroy', [$quiz, ':id']) }}`.replace(':id', questionId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the question card from the list
                            const questionCard = document.querySelector(`[data-question-id="${questionId}"]`);
                            if (questionCard) {
                                questionCard.remove();
                            }

                            // Update statistics
                            updateStatistics();

                            // Show success message
                            toastr.success('Question deleted successfully!');
                        } else {
                            toastr.error('Error deleting question: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Error deleting question');
                    });
            }
        }

        function editQuestion(questionId) {
            console.log('editQuestion called with ID:', questionId);

            // Test if form exists before making the request
            const testForm = document.getElementById('addQuestionForm');
            console.log('Test form exists:', !!testForm);

            // Get the question data and populate the form for editing
            fetch(`{{ route('admin.quizzes.questions.show', [$quiz, ':id']) }}`.replace(':id', questionId), {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Question data received:', data);
                    if (data.success) {
                        showQuestionForm();
                        // Add a small delay to ensure the form is fully rendered
                        setTimeout(() => {
                            populateQuestionForm(data.question, questionId);
                        }, 100);
                    } else {
                        alert('Error loading question: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading question. Please try again.');
                });
        }

        function populateQuestionForm(question, questionId) {
            console.log('populateQuestionForm called with:', {
                question,
                questionId
            });

            // Try to get the cloned form first, fallback to original
            let form = document.getElementById('addQuestionFormClone');
            if (!form) {
                form = document.getElementById('addQuestionForm');
            }

            console.log('Form found:', !!form);

            if (!form) {
                console.error('No form found!');
                return;
            }

            try {
                // Set form values
                const questionTextEl = form.querySelector('#questionText');
                const questionTextArEl = form.querySelector('#questionTextAr');
                const questionPointsEl = form.querySelector('#questionPoints');
                const questionOrderEl = form.querySelector('#questionOrder');
                const explanationEl = form.querySelector('#explanation');
                const explanationArEl = form.querySelector('#explanationAr');

                console.log('Form elements found:', {
                    questionText: !!questionTextEl,
                    questionTextAr: !!questionTextArEl,
                    questionPoints: !!questionPointsEl,
                    questionOrder: !!questionOrderEl,
                    explanation: !!explanationEl,
                    explanationAr: !!explanationArEl
                });

                if (questionTextEl) {
                    questionTextEl.value = question.question_text;
                    console.log('Set question text:', question.question_text);
                } else {
                    console.error('Question text element not found!');
                }
                if (questionTextArEl) {
                    questionTextArEl.value = question.question_text_ar || '';
                    console.log('Set question text (Arabic):', question.question_text_ar || '');
                } else {
                    console.error('Question text (Arabic) element not found!');
                }
                if (questionPointsEl) {
                    questionPointsEl.value = question.points;
                    console.log('Set points:', question.points);
                } else {
                    console.error('Question points element not found!');
                }
                if (questionOrderEl) {
                    questionOrderEl.value = question.order;
                    console.log('Set order:', question.order);
                } else {
                    console.error('Question order element not found!');
                }
                if (explanationEl) {
                    explanationEl.value = question.explanation || '';
                    console.log('Set explanation:', question.explanation || '');
                } else {
                    console.error('Explanation element not found!');
                }
                if (explanationArEl) {
                    explanationArEl.value = question.explanation_ar || '';
                    console.log('Set explanation (Arabic):', question.explanation_ar || '');
                } else {
                    console.error('Explanation (Arabic) element not found!');
                }

                // Set question type and show relevant options
                const questionTypeEl = form.querySelector('#questionType');
                if (questionTypeEl) questionTypeEl.value = question.question_type;
                selectQuestionType(question.question_type);

                // Populate options based on question type
                if (question.question_type === 'multiple_choice' && question.options) {
                    const container = form.querySelector('#optionsContainer');
                    if (container) {
                        container.innerHTML = '';

                        question.options.forEach((option, index) => {
                            const isCorrect = question.correct_answers && question.correct_answers.includes(index);
                            const optionAr = question.options_ar && question.options_ar[index] ? question
                                .options_ar[index] : '';
                            const optionDiv = document.createElement('div');
                            optionDiv.className = 'option-item mb-3';
                            optionDiv.innerHTML = `
                                <div class="d-flex align-items-center mb-2">
                                    <input type="radio" name="correct_answers[]" value="${index}" class="me-2" ${isCorrect ? 'checked' : ''}>
                                    <label class="form-label mb-0 me-2"><strong>Option ${index + 1}</strong></label>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="options[]" placeholder="Option ${index + 1} (English)" value="${option}" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="options_ar[]" placeholder="الخيار ${index + 1} (عربي)" value="${optionAr}" dir="rtl">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeOption(this)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                            container.appendChild(optionDiv);
                        });
                        optionCount = question.options.length;
                    }
                } else if (question.question_type === 'true_false') {
                    const correctAnswerEl = form.querySelector(
                        `input[name="correct_answer_boolean"][value="${question.correct_answer_boolean ? '1' : '0'}"]`);
                    if (correctAnswerEl) correctAnswerEl.checked = true;
                } else if (question.question_type === 'fill_blank' && question.correct_answers_text) {
                    const container = form.querySelector('#fillBlankContainer');
                    if (container) {
                        container.innerHTML = '';

                        question.correct_answers_text.forEach((answer, index) => {
                            const answerAr = question.correct_answers_text_ar && question.correct_answers_text_ar[
                                index] ? question.correct_answers_text_ar[index] : '';
                            const answerDiv = document.createElement('div');
                            answerDiv.className = 'fill-blank-answer mb-3';
                            answerDiv.innerHTML = `
                                <label class="form-label mb-2"><strong>Answer ${index + 1}</strong></label>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="correct_answers_text[]" placeholder="Correct answer ${index + 1} (English)" value="${answer}" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="correct_answers_text_ar[]" placeholder="الإجابة الصحيحة ${index + 1} (عربي)" value="${answerAr}" dir="rtl">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeFillBlank(this)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                            container.appendChild(answerDiv);
                        });
                        fillBlankCount = question.correct_answers_text.length;
                    }
                }

                // Store the question ID for updating
                form.setAttribute('data-edit-question-id', questionId);

                // Change the submit button text
                const submitBtn = form.querySelector('button[onclick="submitQuestion()"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fa fa-save me-2"></i>Update Question';
                }

                console.log('Form populated successfully');
            } catch (error) {
                console.error('Error populating form:', error);
            }
        }

        function addQuestionToList(question) {
            const questionsList = document.getElementById('questionsList');

            // Remove the "no questions" message if it exists
            const noQuestionsMsg = questionsList.querySelector('.text-center');
            if (noQuestionsMsg) {
                noQuestionsMsg.remove();
            }

            // Create the question card HTML
            const questionCard = document.createElement('div');
            questionCard.className = 'question-card';
            questionCard.setAttribute('data-question-id', question.id);

            // Generate options HTML based on question type
            let optionsHtml = '';
            if (question.question_type === 'multiple_choice' && question.options) {
                question.options.forEach((option, index) => {
                    const isCorrect = question.correct_answers && question.correct_answers.includes(index);
                    optionsHtml += `
                        <div class="option-item ${isCorrect ? 'correct' : ''}">
                            <i class="fa fa-${isCorrect ? 'check-circle text-success' : 'circle text-muted'} me-2"></i>
                            ${option}
                        </div>
                    `;
                });
            } else if (question.question_type === 'true_false') {
                optionsHtml = `
                    <div class="option-item ${question.correct_answer_boolean ? 'correct' : ''}">
                        <i class="fa fa-${question.correct_answer_boolean ? 'check-circle text-success' : 'circle text-muted'} me-2"></i>
                        True
                    </div>
                    <div class="option-item ${!question.correct_answer_boolean ? 'correct' : ''}">
                        <i class="fa fa-${!question.correct_answer_boolean ? 'check-circle text-success' : 'circle text-muted'} me-2"></i>
                        False
                    </div>
                `;
            } else if (question.question_type === 'fill_blank' && question.correct_answers_text) {
                optionsHtml = '<p><strong>Correct Answers:</strong></p>';
                question.correct_answers_text.forEach(answer => {
                    optionsHtml += `<span class="badge bg-success me-1">${answer}</span>`;
                });
            }

            // Set the question card HTML
            questionCard.innerHTML = `
                <div class="question-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge question-type-badge bg-primary me-2">${question.formatted_question_type}</span>
                        <span class="badge points-badge">${question.points} pts</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="editQuestion(${question.id})">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteQuestion(${question.id})">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="question-body">
                    <h6>${question.question_text}</h6>
                    ${optionsHtml ? `<div class="mt-3">${optionsHtml}</div>` : ''}
                    ${question.explanation ? `
                                                                                                                                                                    <div class="mt-3">
                                                                                                                                                                        <small class="text-muted"><strong>Explanation:</strong> ${question.explanation}</small>
                                                                                                                                                                    </div>
                                                                                                                                                                ` : ''}
                </div>
            `;

            // Add the question card to the list
            questionsList.appendChild(questionCard);
        }

        function updateStatistics() {
            // Update total questions count
            const totalQuestions = document.querySelectorAll('.question-card').length;
            const statsCard = document.querySelector('.stats-card h3');
            if (statsCard) {
                statsCard.textContent = totalQuestions;
            }

            // Update question type counts
            const questionTypes = ['multiple_choice', 'true_false', 'fill_blank', 'essay'];
            questionTypes.forEach(type => {
                const count = document.querySelectorAll(`[data-question-type="${type}"]`).length;
                const statElement = document.querySelector(`[data-stat-type="${type}"]`);
                if (statElement) {
                    statElement.textContent = count;
                }
            });

            // Update total marks
            let totalMarks = 0;
            document.querySelectorAll('.question-card').forEach(card => {
                const pointsBadge = card.querySelector('.points-badge');
                if (pointsBadge) {
                    const points = parseInt(pointsBadge.textContent.replace(' pts', ''));
                    totalMarks += points;
                }
            });

            const totalMarksElement = document.querySelector('.total-marks-display h3');
            if (totalMarksElement) {
                totalMarksElement.textContent = totalMarks;
            }

            // Update question counter
            const questionCounter = document.querySelector('.question-counter span:last-child');
            if (questionCounter) {
                questionCounter.textContent = totalQuestions;
            }
        }

        // Form validation
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            console.log('Form submission started...');

            const name = document.getElementById('name').value.trim();
            const courseId = document.getElementById('course_id').value;
            const passingScore = document.getElementById('passing_score').value;
            const connectionTypeElement = document.querySelector('input[name="connection_type"]:checked');

            console.log('Form values:', {
                name,
                courseId,
                passingScore,
                connectionType: connectionTypeElement ? connectionTypeElement.value : 'none'
            });

            // Temporarily disable validation for testing
            console.log('Form validation passed, submitting...');

            // Show loading state
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Updating...';
                submitBtn.disabled = true;
            }

            // Uncomment the validation code below when ready
            /*
            let hasErrors = false;
            let errorMessage = '';

            if (!name) {
                errorMessage += 'Please enter a quiz name.\n';
                hasErrors = true;
            }

            if (!courseId) {
                errorMessage += 'Please select a course.\n';
                hasErrors = true;
            }

            if (passingScore < 0 || passingScore > 100) {
                errorMessage += 'Passing score must be between 0 and 100.\n';
                hasErrors = true;
            }

            // Check connection type requirements
            if (connectionTypeElement) {
                const connectionType = connectionTypeElement.value;
                if (connectionType === 'section') {
                    const sectionId = document.getElementById('section_id').value;
                    if (!sectionId) {
                        errorMessage += 'Please select a section.\n';
                        hasErrors = true;
                    }
                } else if (connectionType === 'lecture') {
                    const lectureId = document.getElementById('lecture_id').value;
                    if (!lectureId) {
                        errorMessage += 'Please select a lecture.\n';
                        hasErrors = true;
                    }
                }
            } else {
                errorMessage += 'Please select a connection type.\n';
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                console.log('Validation errors:', errorMessage);
                alert('Please fix the following errors:\n' + errorMessage);
                return false;
            }
            */
        });

        // Update preview on input changes
        document.getElementById('name').addEventListener('input', updateQuizPreview);
        document.getElementById('description').addEventListener('input', updateQuizPreview);
        document.getElementById('time_limit_minutes').addEventListener('input', updateQuizPreview);
        document.getElementById('passing_score').addEventListener('input', updateQuizPreview);
        document.getElementById('publish_toggle').addEventListener('change', updateQuizPreview);

        // Initialize connection type selection
        document.addEventListener('DOMContentLoaded', function() {
            const connectionType = document.querySelector('input[name="connection_type"]:checked').value;
            const courseId = document.getElementById('course_id').value;

            // Load sections/lectures if needed
            if (courseId) {
                if (connectionType === 'section') {
                    loadSections(courseId);
                } else if (connectionType === 'lecture') {
                    loadLectures(courseId);
                }
            }

            selectConnectionType(connectionType);
        });
    </script>
@endpush
