@php
    $title = $title ?? '';
    $subtitle = $subtitle ?? null;
    $activeBreadcrumb = $activeBreadcrumb ?? $title;
    $actions = $actions ?? '';
@endphp

<div class="page-title-box admin-settings-subpage-header mb-3 mb-lg-4">
    <div class="page-title-content">
        <h4 class="page-title mb-1">{{ $title }}</h4>
        @if ($subtitle)
            <p class="text-muted mb-0">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="page-title-right d-none d-lg-block">
        <ol class="breadcrumb m-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ custom_trans('Dashboard', 'admin') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ custom_trans('Settings', 'admin') }}</a></li>
            <li class="breadcrumb-item active">{{ $activeBreadcrumb }}</li>
        </ol>
    </div>
    @if ($actions)
        <div class="admin-settings-header-actions d-none d-lg-flex">
            {!! $actions !!}
        </div>
    @endif
</div>
