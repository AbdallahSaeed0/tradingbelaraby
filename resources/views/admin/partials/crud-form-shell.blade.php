@php
    $title = $title ?? 'Form';
    $subtitle = $subtitle ?? '';
    $backUrl = $backUrl ?? url()->previous();
    $backLabel = $backLabel ?? 'Back';
    $formId = $formId ?? 'adminForm';
    $submitLabel = $submitLabel ?? 'Save';
    $sections = $sections ?? [];
@endphp

@include('admin.partials.form-page-header', compact('title', 'subtitle', 'backUrl', 'backLabel', 'formId', 'submitLabel'))

@if (count($sections))
    @include('admin.partials.form-section-nav', ['sections' => $sections])
@endif

@include('admin.partials.form-mobile-toolbar', compact('formId', 'submitLabel', 'backUrl', 'backLabel'))
