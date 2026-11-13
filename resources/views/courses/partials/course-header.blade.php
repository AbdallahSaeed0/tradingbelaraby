<div class="course-content-topbar d-flex justify-content-between align-items-center px-4 py-3">
    <div class="course-content-title fw-bold" @if (\App\Helpers\TranslationHelper::getFrontendLanguage()->code === 'ar') dir="rtl" @endif>
        {{ $course->localized_name }}
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-light course-details-btn">
            <i class="fas fa-circle-info" aria-hidden="true"></i><span>{{ custom_trans('Course details', 'front') }}</span>
        </a>
    </div>
</div>
