<div class="instructor-section">
    <!-- Instructor Cover Image -->
    @if ($course->instructor->cover)
        <div class="instructor-cover mb-3">
            <img src="{{ $course->instructor->cover_url }}" alt="{{ $course->instructor->name }} Cover"
                class="img-fluid rounded">
        </div>
    @endif

    <div class="d-flex align-items-start mb-3">
        <img src="{{ $course->instructor->avatar ? asset('storage/' . $course->instructor->avatar) : 'https://eclass.mediacity.co.in/demo2/public/images/user_img/159116543729.jpg' }}"
            alt="{{ $course->instructor->name }}" class="instructor-avatar rounded-circle me-3" width="64"
            height="64">
        <div>
            <h4 class="fw-bold mb-1">{{ $course->instructor->name }}</h4>
            <p class="text-muted mb-0">{{ $course->instructor->email ?? '' }}</p>
        </div>
    </div>
    <p class="instructor-description">
        {{ $course->instructor->bio }}
    </p>
</div>
