@php
    $title = $title ?? 'Details';
    $subtitle = $subtitle ?? '';
    $backUrl = $backUrl ?? url()->previous();
    $backLabel = $backLabel ?? 'Back';
    $primaryUrl = $primaryUrl ?? null;
    $primaryLabel = $primaryLabel ?? 'Edit';
    $primaryClass = $primaryClass ?? 'btn-primary';
    $extraActions = $extraActions ?? '';
@endphp

<div class="row mb-3 mb-lg-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center admin-detail-page-header">
            <div>
                <h1 class="h3 mb-0">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="text-muted mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="admin-detail-header-actions">
                <a href="{{ $backUrl }}" class="btn btn-outline-secondary me-2 admin-detail-back-url">
                    <i class="fa fa-arrow-left me-2"></i>{{ $backLabel }}
                </a>
                @if ($primaryUrl)
                    <a href="{{ $primaryUrl }}" class="btn {{ $primaryClass }}">
                        <i class="fa fa-edit me-2"></i>{{ $primaryLabel }}
                    </a>
                @endif
                {!! $extraActions !!}
            </div>
        </div>
        <a href="{{ $backUrl }}" class="visually-hidden admin-detail-back-url">{{ $backLabel }}</a>
    </div>
</div>
