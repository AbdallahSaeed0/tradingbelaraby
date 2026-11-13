{{-- Flag for a language code (en → UK, ar → Saudi Arabia); globe icon for anything else.
     Usage: @include('partials.lang-flag', ['code' => $language->code]) --}}
@php
    $flagCountry = ['en' => 'gb', 'ar' => 'sa'][strtolower($code ?? '')] ?? null;
@endphp
@if ($flagCountry)
    <img class="lang-flag" src="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/7.2.3/flags/4x3/{{ $flagCountry }}.svg"
        alt="" width="22" height="16" decoding="async">
@else
    <i class="fas fa-globe lang-flag-fallback" aria-hidden="true"></i>
@endif
