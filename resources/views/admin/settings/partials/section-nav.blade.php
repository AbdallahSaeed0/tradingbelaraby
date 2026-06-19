@php
    $sections = $sections ?? [];
@endphp

@if (count($sections))
    <nav class="admin-settings-section-nav d-lg-none" aria-label="Settings sections">
        @foreach ($sections as $section)
            <a href="#{{ $section['id'] }}" class="admin-settings-section-nav__link"
                data-settings-section-link="{{ $section['id'] }}">
                @if (!empty($section['icon']))
                    <i class="fa {{ $section['icon'] }} me-1"></i>
                @endif
                {{ $section['label'] }}
            </a>
        @endforeach
    </nav>
@endif
