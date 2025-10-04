@php
    $currentLanguage = \App\Helpers\TranslationHelper::getFrontendLanguage();
    $availableLanguages = \App\Helpers\TranslationHelper::getAvailableLanguages();
@endphp

@if ($availableLanguages->count() > 1)
    <div class="language-switcher">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-globe me-2"></i>
                {{ $currentLanguage ? $currentLanguage->name : 'English' }}
            </button>
            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                @foreach ($availableLanguages as $language)
                    <li>
                        <a class="dropdown-item {{ $currentLanguage && $currentLanguage->id === $language->id ? 'active' : '' }}"
                            href="{{ route('language.switch', $language->code) }}">
                            <span class="flag-icon me-2">
                                @if ($language->code === 'ar')
                                    🇸🇦
                                @else
                                    🇺🇸
                                @endif
                            </span>
                            {{ $language->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
