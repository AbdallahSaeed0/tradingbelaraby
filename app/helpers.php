<?php

// This file contains common helper functions for the application

if (!function_exists('format_date')) {
    /**
     * Format a date in a consistent way
     */
    function format_date($date, $format = 'Y-m-d H:i:s')
    {
        if (!$date) return null;
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_currency')) {
    /**
     * Format currency
     */
    function format_currency($amount, $currency = '$')
    {
        return $currency . number_format($amount, 2);
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text to a specific length
     */
    function truncate_text($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('custom_trans')) {
    /**
     * Custom translation function that works with our database translation system
     * Automatically detects admin/frontend context and uses appropriate group
     */
    function custom_trans($key, $group = null)
    {
        // If group is explicitly specified, use it
        if ($group !== null) {
            return \App\Helpers\TranslationHelper::translate($key, $group);
        }

        // Auto-detect context based on current route
        $currentRoute = request()->route();
        if ($currentRoute) {
            $routeName = $currentRoute->getName();
            if (str_starts_with($routeName, 'admin.') || str_starts_with($routeName, 'admin')) {
                // We're in admin panel
                return \App\Helpers\TranslationHelper::translate($key, 'admin');
            }
        }

        // Default to frontend/general
        return \App\Helpers\TranslationHelper::translate($key, 'front');
    }
}

if (!function_exists('get_current_language_code')) {
    /**
     * Get the current language code consistently across the application
     */
    function get_current_language_code()
    {
        return \App\Helpers\TranslationHelper::getCurrentLanguage()->code;
    }
}
