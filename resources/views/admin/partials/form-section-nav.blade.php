@php
    $sections = $sections ?? [];
@endphp

@if (count($sections))
    <nav class="admin-form-section-nav d-lg-none" aria-label="Form sections">
        @foreach ($sections as $section)
            <a href="#{{ $section['id'] }}" class="admin-form-section-nav__link"
                data-form-section-link="{{ $section['id'] }}">
                @if (!empty($section['icon']))
                    <i class="fa {{ $section['icon'] }} me-1"></i>
                @endif
                {{ $section['label'] }}
            </a>
        @endforeach
    </nav>
@endif
