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
                <h2 class="fw-bold mb-2">{{ $course->name }}</h2>
                <div class="text-muted mb-2">{{ $course->instructor->name ?? __('Instructor') }}</div>
                <div class="mb-3">{{ $course->description }}</div>
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

<!-- Course Info Section -->
<div class="container py-5">
    <h2 class="fw-bold">{{ __('About this course') }}</h2>
    <p class="text-muted">{{ $course->description }}</p>

    <hr class="my-4">

    <!-- Course Stats -->
    <div class="course-stats mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <span class="stats-label">{{ __('By the numbers') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <span class="stats-label">{{ __('students enrolled') }}:</span>
                    <span class="stats-value ms-2">{{ $course->enrolled_students ?? 0 }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <span class="stats-label">{{ __('Classes') }}:</span>
                    <span
                        class="stats-value ms-2">{{ $course->sections->sum(function ($section) {return $section->lectures->count();}) }}</span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <span class="stats-label">{{ __('Languages') }}:</span>
                    <span class="stats-value ms-2">{{ $course->language ?? __('English') }}</span>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- Course Description -->
    <div class="row">
        <div class="col-md-3">
            <h5 class="fw-bold">{{ __('Description') }}</h5>
        </div>
        <div class="col-md-9">
            <h3 class="fw-bold mb-3">{{ __('About this course') }}</h3>
            <p class="text-muted mb-4">{{ $course->description }}</p>

            <h4 class="fw-bold mb-3">{{ __('Description') }}</h4>
            <div class="course-description">
                {!! nl2br(e($course->description)) !!}
            </div>
        </div>
    </div>
</div>
