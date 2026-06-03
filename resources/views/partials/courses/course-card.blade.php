<!-- Course Card Component -->
@php
    $lessonsCount = $course->total_lessons_count
        ?? $course->total_lessons
        ?? $course->lessons_count
        ?? 0;
    $studentsCount = $course->enrolled_students ?? $course->students_count ?? 0;
    $durationHours = null;
    if ($course->total_duration_minutes) {
        $durationHours = max(1, (int) round($course->total_duration_minutes / 60));
    } elseif (is_numeric($course->duration)) {
        $durationHours = (int) $course->duration;
    } elseif (preg_match('/(\d+)/', (string) ($course->duration ?? ''), $durationMatch)) {
        $durationHours = (int) $durationMatch[1];
    }
@endphp
<div class="course-card bg-white rounded-4 shadow-sm h-100 overflow-hidden">
    <!-- Course Image -->
    <div class="course-image position-relative">
        <a href="{{ route('courses.show', $course->id) }}" class="d-block text-decoration-none">
            <img src="{{ $course->image_url }}" alt="{{ $course->localized_name }}" class="w-100 img-h-200">
        </a>

        <!-- Course Badge -->
        @if ($course->is_featured ?? false)
            <div class="position-absolute top-0 start-0 m-3">
                <span class="badge bg-warning">{{ custom_trans('featured', 'front') }}</span>
            </div>
        @endif

        <!-- Course Price -->
        <div class="position-absolute top-0 end-0 m-3">
            @if ($course->is_free || ($course->price ?? 0) <= 0)
                <span class="badge bg-success fs-6">{{ custom_trans('free', 'front') }}</span>
            @else
                <span class="badge bg-info text-white fs-6">{{ $course->formatted_price }}</span>
            @endif
        </div>

        <!-- Course Overlay -->
        <div
            class="course-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 transition-opacity">
            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-orange">
                <i class="fa fa-eye me-2"></i>{{ custom_trans('View Course', 'front') }}
            </a>
        </div>
    </div>

    <!-- Course Content -->
    <div class="course-content p-4">
        <!-- Course Category -->
        <div class="course-category mb-2">
            <span class="badge bg-light text-dark">
                {{ \App\Helpers\TranslationHelper::getLocalizedContent($course->category->name ?? '', $course->category->name_ar ?? '') ?: custom_trans('Not Found', 'front') }}
            </span>
        </div>

        <!-- Course Title -->
        <h5 class="course-title fw-bold mb-2">
            <a href="{{ route('courses.show', $course->id) }}" class="text-dark text-decoration-none">
                {{ Str::limit($course->localized_name, 50) }}
            </a>
        </h5>

        <!-- Course Description -->
        <p class="course-description text-muted mb-3">
            {{ Str::limit($course->localized_description ?? custom_trans('Not Found', 'front'), 100) }}
        </p>

        <!-- Course Meta -->
        <div class="course-meta d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <i class="fa fa-user text-muted me-1"></i>
                <small class="text-muted">
                    @if ($course->instructors && $course->instructors->count() > 0)
                        {{ $course->instructors->pluck('name')->take(2)->join(', ') }}
                        @if ($course->instructors->count() > 2)
                            <span>+{{ $course->instructors->count() - 2 }}</span>
                        @endif
                    @else
                        {{ $course->instructor->name ?? custom_trans('Not Found', 'front') }}
                    @endif
                </small>
            </div>
            <div class="d-flex align-items-center">
                <i class="fa fa-star text-warning me-1"></i>
                <small class="text-muted">{{ $course->average_rating ?? $course->rating ?? 0 }}/5</small>
            </div>
        </div>

        <!-- Course Stats -->
        <div class="course-stats d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <i class="fa fa-play-circle text-muted me-1"></i>
                <small class="text-muted">{{ $lessonsCount }} {{ custom_trans('lessons', 'front') }}</small>
            </div>
            <div class="d-flex align-items-center">
                <i class="fa fa-clock text-muted me-1"></i>
                <small class="text-muted">
                    @if ($durationHours)
                        {{ $durationHours }} {{ custom_trans($durationHours === 1 ? 'hour' : 'hours', 'front') }}
                    @elseif ($course->duration)
                        {{ $course->duration }}
                    @else
                        {{ custom_trans('Not Found', 'front') }}
                    @endif
                </small>
            </div>
            <div class="d-flex align-items-center">
                <i class="fa fa-users text-muted me-1"></i>
                <small class="text-muted">{{ $studentsCount }} {{ custom_trans('students', 'front') }}</small>
            </div>
        </div>

        <!-- Course Actions -->
        <div class="course-actions d-flex gap-2">
            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-primary flex-fill">
                <i class="fa fa-eye me-1"></i>{{ custom_trans('View', 'front') }}
            </a>
            @auth
                <!-- Wishlist Button -->
                <button class="btn btn-outline-danger wishlist-btn" data-course-id="{{ $course->id }}"
                    data-in-wishlist="{{ auth()->user()->hasInWishlist($course) ? 'true' : 'false' }}">
                    <i class="fa fa-heart {{ auth()->user()->hasInWishlist($course) ? 'text-danger' : '' }}"></i>
                </button>

                @if (auth()->user()->enrollments()->where('course_id', $course->id)->exists())
                    <a href="{{ route('courses.learn', $course->id) }}" class="btn btn-success flex-fill">
                        <i class="fa fa-graduation-cap me-1"></i>{{ custom_trans('go_to_course', 'front') }}
                    </a>
                @else
                    <button class="btn btn-orange flex-fill enroll-btn"
                        data-course-id="{{ $course->id }}"
                        data-enroll-type="{{ $course->price > 0 ? 'paid' : 'free' }}">
                        <i class="fa fa-graduation-cap me-1"></i>{{ custom_trans('enroll_now', 'front') }}
                    </button>
                @endif
            @else
                @if ($course->price > 0)
                    <a href="{{ route('login') }}" class="btn btn-orange flex-fill">
                        <i class="fa fa-graduation-cap me-1"></i>{{ custom_trans('enroll_now', 'front') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-success flex-fill">
                        <i class="fa fa-play me-1"></i>{{ custom_trans('start_free', 'front') }}
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>
