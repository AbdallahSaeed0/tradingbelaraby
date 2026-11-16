<?php

namespace App\Http\Middleware;

use App\Services\VerificationSettingsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedForAuthUsers
{
    /**
     * Routes that require a verified account.
     *
     * Students can sign up, enroll, pay and learn without verifying first, so nothing
     * stands between a visitor and the course. Verification (email or WhatsApp,
     * per admin settings) is only required to get a certificate.
     *
     * Note: certificate.verify is the public certificate lookup page and is NOT gated.
     */
    protected array $verificationRequiredRoutes = [
        'certificate.request',
        'certificate.store',
        'certificate.download',
        'certificate.view',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->guard('web')->check()) {
            return $next($request);
        }

        $user = auth()->guard('web')->user();

        if (!$user || !$request->routeIs(...$this->verificationRequiredRoutes)) {
            return $next($request);
        }

        $verification = app(VerificationSettingsService::class);

        if ($verification->isUserVerified($user)) {
            return $next($request);
        }

        session()->put('url.intended', $request->fullUrl());
        $message = custom_trans('Please verify your account to get your certificate.', 'front');

        if ($verification->isWhatsappEnabled() && $user->phone) {
            return redirect()->route('whatsapp.verify')->with('info', $message);
        }

        if ($verification->isEmailEnabled()) {
            return redirect()->route('verification.notice')->with('info', $message);
        }

        // WhatsApp-only verification but no phone on file yet: ask for it first
        return redirect()->route('profile.edit')->with('info', $message);
    }
}
