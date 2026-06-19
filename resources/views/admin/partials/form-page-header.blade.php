@php
    $title = $title ?? 'Form';
    $subtitle = $subtitle ?? '';
    $backUrl = $backUrl ?? url()->previous();
    $backLabel = $backLabel ?? 'Back';
    $formId = $formId ?? 'adminForm';
    $submitLabel = $submitLabel ?? 'Save';
@endphp

<div class="row mb-3 mb-lg-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center admin-form-page-header">
            <div>
                <h1 class="h3 mb-0">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="text-muted mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="admin-form-page-header__actions">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary me-2">
                    <i class="fa fa-arrow-left me-2"></i>{{ $backLabel }}
                </a>
                <button type="submit" form="{{ $formId }}" class="btn btn-primary">
                    <i class="fa fa-save me-2"></i>{{ $submitLabel }}
                </button>
            </div>
        </div>
    </div>
</div>
