@extends('layouts.app')

@section('title', $quiz->localized_name . ' - Quiz')

@section('content')
    <!-- Banner Section -->
    <section class="quiz-banner position-relative d-flex align-items-center justify-content-center">
        <img src="https://eclass.mediacity.co.in/demo2/public/images/breadcum/16953680301690548224bdrc-bg.png" alt="Banner"
            class="quiz-banner-bg position-absolute w-100 h-100 top-0 start-0">
        <div class="quiz-banner-overlay position-absolute w-100 h-100 top-0 start-0"></div>
        <div class="container position-relative z-3 text-center">
            <h1 class="display-4 fw-bold text-white mb-3">{{ $quiz->localized_name }}</h1>
            <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
                <!-- Timer -->
                <div class="quiz-timer bg-white text-dark px-4 py-2 rounded-pill fw-bold shadow">
                    <i class="fa fa-clock me-2 text-orange"></i>
                    {{ __('Quiz Timer') }}: <span id="timer">{{ $quiz->time_limit_minutes ?? '∞' }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Quiz Content Section -->
    <section class="quiz-content-section py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Quiz Card -->
                    <div class="quiz-card bg-white rounded-4 shadow-sm p-4 p-md-5">
                        <!-- Question Counter -->
                        <div class="question-counter text-center mb-4">
                            <span class="question-number fw-bold text-primary fs-4"
                                id="questionCounter">1/{{ $questions->count() }}</span>
                        </div>

                        <!-- Question Title -->
                        <div class="question-title-section mb-4">
                            <h3 class="question-title fw-bold mb-3" id="questionTitle">
                                Q1. {{ $questions->first()->localized_question_text }}
                            </h3>
                        </div>

                        <!-- MCQ Options -->
                        <div class="mcq-options mb-5" id="mcqOptions">
                            @if ($questions->first()->localized_options && is_array($questions->first()->localized_options))
                                @foreach ($questions->first()->localized_options as $index => $option)
                                    <div class="form-check mcq-option mb-3">
                                        <input class="form-check-input mcq-radio" type="radio" name="answer"
                                            id="option{{ $index }}" value="{{ $index }}"
                                            data-question="{{ $questions->first()->id }}">
                                        <label class="form-check-label mcq-label" for="option{{ $index }}">
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i>
                                    {{ __('This question type does not have multiple choice options.') }}
                                </div>
                            @endif
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="quiz-navigation d-flex justify-content-between align-items-center">
                            <button class="btn btn-outline-secondary px-4 py-2 fw-bold d-none-initially" id="prevBtn">
                                <i class="fa fa-arrow-left me-2"></i>{{ __('Previous') }}
                            </button>
                            <div class="flex-grow-1"></div>
                            <button class="btn btn-orange px-4 py-2 fw-bold" id="nextBtn">
                                {{ __('Next') }}<i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quiz Complete Modal -->
    <div class="modal fade" id="quizCompleteModal" tabindex="-1" aria-labelledby="quizCompleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="quizCompleteModalLabel">
                        <i class="fa fa-trophy me-2"></i>{{ __('Quiz Completed!') }}
                    </h5>
                </div>
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <i class="fa fa-check-circle text-success fs-4rem"></i>
                    </div>
                    <h4 class="fw-bold mb-3">{{ __('Congratulations!') }}</h4>
                    <p class="text-muted mb-4">{{ __('You have successfully completed the quiz.') }}</p>
                    <div class="quiz-results">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="result-card bg-light p-3 rounded">
                                    <div class="fw-bold text-primary">{{ __('Score') }}</div>
                                    <div class="fs-4 fw-bold" id="finalScore">-/-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="result-card bg-light p-3 rounded">
                                    <div class="fw-bold text-primary">{{ __('Percentage') }}</div>
                                    <div class="fs-4 fw-bold" id="finalPercentage">-</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="result-card bg-light p-3 rounded">
                                    <div class="fw-bold text-primary">{{ __('Time Taken') }}</div>
                                    <div class="fs-4 fw-bold" id="timeTaken">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <a href="{{ route('quizzes.results', ['quiz' => $quiz, 'attempt' => $attempt]) }}"
                        class="btn btn-success">
                        <i class="fa fa-chart-bar me-2"></i>{{ __('View Detailed Results') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden data for JavaScript -->
    <script type="application/json" id="quizData">
        {!! json_encode($questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question' => $question->localized_question_text,
                'options' => $question->localized_options ?? [],
                'question_type' => $question->question_type,
            ];
        })) !!}
    </script>

    <script type="application/json" id="savedAnswersData">
        {!! json_encode($savedAnswers ?? []) !!}
    </script>
@endsection

@push('scripts')
    <script>
        // Quiz Data
        const quizData = JSON.parse(document.getElementById('quizData').textContent);
        const savedAnswers = JSON.parse(document.getElementById('savedAnswersData').textContent);

        // Quiz State
        let currentQuestion = 0;
        let userAnswers = {};
        let startTime = Date.now();
        let timerInterval;
        let timeLimit = {{ $quiz->time_limit_minutes ?? 60 }}; // seconds per question

        // DOM Elements
        const questionCounter = document.getElementById('questionCounter');
        const questionTitle = document.getElementById('questionTitle');
        const mcqOptions = document.getElementById('mcqOptions');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const timerElement = document.getElementById('timer');

        // Initialize Quiz
        function initQuiz() {
            loadQuestion();
            if (timeLimit !== 60) {
                startTimer();
            }
        }

        // Load Current Question
        function loadQuestion() {
            const question = quizData[currentQuestion];

            // Update question counter
            questionCounter.textContent = `${currentQuestion + 1}/${quizData.length}`;

            // Update question title
            questionTitle.textContent = `Q${currentQuestion + 1}. ${question.question}`;

            // Clear and load options based on question type
            mcqOptions.innerHTML = '';

            if (question.question_type === 'multiple_choice' && question.options && question.options.length > 0) {
                question.options.forEach((option, index) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'form-check mcq-option mb-3';
                    optionDiv.innerHTML = `
                         <input class="form-check-input mcq-radio" type="radio" name="answer" id="option${index}"
                             value="${index}" data-question="${question.id}">
                         <label class="form-check-label mcq-label" for="option${index}">
                             ${option}
                         </label>
                     `;
                    mcqOptions.appendChild(optionDiv);
                });
            } else if (question.question_type === 'true_false') {
                // True/False options
                const trueOption = document.createElement('div');
                trueOption.className = 'form-check mcq-option mb-3';
                trueOption.innerHTML = `
                    <input class="form-check-input mcq-radio" type="radio" name="answer" id="optionTrue"
                        value="true" data-question="${question.id}">
                    <label class="form-check-label mcq-label" for="optionTrue">
                        True
                    </label>
                `;
                mcqOptions.appendChild(trueOption);

                const falseOption = document.createElement('div');
                falseOption.className = 'form-check mcq-option mb-3';
                falseOption.innerHTML = `
                    <input class="form-check-input mcq-radio" type="radio" name="answer" id="optionFalse"
                        value="false" data-question="${question.id}">
                    <label class="form-check-label mcq-label" for="optionFalse">
                        False
                    </label>
                `;
                mcqOptions.appendChild(falseOption);
            } else if (question.question_type === 'essay') {
                // Essay question - show text area
                const textArea = document.createElement('div');
                textArea.className = 'mb-3';
                textArea.innerHTML = `
                    <textarea class="form-control" id="essayAnswer" rows="5"
                        placeholder="Enter your answer here..." data-question="${question.id}"></textarea>
                `;
                mcqOptions.appendChild(textArea);
            } else if (question.question_type === 'fill_blank') {
                // Fill in the blank question
                const inputDiv = document.createElement('div');
                inputDiv.className = 'mb-3';
                inputDiv.innerHTML = `
                    <input type="text" class="form-control" id="fillBlankAnswer"
                        placeholder="Enter your answer..." data-question="${question.id}">
                `;
                mcqOptions.appendChild(inputDiv);
            } else {
                // Default message for other question types
                const messageDiv = document.createElement('div');
                messageDiv.className = 'alert alert-info';
                messageDiv.innerHTML = `
                    <i class="fa fa-info-circle me-2"></i>
                    This question type (${question.question_type}) is not yet supported in this interface.
                `;
                mcqOptions.appendChild(messageDiv);
            }

            // Restore previous answer if exists
            if (userAnswers[question.id]) {
                if (question.question_type === 'essay') {
                    const textArea = document.querySelector('#essayAnswer');
                    if (textArea) textArea.value = userAnswers[question.id];
                } else if (question.question_type === 'fill_blank') {
                    const input = document.querySelector('#fillBlankAnswer');
                    if (input) input.value = userAnswers[question.id];
                } else {
                    const savedAnswer = document.querySelector(`input[value="${userAnswers[question.id]}"]`);
                    if (savedAnswer) savedAnswer.checked = true;
                }
            }

            // Update navigation buttons
            updateNavigationButtons();
        }

        // Update Navigation Buttons
        function updateNavigationButtons() {
            // Show/hide previous button
            if (currentQuestion === 0) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'inline-block';
            }

            // Update next button text
            if (currentQuestion === quizData.length - 1) {
                nextBtn.innerHTML = '{{ __('Finish Quiz') }}<i class="fa fa-check ms-2"></i>';
            } else {
                nextBtn.innerHTML = '{{ __('Next') }}<i class="fa fa-arrow-right ms-2"></i>';
            }
        }

        // Save Current Answer
        function saveCurrentAnswer() {
            const question = quizData[currentQuestion];

            if (question.question_type === 'essay') {
                const textArea = document.querySelector('#essayAnswer');
                if (textArea && textArea.value.trim()) {
                    userAnswers[question.id] = textArea.value.trim();
                    // Don't save essay answers to server during quiz - they'll be saved at submission
                }
            } else if (question.question_type === 'fill_blank') {
                const input = document.querySelector('#fillBlankAnswer');
                if (input && input.value.trim()) {
                    userAnswers[question.id] = input.value.trim();
                    // Don't save fill blank answers to server during quiz - they'll be saved at submission
                }
            } else {
                const selectedOption = document.querySelector('input[name="answer"]:checked');
                if (selectedOption) {
                    const questionId = selectedOption.getAttribute('data-question');
                    const optionId = selectedOption.value;
                    userAnswers[questionId] = optionId;
                    saveAnswerToServer(questionId, optionId);
                }
            }
        }

        // Save Answer to Server (only for multiple choice and true/false)
        function saveAnswerToServer(questionId, answer) {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('attempt_id', {{ $attempt->id }});
            formData.append('question_id', questionId);
            formData.append('option_id', answer);

            fetch('{{ route('quizzes.save-answer', $quiz) }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        console.error('Error saving answer:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Next Question
        function nextQuestion() {
            saveCurrentAnswer();

            if (currentQuestion < quizData.length - 1) {
                currentQuestion++;
                loadQuestion();
                if (timeLimit !== 60) {
                    resetTimer();
                }
            } else {
                finishQuiz();
            }
        }

        // Previous Question
        function prevQuestion() {
            saveCurrentAnswer();

            if (currentQuestion > 0) {
                currentQuestion--;
                loadQuestion();
                if (timeLimit !== 60) {
                    resetTimer();
                }
            }
        }

        // Start Timer
        function startTimer() {
            let timeLeft = timeLimit;
            timerElement.textContent = timeLeft;

            timerInterval = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    // Auto-move to next question when time runs out
                    nextQuestion();
                }
            }, 1000);
        }

        // Reset Timer
        function resetTimer() {
            clearInterval(timerInterval);
            startTimer();
        }

        // Finish Quiz
        function finishQuiz() {
            if (timeLimit !== 60) {
                clearInterval(timerInterval);
            }

            // Prepare answers for submission
            const answers = {};
            Object.keys(userAnswers).forEach(questionId => {
                answers[questionId] = userAnswers[questionId];
            });

            // Submit quiz using JSON
            fetch('{{ route('quizzes.submit', $quiz) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        attempt_id: {{ $attempt->id }},
                        answers: answers
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Server response:', text);
                            throw new Error('Network response was not ok: ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Calculate results for display
                        const totalTime = Math.round((Date.now() - startTime) / 1000);
                        const minutes = Math.floor(totalTime / 60);
                        const seconds = totalTime % 60;

                        // Update modal with results (we'll get actual results from server)
                        document.getElementById('finalScore').textContent =
                            `${Object.keys(userAnswers).length}/${quizData.length}`;
                        document.getElementById('finalPercentage').textContent =
                            `${Math.round((Object.keys(userAnswers).length / quizData.length) * 100)}%`;
                        document.getElementById('timeTaken').textContent =
                            `${minutes}:${seconds.toString().padStart(2, '0')}`;

                        // Show results modal
                        const modal = new bootstrap.Modal(document.getElementById('quizCompleteModal'));
                        modal.show();

                        // Redirect to results page after a delay
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 3000);
                    } else {
                        alert('Error submitting quiz: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error submitting quiz. Please try again.');
                });
        }

        // Event Listeners
        nextBtn.addEventListener('click', nextQuestion);
        prevBtn.addEventListener('click', prevQuestion);

        // Initialize quiz when page loads
        document.addEventListener('DOMContentLoaded', initQuiz);
    </script>
@endpush
