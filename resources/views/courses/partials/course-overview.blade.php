<div class="recent-activity-section" style="background-color: #EFF7FF; padding: 2rem 0;">
    <div class="container">
        <h3 class="fw-bold mb-4">{{ __('Recent Activity') }}</h3>
        <div class="row g-4">
            <!-- Recent Questions Box -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <h4 class="fw-bold mb-4">{{ __('Recent Questions') }}</h4>
                    <div class="recent-questions mb-4">
                        @forelse($course->questionsAnswers ?? [] as $question)
                            <div class="question-item d-flex align-items-start mb-4">
                                <div
                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                    {{ strtoupper(substr($question->user->name ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $question->question_title }}</h6>
                                </div>
                            </div>
                        @empty
                            <div class="question-item d-flex align-items-start mb-4">
                                <div
                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                    AM
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ __('What are') }} {{ $course->name }}?</h6>
                                </div>
                            </div>
                            <div class="question-item d-flex align-items-start">
                                <div
                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                    AM
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ __('How can') }} {{ $course->name }} {{ __('benefit') }}?</h6>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <a href="#" class="btn btn-outline-primary">{{ __('Browse questions') }}</a>
                </div>
            </div>
            <!-- Recent Announcements Box -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <h4 class="fw-bold mb-4">{{ __('Recent Announcements') }}</h4>
                    <div class="recent-announcements mb-4">
                        <div class="accordion" id="announcementsAccordion">
                            @forelse($course->announcements ?? [] as $announcement)
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent p-0" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#announcement{{ $announcement->id }}">
                                            <div class="d-flex align-items-start w-100">
                                                <div
                                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                                    {{ strtoupper(substr($announcement->user->name ?? 'A', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $announcement->user->name ?? 'Admin' }}
                                                        {{ $announcement->created_at->format('jS F Y') }}</h6>
                                                    <small class="text-muted">{{ __('Announcements') }}</small>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="announcement{{ $announcement->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#announcementsAccordion">
                                        <div class="accordion-body ps-5 pt-2">
                                            {{ $announcement->content }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-transparent p-0" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#announcement1">
                                            <div class="d-flex align-items-start w-100">
                                                <div
                                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                                    AM
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ __('Admin Mediacity') }} 4th October 2023
                                                    </h6>
                                                    <small class="text-muted">{{ __('Announcements') }}</small>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="announcement1" class="accordion-collapse collapse"
                                        data-bs-parent="#announcementsAccordion">
                                        <div class="accordion-body ps-5 pt-2">
                                            {{ __('This is the announcement content. It can contain important updates and information about the course.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <a href="#" class="btn btn-outline-primary">{{ __('Browse announcements') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
