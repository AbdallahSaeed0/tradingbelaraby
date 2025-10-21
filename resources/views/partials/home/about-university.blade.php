<!-- About Our University Section -->
@php
    $aboutUniversity = \App\Models\AboutUniversity::active()->first();
    $features = \App\Models\AboutUniversityFeature::active()->ordered()->get();
@endphp

@if ($aboutUniversity)
    <section class="about-uni-section position-relative py-5 bg-light-f3">
        <div class="container position-relative z-2">
            <div class="row align-items-center">
                <!-- Left Image -->
                <div class="col-lg-6 mb-4 mb-lg-0 d-flex justify-content-center">
                    <img src="{{ $aboutUniversity->image_url }}"
                        alt="{{ get_current_language_code() === 'ar' && $aboutUniversity->title_ar ? $aboutUniversity->title_ar : $aboutUniversity->title }}"
                        class="img-fluid">
                </div>
                <!-- Text and Features -->
                <div class="col-lg-6">
                    <span class="text-warning fw-bold mb-2 d-block fs-11">
                        <i class="fas fa-graduation-cap"></i> {{ __('About Our University') }}
                    </span>
                    <h2 class="fw-bold mb-3 fs-25">
                        {{ get_current_language_code() === 'ar' && $aboutUniversity->title_ar ? $aboutUniversity->title_ar : $aboutUniversity->title }}
                    </h2>
                    @php
                        $description =
                            get_current_language_code() === 'ar' && $aboutUniversity->description_ar
                                ? $aboutUniversity->description_ar
                                : $aboutUniversity->description;
                        $descriptionLimit = 200;
                        $isLongDescription = strlen($description) > $descriptionLimit;
                        $shortDescription = $isLongDescription
                            ? substr($description, 0, $descriptionLimit) . '...'
                            : $description;
                    @endphp
                    <div class="about-description-wrapper mb-4">
                        <p class="text-blue-247 mb-2 about-description" id="aboutDescription">
                            <span class="short-text">{{ $shortDescription }}</span>
                            @if ($isLongDescription)
                                <span class="full-text d-none">{{ $description }}</span>
                            @endif
                        </p>
                        <button class="btn btn-link text-warning p-0 fw-semibold text-decoration-none read-more-btn"
                            onclick="toggleDescription()" id="readMoreBtn">
                            <i class="fas fa-arrow-right me-1"></i> {{ __('Read More') }}
                        </button>
                    </div>
                    <div class="row g-3">
                        @forelse($features as $feature)
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start">
                                    <span class="about-number me-3">{{ $feature->number }}</span>
                                    <div>
                                        <div class="fw-bold">
                                            {{ get_current_language_code() === 'ar' && $feature->title_ar ? $feature->title_ar : $feature->title }}
                                        </div>
                                        @php
                                            $featureDesc =
                                                get_current_language_code() === 'ar' && $feature->description_ar
                                                    ? $feature->description_ar
                                                    : $feature->description;
                                            $featureLimit = 80;
                                            $truncatedFeatureDesc =
                                                strlen($featureDesc) > $featureLimit
                                                    ? substr($featureDesc, 0, $featureLimit) . '...'
                                                    : $featureDesc;
                                        @endphp
                                        <div class="text-muted small" title="{{ $featureDesc }}">
                                            {{ $truncatedFeatureDesc }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Fallback to original hardcoded content -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start">
                                    <span class="about-number me-3">1</span>
                                    <div>
                                        <div class="fw-bold">Instructor involvement</div>
                                        <div class="text-muted small">Instructors are the primary facilitators in LMS
                                            courses.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start">
                                    <span class="about-number me-3">2</span>
                                    <div>
                                        <div class="fw-bold">Bundle Courses</div>
                                        <div class="text-muted small">Bundle courses are often created by subject matter
                                            experts.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start">
                                    <span class="about-number me-3">3</span>
                                    <div>
                                        <div class="fw-bold">Instructor Subscription</div>
                                        <div class="text-muted small">An Instructor Subscription in an LMS (Learning
                                            Management System).</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-start">
                                    <span class="about-number me-3">4</span>
                                    <div>
                                        <div class="fw-bold">Live Meetings</div>
                                        <div class="text-muted small">Live Meetings in an LMS (Learning Management
                                            System).</div>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <!-- Absolute background image on the right bottom -->
        @if ($aboutUniversity->background_image)
            <img src="{{ $aboutUniversity->background_image_url }}" alt="contact-bg-an-01"
                class="about-bg-img d-none d-md-block">
        @endif
    </section>

    <script>
        function toggleDescription() {
            const shortText = document.querySelector('#aboutDescription .short-text');
            const fullText = document.querySelector('#aboutDescription .full-text');
            const btn = document.getElementById('readMoreBtn');

            if (fullText && shortText) {
                if (fullText.classList.contains('d-none')) {
                    // Show full text
                    shortText.classList.add('d-none');
                    fullText.classList.remove('d-none');
                    btn.innerHTML = '<i class="fas fa-arrow-up me-1"></i> {{ __('Read Less') }}';
                } else {
                    // Show short text
                    shortText.classList.remove('d-none');
                    fullText.classList.add('d-none');
                    btn.innerHTML = '<i class="fas fa-arrow-right me-1"></i> {{ __('Read More') }}';
                }
            }
        }
    </script>@endif

