<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\EnrollmentFlowService;
use App\Services\SocialLoginSettingsService;
use App\Services\VerificationSettingsService;
use App\Services\WhatsAppService;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(Request $request, SocialLoginSettingsService $socialSettings)
    {
        app(EnrollmentFlowService::class)->rememberIntentFromRequest($request);
        $this->rememberReturnUrl();

        return view('auth.login', [
            'googleLoginEnabled' => $socialSettings->isGoogleEnabled(),
            'twitterLoginEnabled' => $socialSettings->isTwitterEnabled(),
        ]);
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Attempt default user guard
        if (Auth::attempt($credentials, $remember)) {
            // Unverified accounts may sign in too: verification is only required
            // for certificates (see EnsureEmailIsVerifiedForAuthUsers).
            $request->session()->regenerate();

            $redirect = app(EnrollmentFlowService::class)->completeIntent(Auth::user());

            return $redirect ? redirect($redirect) : redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Show registration form.
     */
    public function showRegisterForm(Request $request, SocialLoginSettingsService $socialSettings)
    {
        app(EnrollmentFlowService::class)->rememberIntentFromRequest($request);
        $this->rememberReturnUrl();

        return view('auth.register', [
            'googleLoginEnabled' => $socialSettings->isGoogleEnabled(),
            'enrollCourseId' => session(EnrollmentFlowService::SESSION_KEY),
        ]);
    }

    /**
     * Remember the page the visitor came from (e.g. a course page) so that after
     * login / registration + verification they land back on it instead of home.
     */
    private function rememberReturnUrl(): void
    {
        if (session()->has('url.intended')) {
            return;
        }

        $previous = url()->previous();
        $previousHost = parse_url($previous, PHP_URL_HOST);
        $path = '/' . ltrim((string) parse_url($previous, PHP_URL_PATH), '/');

        if ($previousHost !== request()->getHost()) {
            return;
        }

        // Don't bounce back to auth / verification pages
        foreach (['/login', '/register', '/password', '/email', '/whatsapp', '/auth', '/logout', '/complete-profile'] as $skip) {
            if (str_contains($path, $skip)) {
                return;
            }
        }

        if ($previous !== url()->current()) {
            redirect()->setIntendedUrl($previous);
        }
    }

    /**
     * Check if email is available (for JS validation before submit).
     */
    public function checkEmail(Request $request)
    {
        $email = $request->query('email');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['available' => true]);
        }
        $exists = User::where('email', $email)->exists();
        return response()->json(['available' => !$exists]);
    }

    /**
     * Handle registration request (full page form or the quick sign-up modal via JSON).
     *
     * Only name, email and password are required. Country is detected from the visitor's
     * IP when not sent, and the phone number is optional. The account is usable right
     * away: the verification email goes out in the background and is only required for
     * certificates. If the visitor came from a course's "Enroll" button, the enrollment
     * is completed here too.
     */
    public function register(Request $request)
    {
        $this->guardAgainstSpamRegistration($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:6'],
            'country' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'enroll' => ['nullable', 'integer'],
        ], [
            'name.required' => custom_trans('Please enter your name.', 'front'),
            'name.max' => 'Name must not exceed 255 characters.',
            'email.required' => custom_trans('Please enter your email address.', 'front'),
            'email.email' => custom_trans('Please enter a valid email address.', 'front'),
            'email.unique' => custom_trans('This email is already registered. Please sign in or use a different email.', 'front'),
            'phone.regex' => custom_trans('Please enter a valid phone number for the selected country.', 'front'),
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'password.required' => custom_trans('Please enter a password.', 'front'),
            'password.min' => custom_trans('Password must be at least 6 characters.', 'front'),
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'country'  => $data['country'] ?? app(\App\Services\VisitorCountryService::class)->detect($request),
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();
        ga_event('sign_up', ['method' => 'email']);

        // Verification email is queued and never blocks the student
        try {
            if (app(VerificationSettingsService::class)->isEmailEnabled()) {
                event(new Registered($user));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to queue verification email', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }

        $flow = app(EnrollmentFlowService::class);
        if (!empty($data['enroll'])) {
            $flow->rememberIntent((int) $data['enroll']);
        }

        $redirect = $flow->completeIntent($user)
            ?? redirect()->intended(route('home'))->getTargetUrl();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => $redirect]);
        }

        return redirect($redirect)->with('success', custom_trans('Welcome! Your account is ready.', 'front'));
    }

    /**
     * Cheap bot filters run before validation. Throws a validation error (works for
     * both the normal form and JSON requests from the quick sign-up modal).
     */
    private function guardAgainstSpamRegistration(Request $request): void
    {
        $email = (string) $request->email;
        $ip = $request->ip();

        // Honeypot - hidden field only bots fill in
        if ($request->filled('website')) {
            Log::warning('Spam registration attempt detected (honeypot)', ['ip' => $ip, 'email' => $email]);
            throw ValidationException::withMessages(['email' => 'Registration failed. Please try again.']);
        }

        // Form must take at least 3 seconds to fill
        $formStartTime = $request->input('form_start_time');
        if ($formStartTime && (time() - (int) $formStartTime) < 3) {
            Log::warning('Spam registration attempt detected (too fast)', [
                'ip' => $ip,
                'email' => $email,
                'time_taken' => time() - (int) $formStartTime,
            ]);
            throw ValidationException::withMessages(['email' => 'Registration failed. Please take your time filling the form.']);
        }

        // Many sign-ups with the same email prefix in the last hour (common spam pattern)
        $emailPrefix = explode('@', $email)[0] ?? '';
        if ($emailPrefix !== '') {
            $similarRegistrations = User::where('created_at', '>=', now()->subHour())
                ->where('email', 'like', $emailPrefix . '@%')
                ->count();

            if ($similarRegistrations >= 2) {
                Log::warning('Spam registration attempt detected (too many similar emails)', [
                    'ip' => $ip,
                    'email' => $email,
                    'count' => $similarRegistrations,
                ]);
                throw ValidationException::withMessages(['email' => 'Too many registration attempts. Please try again later.']);
            }
        }

        $suspiciousPatterns = [
            '/^[a-z0-9]+([._-][a-z0-9]+)*@(10minutemail|guerrillamail|tempmail|mailinator|throwaway|trashmail|temp-mail)\./i',
            '/^test\d+@/i',
            '/^user\d+@/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                Log::warning('Spam registration attempt detected (suspicious email)', ['ip' => $ip, 'email' => $email]);
                throw ValidationException::withMessages(['email' => 'Please use a valid email address.']);
            }
        }
    }

    /**
     * Show the WhatsApp OTP verification page.
     */
    public function showWhatsappVerify()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        if ($user->hasVerifiedPhone()) {
            return redirect()->route('home');
        }
        return view('auth.verify-whatsapp');
    }

    /**
     * Handle WhatsApp OTP verification form submission.
     */
    public function verifyWhatsappOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedPhone()) {
            return redirect()->route('home');
        }

        if (!$user->phone) {
            return back()->withErrors(['otp' => 'No phone number is associated with your account.']);
        }

        $whatsapp = app(WhatsAppService::class);

        if (!$whatsapp->verifyOtp($user->phone, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code. Please request a new one.'])->withInput();
        }

        $user->markPhoneAsVerified();

        return redirect()->intended(route('home'))->with('success', 'Phone number verified successfully! Welcome!');
    }

    /**
     * Resend WhatsApp OTP for the currently authenticated user.
     */
    public function resendWhatsappOtp(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->phone) {
            return back()->withErrors(['otp' => 'Unable to resend. Please register again.']);
        }

        if ($user->hasVerifiedPhone()) {
            return redirect()->route('home');
        }

        app(WhatsAppService::class)->sendOtp($user->phone);

        return back()->with('success', 'A new verification code has been sent to your WhatsApp.');
    }
}
