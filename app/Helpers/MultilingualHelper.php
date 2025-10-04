<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class MultilingualHelper
{
    /**
     * Generate slug from multilingual title
     */
    public static function generateSlug(string $title, ?string $titleAr = null, string $locale = 'en'): string
    {
        $sourceTitle = $locale === 'ar' && $titleAr ? $titleAr : $title;

        // For Arabic text, transliterate to Latin characters for URL-friendly slug
        if ($locale === 'ar' && $titleAr) {
            $sourceTitle = self::transliterateArabic($titleAr);
        }

        return Str::slug($sourceTitle);
    }

    /**
     * Transliterate Arabic text to Latin characters
     */
    public static function transliterateArabic(string $text): string
    {
        $transliteration = [
            'ا' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j', 'ح' => 'h', 'خ' => 'kh',
            'د' => 'd', 'ذ' => 'dh', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's',
            'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q',
            'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ه' => 'h', 'و' => 'w', 'ي' => 'y',
            'ء' => 'a', 'ؤ' => 'w', 'ئ' => 'y', 'آ' => 'aa', 'أ' => 'a', 'إ' => 'i', 'ة' => 'h',
            'ى' => 'a', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5',
            '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'
        ];

        return strtr($text, $transliteration);
    }

    /**
     * Get SEO meta data for multilingual content
     */
    public static function getSeoMeta($model, string $locale = 'en'): array
    {
        $meta = [
            'title' => $model->getFieldInLanguage('meta_title', $locale) ??
                      $model->getFieldInLanguage('name', $locale),
            'description' => $model->getFieldInLanguage('meta_description', $locale) ??
                           $model->getFieldInLanguage('description', $locale),
            'keywords' => $model->getFieldInLanguage('meta_keywords', $locale),
            'canonical_url' => $model->canonical_url,
            'robots' => $model->robots ?? 'index,follow',
            'og_title' => $model->getFieldInLanguage('og_title', $locale) ??
                         $model->getFieldInLanguage('name', $locale),
            'og_description' => $model->getFieldInLanguage('og_description', $locale) ??
                              $model->getFieldInLanguage('description', $locale),
            'og_image' => $model->og_image,
            'twitter_title' => $model->getFieldInLanguage('twitter_title', $locale) ??
                             $model->getFieldInLanguage('name', $locale),
            'twitter_description' => $model->getFieldInLanguage('twitter_description', $locale) ??
                                   $model->getFieldInLanguage('description', $locale),
            'twitter_image' => $model->twitter_image,
        ];

        return array_filter($meta);
    }

    /**
     * Generate hreflang tags for multilingual content
     */
    public static function generateHreflangTags($model, string $baseUrl): array
    {
        $hreflangs = [];
        $availableLanguages = $model->getAvailableLanguages();

        foreach ($availableLanguages as $lang) {
            $hreflangs[$lang] = $baseUrl . '?lang=' . $lang;
        }

        return $hreflangs;
    }

    /**
     * Validate multilingual content
     */
    public static function validateMultilingualContent(array $data, array $requiredFields = []): array
    {
        $errors = [];

        foreach ($requiredFields as $field) {
            // Check if English version exists
            if (empty($data[$field])) {
                $errors[$field] = "The {$field} field is required.";
            }

            // Optionally check if Arabic version exists when English exists
            if (!empty($data[$field]) && empty($data[$field . '_ar'])) {
                // This is just a warning, not an error
                // You can customize this logic based on your requirements
            }
        }

        return $errors;
    }

    /**
     * Format multilingual content for display
     */
    public static function formatMultilingualContent($model, string $fieldName, string $locale = 'en'): ?string
    {
        $content = $model->getFieldInLanguage($fieldName, $locale);

        if (!$content) {
            return null;
        }

        // Apply any formatting based on content type
        if (str_contains($fieldName, 'description') || str_contains($fieldName, 'content')) {
            // Convert line breaks to HTML for descriptions
            $content = nl2br(e($content));
        }

        return $content;
    }

    /**
     * Get language direction for RTL support
     */
    public static function getLanguageDirection(string $locale): string
    {
        $rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        return in_array($locale, $rtlLanguages) ? 'rtl' : 'ltr';
    }

    /**
     * Get language name in its native script
     */
    public static function getLanguageName(string $locale): string
    {
        $names = [
            'en' => 'English',
            'ar' => 'العربية',
            'fr' => 'Français',
            'es' => 'Español',
            'de' => 'Deutsch',
        ];

        return $names[$locale] ?? strtoupper($locale);
    }
}
