@php
    $currentLanguage = \App\Helpers\TranslationHelper::getAdminLanguage();
    $availableLanguages = \App\Helpers\TranslationHelper::getAvailableLanguages();
@endphp

@if ($availableLanguages->count() > 1)
    <div class="admin-language-switcher">
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="adminLanguageDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-globe me-2"></i>
                {{ $currentLanguage ? $currentLanguage->name : 'English' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminLanguageDropdown">
                @foreach ($availableLanguages as $language)
                    <li>
                        <a class="dropdown-item {{ $currentLanguage && $currentLanguage->id === $language->id ? 'active' : '' }}"
                            href="{{ route('admin.language.switch', $language->code) }}">
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
