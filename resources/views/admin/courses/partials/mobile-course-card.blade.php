@php
    $statusClasses = [
        'published' => 'bg-success',
        'draft' => 'bg-warning text-dark',
        'archived' => 'bg-secondary',
    ];
    $statusClass = $statusClasses[$course->status] ?? 'bg-secondary';
    $enrollmentCount = $course->enrollments_count ?? $course->enrollments->count();
    $primaryInstructor = $course->instructors->first();
@endphp

<article class="admin-course-mobile-card" data-course-id="{{ $course->id }}">
    <div class="admin-course-mobile-card__toolbar">
        <div class="form-check mb-0">
            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $course->id }}"
                aria-label="Select {{ $course->name }}">
        </div>
        <button type="button"
            class="badge {{ $statusClass }} status-badge border-0 admin-course-mobile-card__status"
            onclick="showStatusModal({{ $course->id }}, '{{ $course->status }}', [
                { value: 'published', label: 'Published' },
                { value: 'draft', label: 'Draft' },
                { value: 'archived', label: 'Archived' }
            ], '{{ route('admin.courses.update_status', $course->id) }}')">
            {{ ucfirst($course->status) }}
        </button>
    </div>

    <a href="{{ route('admin.courses.show', $course) }}" class="admin-course-mobile-card__hero">
        @if ($course->image)
            <img src="{{ $course->image_url }}" alt="{{ $course->name }}"
                class="admin-course-mobile-card__thumb">
        @else
            <div class="admin-course-mobile-card__thumb admin-course-mobile-card__thumb--placeholder">
                {{ strtoupper(substr($course->name, 0, 2)) }}
            </div>
        @endif
        <div class="admin-course-mobile-card__copy">
            <h3 class="admin-course-mobile-card__title">{{ $course->name }}</h3>
            @if ($course->description)
                <p class="admin-course-mobile-card__desc">{{ Str::limit(strip_tags($course->description), 140) }}</p>
            @endif
        </div>
    </a>

    <div class="admin-course-mobile-card__chips">
        @if ($course->category)
            <span class="badge bg-primary">{{ $course->category->name }}</span>
        @else
            <span class="badge bg-secondary">No category</span>
        @endif
        @if ($course->price > 0)
            <span class="badge bg-dark">{{ number_format($course->price, 2) }} SAR</span>
            @if ($course->original_price > $course->price)
                <span class="badge bg-light text-muted text-decoration-line-through">{{ number_format($course->original_price, 2) }} SAR</span>
            @endif
        @else
            <span class="badge bg-success">Free</span>
        @endif
    </div>

    <div class="admin-course-mobile-card__stats">
        <span><i class="fa fa-play-circle" aria-hidden="true"></i> {{ $course->total_lessons }} lessons</span>
        <span><i class="fa fa-clock" aria-hidden="true"></i> {{ $course->duration ?? '—' }}</span>
        <span><i class="fa fa-users" aria-hidden="true"></i> {{ $enrollmentCount }} enrolled</span>
    </div>

    <div class="admin-course-mobile-card__instructor">
        @if ($primaryInstructor)
            @if ($primaryInstructor->avatar)
                <img src="{{ asset('storage/' . $primaryInstructor->avatar) }}" alt="{{ $primaryInstructor->name }}"
                    class="admin-course-mobile-card__avatar" width="28" height="28">
            @else
                <span class="admin-course-mobile-card__avatar admin-course-mobile-card__avatar--placeholder">
                    {{ strtoupper(substr($primaryInstructor->name, 0, 2)) }}
                </span>
            @endif
            <span class="admin-course-mobile-card__instructor-name">{{ $primaryInstructor->name }}</span>
            @if ($course->instructors->count() > 1)
                <span class="text-muted small">+{{ $course->instructors->count() - 1 }} more</span>
            @endif
        @else
            <span class="text-muted small"><i class="fa fa-user-slash me-1"></i>No instructor</span>
        @endif
    </div>

    <div class="admin-course-mobile-card__footer">
        <div class="admin-course-mobile-card__date">
            <span>{{ $course->created_at->format('M d, Y') }}</span>
            <span class="text-muted">{{ $course->created_at->diffForHumans() }}</span>
        </div>
        @include('admin.courses.partials.course-row-actions', ['course' => $course])
    </div>
</article>
