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
    function format_currency($amount, $currency = null)
    {
        $currency = $currency ?? config('app.currency', 'SAR');
        return number_format($amount, 2) . ' ' . $currency;
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text to a specific length (UTF-8 safe)
     */
    function truncate_text($text, $length = 100, $suffix = '...')
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('sanitize_utf8')) {
    /**
     * Remove replacement characters and invalid UTF-8 sequences from text.
     * Use to prevent the replacement character () from displaying on the site.
     */
    function sanitize_utf8($text)
    {
        if ($text === null || $text === '') {
            return $text;
        }
        $text = (string) $text;
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        return preg_replace('/\x{FFFD}/u', '', $text);
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

if (!function_exists('optimized_image_url')) {
    /**
     * URL for optimized image (resized + WebP when accepted). Use for storage images to improve LCP and save bandwidth.
     * Pass storage-relative path (e.g. courses/xyz.jpg). Returns fallback if path is empty.
     *
     * @param string|null $storagePath Path relative to storage/app/public (e.g. from $course->image)
     * @param int $width Desired width in pixels
     * @param int|null $height Optional height (preserves aspect if omitted)
     * @return string URL to /img/opt or original asset URL if no path
     */
    function optimized_image_url(?string $storagePath, int $width, ?int $height = null): string
    {
        if ($storagePath === null || $storagePath === '') {
            return '';
        }
        $params = ['path' => $storagePath, 'w' => $width];
        if ($height !== null) {
            $params['h'] = $height;
        }
        return url('/img/opt?' . http_build_query($params));
    }
}

if (!function_exists('optimized_image_or')) {
    /**
     * Optimized URL for a storage-relative image path, or the given fallback URL
     * when the path is empty or already an absolute URL (which the optimizer cannot serve).
     */
    function optimized_image_or(?string $storagePath, string $fallbackUrl, int $width, ?int $height = null): string
    {
        if ($storagePath === null || $storagePath === '' || filter_var($storagePath, FILTER_VALIDATE_URL)) {
            return $fallbackUrl;
        }
        return optimized_image_url($storagePath, $width, $height);
    }
}

if (!function_exists('versioned_asset')) {
    /**
     * asset() URL with a file-modification-time query string, so public CSS/JS
     * can be cached long-term by browsers and still refresh after a deploy.
     */
    function versioned_asset(string $path): string
    {
        $file = public_path($path);
        $version = is_file($file) ? filemtime($file) : null;
        return asset($path) . ($version ? '?v=' . $version : '');
    }
}

if (!function_exists('css_bundle_urls')) {
    /**
     * Concatenate public CSS files into one cached bundle (public/cache/css) so the layout
     * needs a single stylesheet request instead of many. @import rules are inlined in place
     * and relative url()s are rewritten, so the cascade is the same as linking the files
     * one by one. The bundle name hashes paths + mtimes, so edits produce a new bundle.
     * Falls back to individual versioned links if the bundle cannot be written.
     *
     * @param array<int, string> $paths Paths relative to public/ (e.g. css/rtl/layout.css)
     * @return array<int, string> Stylesheet URLs to link, in order
     */
    function css_bundle_urls(array $paths): array
    {
        $signature = '';
        foreach ($paths as $path) {
            $file = public_path($path);
            $signature .= $path . ':' . (is_file($file) ? filemtime($file) : 0) . ';';
        }
        $bundlePath = 'cache/css/bundle-' . md5($signature) . '.css';
        $bundleFile = public_path($bundlePath);

        if (!is_file($bundleFile)) {
            $css = '';
            foreach ($paths as $path) {
                $css .= "/* {$path} */\n" . css_bundle_read($path) . "\n";
            }
            $dir = dirname($bundleFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            $tmp = $bundleFile . '.' . uniqid('', true) . '.tmp';
            if (@file_put_contents($tmp, $css) === false || !@rename($tmp, $bundleFile)) {
                @unlink($tmp);
                return array_map('versioned_asset', $paths);
            }
        }

        return [asset($bundlePath)];
    }
}

if (!function_exists('css_bundle_read')) {
    /**
     * Read one public CSS file for css_bundle_urls(): inline its local @imports
     * and make relative url() references absolute from the site root.
     */
    function css_bundle_read(string $path, array $seen = []): string
    {
        $path = ltrim($path, '/');
        $file = public_path($path);
        if (isset($seen[$path]) || !is_file($file)) {
            return '';
        }
        $seen[$path] = true;
        $css = (string) file_get_contents($file);
        $baseDir = trim(dirname($path), '/.');

        $resolve = function (string $url) use ($baseDir): string {
            if ($url === '' || $url[0] === '/' || $url[0] === '#' || preg_match('#^(data:|https?:|//)#i', $url)) {
                return $url;
            }
            $parts = [];
            foreach (explode('/', $baseDir . '/' . $url) as $segment) {
                if ($segment === '..') {
                    array_pop($parts);
                } elseif ($segment !== '.' && $segment !== '') {
                    $parts[] = $segment;
                }
            }
            return '/' . implode('/', $parts);
        };

        // Inline local @import rules (remote ones are left untouched)
        $css = preg_replace_callback(
            '/@import\s+(?:url\(\s*)?["\']?([^"\')\s;]+)["\']?\s*\)?\s*;/i',
            function ($m) use ($resolve, $seen) {
                $target = $resolve($m[1]);
                if (preg_match('#^(https?:|//)#i', $target)) {
                    return $m[0];
                }
                return css_bundle_read(strtok($target, '?'), $seen);
            },
            $css
        );

        // Rewrite relative url() references so they still resolve from the bundle location
        return preg_replace_callback(
            '/url\(\s*(["\']?)([^"\')]+)\1\s*\)/i',
            fn ($m) => 'url(' . $m[1] . $resolve(trim($m[2])) . $m[1] . ')',
            $css
        );
    }
}
