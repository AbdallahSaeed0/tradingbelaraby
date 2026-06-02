<?php

namespace App\Services;

use App\Services\VerificationSettingsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends WhatsApp OTP messages via Meta Cloud API.
 *
 * Required .env:
 *   META_WHATSAPP_TOKEN         – permanent system-user access token
 *   META_WHATSAPP_PHONE_NUMBER_ID – your WhatsApp Business phone number ID
 *   META_WHATSAPP_OTP_TEMPLATE  – approved template name (default: otp_verification)
 *   META_WHATSAPP_API_VERSION   – Graph API version (default: v21.0)
 */
class WhatsAppService
{
    private const OTP_CACHE_PREFIX   = 'whatsapp_otp:';
    private const OTP_EXPIRY_MINUTES = 10;

    public function sendOtp(string $phone, string $language = 'en'): bool
    {
        $otp = (string) random_int(100000, 999999);

        $cacheKey = self::OTP_CACHE_PREFIX . $phone;
        Cache::put($cacheKey, $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        return $this->sendOtpMessage($phone, $otp, $language);
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        $cacheKey  = self::OTP_CACHE_PREFIX . $phone;
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp || $storedOtp !== $otp) {
            return false;
        }

        Cache::forget($cacheKey);
        return true;
    }

    public function hasActiveOtp(string $phone): bool
    {
        return Cache::has(self::OTP_CACHE_PREFIX . $phone);
    }

    private function sendOtpMessage(string $phone, string $otp, string $language): bool
    {
        $config        = app(VerificationSettingsService::class)->getWhatsappConfig();
        $token         = $config['token'];
        $phoneNumberId = $config['phone_number_id'];
        $templateName  = $config['otp_template'];
        $apiVersion    = $config['api_version'];

        if (!$token || !$phoneNumberId) {
            Log::error('WhatsApp service is not configured (missing META_WHATSAPP_TOKEN or META_WHATSAPP_PHONE_NUMBER_ID)');
            return false;
        }

        $langCode = $language === 'ar' ? 'ar' : 'en_US';

        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $phone,
            'type'              => 'template',
            'template'          => [
                'name'       => $templateName,
                'language'   => ['code' => $langCode],
                'components' => [
                    [
                        'type'       => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $otp],
                        ],
                    ],
                    [
                        'type'       => 'button',
                        'sub_type'   => 'url',
                        'index'      => '0',
                        'parameters' => [
                            ['type' => 'text', 'text' => $otp],
                        ],
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                Log::info('WhatsApp OTP sent', ['phone' => substr($phone, 0, -4) . '****']);
                return true;
            }

            Log::warning('WhatsApp OTP failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp OTP exception', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
