<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Services\SocialLoginSettingsService;

class AuthController extends Controller
{
    /**
     * Public auth config for mobile (Google/Twitter client IDs from dashboard).
     */
    public function config(SocialLoginSettingsService $social): JsonResponse
    {
        $google = $social->getGoogleConfig();
        $twitter = $social->getTwitterConfig();
        return response()->json([
            'success' => true,
            'data' => [
                'google_login_enabled' => $social->isGoogleEnabled(),
                'google_web_client_id' => $google['client_id'] ?? '',
                'twitter_login_enabled' => $social->isTwitterEnabled(),
                'twitter_client_id' => $twitter['client_id'] ?? '',
            ],
        ]);
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'country' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
        ], [
            'phone.regex' => 'Phone number must contain only numbers (and an optional + at the start).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'phone' => $request->phone,
        ]);

        // Fire the Registered event which will trigger the email verification notification
        event(new Registered($user));

        // Create token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please check your email to verify your account before logging in.',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // Check if email is verified
        if (!$user->email_verified_at) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Email not verified. Please check your inbox and verify your email address before logging in.',
                'email_verified' => false,
            ], 403);
        }

        // Create token using Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Login with Google ID token (for Flutter app). Verifies token then find/create user, return Sanctum token.
     * Social logins are treated as verified; no verification email is sent.
     */
    public function loginWithGoogle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = app(\App\Services\SocialLoginSettingsService::class);
        $googleConfig = $settings->getGoogleConfig();
        $clientId = $googleConfig['client_id'] ?? null;
        $allowedAudiences = array_filter([
            $clientId,
            $googleConfig['android_client_id'] ?? null,
            $googleConfig['ios_client_id'] ?? null,
        ]);

        if (!$settings->isGoogleEnabled() || empty($clientId)) {
            return response()->json([
                'success' => false,
                'message' => 'Google login is not configured',
            ], 503);
        }

        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        if (!$response->successful()) {
            Log::warning('Google tokeninfo failed', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Google token',
            ], 401);
        }

        $payload = $response->json();
        $aud = $payload['aud'] ?? null;
        if (!$aud || !in_array($aud, $allowedAudiences, true)) {
            Log::warning('Google token audience mismatch', ['aud' => $aud, 'allowed' => $allowedAudiences]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid Google token audience',
            ], 401);
        }

        $sub = $payload['sub'] ?? null;
        $email = $payload['email'] ?? null;
        $name = $payload['name'] ?? 'User';
        $picture = $payload['picture'] ?? null;

        if (!$sub || !$email) {
            return response()->json([
                'success' => false,
                'message' => 'Google account email is required',
            ], 400);
        }

        $user = User::where('google_id', $sub)->first();
        if ($user) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
            ]);
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $user->update([
                'google_id' => $sub,
                'avatar' => $picture ?: $user->avatar,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
            ]);
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => null,
            'google_id' => $sub,
            'avatar' => $picture,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login with Twitter access token (for Flutter app). Fetches user from Twitter then find/create user, return Sanctum token.
     * Social logins are treated as verified; no verification email is sent.
     */
    public function loginWithTwitter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'access_token' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = app(\App\Services\SocialLoginSettingsService::class);
        if (!$settings->isTwitterEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Twitter login is not configured',
            ], 503);
        }

        $response = Http::withToken($request->access_token)
            ->get('https://api.twitter.com/2/users/me', [
                'user.fields' => 'id,name,profile_image_url',
            ]);

        if (!$response->successful()) {
            Log::warning('Twitter users/me failed', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Twitter token',
            ], 401);
        }

        $data = $response->json('data');
        if (empty($data) || empty($data['id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Could not get Twitter user',
            ], 401);
        }

        $twitterId = $data['id'];
        $name = $data['name'] ?? 'User';
        $avatar = $data['profile_image_url'] ?? null;

        $user = User::where('twitter_id', $twitterId)->first();
        if ($user) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
            ]);
        }

        $email = $request->input('email');
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'twitter_id' => $twitterId,
                    'avatar' => $avatar ?: $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => new UserResource($user),
                        'token' => $token,
                    ],
                ]);
            }
        }

        if (empty($email)) {
            $email = 'x+' . $twitterId . '@users.noreply.local';
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => null,
            'twitter_id' => $twitterId,
            'avatar' => $avatar,
            'email_verified_at' => now(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user),
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * Verify email with code
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // In a real implementation, you would verify the code against a stored verification code
        // For now, we'll just mark the email as verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email already verified',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent',
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]{9,15}$/'],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]{9,15}$/'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'country' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar_url' => ['nullable', 'string', 'url'],
        ], [
            'phone.regex' => 'Please enter a valid phone number (at least 9 digits).',
            'phone_number.regex' => 'Please enter a valid phone number (at least 9 digits).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->only(['name', 'email', 'phone', 'gender', 'date_of_birth', 'country', 'bio']);

        // Handle phone_number or phone
        if ($request->has('phone_number')) {
            $data['phone'] = $request->phone_number;
        }

        // Handle avatar_url
        if ($request->has('avatar_url')) {
            $data['avatar'] = $request->avatar_url;
        }

        // Remove null values
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Delete user account (auth required)
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string'],
            'confirmation' => ['required', 'string', 'in:DELETE'],
        ], [
            'confirmation.in' => 'Please type DELETE to confirm account deletion.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password',
            ], 401);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        DB::transaction(function () use ($user) {
            $user->wishlistItems()->delete();
            $user->cartItems()->delete();
            $user->lectureCompletions()->delete();
            \App\Models\LiveClassRegistration::where('user_id', $user->id)->delete();
            $user->enrollments()->delete();
            foreach ($user->orders as $order) {
                $order->items()->delete();
            }
            $user->orders()->delete();
            \App\Models\CourseRating::where('user_id', $user->id)->delete();
            \App\Models\QuestionsAnswer::where('user_id', $user->id)->delete();
            \App\Models\QuizAttempt::where('user_id', $user->id)->delete();
            \App\Models\HomeworkSubmission::where('user_id', $user->id)->delete();
            \App\Models\CouponUsage::where('user_id', $user->id)->delete();
            $user->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }
}
