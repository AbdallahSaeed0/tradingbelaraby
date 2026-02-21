<div class="quiz-section bg-light-blue-section py-5">
    <div class="container">
        <h3 class="fw-bold mb-4">{{ custom_trans('Objective', 'front') }}</h3>

        <!-- Quiz Cards -->
        <div class="row g-4">
            @forelse($course->quizzes ?? [] as $quiz)
                <div class="col-lg-6">
                    <div class="quiz-card bg-white p-4 rounded-4 shadow-sm h-100">
                        <h4 class="quiz-title fw-bold mb-3">
                            <a href="{{ route('quizzes.show', $quiz->id) }}" class="text-decoration-none text-dark">
                                {{ $quiz->localized_name }}
                            </a>
                        </h4>
                        <p class="quiz-description text-muted mb-4">
                            {{ $quiz->localized_description }}
                        </p>

                        <!-- Quiz Features -->
                        <div class="quiz-features mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Per Question Mark', 'front') }}</span>
                                        <span
                                            class="feature-value fw-bold text-primary">{{ $quiz->marks_per_question ?? 5 }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Total Marks', 'front') }}</span>
                                        <span
                                            class="feature-value fw-bold text-primary">{{ $quiz->total_marks ?? 10 }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Total Questions', 'front') }}</span>
                                        <span
                                            class="feature-value fw-bold text-primary">{{ $quiz->questions_count ?? $quiz->questions->count() ?? 0 }}</span>
                                    </div>
                                </div>
                                @if(isset($quiz->price))
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Quiz Price', 'front') }}</span>
                                        <span
                                            class="feature-value fw-bold {{ $quiz->price > 0 ? 'text-warning' : 'text-success' }}">
                                            {{ $quiz->price > 0 ? number_format($quiz->price, 2) . ' SAR' : custom_trans('Free', 'front') }}
                                        </span>
                                    </div>
                                </div>
                                @else
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Quiz Price', 'front') }}</span>
                                        <span class="feature-value fw-bold text-success">{{ custom_trans('Free', 'front') }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Start Quiz Button -->
                        <a href="{{ route('quizzes.show', $quiz->id) }}" class="btn btn-orange w-100 fw-bold">
                            <i class="fa fa-play me-2"></i>{{ custom_trans('Start Quiz', 'front') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fa fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h4 class="fw-bold mb-2">{{ custom_trans('No Quizzes Yet', 'front') }}</h4>
                        <p class="text-muted mb-0">{{ custom_trans('Quizzes for this course will appear here when added by your instructor.', 'front') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
