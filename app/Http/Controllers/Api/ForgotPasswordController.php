<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ForgotPasswordOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;
    private const OTP_CACHE_PREFIX = 'password_reset_otp:';

    /**
     * Send OTP to email for password reset
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Always return success to prevent email enumeration
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'If the email exists, you will receive an OTP shortly.',
            ]);
        }

        $otp = (string) random_int(100000, 999999);
        $cacheKey = self::OTP_CACHE_PREFIX . $email;
        Cache::put($cacheKey, $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        $language = $request->header('Accept-Language', 'en');
        $language = str_starts_with($language, 'ar') ? 'ar' : 'en';

        $user->notify(new ForgotPasswordOtpNotification($otp, $language));

        return response()->json([
            'success' => true,
            'message' => 'If the email exists, you will receive an OTP shortly.',
        ]);
    }

    /**
     * Verify OTP only (does not consume OTP - used to proceed to password reset step)
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $otp = $request->otp;
        $cacheKey = self::OTP_CACHE_PREFIX . $email;
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp || $storedOtp !== $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please request a new one.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
        ]);
    }

    /**
     * Verify OTP and reset password
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;
        $otp = $request->otp;
        $cacheKey = self::OTP_CACHE_PREFIX . $email;
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp || $storedOtp !== $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please request a new one.',
            ], 400);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            Cache::forget($cacheKey);
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You can now login with your new password.',
        ]);
    }
}
