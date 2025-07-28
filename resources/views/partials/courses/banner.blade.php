<!-- Course Banner Section -->
<section class="course-banner-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"
                                class="text-white text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('categories') }}"
                                class="text-white text-decoration-none">Courses</a></li>
                        @if (isset($category))
                            <li class="breadcrumb-item"><a href="#"
                                    class="text-white text-decoration-none">{{ $category->name }}</a></li>
                        @endif
                        <li class="breadcrumb-item active" aria-current="page">
                            @if (isset($course))
                                {{ $course->name }}
                            @else
                                {{ $title ?? 'Course Details' }}
                            @endif
                        </li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-3">
                    @if (isset($course))
                        {{ $course->name }}
                    @else
                        {{ $title ?? 'Course Details' }}
                    @endif
                </h1>
                @if (isset($course) && $course->description)
                    <p class="lead mb-0">{{ Str::limit($course->description, 200) }}</p>
                @endif
            </div>
            <div class="col-lg-4 text-center">
                <div class="banner-stats">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="fw-bold mb-1">
                                    @if (isset($course))
                                        {{ $course->students_count ?? 0 }}
                                    @else
                                        0
                                    @endif
                                </h3>
                                <small>Students</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h3 class="fw-bold mb-1">
                                    @if (isset($course))
                                        {{ $course->lessons_count ?? 0 }}
                                    @else
                                        0
                                    @endif
                                </h3>
                                <small>Lessons</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
