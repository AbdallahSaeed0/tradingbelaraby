@php
    $formId = $formId ?? 'courseForm';
    $submitLabel = $submitLabel ?? 'Save Course';
    $backUrl = $backUrl ?? route('admin.courses.index');
    $statusValue = $statusValue ?? old('status', 'draft');
@endphp

@include('admin.partials.form-section-nav', [
    'sections' => [
        ['id' => 'section-publish', 'label' => 'Publish', 'icon' => 'fa-rocket'],
        ['id' => 'section-basics', 'label' => 'Basics', 'icon' => 'fa-info-circle'],
        ['id' => 'section-content', 'label' => 'Content', 'icon' => 'fa-list'],
        ['id' => 'section-learn', 'label' => 'Learning', 'icon' => 'fa-graduation-cap'],
        ['id' => 'section-faq', 'label' => 'FAQ', 'icon' => 'fa-question-circle'],
        ['id' => 'section-seo', 'label' => 'SEO', 'icon' => 'fa-search'],
    ],
])

<div class="admin-form-publish-summary d-lg-none" id="mobilePublishSummary" aria-live="polite">
    <span class="badge bg-secondary" data-summary="status">{{ ucfirst($statusValue) }}</span>
    <span class="badge bg-light text-dark border" data-summary="price">Pricing</span>
    <span class="badge bg-light text-dark border" data-summary="featured">Not featured</span>
</div>

@include('admin.partials.form-mobile-toolbar', [
    'formId' => $formId,
    'submitLabel' => $submitLabel,
    'backUrl' => $backUrl,
    'backLabel' => 'Courses',
])
