<?php

namespace App\Services;

use App\Models\VerificationSettings;

/**
 * Reads the active verification settings and exposes helper methods.
 * Results are cached for the duration of the request.
 */
class VerificationSettingsService
{
    private ?VerificationSettings $settings = null;

    private function settings(): VerificationSettings
    {
        return $this->settings ??= VerificationSettings::getSettings();
    }

    public function getMethod(): string
    {
        return $this->settings()->method;
    }

    public function isEmailEnabled(): bool
    {
        return $this->settings()->isEmailEnabled();
    }

    public function isWhatsappEnabled(): bool
    {
        return $this->settings()->isWhatsappEnabled();
    }

    /**
     * Returns the WhatsApp API config, preferring DB values over .env fallbacks.
     */
    public function getWhatsappConfig(): array
    {
        $s = $this->settings();
        return [
            'token'           => $s->whatsapp_token           ?: config('services.meta_whatsapp.token'),
            'phone_number_id' => $s->whatsapp_phone_number_id ?: config('services.meta_whatsapp.phone_number_id'),
            'otp_template'    => $s->whatsapp_otp_template    ?: config('services.meta_whatsapp.otp_template', 'otp_verification'),
            'api_version'     => $s->whatsapp_api_version      ?: config('services.meta_whatsapp.api_version', 'v21.0'),
        ];
    }

    /**
     * Whether a user is considered "verified" given their current state.
     * For 'both' mode a user is verified if EITHER phone OR email is verified.
     */
    public function isUserVerified(\App\Models\User $user): bool
    {
        $method = $this->getMethod();

        if ($method === VerificationSettings::METHOD_WHATSAPP) {
            return $user->phone_verified_at !== null;
        }

        if ($method === VerificationSettings::METHOD_EMAIL) {
            return $user->email_verified_at !== null;
        }

        // 'both' — either one is sufficient
        return $user->phone_verified_at !== null || $user->email_verified_at !== null;
    }
}
