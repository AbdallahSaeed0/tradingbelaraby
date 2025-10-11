<div class="instructor-section">
    @if ($course->instructors && $course->instructors->count() > 0)
        @foreach ($course->instructors as $instructor)
            <div class="d-flex align-items-start mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                <img src="{{ $instructor->avatar ? asset('storage/' . $instructor->avatar) : 'https://eclass.mediacity.co.in/demo2/public/images/user_img/159116543729.jpg' }}"
                    alt="{{ $instructor->name }}" class="instructor-avatar rounded-circle me-3" width="64"
                    height="64">
                <div>
                    <h4 class="fw-bold mb-1">
                        <a href="{{ route('instructor.show', $instructor->id) }}" class="text-decoration-none text-dark">
                            {{ $instructor->name }}
                        </a>
                    </h4>
                    <p class="text-muted mb-0">{{ $instructor->email ?? '' }}</p>
                    @if ($instructor->bio)
                        <p class="instructor-description mt-2">
                            {{ $instructor->bio }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    @elseif($course->instructor)
        <!-- Fallback to legacy single instructor -->
        <div class="d-flex align-items-start mb-3">
            <img src="{{ $course->instructor->avatar ? asset('storage/' . $course->instructor->avatar) : 'https://eclass.mediacity.co.in/demo2/public/images/user_img/159116543729.jpg' }}"
                alt="{{ $course->instructor->name }}" class="instructor-avatar rounded-circle me-3" width="64"
                height="64">
            <div>
                <h4 class="fw-bold mb-1">
                    <a href="{{ route('instructor.show', $course->instructor->id) }}"
                        class="text-decoration-none text-dark">
                        {{ $course->instructor->name }}
                    </a>
                </h4>
                <p class="text-muted mb-0">{{ $course->instructor->email ?? '' }}</p>
            </div>
        </div>
        <p class="instructor-description">
            {{ $course->instructor->bio }}
        </p>
    @endif
</div>
