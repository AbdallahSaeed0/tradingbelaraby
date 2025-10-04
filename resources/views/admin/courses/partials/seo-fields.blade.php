{{-- SEO Fields Partial --}}
{{-- Usage: @include('admin.courses.partials.seo-fields', ['course' => $course]) --}}

@php
    $course = $course ?? null;
@endphp

<div class="form-section">
    <div class="section-header">
        <h5><i class="fa fa-search me-2"></i>SEO Settings</h5>
    </div>

    <div class="row">
        <!-- Meta Title -->
        <div class="col-md-12 mb-3">
            @include('admin.courses.partials.multilingual-fields', [
                'fieldName' => 'meta_title',
                'label' => 'Meta Title',
                'type' => 'input',
                'required' => false,
                'placeholder' => 'Enter meta title for search engines',
                'value' => $course->meta_title ?? '',
                'valueAr' => $course->meta_title_ar ?? '',
            ])
            <small class="form-text text-muted">Recommended length: 50-60 characters</small>
        </div>

        <!-- Meta Description -->
        <div class="col-md-12 mb-3">
            @include('admin.courses.partials.multilingual-fields', [
                'fieldName' => 'meta_description',
                'label' => 'Meta Description',
                'type' => 'textarea',
                'required' => false,
                'rows' => 3,
                'placeholder' => 'Enter meta description for search engines',
                'value' => $course->meta_description ?? '',
                'valueAr' => $course->meta_description_ar ?? '',
            ])
            <small class="form-text text-muted">Recommended length: 150-160 characters</small>
        </div>

        <!-- Meta Keywords -->
        <div class="col-md-12 mb-3">
            @include('admin.courses.partials.multilingual-fields', [
                'fieldName' => 'meta_keywords',
                'label' => 'Meta Keywords',
                'type' => 'input',
                'required' => false,
                'placeholder' => 'Enter keywords separated by commas',
                'value' => $course->meta_keywords ?? '',
                'valueAr' => $course->meta_keywords_ar ?? '',
            ])
            <small class="form-text text-muted">Separate keywords with commas</small>
        </div>


        <!-- Default Language -->
        <div class="col-md-12 mb-3">
            <label for="default_language" class="form-label">Default Language</label>
            <select class="form-select @error('default_language') is-invalid @enderror" id="default_language"
                name="default_language">
                <option value="en"
                    {{ old('default_language', $course->default_language ?? 'en') == 'en' ? 'selected' : '' }}>
                    English
                </option>
                <option value="ar"
                    {{ old('default_language', $course->default_language ?? '') == 'ar' ? 'selected' : '' }}>
                    العربية
                </option>
            </select>
            @error('default_language')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

</div>
