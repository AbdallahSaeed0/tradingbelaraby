<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
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

        // Attempt default user guard
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if email is verified
            if (!$user->email_verified_at) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Please verify your email address before logging in. Check your inbox for the verification link.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        // Attempt admin guard
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
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
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::check()) {
            Auth::logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    /**
     * Show registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
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
     * Handle registration request.
     */
    public function register(Request $request)
    {
        // Honeypot check - if this field is filled, it's a bot
        if ($request->filled('website')) {
            Log::warning('Spam registration attempt detected (honeypot)', [
                'ip' => $request->ip(),
                'email' => $request->email
            ]);
            return back()->withErrors(['email' => 'Registration failed. Please try again.'])->withInput();
        }

        // Time-based validation - form must take at least 3 seconds to fill
        $formStartTime = $request->input('form_start_time');
        if ($formStartTime && (time() - (int)$formStartTime) < 3) {
            Log::warning('Spam registration attempt detected (too fast)', [
                'ip' => $request->ip(),
                'email' => $request->email,
                'time_taken' => time() - (int)$formStartTime
            ]);
            return back()->withErrors(['email' => 'Registration failed. Please take your time filling the form.'])->withInput();
        }

        // IP-based rate limiting - check if this IP has registered too many accounts recently
        $ip = $request->ip();
        
        // Check for multiple registrations from same email prefix in last hour (common spam pattern)
        $emailPrefix = explode('@', $request->email)[0] ?? '';
        $similarRegistrations = User::where('created_at', '>=', now()->subHour())
            ->where('email', 'like', $emailPrefix . '@%')
            ->count();

        if ($similarRegistrations >= 2) {
            Log::warning('Spam registration attempt detected (too many similar emails)', [
                'ip' => $ip,
                'email' => $request->email,
                'count' => $similarRegistrations
            ]);
            return back()->withErrors(['email' => 'Too many registration attempts. Please try again later.'])->withInput();
        }

        // Check for suspicious email patterns (common spam patterns)
        $email = $request->email;
        $suspiciousPatterns = [
            '/^[a-z0-9]+([._-][a-z0-9]+)*@(10minutemail|guerrillamail|tempmail|mailinator|throwaway|trashmail|temp-mail)\./i',
            '/^test\d+@/i',
            '/^user\d+@/i',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $email)) {
                Log::warning('Spam registration attempt detected (suspicious email)', [
                    'ip' => $ip,
                    'email' => $email
                ]);
                return back()->withErrors(['email' => 'Please use a valid email address.'])->withInput();
            }
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'country' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'name.required' => 'Please enter your name.',
            'name.max' => 'Name must not exceed 255 characters.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please sign in or use a different email.',
            'country.required' => 'Please select your country.',
            'phone.required' => 'Please enter your phone number.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'password.required' => 'Please enter a password.',
            'password.confirmed' => 'Password and confirmation do not match.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'country' => $data['country'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        // Fire the Registered event which will trigger the email verification notification
        event(new Registered($user));

        // Log user in so they can see the verification notice page (which requires auth)
        Auth::login($user);

        return redirect()->route('verification.notice')->with('success', custom_trans('Registration successful! Please check your email to verify your account before logging in.', 'front'));
    }
}
