<section class="course-content-hero py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-5 text-center">
                <div class="course-content-media-wrap position-relative">
                    <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}"
                        class="img-fluid rounded-3 course-content-media-img cursor-pointer">
                    <button
                        class="btn btn-orange course-content-play-btn position-absolute top-50 start-50 translate-middle play-btn-circle">
                        <i class="fa fa-play fa-lg"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-7">
                <h2 class="fw-bold mb-2" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                    {{ $course->localized_name }}
                </h2>
                <div class="text-muted mb-2">{{ $course->instructor->name ?? custom_trans('Instructor', 'front') }}</div>
                <div class="mb-3" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
                    {{ $course->localized_description }}
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <div class="progress progress-h-8">
                            <div class="progress-bar bg-orange" role="progressbar"
                                style="width: {{ $userEnrollment->progress_percentage ?? 0 }}%;"
                                aria-valuenow="{{ $userEnrollment->progress_percentage ?? 0 }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                    <i class="fa fa-award ms-3 text-warning fs-1-5rem"></i>
                </div>
                <button class="btn btn-orange px-4 py-2 fw-bold">{{ custom_trans('Continue To Lecture', 'front') }}</button>
            </div>
        </div>
    </div>
</section>
