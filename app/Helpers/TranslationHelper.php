<?php

namespace App\Helpers;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class TranslationHelper
{
    public static function getCurrentLanguage()
    {
        // Check if we're in admin area (dashboard defaults to English; frontend uses app locale)
        $isAdmin = request()->is('admin*');
        $sessionKey = $isAdmin ? 'admin_locale' : 'frontend_locale';
        $defaultLocale = $isAdmin ? 'en' : config('app.locale');

        $languageCode = Session::get($sessionKey, $defaultLocale);
        return Language::where('code', $languageCode)->first() ?? Language::default()->first();
    }

    public static function translate($key, $group = 'general')
    {
        $currentLanguage = self::getCurrentLanguage();

        if (!$currentLanguage) {
            return $key; // Return key if no language found
        }

        // Try to get from cache first
        $cacheKey = "translation_{$currentLanguage->code}_{$group}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($currentLanguage, $key, $group) {
            // First try the specified group
            $translation = Translation::where('language_id', $currentLanguage->id)
                ->where('translation_key', $key)
                ->where('group', $group)
                ->first();

            if ($translation) {
                return $translation->translation_value;
            }

            // If not found in specified group, try 'general' group as fallback
            if ($group !== 'general') {
                $translation = Translation::where('language_id', $currentLanguage->id)
                    ->where('translation_key', $key)
                    ->where('group', 'general')
                    ->first();

                if ($translation) {
                    return $translation->translation_value;
                }
            }

            // If still not found, try 'front' group as fallback
            if ($group !== 'front') {
                $translation = Translation::where('language_id', $currentLanguage->id)
                    ->where('translation_key', $key)
                    ->where('group', 'front')
                    ->first();

                if ($translation) {
                    return $translation->translation_value;
                }
            }

            // If still not found, try 'admin' group as fallback
            if ($group !== 'admin') {
                $translation = Translation::where('language_id', $currentLanguage->id)
                    ->where('translation_key', $key)
                    ->where('group', 'admin')
                    ->first();

                if ($translation) {
                    return $translation->translation_value;
                }
            }

            // Final fallback: try to load from front_page_translations.php data file
            if ($group === 'front' || $group === 'general') {
                $translationsFile = database_path('seeders/data/front_page_translations.php');
                if (file_exists($translationsFile)) {
                    $translations = include $translationsFile;
                    if (isset($translations[$key])) {
                        $langCode = $currentLanguage->code;
                        // Return translation for current language or fallback to English or key
                        return $translations[$key][$langCode] ?? $translations[$key]['en'] ?? $key;
                    }
                }
            }

            // Return key if no translation found in any group or file
            return $key;
        });
    }

    public static function setLanguage($languageCode)
    {
        $language = Language::where('code', $languageCode)->first();

        if ($language && $language->is_active) {
            // Check if we're in admin area
            $isAdmin = request()->is('admin*');
            $sessionKey = $isAdmin ? 'admin_locale' : 'frontend_locale';

            Session::put($sessionKey, $languageCode);
            app()->setLocale($languageCode);
            return true;
        }

        return false;
    }

    public static function clearCache()
    {
        Cache::flush();
    }

    public static function getAvailableLanguages()
    {
        return Language::active()->get();
    }

    /**
     * Get localized content based on current language
     * Returns Arabic content if current language is Arabic, otherwise returns English content
     */
    public static function getLocalizedContent($englishContent, $arabicContent = null)
    {
        $currentLanguage = self::getCurrentLanguage();

        // If current language is Arabic and Arabic content exists, return Arabic content
        if ($currentLanguage && $currentLanguage->code === 'ar' && !empty($arabicContent)) {
            return $arabicContent;
        }

        // Otherwise return English content
        return $englishContent;
    }

    /**
     * Get localized array content (for arrays like learning objectives, FAQ)
     */
    public static function getLocalizedArray($englishArray, $arabicArray = null)
    {
        $currentLanguage = self::getCurrentLanguage();

        // If current language is Arabic and Arabic array exists and is not empty, return Arabic array
        if ($currentLanguage && $currentLanguage->code === 'ar' && !empty($arabicArray) && is_array($arabicArray)) {
            return $arabicArray;
        }

        // Otherwise return English array
        return is_array($englishArray) ? $englishArray : [];
    }

    /**
     * Get current language for frontend specifically
     */
    public static function getFrontendLanguage()
    {
        $languageCode = Session::get('frontend_locale', config('app.locale'));
        return Language::where('code', $languageCode)->first() ?? Language::default()->first();
    }

    /**
     * Get current language for admin specifically (dashboard defaults to English)
     */
    public static function getAdminLanguage()
    {
        $languageCode = Session::get('admin_locale', 'en');
        return Language::where('code', $languageCode)->first() ?? Language::default()->first();
    }

    /**
     * Set language for frontend specifically
     */
    public static function setFrontendLanguage($languageCode)
    {
        $language = Language::where('code', $languageCode)->first();

        if ($language && $language->is_active) {
            Session::put('frontend_locale', $languageCode);
            return true;
        }

        return false;
    }

    /**
     * Set language for admin specifically
     */
    public static function setAdminLanguage($languageCode)
    {
        $language = Language::where('code', $languageCode)->first();

        if ($language && $language->is_active) {
            Session::put('admin_locale', $languageCode);
            return true;
        }

        return false;
    }
}
