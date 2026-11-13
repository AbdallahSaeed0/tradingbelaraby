{{-- Course curriculum (learn page). Hooks used by the page script: #markCompleteBtn, #markIncompleteBtn,
     .section-select-checkbox, .lecture-select-checkbox[data-lecture], .class-card(.completed-lecture),
     .play-video-btn, .class-title (gets a .badge appended), status icon .fa-circle / .fa-check-circle --}}
@php
    $isArabicUi = \App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar';
@endphp
<div class="course-content-section curriculum">
    <div class="curriculum-toolbar">
        <h3 class="curriculum-heading">{{ custom_trans('Course Content', 'front') }}</h3>
        <div class="curriculum-actions">
            <button class="btn curriculum-action curriculum-action--done" id="markCompleteBtn" disabled>
                <i class="fa fa-check" aria-hidden="true"></i><span>{{ custom_trans('Mark Selected as Complete', 'front') }}</span>
            </button>
            <button class="btn curriculum-action curriculum-action--undo" id="markIncompleteBtn" disabled>
                <i class="fa fa-times" aria-hidden="true"></i><span>{{ custom_trans('Mark Selected as Incomplete', 'front') }}</span>
            </button>
        </div>
    </div>

    <div class="accordion" id="courseSectionsAccordion">
        @foreach ($course->sections as $section)
            <div class="accordion-item curriculum-section">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed curriculum-section-toggle" type="button" data-bs-toggle="collapse"
                        data-bs-target="#section{{ $section->id }}" aria-expanded="false"
                        aria-controls="section{{ $section->id }}">
                        <input type="checkbox" class="form-check-input section-select-checkbox"
                            data-section="{{ $section->id }}" onclick="event.stopPropagation();"
                            aria-label="{{ custom_trans('Select', 'front') }}">
                        <span class="curriculum-section-text" @if ($isArabicUi) dir="rtl" @endif>
                            <span class="curriculum-section-title">
                                {{ \App\Helpers\TranslationHelper::getLocalizedContent($section->title, $section->title_ar) }}
                            </span>
                            @php $sectionDesc = \App\Helpers\TranslationHelper::getLocalizedContent($section->description, $section->description_ar); @endphp
                            @if (filled($sectionDesc))
                                <span class="curriculum-section-desc">{{ $sectionDesc }}</span>
                            @endif
                        </span>
                        <span class="curriculum-section-count">
                            {{ $section->lectures->count() }} {{ custom_trans('lectures_count_label', 'front') }}
                        </span>
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
                                    $lectureDesc = \App\Helpers\TranslationHelper::getLocalizedContent($lecture->description, $lecture->description_ar);
                                @endphp
                                <div class="class-card {{ $isCompleted ? 'completed-lecture' : '' }}">
                                    <!-- Selection checkbox for bulk operations -->
                                    <input type="checkbox" class="form-check-input lecture-select-checkbox"
                                        data-lecture="{{ $lecture->id }}" data-section="{{ $section->id }}"
                                        aria-label="{{ custom_trans('Select', 'front') }}">

                                    <!-- Completion status indicator -->
                                    <span class="lecture-status">
                                        @if ($isCompleted)
                                            <i class="fa fa-check-circle text-success" title="{{ custom_trans('Completed', 'front') }}"></i>
                                        @else
                                            <i class="fa fa-circle text-muted" title="{{ custom_trans('Not Completed', 'front') }}"></i>
                                        @endif
                                    </span>

                                    <button class="btn play-video-btn" data-video="{{ $lecture->video_url }}"
                                        aria-label="{{ custom_trans('Play', 'front') }}">
                                        <i class="fa fa-play" aria-hidden="true"></i>
                                    </button>

                                    <div class="lecture-body">
                                        <h6 class="class-title {{ filled($lectureDesc) ? 'cursor-pointer' : '' }}"
                                            @if (filled($lectureDesc)) data-bs-toggle="collapse" data-bs-target="#class{{ $lecture->id }}-desc" @endif
                                            @if ($isArabicUi) dir="rtl" @endif>
                                            {{ \App\Helpers\TranslationHelper::getLocalizedContent($lecture->title, $lecture->title_ar) }}
                                            @if ($isCompleted)
                                                <span class="badge bg-success ms-2">
                                                    <i class="fa fa-check"></i> {{ custom_trans('Completed', 'front') }}
                                                </span>
                                            @endif
                                        </h6>
                                        @if (filled($lectureDesc))
                                            <div class="collapse class-description" id="class{{ $lecture->id }}-desc">
                                                <p class="text-muted small mb-0" @if ($isArabicUi) dir="rtl" @endif>{{ $lectureDesc }}</p>
                                            </div>
                                        @endif
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
