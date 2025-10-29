@php
    $user = auth()->user();
    $allQuestions = $course
        ->questionsAnswers()
        ->with(['user', 'instructor']) // Eager load relationships
        ->where(function ($query) use ($user) {
            $query->where('is_public', true)->whereIn('status', ['pending', 'answered']);
            // Show user's own questions even if not public yet
        if ($user) {
            $query->orWhere('user_id', $user->id);
        }
    })
    ->orderBy('created_at', 'desc')
        ->get();
@endphp

<div class="qa-section bg-light-blue-section py-5">
    <div class="container">
        <!-- Header with title and add question button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">{{ custom_trans('Questions & Answers') }} <span
                    class="text-muted">({{ $allQuestions->count() ?? 0 }})</span></h3>
            <button class="btn btn-orange" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="fa fa-plus me-2"></i>{{ custom_trans('Add New Question') }}
            </button>
        </div>

        <!-- Q&A Accordion -->
        <div class="accordion" id="qaAccordion">
            @forelse($allQuestions as $question)
                <div class="accordion-item border-0 mb-3 bg-white rounded-4 shadow-sm">
                    <div class="d-flex align-items-start p-3">
                        <button class="btn me-3 qa-toggle-btn" type="button" data-bs-toggle="collapse"
                            data-bs-target="#qa{{ $question->id }}" aria-expanded="false"
                            aria-controls="qa{{ $question->id }}">
                            <div class="qa-toggle-icon">
                                <i class="fa"></i>
                            </div>
                        </button>
                        <div
                            class="qa-avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                            {{ strtoupper(substr($question->user->name ?? 'U', 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start w-100">
                                <div>
                                    <div class="qa-user-info text-muted mb-1">
                                        {{ $question->user->name ?? custom_trans('User') }}
                                        • {{ $question->created_at->format('jS F Y') }}
                                        @if ($question->user_id === $user?->id && !$question->is_public)
                                            <span
                                                class="badge bg-warning text-dark ms-2">{{ custom_trans('Pending Approval') }}</span>
                                        @endif
                                    </div>
                                    <h6 class="qa-question-title mb-0">{{ $question->question_title }}</h6>
                                </div>
                                <div class="qa-actions d-flex flex-column align-items-end">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="me-3">{{ $question->answer_content ? 1 : 0 }}
                                            {{ custom_trans('answers') }}</span>
                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#reportModal">
                                            <i class="fa fa-flag"></i>
                                        </button>
                                    </div>
                                    @php
                                        $user = auth()->user();
                                        $isAdmin = $user instanceof \App\Models\Admin;
                                        $isCourseInstructor =
                                            $question->course && $question->course->instructor_id === ($user->id ?? 0);
                                        $canAnswer = $isAdmin && $isCourseInstructor;
                                    @endphp
                                    @if ($canAnswer)
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#addAnswerModal"
                                            data-question="{{ $question->question_title }}"
                                            data-question-id="{{ $question->id }}">
                                            {{ custom_trans('Add Answer') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="qa{{ $question->id }}" class="accordion-collapse collapse" data-bs-parent="#qaAccordion">
                        <div class="accordion-body">
                            @if ($question->answer_content)
                                <div class="qa-answer p-3 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div
                                            class="qa-avatar-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center">
                                            {{ strtoupper(substr($question->instructor->name ?? 'I', 0, 2)) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="qa-user-info text-muted mb-2">
                                                @if ($question->instructor)
                                                    {{ $question->instructor->name }}
                                                    ({{ $question->instructor->isInstructor() ? custom_trans('Instructor') : custom_trans('Admin') }})
                                                @else
                                                    {{ custom_trans('Instructor') }}
                                                @endif
                                                •
                                                {{ $question->answered_at ? $question->answered_at->format('jS F Y') : custom_trans('Recently') }}
                                            </div>
                                            <div class="qa-answer-content">
                                                {!! nl2br(e($question->answer_content)) !!}

                                                <!-- Audio Answer -->
                                                @if ($question->hasAudio())
                                                    <div class="audio-answer mt-3">
                                                        <div class="audio-player-container">
                                                            <audio controls style="width: 100%;">
                                                                <source src="{{ $question->audio_url }}"
                                                                    type="audio/webm">
                                                                <source src="{{ $question->audio_url }}"
                                                                    type="audio/mp3">
                                                                {{ custom_trans('Your browser does not support the audio element.') }}
                                                            </audio>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="qa-answer p-3 mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="qa-answer-content">
                                                <p class="text-muted mb-0">
                                                    {{ custom_trans('No answers yet. Be the first to answer this question!') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- No questions yet -->
                <div class="text-center py-5">
                    <div class="qa-empty-state">
                        <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted mb-2">{{ custom_trans('No questions yet') }}</h5>
                        <p class="text-muted mb-4">
                            {{ custom_trans('Be the first to ask a question about this course!') }}</p>
                        <button class="btn btn-orange" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                            <i class="fa fa-plus me-2"></i>{{ custom_trans('Ask First Question') }}
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">{{ custom_trans('Add New Question') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addQuestionForm">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    <div class="mb-3">
                        <label for="qaQuestionTitle" class="form-label">{{ custom_trans('Question Title') }}</label>
                        <input type="text" class="form-control" id="qaQuestionTitle" name="question_title"
                            placeholder="{{ custom_trans('Enter your question title') }}" minlength="3"
                            maxlength="500" required>
                        <div class="form-text">{{ custom_trans('Minimum 3 characters required') }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="questionContent"
                            class="form-label">{{ custom_trans('Question Details') }}</label>
                        <textarea class="form-control" id="questionContent" name="question_content" rows="4"
                            placeholder="{{ custom_trans('Provide more details about your question...') }}"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="questionType" class="form-label">{{ custom_trans('Question Type') }}</label>
                        <select class="form-control" id="questionType" name="question_type" required>
                            <option value="">{{ custom_trans('Select question type') }}</option>
                            <option value="general">{{ custom_trans('General') }}</option>
                            <option value="technical">{{ custom_trans('Technical') }}</option>
                            <option value="assignment">{{ custom_trans('Assignment Help') }}</option>
                            <option value="schedule">{{ custom_trans('Schedule & Deadlines') }}</option>
                            <option value="content">{{ custom_trans('Course Content') }}</option>
                            <option value="other">{{ custom_trans('Other') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isAnonymous" name="is_anonymous">
                            <label class="form-check-label" for="isAnonymous">
                                {{ custom_trans('Ask anonymously') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ custom_trans('Cancel') }}</button>
                <button type="button" class="btn btn-orange"
                    id="submitQuestion">{{ custom_trans('Submit Question') }}</button>
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
                <h5 class="modal-title" id="addAnswerModalLabel">{{ custom_trans('Add Answer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="text-muted">{{ custom_trans('Question:') }}</h6>
                    <p id="answerQuestionTitle" class="fw-bold"></p>
                </div>
                <form id="addAnswerForm">
                    @csrf
                    <div class="mb-3">
                        <label for="answerContent" class="form-label">{{ custom_trans('Your Answer') }}</label>
                        <textarea class="form-control" id="answerContent" name="answer_content" rows="4"
                            placeholder="{{ custom_trans('Write your answer here...') }}" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ custom_trans('Cancel') }}</button>
                <button type="button" class="btn btn-orange"
                    id="submitAnswer">{{ custom_trans('Submit Answer') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">{{ custom_trans('Report Question') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    @csrf
                    <div class="mb-3">
                        <label for="reportTitle" class="form-label">{{ custom_trans('Report Title') }}</label>
                        <input type="text" class="form-control" id="reportTitle" name="report_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportEmail" class="form-label">{{ custom_trans('Your Email') }}</label>
                        <input type="email" class="form-control" id="reportEmail" name="report_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="reportDetails" class="form-label">{{ custom_trans('Report Details') }}</label>
                        <textarea class="form-control" id="reportDetails" name="report_details" rows="4"
                            placeholder="{{ custom_trans('Please provide details about your report...') }}" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">{{ custom_trans('Cancel') }}</button>
                <button type="button" class="btn btn-danger"
                    id="submitReport">{{ custom_trans('Submit Report') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Add Answer button clicks
        document.querySelectorAll('[data-bs-target="#addAnswerModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const questionText = this.getAttribute('data-question');
                const questionId = this.getAttribute('data-question-id');
                document.getElementById('answerQuestionTitle').textContent = questionText;

                // Set the question ID on the submit button
                const submitAnswerBtn = document.getElementById('submitAnswer');
                if (submitAnswerBtn) {
                    submitAnswerBtn.setAttribute('data-question-id', questionId);
                }
            });
        });



        // Handle form submissions - Remove any existing listeners first
        const submitQuestionBtn = document.getElementById('submitQuestion');
        if (submitQuestionBtn) {
            // Remove existing event listeners by cloning the element
            const newSubmitBtn = submitQuestionBtn.cloneNode(true);
            submitQuestionBtn.parentNode.replaceChild(newSubmitBtn, submitQuestionBtn);

            newSubmitBtn.addEventListener('click', function() {
                const form = document.getElementById('addQuestionForm');

                // Get values directly from form fields since submit button is outside form
                const titleField = document.getElementById('qaQuestionTitle');
                const contentField = document.getElementById('questionContent');
                const typeField = document.getElementById('questionType');
                const anonymousField = document.getElementById('isAnonymous');
                const courseIdField = document.querySelector('input[name="course_id"]');



                // Create FormData manually
                const formData = new FormData();
                formData.append('question_title', titleField ? titleField.value : '');
                formData.append('question_content', contentField ? contentField.value : '');
                formData.append('question_type', typeField ? typeField.value : '');
                formData.append('is_anonymous', anonymousField ? (anonymousField.checked ? '1' : '0') :
                    '0');
                formData.append('course_id', courseIdField ? courseIdField.value : '');
                formData.append('_token', '{{ csrf_token() }}');

                // Client-side validation
                const questionTitle = formData.get('question_title');
                const questionType = formData.get('question_type');

                // Get the final title value
                const finalTitle = questionTitle || (titleField ? titleField.value : '');

                if (!finalTitle) {
                    showToast('{{ custom_trans('Question title is required') }}', 'error');
                    return;
                }

                if (finalTitle.trim().length < 3) {
                    showToast('{{ custom_trans('Question title must be at least 3 characters') }}',
                        'error');
                    return;
                }

                if (!questionType) {
                    showToast('{{ custom_trans('Please select a question type') }}', 'error');
                    return;
                }

                // Show loading state
                const submitBtn = this;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    `<i class="fa fa-spinner fa-spin me-2"></i>{{ custom_trans('Submitting...') }}`;
                submitBtn.disabled = true;



                fetch('{{ route('qa.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');

                            // Reset form
                            form.reset();

                            // Close modal and clean up backdrop
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'addQuestionModal'));
                            if (modal) {
                                modal.hide();
                            }

                            // Remove modal backdrop if it exists
                            setTimeout(() => {
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) {
                                    backdrop.remove();
                                }
                                // Remove modal-open class from body
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';

                                // Refresh the page to show the new question
                                window.location.reload();
                            }, 300);

                            // Question is now visible immediately
                            showToast(
                                '{{ custom_trans('Question submitted successfully!') }}',
                                'success');
                        } else {
                            showToast(data.message ||
                                '{{ custom_trans('Error submitting question') }}', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message ||
                            '{{ custom_trans('Error submitting question') }}',
                            'error');
                    })
                    .finally(() => {
                        // Restore button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Handle answer submission - Remove any existing listeners first
        const submitAnswerBtn = document.getElementById('submitAnswer');
        if (submitAnswerBtn) {
            // Remove existing event listeners by cloning the element
            const newAnswerBtn = submitAnswerBtn.cloneNode(true);
            submitAnswerBtn.parentNode.replaceChild(newAnswerBtn, submitAnswerBtn);

            newAnswerBtn.addEventListener('click', function() {
                const content = document.getElementById('answerContent').value;
                const questionId = this.getAttribute('data-question-id');

                if (content.trim() === '') {
                    showToast('{{ custom_trans('Please enter your answer') }}', 'error');
                    return;
                }

                if (!questionId) {
                    showToast('{{ custom_trans('Question ID not found') }}', 'error');
                    return;
                }

                // Check if user is authorized to answer
                @php
                    $user = auth()->user();
                    $isAdmin = $user instanceof \App\Models\Admin;
                @endphp
                @if (!$isAdmin)
                    showToast(
                        '{{ custom_trans('Only instructors and administrators can answer questions') }}',
                        'error');
                    return;
                @endif

                // Show loading state
                const submitBtn = this;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    `<i class="fa fa-spinner fa-spin me-2"></i>{{ custom_trans('Submitting...') }}`;
                submitBtn.disabled = true;

                // Create FormData for answer submission
                const formData = new FormData();
                formData.append('answer_content', content);
                formData.append('_token', '{{ csrf_token() }}');

                fetch(`/qa/${questionId}/answer`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message || 'Server error');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');

                            // Reset form
                            document.getElementById('answerContent').value = '';

                            // Close modal and clean up backdrop
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'addAnswerModal'));
                            if (modal) {
                                modal.hide();
                            }

                            // Remove modal backdrop if it exists
                            setTimeout(() => {
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) {
                                    backdrop.remove();
                                }
                                // Remove modal-open class from body
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';

                                // Refresh the page to show the new answer
                                window.location.reload();
                            }, 300);
                        } else {
                            showToast(data.message ||
                                '{{ custom_trans('Error submitting answer') }}', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast(error.message || '{{ custom_trans('Error submitting answer') }}',
                            'error');
                    })
                    .finally(() => {
                        // Restore button state
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        }

        // Handle report submission - Remove any existing listeners first
        const submitReportBtn = document.getElementById('submitReport');
        if (submitReportBtn) {
            // Remove existing event listeners by cloning the element
            const newReportBtn = submitReportBtn.cloneNode(true);
            submitReportBtn.parentNode.replaceChild(newReportBtn, submitReportBtn);

            newReportBtn.addEventListener('click', function() {
                const title = document.getElementById('reportTitle').value;
                const email = document.getElementById('reportEmail').value;
                const details = document.getElementById('reportDetails').value;

                if (title.trim() === '' || email.trim() === '') {
                    showToast('{{ custom_trans('Please fill in all required fields') }}', 'error');
                    return;
                }

                // Show success message
                showToast('{{ custom_trans('Report submitted successfully!') }}', 'success');

                // Reset form
                document.getElementById('reportForm').reset();

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
            });
        }

        function showToast(message, type = 'success') {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.qa-toast');
            existingToasts.forEach(toast => toast.remove());

            const toast = document.createElement('div');
            toast.className =
                `qa-toast alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
            toast.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 250px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
            toast.innerHTML = `
            <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
        `;

            document.body.appendChild(toast);

            // Show toast
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            }, 10);

            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // Modal cleanup function
        function cleanupModal(modalId) {
            const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
            if (modal) {
                modal.hide();
            }

            // Remove modal backdrop and clean up body classes
            setTimeout(() => {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 300);
        }

        // Add event listeners for modal cleanup on hidden event
        const addQuestionModal = document.getElementById('addQuestionModal');
        if (addQuestionModal) {
            addQuestionModal.addEventListener('hidden.bs.modal', function() {
                cleanupModal('addQuestionModal');
            });
        }

        const addAnswerModal = document.getElementById('addAnswerModal');
        if (addAnswerModal) {
            addAnswerModal.addEventListener('hidden.bs.modal', function() {
                cleanupModal('addAnswerModal');
            });
        }

        // Handle accordion toggle buttons for proper open/close functionality
        const accordionElement = document.getElementById('qaAccordion');
        if (accordionElement) {
            // Listen for Bootstrap collapse events
            accordionElement.addEventListener('show.bs.collapse', function(event) {
                // Find the button that controls this collapse
                const collapseId = event.target.id;
                const button = document.querySelector(`[data-bs-target="#${collapseId}"]`);
                if (button) {
                    button.setAttribute('aria-expanded', 'true');
                }
            });

            accordionElement.addEventListener('hide.bs.collapse', function(event) {
                // Find the button that controls this collapse
                const collapseId = event.target.id;
                const button = document.querySelector(`[data-bs-target="#${collapseId}"]`);
                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                }
            });

            // Ensure buttons work properly on click
            const toggleButtons = document.querySelectorAll('.qa-toggle-btn');
            toggleButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const targetId = this.getAttribute('data-bs-target');
                    const target = document.querySelector(targetId);

                    if (target) {
                        const bsCollapse = new bootstrap.Collapse(target, {
                            toggle: true
                        });
                    }
                });
            });
        }
    });
</script>
