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
     * Students can sign up, enroll and pay without verifying first (enrollment is kept),
     * but opening the course content and getting a certificate need a verified account
     * (email or WhatsApp, per admin settings).
     *
     * Note: certificate.verify is the public certificate lookup page and is NOT gated.
     */
    protected array $verificationRequiredRoutes = [
        'courses.learn',
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
        $message = $request->routeIs('courses.learn')
            ? custom_trans('Please verify your account to open the course.', 'front')
            : custom_trans('Please verify your account to get your certificate.', 'front');

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
