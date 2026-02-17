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
                
                @if ($userEnrollment && $userEnrollment->status == 'completed')
                    @php
                        // Refresh course to get latest enable_certificate value
                        $course->refresh();
                    @endphp
                    @if ($course->enable_certificate)
                        @if ($userEnrollment->certificate_path)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-certificate me-2"></i>
                                <strong>{{ custom_trans('Congratulations!', 'front') }}</strong>
                                {{ custom_trans('You have completed this course and earned a certificate.', 'front') }}
                                <a href="{{ route('certificate.download', $userEnrollment->id) }}" class="btn btn-success btn-sm ms-2">
                                    <i class="fas fa-download me-1"></i>{{ custom_trans('Download Certificate', 'front') }}
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-certificate me-2"></i>
                                <strong>{{ custom_trans('Course Completed!', 'front') }}</strong>
                                {{ custom_trans('Get your certificate by entering your name.', 'front') }}
                                <a href="{{ route('certificate.request', $course->id) }}" class="btn btn-warning btn-sm ms-2">
                                    <i class="fas fa-certificate me-1"></i>{{ custom_trans('Get Certificate', 'front') }}
                                </a>
                            </div>
                        @endif
                    @endif
                @else
                    @php
                        $resumeLecture = $resumeLecture ?? null;
                    @endphp
                    <button type="button" class="btn btn-orange px-4 py-2 fw-bold continue-to-lecture-btn"
                        data-resume-lecture="{{ $resumeLecture ? json_encode($resumeLecture) : '' }}">
                        {{ custom_trans('Continue To Lecture', 'front') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</section>

@if (isset($resumeLecture) && $resumeLecture && !($userEnrollment && $userEnrollment->status == 'completed'))
{{-- Continue to Lecture Modal - rendered when resumeLecture exists --}}
<div class="modal fade" id="continueToLectureModal" tabindex="-1" aria-labelledby="continueToLectureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="continueToLectureModalLabel">{{ custom_trans('Continue To Lecture', 'front') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="continueToLectureTitle" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ custom_trans('Close', 'front') }}</button>
                <button type="button" class="btn btn-orange" id="continueToLectureGoBtn">{{ custom_trans('Continue', 'front') }}</button>
            </div>
        </div>
    </div>
</div>
@endif
