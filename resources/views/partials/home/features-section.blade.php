<!-- Features Section -->
@php
    /**
     * Resolve a FontAwesome icon class for a feature.
     * Priority:
     *   1. icon field already looks like an FA class (contains a space, e.g. "fas fa-star")
     *   2. Title/title_ar keyword mapping
     *   3. Default fallback icon
     */
    function resolveFeatureIcon($feature): string
    {
        $icon = $feature->icon ?? '';

        // Already an FA class
        if (str_contains($icon, ' ') && (str_starts_with($icon, 'fas ') || str_starts_with($icon, 'far ') || str_starts_with($icon, 'fab ') || str_starts_with($icon, 'fal ') || str_starts_with($icon, 'fad '))) {
            return $icon;
        }

        // Keyword → FA icon map (English + Arabic keywords)
        $map = [
            'instructor'   => 'fas fa-chalkboard-teacher',
            'teacher'      => 'fas fa-chalkboard-teacher',
            'مدرب'         => 'fas fa-chalkboard-teacher',
            'student'      => 'fas fa-user-graduate',
            'طالب'         => 'fas fa-user-graduate',
            'متدرب'        => 'fas fa-user-graduate',
            'سعداء'        => 'fas fa-smile',
            'happy'        => 'fas fa-smile',
            'live'         => 'fas fa-broadcast-tower',
            'مباشر'        => 'fas fa-broadcast-tower',
            'مذاع'         => 'fas fa-broadcast-tower',
            'video'        => 'fas fa-play-circle',
            'فيديو'        => 'fas fa-play-circle',
            'مقاطع'        => 'fas fa-play-circle',
            'recorded'     => 'fas fa-play-circle',
            'certificate'  => 'fas fa-certificate',
            'شهادة'        => 'fas fa-certificate',
            'course'       => 'fas fa-graduation-cap',
            'دورة'         => 'fas fa-graduation-cap',
            'support'      => 'fas fa-headset',
            'دعم'          => 'fas fa-headset',
        ];

        $haystack = strtolower(($feature->title ?? '') . ' ' . ($feature->title_ar ?? ''));
        foreach ($map as $keyword => $faClass) {
            if (str_contains($haystack, strtolower($keyword))) {
                return $faClass;
            }
        }

        return 'fas fa-star'; // generic fallback
    }
@endphp

<section class="features bg-white" aria-labelledby="features-heading">
    <div class="container">
        <h2 id="features-heading" class="text-center fw-bold mb-4">
            {{ custom_trans('Our Features', 'front') }}
        </h2>
        <div class="row g-4 justify-content-center">
            @php $features = \App\Models\Feature::active()->ordered()->get(); @endphp

            @forelse($features as $feature)
                @php $faIcon = resolveFeatureIcon($feature); @endphp
                <div class="col-md-3 col-sm-6">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <i class="{{ $faIcon }} feature-fa-icon" aria-hidden="true"></i>
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
                {{-- Fallback hardcoded features (no DB data) --}}
                @foreach ([
                    ['icon' => 'fas fa-play-circle',          'num' => 63, 'en' => 'Recorded Videos',       'ar' => 'مقاطع فيديو مسجلة',         'en_desc' => 'High-quality recorded lessons available anytime, on any device.', 'ar_desc' => 'دروس مسجلة عالية الجودة متاحة في أي وقت وعلى أي جهاز.'],
                    ['icon' => 'fas fa-broadcast-tower',      'num' => 94, 'en' => 'Live Classes',           'ar' => 'الدورات المباشرة',            'en_desc' => 'Interactive live sessions delivered over the internet via Zoom.', 'ar_desc' => 'جلسات مباشرة تفاعلية عبر الإنترنت باستخدام Zoom.'],
                    ['icon' => 'fas fa-smile',                'num' => 84, 'en' => 'Happy Trainees',         'ar' => 'متدربين سعداء',              'en_desc' => 'Thousands of satisfied learners who achieved their financial goals.', 'ar_desc' => 'آلاف المتدربين الراضين الذين حققوا أهدافهم المالية.'],
                    ['icon' => 'fas fa-chalkboard-teacher',  'num' => 45, 'en' => 'Skillful Instructors',   'ar' => 'مدربين ماهرين',              'en_desc' => 'Certified instructors with 10+ years of financial markets experience.', 'ar_desc' => 'مدربون معتمدون بخبرة تزيد عن عشر سنوات في الأسواق المالية.'],
                ] as $fb)
                <div class="col-md-3 col-sm-6">
                    <div class="card feature-card border-0 text-center p-4 h-100">
                        <div class="position-relative d-inline-block mb-3">
                            <span class="feature-icon-circle d-flex align-items-center justify-content-center mx-auto">
                                <i class="{{ $fb['icon'] }} feature-fa-icon" aria-hidden="true"></i>
                            </span>
                            <span class="feature-number-circle">{{ $fb['num'] }}</span>
                        </div>
                        <h3 class="h5 fw-bold mb-2">
                            {{ get_current_language_code() === 'ar' ? $fb['ar'] : $fb['en'] }}
                        </h3>
                        <p class="mb-0 text-muted">
                            {{ get_current_language_code() === 'ar' ? $fb['ar_desc'] : $fb['en_desc'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>
