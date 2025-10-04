<div class="course-content-section" style="background-color: #EFF7FF; padding: 2rem 0;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">{{ __('Course Content') }}</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" id="markCompleteBtn" disabled>
                    <i class="fa fa-check me-2"></i>{{ __('Mark Selected as Complete') }}
                </button>
                <button class="btn btn-outline-warning" id="markIncompleteBtn" disabled>
                    <i class="fa fa-times me-2"></i>{{ __('Mark Selected as Incomplete') }}
                </button>
            </div>
        </div>
        <div class="accordion" id="courseSectionsAccordion">
            @foreach ($course->sections as $section)
                <div class="accordion-item border-0 mb-3 bg-white rounded-4 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#section{{ $section->id }}" aria-expanded="false"
                            aria-controls="section{{ $section->id }}">
                            <div class="d-flex align-items-center w-100">
                                <input type="checkbox" class="form-check-input me-3 section-select-checkbox"
                                    data-section="{{ $section->id }}" onclick="event.stopPropagation();">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <div>
                                            <h5 class="mb-1 fw-bold" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($section->title, $section->title_ar) }}
                                            </h5>
                                            <small class="text-muted"
                                                @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($section->description, $section->description_ar) }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-primary">{{ $section->lectures->count() }}
                                                Classes</span><br>
                                            <small class="text-muted">{{ $section->total_duration ?? '0 Min' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="section{{ $section->id }}" class="accordion-collapse collapse"
                        data-bs-parent="#courseSectionsAccordion">
                        <div class="accordion-body">
                            <div class="class-cards">
                                @foreach ($section->lectures as $lecture)
                                    @php
                                        $isCompleted = auth()
                                            ->user()
                                            ->lectureCompletions()
                                            ->where('lecture_id', $lecture->id)
                                            ->where('is_completed', true)
                                            ->exists();
                                    @endphp
                                    <div
                                        class="class-card d-flex align-items-center p-3 mb-3 bg-light rounded {{ $isCompleted ? 'completed-lecture' : '' }}">
                                        <!-- Selection checkbox for bulk operations -->
                                        <input type="checkbox" class="form-check-input me-2 lecture-select-checkbox"
                                            data-lecture="{{ $lecture->id }}" data-section="{{ $section->id }}">

                                        <!-- Completion status indicator -->
                                        <div class="me-3">
                                            @if ($isCompleted)
                                                <i class="fa fa-check-circle text-success" style="font-size: 1.2rem;"
                                                    title="{{ __('Completed') }}"></i>
                                            @else
                                                <i class="fa fa-circle text-muted" style="font-size: 1.2rem;"
                                                    title="{{ __('Not Completed') }}"></i>
                                            @endif
                                        </div>

                                        <button class="btn btn-outline-primary btn-sm me-3 play-video-btn"
                                            data-video="{{ $lecture->video_url }}">
                                            <i class="fa fa-play"></i>
                                        </button>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1 fw-bold class-title" style="cursor: pointer;"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#class{{ $lecture->id }}-desc"
                                                        @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                                        {{ \App\Helpers\TranslationHelper::getLocalizedContent($lecture->title, $lecture->title_ar) }}
                                                        @if ($isCompleted)
                                                            <span class="badge bg-success ms-2">
                                                                <i class="fa fa-check"></i> {{ __('Completed') }}
                                                            </span>
                                                        @endif
                                                    </h6>
                                                    <div class="collapse class-description"
                                                        id="class{{ $lecture->id }}-desc">
                                                        <p class="text-muted small mb-0"
                                                            @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                                                            {{ \App\Helpers\TranslationHelper::getLocalizedContent($lecture->description, $lecture->description_ar) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <span
                                                    class="text-muted small">{{ $lecture->duration ?? '0 min' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
