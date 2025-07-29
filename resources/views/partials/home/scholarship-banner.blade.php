<!-- Scholarship Programs Banner Section -->
@php
    $scholarshipBanner = \App\Models\ScholarshipBanner::active()->first();
@endphp

@if ($scholarshipBanner)
    <section class="scholarship-banner-section">
        <div class="scholarship-banner-overlay">
            <div class="scholarship-banner-content text-center">
                <h2 class="scholarship-banner-title">
                    {{ get_current_language_code() === 'ar' && $scholarshipBanner->title_ar ? $scholarshipBanner->title_ar : $scholarshipBanner->title }}
                </h2>
                <a href="{{ $scholarshipBanner->button_url ?: '#' }}" class="scholarship-banner-btn">
                    {{ get_current_language_code() === 'ar' && $scholarshipBanner->button_text_ar ? $scholarshipBanner->button_text_ar : $scholarshipBanner->button_text }}
                </a>
            </div>
        </div>
    </section>
@else
    <!-- Fallback to original hardcoded content -->
    <section class="scholarship-banner-section">
        <div class="scholarship-banner-overlay">
            <div class="scholarship-banner-content text-center">
                <h2 class="scholarship-banner-title">Scholarship Programs</h2>
                <a href="#" class="scholarship-banner-btn">Become An Instructor</a>
            </div>
        </div>
    </section>
@endif
