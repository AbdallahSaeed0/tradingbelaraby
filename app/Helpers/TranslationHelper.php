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
        $languageCode = Session::get('locale', config('app.locale'));
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

            // Return key if no translation found in any group
            return $key;
        });
    }

    public static function setLanguage($languageCode)
    {
        $language = Language::where('code', $languageCode)->first();

        if ($language && $language->is_active) {
            Session::put('locale', $languageCode);
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
}
