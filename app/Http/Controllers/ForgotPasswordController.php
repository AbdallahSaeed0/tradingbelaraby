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

        $request->session()->put('email', $email);
        return redirect()->route('password.verify-otp.form');
    }

    /**
     * Show verify OTP form (OTP only)
     */
    public function showVerifyOtpForm(Request $request)
    {
        $email = $request->session()->get('email');
        if (!$email) {
            return redirect()->route('password.forgot.form')->withErrors(['email' => custom_trans('Please enter your email first.', 'front')]);
        }
        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Verify OTP and redirect to set new password
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = $request->email;
        $otp = $request->otp;
        $cacheKey = self::OTP_CACHE_PREFIX . $email;
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp || $storedOtp !== $otp) {
            return back()->withErrors(['otp' => custom_trans('Invalid or expired OTP. Please request a new one.', 'front')])->withInput($request->only('email'));
        }

        $request->session()->put(['email' => $email, 'otp' => $otp]);
        return redirect()->route('password.reset.form');
    }

    /**
     * Show reset password form (password only - OTP already verified)
     */
    public function showResetForm(Request $request)
    {
        $email = $request->session()->get('email');
        $otp = $request->session()->get('otp');
        if (!$email || !$otp) {
            return redirect()->route('password.forgot.form')->withErrors(['email' => custom_trans('Please verify your OTP first.', 'front')]);
        }
        return view('auth.reset-password', ['email' => $email, 'otp' => $otp]);
    }

    /**
     * Reset password (OTP already verified in session)
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

        // Re-verify OTP (might have expired)
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

        $request->session()->forget(['email', 'otp']);
        return redirect()->route('login')->with('success', custom_trans('Password reset successfully. You can now log in with your new password.', 'front'));
    }
}
