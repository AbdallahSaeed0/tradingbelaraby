<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedForAuthUsers
{
    /**
     * Routes that require email verification. Unverified users can browse all other pages.
     */
    protected array $verificationRequiredRoutes = [
        'profile.*',
        'student.*',
        'cart.*',
        'wishlist.*',
        'checkout.*',
        'purchase.*',
        'purchases.*',
        'notifications.*',
        'enrollments.*',
        'progress.*',
        'certificate.*',
        'homework.*',
        'qa.*',
        'courses.learn',
        'courses.enroll',
        'courses.review',
        'courses.schedule-live-class',
        'courses.join-live-class',
        'courses.live-classes.*',
        'courses.submit-homework*',
        'live-classes.register',
        'live-classes.unregister',
        'live-classes.join',
        'tabby.*',
        'paypal.success',
        'paypal.cancel',
        'paypal.failure',
        'bundles.index',
        'bundles.show',
    ];

    /**
     * Redirect authenticated (frontend) users who have not verified their email
     * to the verification notice page ONLY when accessing routes that require verification.
     * Unverified users can browse public pages (home, courses, blog, etc.).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('web')->check()) {
            return $next($request);
        }

        $user = auth()->guard('web')->user();

        if (!$user || !method_exists($user, 'hasVerifiedEmail')) {
            return $next($request);
        }

        if ($user->hasVerifiedEmail()) {
            return $next($request);
        }

        // Always allow verification routes and logout
        if ($request->routeIs('verification.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Allow access unless the current route requires verification
        foreach ($this->verificationRequiredRoutes as $pattern) {
            if ($request->routeIs($pattern)) {
                return redirect()->route('verification.notice');
            }
        }

        return $next($request);
    }
}
