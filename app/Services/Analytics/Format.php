<?php

namespace App\Services\Analytics;

/** Display formatting for GA4 metric and dimension values. */
class Format
{
    public static function metric($value, string $format): string
    {
        $value = (float) $value;

        return match ($format) {
            'percent' => round($value * 100, 1) . '%',
            'duration' => self::duration($value),
            'decimal' => number_format($value, 2),
            default => number_format($value),
        };
    }

    public static function duration(float $seconds): string
    {
        $seconds = (int) round($seconds);
        if ($seconds >= 3600) {
            return intdiv($seconds, 3600) . 'h ' . intdiv($seconds % 3600, 60) . 'm';
        }

        return $seconds >= 60 ? intdiv($seconds, 60) . 'm ' . ($seconds % 60) . 's' : $seconds . 's';
    }

    public static function dimension(?string $value, ?string $format = null): string
    {
        $value = (string) $value;
        if ($value === '' || $value === '(not set)') {
            return '(not set)';
        }

        return match ($format) {
            'path' => urldecode($value),
            'capitalize' => ucfirst($value),
            'hour' => sprintf('%02d:00', (int) $value),
            default => $value,
        };
    }
}
