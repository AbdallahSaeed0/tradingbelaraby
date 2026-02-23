<?php

namespace App\Services;

use App\Models\SocialProviderSetting;
use Illuminate\Support\Facades\Config;

class SocialLoginSettingsService
{
    /**
     * Effective Google config: DB overrides env. Used at runtime so cache does not block changes.
     */
    public function getGoogleConfig(): array
    {
        $fromEnv = [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect' => config('services.google.redirect'),
            'android_client_id' => config('services.google.android_client_id'),
            'ios_client_id' => config('services.google.ios_client_id'),
        ];

        $row = SocialProviderSetting::getForProvider(SocialProviderSetting::PROVIDER_GOOGLE);
        if (!$row || !$row->enabled) {
            return $fromEnv;
        }

        return [
            'client_id' => $row->client_id ?? $fromEnv['client_id'],
            'client_secret' => $row->client_secret ?? $fromEnv['client_secret'],
            'redirect' => $row->redirect_uri ?? $fromEnv['redirect'],
            'android_client_id' => $row->getExtraValue('android_client_id') ?? $fromEnv['android_client_id'],
            'ios_client_id' => $row->getExtraValue('ios_client_id') ?? $fromEnv['ios_client_id'],
        ];
    }

    /**
     * Effective Twitter config: DB overrides env.
     */
    public function getTwitterConfig(): array
    {
        $fromEnv = [
            'client_id' => config('services.twitter.client_id'),
            'client_secret' => config('services.twitter.client_secret'),
            'redirect' => config('services.twitter.redirect'),
        ];

        $row = SocialProviderSetting::getForProvider(SocialProviderSetting::PROVIDER_TWITTER);
        if (!$row || !$row->enabled) {
            return $fromEnv;
        }

        return [
            'client_id' => $row->client_id ?? $fromEnv['client_id'],
            'client_secret' => $row->client_secret ?? $fromEnv['client_secret'],
            'redirect' => $row->redirect_uri ?? $fromEnv['redirect'],
        ];
    }

    /**
     * Whether Google login is enabled (has client_id from env or DB).
     */
    public function isGoogleEnabled(): bool
    {
        $row = SocialProviderSetting::getForProvider(SocialProviderSetting::PROVIDER_GOOGLE);
        if ($row && !$row->enabled) {
            return false;
        }
        $clientId = ($row && $row->client_id) ? $row->client_id : config('services.google.client_id');
        return !empty($clientId);
    }

    /**
     * Whether Twitter login is enabled.
     */
    public function isTwitterEnabled(): bool
    {
        $row = SocialProviderSetting::getForProvider(SocialProviderSetting::PROVIDER_TWITTER);
        if ($row && !$row->enabled) {
            return false;
        }
        $clientId = ($row && $row->client_id) ? $row->client_id : config('services.twitter.client_id');
        return !empty($clientId);
    }
}
