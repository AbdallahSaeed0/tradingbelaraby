<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SocialLoginSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth.
     */
    public function redirectToGoogle(SocialLoginSettingsService $settings): RedirectResponse
    {
        if (!$settings->isGoogleEnabled()) {
            return redirect()->route('login')->with('error', __('Google login is not available.'));
        }
        try {
            Config::set('services.google', $settings->getGoogleConfig());
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            Log::error('Google redirect failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', __('Social login is temporarily unavailable. Please try again later.'));
        }
    }

    /**
     * Handle Google OAuth callback.
     * Find or create user by google_id or email; link if existing email.
     * Social logins are treated as email-verified.
     */
    public function handleGoogleCallback(SocialLoginSettingsService $settings): RedirectResponse
    {
        if (!$settings->isGoogleEnabled()) {
            return redirect()->route('login')->with('error', __('Google login is not available.'));
        }
        try {
            Config::set('services.google', $settings->getGoogleConfig());
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Google callback invalid state');
            return redirect()->route('login')->with('error', __('Session expired. Please try again.'));
        } catch (\Throwable $e) {
            Log::error('Google callback failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', __('Unable to sign in with Google. Please try again.'));
        }

        $user = $this->findOrCreateSocialUser(
            provider: 'google',
            providerId: $googleUser->getId(),
            email: $googleUser->getEmail(),
            name: $googleUser->getName(),
            avatar: $googleUser->getAvatar(),
        );

        if (!$user) {
            return redirect()->route('login')->with('error', __('We could not get your email from Google. Please use email/password or ensure your Google account has an email.'));
        }

        Auth::login($user, true);
        return redirect()->intended(route('home'));
    }

    /**
     * Redirect to Twitter (X) OAuth 2.
     */
    public function redirectToTwitter(SocialLoginSettingsService $settings): RedirectResponse
    {
        if (!$settings->isTwitterEnabled()) {
            return redirect()->route('login')->with('error', __('Twitter login is not available.'));
        }
        try {
            Config::set('services.twitter', $settings->getTwitterConfig());
            return Socialite::driver('twitter-oauth-2')->redirect();
        } catch (\Throwable $e) {
            Log::error('Twitter redirect failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', __('Social login is temporarily unavailable. Please try again later.'));
        }
    }

    /**
     * Handle Twitter (X) OAuth 2 callback.
     */
    public function handleTwitterCallback(SocialLoginSettingsService $settings): RedirectResponse
    {
        if (!$settings->isTwitterEnabled()) {
            return redirect()->route('login')->with('error', __('Twitter login is not available.'));
        }
        try {
            Config::set('services.twitter', $settings->getTwitterConfig());
            $twitterUser = Socialite::driver('twitter-oauth-2')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Twitter callback invalid state');
            return redirect()->route('login')->with('error', __('Session expired. Please try again.'));
        } catch (\Throwable $e) {
            Log::error('Twitter callback failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', __('Unable to sign in with X. Please try again.'));
        }

        $email = $twitterUser->getEmail();
        $name = $twitterUser->getName() ?: $twitterUser->getNickname() ?: 'User';
        $avatar = $twitterUser->getAvatar();

        $user = $this->findOrCreateSocialUser(
            provider: 'twitter',
            providerId: $twitterUser->getId(),
            email: $email,
            name: $name,
            avatar: $avatar,
        );

        if (!$user) {
            return redirect()->route('login')->with('error', __('We could not get your email from X. Please use email/password or connect an email to your X account.'));
        }

        Auth::login($user, true);
        return redirect()->intended(route('home'));
    }

    /**
     * Find user by provider id or email; create or link. Mark email verified for social logins.
     */
    private function findOrCreateSocialUser(string $provider, string $providerId, ?string $email, string $name, ?string $avatar): ?User
    {
        $idColumn = $provider === 'google' ? 'google_id' : 'twitter_id';

        $user = User::where($idColumn, $providerId)->first();
        if ($user) {
            return $user;
        }

        if (empty($email)) {
            return null;
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update([$idColumn => $providerId]);
            if ($avatar && !$user->avatar) {
                $user->update(['avatar' => $this->normalizeAvatarUrl($avatar)]);
            }
            $user->markEmailAsVerified();
            return $user;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => null,
            $idColumn => $providerId,
            'avatar' => $avatar ? $this->normalizeAvatarUrl($avatar) : null,
            'email_verified_at' => now(),
        ]);

        return $user;
    }

    private function normalizeAvatarUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }
        return str_starts_with($url, 'http') ? $url : null;
    }
}
