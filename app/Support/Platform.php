<?php

namespace App\Support;

use Illuminate\Http\Request;

class Platform
{
    public static function fromRequest(Request $request): string
    {
        $detected = $request->attributes->get('client_platform');
        if (is_string($detected) && $detected !== '') {
            return strtolower($detected);
        }

        return strtolower((string) $request->header('X-Platform', ''));
    }

    public static function isIOS(Request $request): bool
    {
        return self::fromRequest($request) === 'ios';
    }

    public static function isAndroid(Request $request): bool
    {
        return self::fromRequest($request) === 'android';
    }
}
