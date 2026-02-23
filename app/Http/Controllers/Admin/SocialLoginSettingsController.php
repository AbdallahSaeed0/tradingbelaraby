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
            'client_secret' => ['nullable', 'string', 'max:1024'],
            'redirect_uri' => ['nullable', 'string', 'max:512'],
            'android_client_id' => ['nullable', 'string', 'max:512'],
            'ios_client_id' => ['nullable', 'string', 'max:512'],
        ]);

        $row = SocialProviderSetting::firstOrNew(['provider' => SocialProviderSetting::PROVIDER_GOOGLE]);
        $row->enabled = $request->boolean('enabled');
        $row->client_id = $validated['client_id'] ?? $row->client_id;
        if ($request->filled('client_secret')) {
            $row->client_secret = $validated['client_secret'];
        }
        $row->redirect_uri = $validated['redirect_uri'] ?? null;
        $extra = $row->extra ?? [];
        if (array_key_exists('android_client_id', $validated)) {
            $extra['android_client_id'] = $validated['android_client_id'] ?: null;
        }
        if (array_key_exists('ios_client_id', $validated)) {
            $extra['ios_client_id'] = $validated['ios_client_id'] ?: null;
        }
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
            'client_secret' => ['nullable', 'string', 'max:1024'],
            'redirect_uri' => ['nullable', 'string', 'max:512'],
        ]);

        $row = SocialProviderSetting::firstOrNew(['provider' => SocialProviderSetting::PROVIDER_TWITTER]);
        $row->enabled = $request->boolean('enabled');
        $row->client_id = $validated['client_id'] ?? $row->client_id;
        if ($request->filled('client_secret')) {
            $row->client_secret = $validated['client_secret'];
        }
        $row->redirect_uri = $validated['redirect_uri'] ?? null;
        $row->save();

        return redirect()->route('admin.settings.social-login.index')
            ->with('success', __('Twitter provider settings saved.'));
    }
}
