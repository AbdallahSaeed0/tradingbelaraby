<!-- Features Section -->
<section class="features bg-white" aria-labelledby="features-heading">
    <div class="container">
        <h2 id="features-heading" class="text-center fw-bold mb-4">{{ custom_trans('Our Features', 'front') ?? 'Our Features' }}</h2>
        <div class="row g-4 justify-content-center">
            @php
                $features = \App\Models\Feature::active()->ordered()->get();
            @endphp

            @forelse($features as $feature)
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="{{ $feature->icon_url }}" class="img-fluid wh-56" alt="{{ $feature->title }}" width="56" height="56">
                            </span>
                            <span class="feature-number-circle">{{ $feature->number }}</span>
                        </div>
                        <h3 class="h5 fw-bold mb-2">
                            {{ get_current_language_code() === 'ar' && $feature->title_ar ? $feature->title_ar : $feature->title }}
                        </h3>
                        <p class="mb-0 text-muted">
                            {{ get_current_language_code() === 'ar' && $feature->description_ar ? $feature->description_ar : $feature->description }}
                        </p>
                    </div>
                </div>
            @empty
                <!-- Fallback to default features if no data in database -->
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="{{ asset('images/icon-instructor.png') }}"
                                    class="img-fluid wh-56" alt="Instructor" width="56" height="56">
                            </span>
                            <span class="feature-number-circle">45</span>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Skillful Instructor</h3>
                        <p class="mb-0 text-muted">Skillful Instructor is a LMS designed to help instructors create,
                            manage, and deliver online courses.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="{{ asset('images/icon-student.png') }}"
                                    class="img-fluid wh-56" alt="Student" width="56" height="56">
                            </span>
                            <span class="feature-number-circle">84</span>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Happy Student</h3>
                        <p class="mb-0 text-muted">Happy Student is likely a company or brand name that provides
                            educational services, although without further context.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="{{ asset('images/icon-live.png') }}"
                                    class="img-fluid wh-56" alt="Live" width="56" height="56">
                            </span>
                            <span class="feature-number-circle">94</span>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Live Classes</h3>
                        <p class="mb-0 text-muted">Live classes (LMS) refer to educational or training sessions that are
                            delivered in real-time, usually over the internet.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <img src="{{ asset('images/icon-video.png') }}"
                                    class="img-fluid wh-56" alt="Video" width="56" height="56">
                            </span>
                            <span class="feature-number-circle">63</span>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Video</h3>
                        <p class="mb-0 text-muted">LMS videos refer to videos that are used as part of a (LMS) to
                            deliver educational content to students.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
