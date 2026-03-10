<?php

namespace App\Http\Middleware;

use App\Helpers\TranslationHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set application locale from session (or ?lang= param) so __() and custom_trans use the right language.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Optional: support ?lang=en or ?lang=ar to switch language and redirect (avoids caching wrong language)
        $langParam = $request->query('lang');
        if ($langParam !== null && in_array($langParam, ['en', 'ar'], true)) {
            if (TranslationHelper::setFrontendLanguage($langParam)) {
                TranslationHelper::clearCache();
                return redirect()->to($request->url())->with('lang_set', $langParam);
            }
        }

        $isAdmin = $request->is('admin*');
        $sessionKey = $isAdmin ? 'admin_locale' : 'frontend_locale';
        $default = $isAdmin ? 'en' : config('app.locale');
        $locale = $request->session()->get($sessionKey, $default);

        if (in_array($locale, ['en', 'ar'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
