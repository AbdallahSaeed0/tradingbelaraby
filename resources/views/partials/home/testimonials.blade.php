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
                        
                        @if ($testimonial->getDisplayContent())
                            <p class="testimonial-text mb-4">{{ $testimonial->getDisplayContent() }}</p>
                        @endif

                        <!-- Voice Recording Player -->
                        @if ($testimonial->voice_playback_url)
                            <div class="testimonial-voice-wrapper mb-4">
                                <div class="testimonial-voice-header mb-2">
                                    <i class="fas fa-microphone text-warning me-2"></i>
                                    <span class="text-muted small">{{ custom_trans('Voice Testimonial', 'front') }}</span>
                                </div>
                                <div class="testimonial-voice-player">
                                    <audio controls class="testimonial-audio-player">
                                        <source src="{{ $testimonial->voice_playback_url }}" type="audio/mpeg">
                                        <source src="{{ $testimonial->voice_playback_url }}" type="audio/wav">
                                        <source src="{{ $testimonial->voice_playback_url }}" type="audio/mp4">
                                        <source src="{{ $testimonial->voice_playback_url }}" type="audio/ogg">
                                        {{ custom_trans('Your browser does not support the audio element.', 'front') }}
                                    </audio>
                                </div>
                            </div>
                        @endif

                        <!-- Rating Stars -->
                        <div class="testimonial-rating mb-3">
                            <div class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $testimonial->rating ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <div class="testimonial-avatar-wrapper d-flex justify-content-center mb-2">
                            <img src="{{ $testimonial->avatar_url }}" class="testimonial-avatar"
                                alt="{{ $testimonial->getDisplayName() }}">
                        </div>
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
                        <div class="testimonial-avatar-wrapper d-flex justify-content-center mb-2">
                            <img src="{{ $t['img'] }}" class="testimonial-avatar" alt="{{ $t['name'] }}">
                        </div>
                        <h5 class="mb-0">{{ $t['name'] }}</h5>
                        <small class="text-muted">{{ $t['role'] }}</small>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>
