<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ForgotPasswordOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const OTP_CACHE_PREFIX = 'password_reset_otp:';

    /**
     * Show forgot password form (email input)
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->with('info', 'If the email exists, you will receive an OTP shortly.');
        }

        $otp = (string) random_int(100000, 999999);
        $cacheKey = self::OTP_CACHE_PREFIX . $email;
        Cache::put($cacheKey, $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        $language = app()->getLocale();
        $language = in_array($language, ['ar', 'en']) ? $language : 'en';
        $user->notify(new ForgotPasswordOtpNotification($otp, $language));

        return redirect()->route('password.reset.form')->with('email', $email);
    }

    /**
     * Show reset password form (OTP + new password)
     */
    public function showResetForm(Request $request)
    {
        $email = $request->session()->get('email');
        if (!$email) {
            return redirect()->route('password.forgot.form')->withErrors(['email' => 'Please enter your email first.']);
        }
        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Verify OTP and reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $email = $request->email;
        $otp = $request->otp;
        $cacheKey = self::OTP_CACHE_PREFIX . $email;
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp || $storedOtp !== $otp) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please request a new one.'])->withInput($request->only('email'));
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            Cache::forget($cacheKey);
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        $user->update(['password' => Hash::make($request->password)]);
        Cache::forget($cacheKey);

        $request->session()->forget('email');
        return redirect()->route('login')->with('success', custom_trans('Password reset successfully. You can now log in with your new password.', 'front'));
    }
}
