<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Detects the visitor's country from their IP, to preselect country / phone dial code in forms.
 */
class VisitorCountryService
{
    public const FALLBACK = 'EG';

    /**
     * Two-letter ISO country code (uppercase) for the request's IP.
     * Uses Cloudflare's CF-IPCountry header when present, otherwise a cached ip-api.com lookup.
     * Falls back to Egypt when the IP is private or the lookup fails.
     */
    public function detect(Request $request): string
    {
        $cf = strtoupper((string) $request->header('CF-IPCountry'));
        if (preg_match('/^[A-Z]{2}$/', $cf) && !in_array($cf, ['XX', 'T1'], true)) {
            return $cf;
        }

        $ip = $request->ip();
        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return self::FALLBACK;
        }

        return Cache::remember('ip_country:' . $ip, now()->addDay(), function () use ($ip) {
            try {
                $response = Http::timeout(2)->get('http://ip-api.com/json/' . $ip, ['fields' => 'status,countryCode']);
                $code = $response->ok() && $response->json('status') === 'success'
                    ? strtoupper((string) $response->json('countryCode'))
                    : '';

                return preg_match('/^[A-Z]{2}$/', $code) ? $code : self::FALLBACK;
            } catch (\Throwable $e) {
                Log::info('IP country lookup failed', ['error' => $e->getMessage()]);

                return self::FALLBACK;
            }
        });
    }
}
