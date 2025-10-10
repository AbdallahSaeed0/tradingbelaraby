<!-- Course Sections -->
<section class="course-sections py-5 bg-light">
    <div class="container">
        <!-- Latest Courses Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-2">
                            <i class="fas fa-clock text-primary me-2"></i>Latest Courses
                        </h3>
                        <p class="text-muted mb-0">Discover our newest courses</p>
                    </div>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @forelse($latestCourses ?? [] as $course)
                        <div class="col-lg-4 col-md-6">
                            <div class="course-card h-100 shadow-sm">
                                <div class="course-image">
                                    @if ($course->image)
                                        <img src="{{ $course->image_url }}" alt="{{ $course->name }}" class="img-fluid">
                                    @else
                                        <div class="course-placeholder">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    @endif

                                    @if ($course->is_featured)
                                        <span class="badge bg-warning position-absolute top-0 start-0 m-2">
                                            <i class="fas fa-star me-1"></i>Featured
                                        </span>
                                    @endif

                                    <div class="course-overlay">
                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Course
                                        </a>
                                    </div>
                                </div>

                                <div class="course-content p-3">
                                    <div class="course-category mb-2">
                                        <span class="badge bg-light text-dark">
                                            <i
                                                class="fas fa-folder me-1"></i>{{ \App\Helpers\TranslationHelper::getLocalizedContent($course->category->name ?? '', $course->category->name_ar ?? '') ?: 'Uncategorized' }}
                                        </span>
                                    </div>

                                    <h5 class="course-title mb-2">
                                        <a href="{{ route('courses.show', $course) }}"
                                            class="text-decoration-none text-dark">
                                            {{ Str::limit($course->name, 50) }}
                                        </a>
                                    </h5>

                                    <p class="course-description text-muted small mb-3">
                                        {{ Str::limit($course->description, 80) }}
                                    </p>

                                    <div class="course-meta d-flex justify-content-between align-items-center">
                                        <div class="course-instructor">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                @if ($course->instructors && $course->instructors->count() > 0)
                                                    {{ $course->instructors->pluck('name')->take(2)->join(', ') }}
                                                    @if ($course->instructors->count() > 2)
                                                        +{{ $course->instructors->count() - 2 }}
                                                    @endif
                                                @else
                                                    {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="course-rating">
                                            @if ($course->average_rating > 0)
                                                <small class="text-warning">
                                                    <i class="fas fa-star"></i>
                                                    {{ number_format($course->average_rating, 1) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="course-footer mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="course-price">
                                                @if ($course->is_free)
                                                    <span class="text-success fw-bold">Free</span>
                                                @else
                                                    <span
                                                        class="text-primary fw-bold">${{ number_format($course->price, 2) }}</span>
                                                @endif
                                            </div>
                                            <div class="course-enrollments">
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $course->enrolled_students ?? 0 }} enrolled
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No courses available yet. Check back soon!
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Popular Courses Section -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-2">
                            <i class="fas fa-fire text-danger me-2"></i>Popular Courses
                        </h3>
                        <p class="text-muted mb-0">Most enrolled courses by students</p>
                    </div>
                    <a href="{{ route('courses.index') }}" class="btn btn-outline-danger">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @forelse($popularCourses ?? [] as $course)
                        <div class="col-lg-4 col-md-6">
                            <div class="course-card h-100 shadow-sm">
                                <div class="course-image">
                                    @if ($course->image)
                                        <img src="{{ $course->image_url }}" alt="{{ $course->name }}"
                                            class="img-fluid">
                                    @else
                                        <div class="course-placeholder">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                    @endif

                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                                        <i class="fas fa-fire me-1"></i>Popular
                                    </span>

                                    <div class="course-overlay">
                                        <a href="{{ route('courses.show', $course) }}" class="btn btn-danger btn-sm">
                                            <i class="fas fa-eye me-1"></i>View Course
                                        </a>
                                    </div>
                                </div>

                                <div class="course-content p-3">
                                    <div class="course-category mb-2">
                                        <span class="badge bg-light text-dark">
                                            <i
                                                class="fas fa-folder me-1"></i>{{ \App\Helpers\TranslationHelper::getLocalizedContent($course->category->name ?? '', $course->category->name_ar ?? '') ?: 'Uncategorized' }}
                                        </span>
                                    </div>

                                    <h5 class="course-title mb-2">
                                        <a href="{{ route('courses.show', $course) }}"
                                            class="text-decoration-none text-dark">
                                            {{ Str::limit($course->name, 50) }}
                                        </a>
                                    </h5>

                                    <p class="course-description text-muted small mb-3">
                                        {{ Str::limit($course->description, 80) }}
                                    </p>

                                    <div class="course-meta d-flex justify-content-between align-items-center">
                                        <div class="course-instructor">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                @if ($course->instructors && $course->instructors->count() > 0)
                                                    {{ $course->instructors->pluck('name')->take(2)->join(', ') }}
                                                    @if ($course->instructors->count() > 2)
                                                        +{{ $course->instructors->count() - 2 }}
                                                    @endif
                                                @else
                                                    {{ $course->instructor->name ?? 'Unknown Instructor' }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="course-rating">
                                            @if ($course->average_rating > 0)
                                                <small class="text-warning">
                                                    <i class="fas fa-star"></i>
                                                    {{ number_format($course->average_rating, 1) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="course-footer mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="course-price">
                                                @if ($course->is_free)
                                                    <span class="text-success fw-bold">Free</span>
                                                @else
                                                    <span
                                                        class="text-primary fw-bold">${{ number_format($course->price, 2) }}</span>
                                                @endif
                                            </div>
                                            <div class="course-enrollments">
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $course->enrolled_students ?? 0 }} enrolled
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No popular courses available yet.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
