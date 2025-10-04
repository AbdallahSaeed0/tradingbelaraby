<section class="course-content-hero py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-5 text-center">
                <div class="course-content-media-wrap position-relative">
                    <img src="{{ $course->image_url }}" alt="{{ $course->name }}"
                        class="img-fluid rounded-3 course-content-media-img" style="cursor:pointer;">
                    <button
                        class="btn btn-orange course-content-play-btn position-absolute top-50 start-50 translate-middle"
                        style="border-radius:50%;width:56px;height:56px;">
                        <i class="fa fa-play fa-lg"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-7">
                <h2 class="fw-bold mb-2" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                    {{ \App\Helpers\TranslationHelper::getLocalizedContent($course->name, $course->name_ar) }}
                </h2>
                <div class="text-muted mb-2">{{ $course->instructor->name ?? __('Instructor') }}</div>
                <div class="mb-3" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                    {{ \App\Helpers\TranslationHelper::getLocalizedContent($course->description, $course->description_ar) }}
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar bg-orange" role="progressbar"
                                style="width: {{ $userEnrollment->progress_percentage ?? 0 }}%;"
                                aria-valuenow="{{ $userEnrollment->progress_percentage ?? 0 }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <i class="fa fa-award ms-3 text-warning" style="font-size:1.5rem;"></i>
                </div>
                <button class="btn btn-orange px-4 py-2 fw-bold">{{ __('Continue To Lecture') }}</button>
            </div>
        </div>
    </div>
</section>
