<?php

namespace App\Traits;

trait HasMultilingualFields
{
    /**
     * Get localized field value based on current locale
     */
    public function getLocalizedField(string $fieldName, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        $localizedField = $fieldName . '_' . $locale;

        if ($locale !== 'en' && $this->{$localizedField}) {
            return $this->{$localizedField};
        }

        return $this->{$fieldName};
    }

    /**
     * Get localized array field value based on current locale
     */
    public function getLocalizedArrayField(string $fieldName, ?string $locale = null): ?array
    {
        $locale = $locale ?? app()->getLocale();

        $localizedField = $fieldName . '_' . $locale;

        if ($locale !== 'en' && $this->{$localizedField}) {
            return $this->{$localizedField};
        }

        return $this->{$fieldName};
    }

    /**
     * Check if field has localized content
     */
    public function hasLocalizedContent(string $fieldName, string $locale): bool
    {
        $localizedField = $fieldName . '_' . $locale;
        return !empty($this->{$localizedField});
    }

    /**
     * Get available languages for this model
     */
    public function getAvailableLanguages(): array
    {
        $languages = ['en'];

        // Check which localized fields have content
        $localizedFields = ['name', 'description'];

        foreach ($localizedFields as $field) {
            if ($this->hasLocalizedContent($field, 'ar')) {
                $languages[] = 'ar';
                break;
            }
        }

        return array_unique($languages);
    }

    /**
     * Get field in specific language with fallback
     */
    public function getFieldInLanguage(string $fieldName, string $locale, ?string $fallbackLocale = 'en'): ?string
    {
        $value = $this->getLocalizedField($fieldName, $locale);

        if (!$value && $fallbackLocale && $fallbackLocale !== $locale) {
            $value = $this->getLocalizedField($fieldName, $fallbackLocale);
        }

        return $value;
    }

    /**
     * Get array field in specific language with fallback
     */
    public function getArrayFieldInLanguage(string $fieldName, string $locale, ?string $fallbackLocale = 'en'): ?array
    {
        $value = $this->getLocalizedArrayField($fieldName, $locale);

        if (!$value && $fallbackLocale && $fallbackLocale !== $locale) {
            $value = $this->getLocalizedArrayField($fieldName, $fallbackLocale);
        }

        return $value;
    }
}
