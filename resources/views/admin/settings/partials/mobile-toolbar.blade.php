@php
    $formId = $formId ?? null;
    $submitLabel = $submitLabel ?? 'Save';
    $backUrl = $backUrl ?? route('admin.settings.index');
    $backLabel = $backLabel ?? 'Settings';
    $actionType = $actionType ?? 'submit';
    $actionTarget = $actionTarget ?? null;
    $actionLabel = $actionLabel ?? $submitLabel;
    $actionClass = $actionClass ?? 'btn-primary';
@endphp

<div class="admin-settings-mobile-toolbar d-lg-none" id="settingsMobileToolbar">
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left me-1"></i>{{ $backLabel }}
    </a>
    @if ($actionType === 'submit' && $formId)
        <button type="submit" form="{{ $formId }}" class="btn {{ $actionClass }} btn-sm">
            <i class="fa fa-save me-1"></i>{{ $submitLabel }}
        </button>
    @elseif ($actionType === 'button' && $actionTarget)
        <button type="button" class="btn {{ $actionClass }} btn-sm" data-bs-toggle="modal"
            data-bs-target="{{ $actionTarget }}">
            <i class="fa fa-plus me-1"></i>{{ $actionLabel }}
        </button>
    @elseif ($actionType === 'link' && $actionTarget)
        <a href="{{ $actionTarget }}" class="btn {{ $actionClass }} btn-sm">
            {{ $actionLabel }}
        </a>
    @endif
</div>
