<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectPlatform
{
    public function handle(Request $request, Closure $next): Response
    {
        $platform = strtolower((string) $request->header('X-Platform', ''));

        if (in_array($platform, ['ios', 'android'], true)) {
            $request->attributes->set('client_platform', $platform);
        } else {
            $request->attributes->set('client_platform', 'unknown');
        }

        return $next($request);
    }
}
