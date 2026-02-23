<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialProviderSetting;
use App\Services\SocialLoginSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SocialLoginSettingsController extends Controller
{
    public function index(SocialLoginSettingsService $settings): View
    {
        $google = SocialProviderSetting::getForProvider(SocialProviderSetting::PROVIDER_GOOGLE)
            ?? new SocialProviderSetting(['provider' => SocialProviderSetting::PROVIDER_GOOGLE, 'enabled' => false]);
        $twitter = SocialProviderSetting::getForProvider(SocialProviderSetting::PROVIDER_TWITTER)
            ?? new SocialProviderSetting(['provider' => SocialProviderSetting::PROVIDER_TWITTER, 'enabled' => false]);

        $defaultRedirectGoogle = rtrim(config('app.url'), '/') . '/auth/google/callback';
        $defaultRedirectTwitter = rtrim(config('app.url'), '/') . '/auth/twitter/callback';

        return view('admin.settings.social-login.index', compact('google', 'twitter', 'defaultRedirectGoogle', 'defaultRedirectTwitter'));
    }

    public function updateGoogle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'client_id' => ['nullable', 'string', 'max:512'],
            'new_client_secret' => ['nullable', 'string', 'max:2048'],
            'redirect_uri' => ['nullable', 'string', 'max:512'],
            'android_client_id' => ['nullable', 'string', 'max:512'],
            'ios_client_id' => ['nullable', 'string', 'max:512'],
        ]);

        $row = SocialProviderSetting::firstOrNew(['provider' => SocialProviderSetting::PROVIDER_GOOGLE]);
        $extra = $row->extra ?? [];
        $extra['android_client_id'] = $validated['android_client_id'] ?? $extra['android_client_id'] ?? null;
        $extra['ios_client_id'] = $validated['ios_client_id'] ?? $extra['ios_client_id'] ?? null;

        $clientSecret = $request->filled('new_client_secret')
            ? trim((string) $request->input('new_client_secret'))
            : ($row->exists ? $row->getOriginal('client_secret') : null);

        $row->enabled = $request->boolean('enabled');
        $row->client_id = $validated['client_id'] ?? $row->client_id;
        $row->client_secret = $clientSecret;
        $row->redirect_uri = $validated['redirect_uri'] ?? null;
        $row->extra = $extra;
        $row->save();

        return redirect()->route('admin.settings.social-login.index')
            ->with('success', __('Google provider settings saved.'));
    }

    public function updateTwitter(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'client_id' => ['nullable', 'string', 'max:512'],
            'new_client_secret' => ['nullable', 'string', 'max:2048'],
            'redirect_uri' => ['nullable', 'string', 'max:512'],
        ]);

        $row = SocialProviderSetting::firstOrNew(['provider' => SocialProviderSetting::PROVIDER_TWITTER]);
        $clientSecret = $request->filled('new_client_secret')
            ? trim((string) $request->input('new_client_secret'))
            : ($row->exists ? $row->getOriginal('client_secret') : null);

        $row->enabled = $request->boolean('enabled');
        $row->client_id = $validated['client_id'] ?? $row->client_id;
        $row->client_secret = $clientSecret;
        $row->redirect_uri = $validated['redirect_uri'] ?? null;
        $row->save();

        return redirect()->route('admin.settings.social-login.index')
            ->with('success', __('Twitter provider settings saved.'));
    }
}
