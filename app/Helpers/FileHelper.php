<?php

if (!function_exists('formatFileSize')) {
    /**
     * Format file size in human readable format
     *
     * @param int $bytes
     * @return string
     */
    function formatFileSize($bytes)
    {
        if ($bytes === 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
