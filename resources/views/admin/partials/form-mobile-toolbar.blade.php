@php
    $formId = $formId ?? 'adminForm';
    $submitLabel = $submitLabel ?? 'Save';
    $backUrl = $backUrl ?? url()->previous();
    $backLabel = $backLabel ?? 'Back';
@endphp

<div class="admin-form-mobile-toolbar d-lg-none">
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i>{{ $backLabel }}
    </a>
    <button type="submit" form="{{ $formId }}" class="btn btn-primary btn-sm">
        <i class="fa fa-save me-1"></i>{{ $submitLabel }}
    </button>
</div>
