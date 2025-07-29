<div class="course-content-topbar d-flex justify-content-between align-items-center px-4 py-3">
    <div class="course-content-title fw-bold">{{ $course->name }}</div>
    <div class="d-flex gap-2">
        <button class="btn btn-orange"><i class="fa fa-certificate me-2"></i>{{ __('Get Certificate') }}</button>
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-light course-details-btn">
            <i class="fa fa-arrow-right me-2"></i>{{ __('Course details') }}
        </a>
    </div>
</div>
