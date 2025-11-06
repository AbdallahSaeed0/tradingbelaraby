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
                                            class="feature-value fw-bold text-primary">{{ $quiz->questions->count() ?? 2 }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Quiz Price', 'front') }}</span>
                                        <span
                                            class="feature-value fw-bold {{ $quiz->price > 0 ? 'text-warning' : 'text-success' }}">
                                            {{ $quiz->price > 0 ? number_format($quiz->price, 2) . ' SAR' : custom_trans('Free', 'front') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Start Quiz Button -->
                        <a href="{{ route('quizzes.show', $quiz->id) }}" class="btn btn-orange w-100 fw-bold">
                            <i class="fa fa-play me-2"></i>{{ custom_trans('Start Quiz', 'front') }}
                        </a>
                    </div>
                </div>
            @empty
                <!-- Default Quiz Cards -->
                <div class="col-lg-6">
                    <div class="quiz-card bg-white p-4 rounded-4 shadow-sm h-100">
                        <h4 class="quiz-title fw-bold mb-3">{{ $course->localized_name }}</h4>
                        <p class="quiz-description text-muted mb-4">
                            {{ custom_trans('Test your knowledge of', 'front') }} {{ $course->localized_name }}
                            {{ custom_trans('fundamentals and concepts.', 'front') }}
                        </p>

                        <!-- Quiz Features -->
                        <div class="quiz-features mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Per Question Mark', 'front') }}</span>
                                        <span class="feature-value fw-bold text-primary">5</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Total Marks', 'front') }}</span>
                                        <span class="feature-value fw-bold text-primary">10</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Total Questions', 'front') }}</span>
                                        <span class="feature-value fw-bold text-primary">2</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Quiz Price', 'front') }}</span>
                                        <span class="feature-value fw-bold text-success">{{ custom_trans('Free', 'front') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Start Quiz Button -->
                        <a href="#" class="btn btn-orange w-100 fw-bold">
                            <i class="fa fa-play me-2"></i>{{ custom_trans('Start Quiz', 'front') }}
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="quiz-card bg-white p-4 rounded-4 shadow-sm h-100">
                        <h4 class="quiz-title fw-bold mb-3">{{ custom_trans('Digital Marketing Fundamentals', 'front') }}</h4>
                        <p class="quiz-description text-muted mb-4">
                            {{ custom_trans('Test your knowledge of digital marketing fundamentals including SEO, social media marketing, content marketing, and online advertising strategies.', 'front') }}
                        </p>

                        <!-- Quiz Features -->
                        <div class="quiz-features mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Per Question Mark', 'front') }}</span>
                                        <span class="feature-value fw-bold text-primary">3</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Total Marks', 'front') }}</span>
                                        <span class="feature-value fw-bold text-primary">15</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Total Questions', 'front') }}</span>
                                        <span class="feature-value fw-bold text-primary">5</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="feature-label text-muted">{{ custom_trans('Quiz Price', 'front') }}</span>
                                        <span class="feature-value fw-bold text-success">{{ custom_trans('Free', 'front') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Start Quiz Button -->
                        <a href="#" class="btn btn-orange w-100 fw-bold">
                            <i class="fa fa-play me-2"></i>{{ custom_trans('Start Quiz', 'front') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
