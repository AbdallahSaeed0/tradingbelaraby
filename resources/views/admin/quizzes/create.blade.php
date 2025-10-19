@extends('admin.layout')

@section('title', 'Create Quiz')

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
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Create New Quiz</h1>
                        <p class="text-muted">Set up a new quiz for your course</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left me-2"></i>Back to Quizzes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.quizzes.store') }}" method="POST" id="quizForm">
            @csrf
            <input type="hidden" id="is_published" name="is_published" value="1">

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
                                            <label for="name" class="form-label">Quiz Title (English) *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="name_ar" class="form-label">Quiz Title (Arabic)</label>
                                            <input type="text"
                                                class="form-control @error('name_ar') is-invalid @enderror" id="name_ar"
                                                name="name_ar" value="{{ old('name_ar') }}" dir="rtl">
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
                                                        {{ old('course_id') == $course->id ? 'selected' : '' }}>
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
                                                rows="3" placeholder="Describe what this quiz covers...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="description_ar" class="form-label">Description (Arabic)</label>
                                            <textarea class="form-control @error('description_ar') is-invalid @enderror" id="description_ar" name="description_ar"
                                                rows="3" placeholder="وصف الاختبار..." dir="rtl">{{ old('description_ar') }}</textarea>
                                            @error('description_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="instructions" class="form-label">Instructions (English)</label>
                                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions"
                                                rows="3" placeholder="Enter quiz instructions...">{{ old('instructions') }}</textarea>
                                            @error('instructions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="instructions_ar" class="form-label">Instructions (Arabic)</label>
                                            <textarea class="form-control @error('instructions_ar') is-invalid @enderror" id="instructions_ar"
                                                name="instructions_ar" rows="3" placeholder="تعليمات الاختبار..." dir="rtl">{{ old('instructions_ar') }}</textarea>
                                            @error('instructions_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Quiz Connection -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fa fa-link me-2"></i>Quiz Connection</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">Choose how this quiz connects to your course content:
                                        </p>

                                        <div class="connection-type-selector"
                                            onclick="selectConnectionType('course', event)">
                                            <input type="radio" name="connection_type" id="connection_course"
                                                value="course" checked>
                                            <label for="connection_course" class="fw-bold">Course Level</label>
                                            <p class="text-muted mb-0">Quiz available for the entire course</p>
                                        </div>

                                        <div class="connection-type-selector"
                                            onclick="selectConnectionType('section', event)">
                                            <input type="radio" name="connection_type" id="connection_section"
                                                value="section">
                                            <label for="connection_section" class="fw-bold">Section Level</label>
                                            <p class="text-muted mb-0">Quiz available after completing a specific section
                                            </p>
                                            <div id="section_selection" class="mt-3 d-none-initially"
                                                onclick="event.stopPropagation()">
                                                <select class="form-select" id="section_id" name="section_id">
                                                    <option value="">Select Section</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="connection-type-selector"
                                            onclick="selectConnectionType('lecture', event)">
                                            <input type="radio" name="connection_type" id="connection_lecture"
                                                value="lecture">
                                            <label for="connection_lecture" class="fw-bold">Lecture Level</label>
                                            <p class="text-muted mb-0">Quiz available after completing a specific lecture
                                            </p>
                                            <div id="lecture_selection" class="mt-3 d-none-initially"
                                                onclick="event.stopPropagation()">
                                                <select class="form-select" id="lecture_id" name="lecture_id">
                                                    <option value="">Select Lecture</option>
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
                                                    value="{{ old('time_limit_minutes') }}" min="1"
                                                    placeholder="No limit">
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
                                                    value="{{ old('passing_score', 60) }}" min="0" max="100"
                                                    required>
                                                @error('passing_score')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="available_from" class="form-label">Available From</label>
                                                <input type="datetime-local"
                                                    class="form-control @error('available_from') is-invalid @enderror"
                                                    id="available_from" name="available_from"
                                                    value="{{ old('available_from') }}">
                                                @error('available_from')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="available_until" class="form-label">Available Until</label>
                                                <input type="datetime-local"
                                                    class="form-control @error('available_until') is-invalid @enderror"
                                                    id="available_until" name="available_until"
                                                    value="{{ old('available_until') }}">
                                                @error('available_until')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_randomized"
                                                        name="is_randomized" value="1"
                                                        {{ old('is_randomized') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_randomized">
                                                        <i class="fa fa-random me-1"></i>Randomize Questions
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="show_results_immediately" name="show_results_immediately"
                                                        value="1"
                                                        {{ old('show_results_immediately', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_results_immediately">
                                                        <i class="fa fa-eye me-1"></i>Show Results Immediately
                                                    </label>
                                                </div>
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
                                        <div class="text-center text-muted py-4">
                                            <i class="fa fa-question-circle fa-3x mb-3"></i>
                                            <p>Fill in the quiz details to see a preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fa fa-save me-2"></i>Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save me-2"></i>Create Quiz
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="saveAsDraft()">
                                            <i class="fa fa-edit me-2"></i>Save as Draft
                                        </button>
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
                                    <button class="btn btn-primary btn-sm" onclick="showQuestionForm()">
                                        <i class="fa fa-plus me-2"></i>Add Question
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Question Form (Hidden by default) -->
                                    <div id="questionForm" class="question-form d-none-initially">
                                        <h6 class="mb-3">Add New Question</h6>

                                        <!-- Question Type Selection -->
                                        <div class="question-type-selector">
                                            <div class="type-card" onclick="selectQuestionType('multiple_choice')">
                                                <i class="fa fa-list-ul"></i>
                                                <div><strong>Multiple Choice</strong></div>
                                                <small class="text-muted">Choose one correct answer</small>
                                            </div>
                                            <div class="type-card" onclick="selectQuestionType('true_false')">
                                                <i class="fa fa-toggle-on"></i>
                                                <div><strong>True/False</strong></div>
                                                <small class="text-muted">True or false statement</small>
                                            </div>
                                            <div class="type-card" onclick="selectQuestionType('fill_blank')">
                                                <i class="fa fa-pencil"></i>
                                                <div><strong>Fill in the Blank</strong></div>
                                                <small class="text-muted">Text input answer</small>
                                            </div>
                                            <div class="type-card" onclick="selectQuestionType('essay')">
                                                <i class="fa fa-file-text"></i>
                                                <div><strong>Essay</strong></div>
                                                <small class="text-muted">Long text answer</small>
                                            </div>
                                        </div>

                                        <form id="addQuestionForm">
                                            <input type="hidden" id="questionType" name="question_type">

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="questionText" class="form-label">Question Text (English)
                                                        *</label>
                                                    <textarea class="form-control" id="questionText" name="question_text" rows="3" required></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="questionTextAr" class="form-label">Question Text
                                                        (Arabic)</label>
                                                    <textarea class="form-control" id="questionTextAr" name="question_text_ar" rows="3" dir="rtl"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="questionPoints" class="form-label">Points *</label>
                                                    <input type="number" class="form-control" id="questionPoints"
                                                        name="points" min="1" value="1" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="questionOrder" class="form-label">Order</label>
                                                    <input type="number" class="form-control" id="questionOrder"
                                                        name="order" min="1" value="1">
                                                </div>

                                                <!-- Multiple Choice Options -->
                                                <div id="multipleChoiceOptions" class="col-12 d-none-initially">
                                                    <label class="form-label">Options *</label>
                                                    <div id="optionsContainer">
                                                        <div class="option-item mb-3">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <input type="radio" name="correct_answer"
                                                                    value="0" class="me-2">
                                                                <label class="form-label mb-0 me-2"><strong>Option
                                                                        1</strong></label>
                                                            </div>
                                                            <div class="row g-2">
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control"
                                                                        name="options[]" placeholder="Option 1 (English)">
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control"
                                                                        name="options_ar[]" placeholder="الخيار 1 (عربي)"
                                                                        dir="rtl">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger w-100"
                                                                        onclick="removeOption(this)">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="option-item mb-3">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <input type="radio" name="correct_answer"
                                                                    value="1" class="me-2">
                                                                <label class="form-label mb-0 me-2"><strong>Option
                                                                        2</strong></label>
                                                            </div>
                                                            <div class="row g-2">
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control"
                                                                        name="options[]" placeholder="Option 2 (English)">
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control"
                                                                        name="options_ar[]" placeholder="الخيار 2 (عربي)"
                                                                        dir="rtl">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger w-100"
                                                                        onclick="removeOption(this)">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                                        onclick="addOption()">
                                                        <i class="fa fa-plus me-1"></i>Add Option
                                                    </button>
                                                </div>

                                                <!-- True/False Options -->
                                                <div id="trueFalseOptions" class="col-12 d-none-initially">
                                                    <label class="form-label">Correct Answer *</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="correct_answer_boolean" id="true" value="1">
                                                        <label class="form-check-label" for="true">True</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="correct_answer_boolean" id="false" value="0">
                                                        <label class="form-check-label" for="false">False</label>
                                                    </div>
                                                </div>

                                                <!-- Fill in the Blank Options -->
                                                <div id="fillBlankOptions" class="col-12 d-none-initially">
                                                    <label class="form-label">Correct Answers *</label>
                                                    <div id="fillBlankContainer">
                                                        <div class="fill-blank-answer mb-3">
                                                            <label class="form-label mb-2"><strong>Answer
                                                                    1</strong></label>
                                                            <div class="row g-2">
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control"
                                                                        name="correct_answers_text[]"
                                                                        placeholder="Correct answer 1 (English)">
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control"
                                                                        name="correct_answers_text_ar[]"
                                                                        placeholder="الإجابة الصحيحة 1 (عربي)"
                                                                        dir="rtl">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger w-100"
                                                                        onclick="removeFillBlank(this)">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="addFillBlank()">
                                                        <i class="fa fa-plus me-1"></i>Add Answer
                                                    </button>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="explanation" class="form-label">Explanation
                                                        (English)</label>
                                                    <textarea class="form-control" id="explanation" name="explanation" rows="2"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="explanationAr" class="form-label">Explanation
                                                        (Arabic)</label>
                                                    <textarea class="form-control" id="explanationAr" name="explanation_ar" rows="2" dir="rtl"></textarea>
                                                </div>

                                                <div class="col-12">
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fa fa-save me-2"></i>Add Question
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary"
                                                            onclick="hideQuestionForm()">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Questions List -->
                                    <div id="questionsList">
                                        <div class="text-center py-4">
                                            <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No questions added yet. Click "Add Question" to get
                                                started.</p>
                                        </div>
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
                                        <h3 id="totalQuestions">0</h3>
                                        <p class="mb-0">Total Questions</p>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" id="multipleChoiceCount">0</h4>
                                                <small class="text-muted">Multiple Choice</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" id="trueFalseCount">0</h4>
                                                <small class="text-muted">True/False</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" id="fillBlankCount">0</h4>
                                                <small class="text-muted">Fill in Blank</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h4 class="mb-1" id="essayCount">0</h4>
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
                                        <h3 id="totalMarks">0</h3>
                                        <p class="mb-0">Total Points</p>
                                    </div>
                                    <div class="question-counter mt-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Questions:</span>
                                            <span id="questionCount">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        let questions = [];

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
        function selectConnectionType(type, event) {
            // Prevent default action and stop propagation
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Remove selected class from all selectors
            document.querySelectorAll('.connection-type-selector').forEach(selector => {
                selector.classList.remove('selected');
            });

            // Add selected class to clicked selector
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('selected');
            } else {
                // Fallback if event is not available
                document.querySelector(`.connection-type-selector input[value="${type}"]`)?.closest(
                    '.connection-type-selector')?.classList.add('selected');
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
            fetch(`/admin/quizzes/get-sections/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('section_id');
                    select.innerHTML = '<option value="">Select Section</option>';

                    data.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.title;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading sections:', error);
                });
        }

        // Load lectures for selected course
        function loadLectures(courseId) {
            fetch(`/admin/quizzes/get-lectures/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('lecture_id');
                    select.innerHTML = '<option value="">Select Lecture</option>';

                    data.forEach(lecture => {
                        const option = document.createElement('option');
                        option.value = lecture.id;
                        option.textContent = `${lecture.section_title} - ${lecture.title}`;
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
            const connectionType = document.querySelector('input[name="connection_type"]:checked').value;
            const isPublished = document.getElementById('is_published').value === '1';

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
                                <small class="text-muted">Connection:</small><br>
                                <span class="badge bg-info">${connectionType.toUpperCase()}</span>
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
            } else {
                preview.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fa fa-question-circle fa-3x mb-3"></i>
                        <p>Fill in the quiz details to see a preview</p>
                    </div>
                `;
            }
        }

        // Save as draft functionality
        function saveAsDraft() {
            document.getElementById('is_published').value = '0';
            document.getElementById('quizForm').submit();
        }

        // Question management functions
        function showQuestionForm() {
            document.getElementById('questionForm').style.display = 'block';
            document.getElementById('questionForm').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function hideQuestionForm() {
            document.getElementById('questionForm').style.display = 'none';
            resetQuestionForm();
        }

        function selectQuestionType(type) {
            // Remove selected class from all type cards
            document.querySelectorAll('.type-card').forEach(card => {
                card.classList.remove('selected');
            });

            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');

            // Set the question type
            document.getElementById('questionType').value = type;

            // Remove required attributes from all option fields first
            document.querySelectorAll('input[name="correct_answer_boolean"]').forEach(input => {
                input.removeAttribute('required');
            });
            document.querySelectorAll('input[name="correct_answers_text[]"]').forEach(input => {
                input.removeAttribute('required');
            });
            document.querySelectorAll('input[name="options[]"]').forEach(input => {
                input.removeAttribute('required');
            });

            // Hide all option sections
            document.getElementById('multipleChoiceOptions').style.display = 'none';
            document.getElementById('trueFalseOptions').style.display = 'none';
            document.getElementById('fillBlankOptions').style.display = 'none';

            // Show relevant option section and add required attributes
            if (type === 'multiple_choice') {
                document.getElementById('multipleChoiceOptions').style.display = 'block';
                document.querySelectorAll('input[name="options[]"]').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            } else if (type === 'true_false') {
                document.getElementById('trueFalseOptions').style.display = 'block';
                document.querySelectorAll('input[name="correct_answer_boolean"]').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            } else if (type === 'fill_blank') {
                document.getElementById('fillBlankOptions').style.display = 'block';
                document.querySelectorAll('input[name="correct_answers_text[]"]').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            }
        }

        function addOption() {
            const container = document.getElementById('optionsContainer');
            const newOption = document.createElement('div');
            newOption.className = 'option-item mb-3';
            const isRequired = document.getElementById('questionType').value === 'multiple_choice';
            newOption.innerHTML = `
                <div class="d-flex align-items-center mb-2">
                    <input type="radio" name="correct_answer" value="${optionCount}" class="me-2">
                    <label class="form-label mb-0 me-2"><strong>Option ${optionCount + 1}</strong></label>
                </div>
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="options[]" placeholder="Option ${optionCount + 1} (English)" ${isRequired ? 'required' : ''}>
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
            const container = document.getElementById('fillBlankContainer');
            const newAnswer = document.createElement('div');
            newAnswer.className = 'fill-blank-answer mb-3';
            const isRequired = document.getElementById('questionType').value === 'fill_blank';
            newAnswer.innerHTML = `
                <label class="form-label mb-2"><strong>Answer ${fillBlankCount + 1}</strong></label>
                <div class="row g-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="correct_answers_text[]" placeholder="Correct answer ${fillBlankCount + 1} (English)" ${isRequired ? 'required' : ''}>
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
            document.getElementById('addQuestionForm').reset();
            document.querySelectorAll('.type-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.getElementById('multipleChoiceOptions').style.display = 'none';
            document.getElementById('trueFalseOptions').style.display = 'none';
            document.getElementById('fillBlankOptions').style.display = 'none';
            optionCount = 2;
            fillBlankCount = 1;
        }

        function updateStatistics() {
            const totalQuestions = questions.length;
            const totalMarks = questions.reduce((sum, q) => sum + q.points, 0);

            const typeCounts = {
                multiple_choice: questions.filter(q => q.type === 'multiple_choice').length,
                true_false: questions.filter(q => q.type === 'true_false').length,
                fill_blank: questions.filter(q => q.type === 'fill_blank').length,
                essay: questions.filter(q => q.type === 'essay').length
            };

            document.getElementById('totalQuestions').textContent = totalQuestions;
            document.getElementById('totalMarks').textContent = totalMarks;
            document.getElementById('questionCount').textContent = totalQuestions;
            document.getElementById('multipleChoiceCount').textContent = typeCounts.multiple_choice;
            document.getElementById('trueFalseCount').textContent = typeCounts.true_false;
            document.getElementById('fillBlankCount').textContent = typeCounts.fill_blank;
            document.getElementById('essayCount').textContent = typeCounts.essay;
        }

        function renderQuestionsList() {
            const container = document.getElementById('questionsList');

            if (questions.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No questions added yet. Click "Add Question" to get started.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = questions.map((question, index) => `
                <div class="question-card">
                    <div class="question-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge question-type-badge bg-primary me-2">${question.type.replace('_', ' ').toUpperCase()}</span>
                            <span class="badge points-badge">${question.points} pts</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="editQuestion(${index})">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteQuestion(${index})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="question-body">
                        <h6>${question.text}</h6>
                        ${question.options ? `
                                                                                                                                            <div class="mt-3">
                                                                                                                                                ${question.options.map((option, optIndex) => `
                                    <div class="option-item ${question.correctAnswers.includes(optIndex) ? 'correct' : ''}">
                                        <i class="fa fa-${question.correctAnswers.includes(optIndex) ? 'check-circle text-success' : 'circle text-muted'} me-2"></i>
                                        ${option}
                                    </div>
                                `).join('')}
                                                                                                                                            </div>
                                                                                                                                        ` : ''}
                        ${question.explanation ? `
                                                                                                                                            <div class="mt-3">
                                                                                                                                                <small class="text-muted"><strong>Explanation:</strong> ${question.explanation}</small>
                                                                                                                                            </div>
                                                                                                                                        ` : ''}
                    </div>
                </div>
            `).join('');
        }

        function editQuestion(index) {
            // Implementation for editing question
            console.log('Edit question:', index);
        }

        function deleteQuestion(index) {
            if (confirm('Are you sure you want to delete this question?')) {
                questions.splice(index, 1);
                renderQuestionsList();
                updateStatistics();
                toastr.success('Question deleted successfully!');
            }
        }

        // Handle question form submission
        document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `<i class="fa fa-spinner fa-spin me-2"></i>Adding...`;
            submitBtn.disabled = true;

            // Store question data temporarily (will be sent with quiz)
            const questionData = Object.fromEntries(formData);

            const question = {
                type: questionData.question_type,
                text: questionData.question_text,
                text_ar: questionData.question_text_ar || '',
                points: parseInt(questionData.points),
                order: parseInt(questionData.order) || questions.length + 1,
                explanation: questionData.explanation || '',
                explanation_ar: questionData.explanation_ar || '',
                options: formData.getAll('options[]').filter(opt => opt.trim()),
                options_ar: formData.getAll('options_ar[]').filter(opt => opt.trim()),
                correctAnswers: questionData.correct_answer ? [parseInt(questionData.correct_answer)] : [],
                correctAnswerBoolean: questionData.correct_answer_boolean ? questionData
                    .correct_answer_boolean === '1' : null,
                correctAnswersText: formData.getAll('correct_answers_text[]').filter(ans => ans.trim()),
                correctAnswersTextAr: formData.getAll('correct_answers_text_ar[]').filter(ans => ans.trim())
            };

            questions.push(question);
            console.log('Question added to array. Total questions:', questions.length);
            console.log('Question data:', question);
            renderQuestionsList();
            updateStatistics();
            hideQuestionForm();

            // Show success message
            toastr.success('Question added successfully!');

            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });

        // Form validation
        document.getElementById('quizForm').addEventListener('submit', function(e) {
            // Remove required attributes from hidden fields before submission
            const questionType = document.getElementById('questionType').value;

            if (questionType !== 'multiple_choice') {
                document.querySelectorAll('input[name="options[]"]').forEach(input => {
                    input.removeAttribute('required');
                });
            }

            if (questionType !== 'true_false') {
                document.querySelectorAll('input[name="correct_answer_boolean"]').forEach(input => {
                    input.removeAttribute('required');
                });
            }

            if (questionType !== 'fill_blank') {
                document.querySelectorAll('input[name="correct_answers_text[]"]').forEach(input => {
                    input.removeAttribute('required');
                });
            }

            const name = document.getElementById('name').value.trim();
            const courseId = document.getElementById('course_id').value;
            const passingScore = document.getElementById('passing_score').value;
            const connectionType = document.querySelector('input[name="connection_type"]:checked').value;
            const sectionId = document.getElementById('section_id').value;
            const lectureId = document.getElementById('lecture_id').value;

            console.log('Form Submission Debug:', {
                name: name,
                courseId: courseId,
                connectionType: connectionType,
                sectionId: sectionId,
                lectureId: lectureId
            });

            if (!name) {
                e.preventDefault();
                toastr.error('Please enter a quiz name');
                return false;
            }

            if (!courseId) {
                e.preventDefault();
                toastr.error('Please select a course');
                return false;
            }

            if (passingScore < 0 || passingScore > 100) {
                e.preventDefault();
                toastr.error('Passing score must be between 0 and 100');
                return false;
            }

            // Validate connection type requirements
            if (connectionType === 'section' && !sectionId) {
                e.preventDefault();
                toastr.error('Please select a section');
                return false;
            }

            if (connectionType === 'lecture' && !lectureId) {
                e.preventDefault();
                toastr.error('Please select a lecture');
                return false;
            }

            // Add questions data to form before submission
            console.log('Submitting quiz with questions:', questions);
            if (questions.length > 0) {
                const questionsInput = document.createElement('input');
                questionsInput.type = 'hidden';
                questionsInput.name = 'questions_data';
                questionsInput.value = JSON.stringify(questions);
                this.appendChild(questionsInput);
                console.log('Questions data added to form:', questionsInput.value);
            } else {
                console.warn('No questions added to quiz!');
            }
        });

        // Update preview on input changes
        document.getElementById('name').addEventListener('input', updateQuizPreview);
        document.getElementById('description').addEventListener('input', updateQuizPreview);
        document.getElementById('time_limit_minutes').addEventListener('input', updateQuizPreview);
        document.getElementById('passing_score').addEventListener('input', updateQuizPreview);
        document.getElementById('is_published').addEventListener('change', updateQuizPreview);

        // Initialize connection type selection
        document.addEventListener('DOMContentLoaded', function() {
            selectConnectionType('course');
            updateStatistics();
        });
    </script>
@endpush
