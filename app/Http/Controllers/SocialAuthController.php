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
            return redirect()->route('login')->with('error', custom_trans('Google login is not available.', 'front'));
        }
        try {
            Config::set('services.google', $settings->getGoogleConfig());
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            Log::error('Google redirect failed: ' . $e->getMessage(), ['exception' => $e]);
            $message = config('app.debug')
                ? 'Google: ' . $e->getMessage()
                : custom_trans('Social login is temporarily unavailable. Please try again later.', 'front');
            return redirect()->route('login')->with('error', $message);
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
            return redirect()->route('login')->with('error', custom_trans('Google login is not available.', 'front'));
        }
        try {
            Config::set('services.google', $settings->getGoogleConfig());
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Google callback invalid state');
            return redirect()->route('login')->with('error', custom_trans('Session expired. Please try again.', 'front'));
        } catch (\Throwable $e) {
            Log::error('Google callback failed: ' . $e->getMessage(), ['exception' => $e]);
            $message = config('app.debug') ? 'Google callback: ' . $e->getMessage() : custom_trans('Unable to sign in with Google. Please try again.', 'front');
            return redirect()->route('login')->with('error', $message);
        }

        $user = $this->findOrCreateSocialUser(
            provider: 'google',
            providerId: $googleUser->getId(),
            email: $googleUser->getEmail(),
            name: $googleUser->getName(),
            avatar: $googleUser->getAvatar(),
        );

        if (!$user) {
            return redirect()->route('login')->with('error', custom_trans('We could not get your email from Google. Please use email/password or ensure your Google account has an email.', 'front'));
        }

        Auth::login($user, true);

        if ($this->needsCompleteProfile($user)) {
            return redirect()->route('auth.x.complete-profile');
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Redirect to Twitter (X) OAuth 2.
     */
    public function redirectToTwitter(SocialLoginSettingsService $settings): RedirectResponse
    {
        if (!$settings->isTwitterEnabled()) {
            return redirect()->route('login')->with('error', custom_trans('Twitter login is not available.', 'front'));
        }
        try {
            Config::set('services.twitter', $settings->getTwitterConfig());
            return Socialite::driver('twitter')->redirect();
        } catch (\Throwable $e) {
            Log::error('Twitter redirect failed: ' . $e->getMessage(), ['exception' => $e]);
            $msg = $e->getMessage();
            if (str_contains(strtolower($msg), 'driver') || str_contains(strtolower($msg), 'not supported')) {
                $message = 'Twitter login: run "composer require socialiteproviders/twitter" then "composer update".';
            } else {
                $message = config('app.debug') ? 'Twitter: ' . $msg : custom_trans('Social login is temporarily unavailable. Please try again later.', 'front');
            }
            return redirect()->route('login')->with('error', $message);
        }
    }

    /**
     * Handle Twitter (X) OAuth 2 callback.
     */
    public function handleTwitterCallback(SocialLoginSettingsService $settings): RedirectResponse
    {
        if (!$settings->isTwitterEnabled()) {
            return redirect()->route('login')->with('error', custom_trans('Twitter login is not available.', 'front'));
        }
        try {
            Config::set('services.twitter', $settings->getTwitterConfig());
            $twitterUser = Socialite::driver('twitter')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('Twitter callback invalid state');
            return redirect()->route('login')->with('error', custom_trans('Session expired. Please try again.', 'front'));
        } catch (\Throwable $e) {
            Log::error('Twitter callback failed: ' . $e->getMessage(), ['exception' => $e]);
            $message = config('app.debug') ? 'Twitter callback: ' . $e->getMessage() : custom_trans('Unable to sign in with X. Please try again.', 'front');
            return redirect()->route('login')->with('error', $message);
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
            return redirect()->route('login')->with('error', custom_trans('We could not sign you in with X. Please try again or use email/password.', 'front'));
        }

        Auth::login($user, true);

        if ($this->needsCompleteProfile($user)) {
            return redirect()->route('auth.x.complete-profile');
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Show form to collect missing profile data (email for X placeholder, phone for Google/X).
     */
    public function showCompleteProfileForm(): \Illuminate\View\View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$this->needsCompleteProfile($user)) {
            return redirect()->intended(route('home'));
        }
        $needsEmail = $this->isPlaceholderEmail($user->email);
        $needsPhone = empty(trim((string) $user->phone));
        return view('auth.complete-profile', compact('needsEmail', 'needsPhone'));
    }

    /**
     * Save profile data from complete-profile form (email and/or phone).
     */
    public function saveCompleteProfile(\Illuminate\Http\Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$this->needsCompleteProfile($user)) {
            return redirect()->intended(route('home'));
        }

        $needsEmail = $this->isPlaceholderEmail($user->email);
        $needsPhone = empty(trim((string) $user->phone));

        $rules = [];
        if ($needsEmail) {
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email,' . $user->id];
        }
        if ($needsPhone) {
            $rules['phone'] = [
                'required',
                'string',
                'max:20',
                'regex:/^\+?[0-9]{9,15}$/',
            ];
        }

        $validated = $request->validate($rules, [
            'email.unique' => custom_trans('This email is already registered. Use another or sign in with that account.', 'front'),
            'phone.regex' => custom_trans('Please enter a valid phone number (at least 9 digits).', 'front'),
        ]);

        $updates = [];
        if ($needsEmail && !empty($validated['email'] ?? null)) {
            $updates['email'] = $validated['email'];
            $updates['email_verified_at'] = now();
        }
        if ($needsPhone && isset($validated['phone'])) {
            $updates['phone'] = trim($validated['phone']);
        }
        if (!empty($updates)) {
            $user->update($updates);
        }

        return redirect()->intended(route('home'))->with('success', custom_trans('Your profile has been updated.', 'front'));
    }

    private function needsCompleteProfile(User $user): bool
    {
        if ($this->isPlaceholderEmail($user->email)) {
            return true;
        }
        return empty(trim((string) $user->phone));
    }

    private function isPlaceholderEmail(string $email): bool
    {
        return (bool) preg_match('/^x\+.+@users\.noreply\.local$/', $email);
    }

    /**
     * Find user by provider id or email; create or link. Mark email verified for social logins.
     * For Twitter, if X does not return an email we create a user with a unique placeholder email.
     */
    private function findOrCreateSocialUser(string $provider, string $providerId, ?string $email, string $name, ?string $avatar): ?User
    {
        $idColumn = $provider === 'google' ? 'google_id' : 'twitter_id';

        $user = User::where($idColumn, $providerId)->first();
        if ($user) {
            return $user;
        }

        if (empty($email)) {
            if ($provider === 'twitter') {
                $email = 'x+' . $providerId . '@users.noreply.local';
            } else {
                return null;
            }
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
