<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if coming soon mode is enabled
        $settings = \App\Models\MainContentSettings::getActive();
        $comingSoonEnabled = $settings ? $settings->coming_soon_enabled : false;

        if (!$comingSoonEnabled) {
            return $next($request);
        }

                // Allow admin routes
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        // Allow authentication routes
        if ($request->is('login') || $request->is('register') || $request->is('logout')) {
            return $next($request);
        }

        // Allow coming soon routes
        if ($request->is('coming-soon') || $request->is('coming-soon/*')) {
            return $next($request);
        }

        // Allow API routes for coming soon form
        if ($request->is('api/coming-soon/*')) {
            return $next($request);
        }

        // Allow asset routes
        if ($request->is('css/*') || $request->is('js/*') || $request->is('images/*') || $request->is('storage/*')) {
            return $next($request);
        }

        // Redirect all other requests to coming soon page
        return redirect()->route('coming-soon');
    }
}
