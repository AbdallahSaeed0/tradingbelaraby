<div class="recent-activity-section bg-light-blue-section py-5">
    <div class="container">
        <h3 class="fw-bold mb-4">{{ custom_trans('Recent Activity', 'front') }}</h3>
        <div class="row g-4">
            <!-- Recent Questions Box -->
            <div class="col-md-6">
                <div id="questions-section" class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <h4 class="fw-bold mb-4">{{ custom_trans('Recent Questions', 'front') }}</h4>
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
                                    <h6 class="mb-1">{{ custom_trans('What are', 'front') }}
                                        {{ $course->localized_name }}?
                                    </h6>
                                </div>
                            </div>
                            <div class="question-item d-flex align-items-start">
                                <div
                                    class="avatar-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center">
                                    AM
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ custom_trans('How can', 'front') }}
                                        {{ \App\Helpers\TranslationHelper::getLocalizedContent($course->name, $course->name_ar) }}
                                        {{ custom_trans('benefit', 'front') }}?</h6>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <a href="#questions-section" class="btn btn-outline-primary">{{ custom_trans('Browse questions', 'front') }}</a>
                </div>
            </div>
            <!-- Recent Announcements Box -->
            <div class="col-md-6">
                <div id="homework-section" class="bg-white p-4 rounded-4 shadow-sm h-100">
                    <!-- Recent Homework Assignments -->
                    <h5 class="fw-bold mb-3">{{ custom_trans('Recent Homework Assignments', 'front') }}</h5>
                    <ul class="list-group mb-3">
                        @php $recentHomeworks = ($course->publishedHomework ?? collect())->sortByDesc('assigned_at')->take(3); @endphp
                        @forelse($recentHomeworks as $homework)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold">{{ $homework->name }}</span>
                                    <br>
                                    <small class="text-muted">{{ custom_trans('Due:', 'front') }}
                                        {{ $homework->formatted_due_date ?? ($homework->due_date ? $homework->due_date->format('M d, Y') : custom_trans('N/A', 'front')) }}</small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-secondary">{{ custom_trans('View', 'front') }}</a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">{{ custom_trans('No homework assignments found.', 'front') }}</li>
                        @endforelse
                    </ul>
                    <a href="#homework-section"
                        class="btn btn-outline-primary mb-3">{{ custom_trans('Browse all assignments', 'front') }}</a>
                    @if (($course->publishedHomework ?? collect())->count() > 3)
                        <a href="#homework-section"
                            class="btn btn-outline-primary">{{ custom_trans('Browse all assignments', 'front') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <hr class="my-4">
        <div class="row">
            <div class="col-md-3">
                <h5 class="fw-bold">
                    @if ($course->instructors && $course->instructors->count() > 1)
                        {{ custom_trans('Instructors', 'front') }}
                    @else
                        {{ custom_trans('Instructor', 'front') }}
                    @endif
                </h5>
            </div>
            <div class="col-md-9">
                @include('courses.partials.course-instructor', ['course' => $course])
            </div>
        </div>
    </div>
</div>
