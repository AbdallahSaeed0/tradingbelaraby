@php
    $itemId = $itemId ?? null;
    $checkboxClass = $checkboxClass ?? 'row-checkbox';
    $checkboxValue = $checkboxValue ?? null;
    $checkboxLabel = $checkboxLabel ?? 'Select item';
    $checkboxExtraAttrs = $checkboxExtraAttrs ?? '';
    $statusHtml = $statusHtml ?? '';
    $heroUrl = $heroUrl ?? null;
    $heroTag = $heroUrl ? 'a' : 'div';
    $imageUrl = $imageUrl ?? null;
    $iconClass = $iconClass ?? null;
    $placeholder = $placeholder ?? '?';
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $chips = $chips ?? [];
    $stats = $stats ?? [];
    $metaHtml = $metaHtml ?? null;
    $footerPrimary = $footerPrimary ?? null;
    $footerSecondary = $footerSecondary ?? null;
    $actionsHtml = $actionsHtml ?? '';
@endphp

<article class="admin-mobile-card" @if ($itemId) data-item-id="{{ $itemId }}" @endif>
    @if ($checkboxValue !== null || $statusHtml)
        <div class="admin-mobile-card__toolbar">
            @if ($checkboxValue !== null)
                <div class="form-check mb-0">
                    <input class="form-check-input {{ $checkboxClass }}" type="checkbox" value="{{ $checkboxValue }}"
                        aria-label="{{ $checkboxLabel }}" {!! $checkboxExtraAttrs !!}>
                </div>
            @else
                <span></span>
            @endif
            @if ($statusHtml)
                {!! $statusHtml !!}
            @endif
        </div>
    @endif

    <{{ $heroTag }} @if ($heroUrl) href="{{ $heroUrl }}" @endif class="admin-mobile-card__hero">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $title }}" class="admin-mobile-card__thumb">
        @elseif ($iconClass)
            <div class="admin-mobile-card__thumb admin-mobile-card__thumb--icon">
                <i class="fa {{ $iconClass }}"></i>
            </div>
        @else
            <div class="admin-mobile-card__thumb admin-mobile-card__thumb--placeholder">{{ $placeholder }}</div>
        @endif
        <div class="admin-mobile-card__copy">
            <h3 class="admin-mobile-card__title">{{ $title }}</h3>
            @if ($subtitle)
                <p class="admin-mobile-card__desc">{{ $subtitle }}</p>
            @endif
        </div>
    </{{ $heroTag }}>

    @if (count($chips))
        <div class="admin-mobile-card__chips">
            @foreach ($chips as $chip)
                {!! $chip !!}
            @endforeach
        </div>
    @endif

    @if (count($stats))
        <div class="admin-mobile-card__stats">
            @foreach ($stats as $stat)
                <span><i class="fa {{ $stat['icon'] ?? 'fa-circle' }}" aria-hidden="true"></i> {{ $stat['text'] }}</span>
            @endforeach
        </div>
    @endif

    @if ($metaHtml)
        <div class="admin-mobile-card__meta">{!! $metaHtml !!}</div>
    @endif

    @if ($footerPrimary || $actionsHtml)
        <div class="admin-mobile-card__footer">
            @if ($footerPrimary || $footerSecondary)
                <div class="admin-mobile-card__date">
                    @if ($footerPrimary)
                        <span>{{ $footerPrimary }}</span>
                    @endif
                    @if ($footerSecondary)
                        <span class="text-muted">{{ $footerSecondary }}</span>
                    @endif
                </div>
            @endif
            @if ($actionsHtml)
                <div class="admin-mobile-card__actions">{!! $actionsHtml !!}</div>
            @endif
        </div>
    @endif
</article>
