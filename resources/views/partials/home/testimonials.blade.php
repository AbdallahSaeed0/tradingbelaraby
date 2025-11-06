<!-- Testimonial Slider Section -->
<section class="testimonial-section position-relative py-5">
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-01.png" alt="Left"
        class="testimonial-img-left d-none d-md-block">
    <img src="https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-02.png" alt="Right"
        class="testimonial-img-right d-none d-md-block">
    <div class="container">
        <div class="text-center mb-4">
            <span class="text-warning fw-bold d-block mb-2 fs-11">
                <i class="fas fa-graduation-cap"></i> {{ custom_trans('Testimonial', 'front') }}
            </span>
            <h2 class="fw-bold mb-3">{{ custom_trans('What Our Clients Says', 'front') }}</h2>
        </div>
        <div class="testimonial-slider">
            @php
                // Fetch active testimonials from the database
                $testimonials = \App\Models\Testimonial::active()->ordered()->get();
            @endphp

            @if ($testimonials->count() > 0)
                @foreach ($testimonials as $testimonial)
                    <div class="testimonial-card text-center p-4 mx-2">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-right fa-2x text-warning"></i>
                        </div>
                        <p class="testimonial-text mb-4">{{ $testimonial->getDisplayContent() }}</p>

                        <!-- Rating Stars -->
                        <div class="testimonial-rating mb-3">
                            <div class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $testimonial->rating ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <img src="{{ $testimonial->avatar_url }}" class="testimonial-avatar mb-2"
                            alt="{{ $testimonial->getDisplayName() }}" class="testimonial-avatar-img">
                        <h5 class="mb-0">{{ $testimonial->getDisplayName() }}</h5>
                        <small class="text-muted">{{ $testimonial->getDisplayPosition() }} at
                            {{ $testimonial->getDisplayCompany() }}</small>
                    </div>
                @endforeach
            @else
                <!-- Fallback content when no testimonials are available -->
                @php
                    $fallbackTestimonials = [
                        [
                            'img' => 'https://randomuser.me/api/portraits/men/32.jpg',
                            'name' => 'Marry Ieee',
                            'role' => 'Student',
                        ],
                        [
                            'img' => 'https://randomuser.me/api/portraits/women/44.jpg',
                            'name' => 'Kristin Joy',
                            'role' => 'Employee',
                        ],
                        [
                            'img' => 'https://randomuser.me/api/portraits/men/45.jpg',
                            'name' => 'Tom Hardy',
                            'role' => 'Assistant Director',
                        ],
                    ];
                @endphp
                @foreach ($fallbackTestimonials as $t)
                    <div class="testimonial-card text-center p-4 mx-2">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-right fa-2x text-warning"></i>
                        </div>
                        <p class="testimonial-text mb-4">
                            {{ custom_trans('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.', 'front') }}
                        </p>
                        <img src="{{ $t['img'] }}" class="testimonial-avatar mb-2" alt="{{ $t['name'] }}"
                            class="testimonial-avatar-img">
                        <h5 class="mb-0">{{ $t['name'] }}</h5>
                        <small class="text-muted">{{ $t['role'] }}</small>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>
