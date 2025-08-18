<div class="recent-activity-section" style="background-color: #EFF7FF; padding: 2rem 0;">
    <div class="container">
        <h3 class="fw-bold mb-4">{{ __('Recent Activity') }}</h3>
        <div class="row g-4">
            <!-- Recent Questions Box -->
            <div class="col-md-6">
                <div id="questions-section" class="bg-white p-4 rounded-4 shadow-sm h-100">
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
                    <a href="#questions-section" class="btn btn-outline-primary">{{ __('Browse questions') }}</a>
                </div>
            </div>
            <!-- Recent Announcements Box -->
            <div class="col-md-6">
                <div id="homework-section" class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <!-- Recent Homework Assignments -->
                    <h5 class="fw-bold mb-3">{{ __('Recent Homework Assignments') }}</h5>
                    <ul class="list-group mb-3">
                        @php $recentHomeworks = ($course->publishedHomework ?? collect())->sortByDesc('assigned_at')->take(3); @endphp
                        @forelse($recentHomeworks as $homework)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold">{{ $homework->name }}</span>
                                    <br>
                                    <small class="text-muted">{{ __('Due:') }}
                                        {{ $homework->formatted_due_date ?? ($homework->due_date ? $homework->due_date->format('M d, Y') : __('N/A')) }}</small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">{{ __('No homework assignments found.') }}</li>
                        @endforelse
                    </ul>
                    <a href="#homework-section"
                        class="btn btn-outline-primary mb-3">{{ __('Browse all assignments') }}</a>
                    @if (($course->publishedHomework ?? collect())->count() > 3)
                        <a href="#homework-section"
                            class="btn btn-outline-primary">{{ __('Browse all assignments') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <hr class="my-4">
        <div class="row">
            <div class="col-md-3">
                <h5 class="fw-bold">{{ __('Instructor') }}</h5>
            </div>
            <div class="col-md-9">
                @include('courses.partials.course-instructor', ['course' => $course])
            </div>
        </div>
    </div>
</div>
