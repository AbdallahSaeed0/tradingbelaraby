<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedForAuthUsers
{
    /**
     * Redirect authenticated (frontend) users who have not verified their email
     * to the verification notice page. They can only access verification routes and logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check frontend users (not admin guard)
        if (!auth()->guard('web')->check()) {
            return $next($request);
        }

        $user = auth()->guard('web')->user();

        // Must be a User model with MustVerifyEmail (not Admin)
        if (!$user || !method_exists($user, 'hasVerifiedEmail')) {
            return $next($request);
        }

        if ($user->hasVerifiedEmail()) {
            return $next($request);
        }

        // Allow verification routes and logout
        if ($request->routeIs('verification.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        return redirect()->route('verification.notice');
    }
}
