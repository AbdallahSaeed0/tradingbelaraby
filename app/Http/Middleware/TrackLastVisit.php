<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records when a signed-in student last used the website or the app (users.last_visit_at).
 * Written at most once every few minutes per user, without touching updated_at.
 */
class TrackLastVisit
{
    private const EVERY_MINUTES = 5;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            // Web session user, or the app's token user once auth:sanctum has resolved it
            $user = $request->user() ?? auth()->guard('web')->user();

            if ($user instanceof \App\Models\User) {
                $last = $user->last_visit_at;

                if (!$last || $last->lt(now()->subMinutes(self::EVERY_MINUTES))) {
                    DB::table('users')->where('id', $user->id)->update(['last_visit_at' => now()]);
                }
            }
        } catch (\Throwable $e) {
            // Never break a page over this (e.g. before the migration has run)
        }

        return $response;
    }
}
