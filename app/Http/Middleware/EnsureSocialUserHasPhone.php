<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Students who signed up with Google (or X) and never gave a phone number are sent to the
 * complete-profile page to add it before they continue on the website.
 */
class EnsureSocialUserHasPhone
{
    /** Routes that stay reachable while the phone is missing. */
    protected array $allowedRoutes = [
        'auth.x.complete-profile',
        'auth.x.complete-profile.save',
        'logout',
        'language.switch',
        'verification.*',
        'whatsapp.verify*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->guard('web')->user();

        if (
            !$user
            || !$request->isMethod('GET')
            || $request->is('admin', 'admin/*')
            || $request->ajax()
            || $request->expectsJson()
            || $request->routeIs(...$this->allowedRoutes)
            || !self::needsPhone($user)
        ) {
            return $next($request);
        }

        // Come back to this page after saving the number
        session()->put('url.intended', $request->fullUrl());

        return redirect()->route('auth.x.complete-profile')
            ->with('info', custom_trans('Please add your phone number to complete your profile.', 'front'));
    }

    public static function needsPhone($user): bool
    {
        $isSocial = !empty($user->google_id) || !empty($user->twitter_id);

        return $isSocial && trim((string) $user->phone) === '';
    }
}
