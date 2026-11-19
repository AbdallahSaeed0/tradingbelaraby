{{--
    "Get the app" banner shown at the top of the site on phone browsers only.
    Android → Google Play, iPhone/iPad → App Store. Hidden inside the app's own WebView,
    on desktop, and for 7 days after the visitor closes it.
    Needs: $faviconUrl (app icon)
--}}
<div id="appDownloadBanner" class="app-dl-banner" hidden role="region" aria-label="{{ custom_trans('Download our app', 'front') }}">
    <button type="button" class="app-dl-banner__close" data-app-dl-close aria-label="{{ custom_trans('Close', 'front') }}">&times;</button>
    <img src="{{ $faviconUrl }}" alt="" class="app-dl-banner__icon" width="40" height="40">
    <div class="app-dl-banner__text">
        <strong>{{ custom_trans('Trading Bel Araby app', 'front') }}</strong>
        <span>{{ custom_trans('A better experience: lessons, live sessions and alerts on your phone.', 'front') }}</span>
    </div>
    <a href="#" class="app-dl-banner__cta" data-app-dl-link target="_blank" rel="noopener">{{ custom_trans('Download', 'front') }}</a>
</div>

<style>
    .app-dl-banner { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: #fff; border-bottom: 1px solid rgba(0, 0, 0, .08); box-shadow: 0 2px 6px rgba(0, 0, 0, .06); position: relative; z-index: 1031; }
    .app-dl-banner[hidden] { display: none; }
    .app-dl-banner__close { background: none; border: 0; font-size: 22px; line-height: 1; color: #888; padding: 0 4px; }
    .app-dl-banner__icon { width: 40px; height: 40px; border-radius: 10px; object-fit: contain; flex-shrink: 0; }
    .app-dl-banner__text { display: flex; flex-direction: column; min-width: 0; flex: 1; font-size: 12px; line-height: 1.35; color: #555; }
    .app-dl-banner__text strong { font-size: 14px; color: #1f2937; }
    .app-dl-banner__cta { flex-shrink: 0; background: #e85d26; color: #fff !important; font-weight: 700; font-size: 13px; padding: 7px 14px; border-radius: 999px; text-decoration: none; }
</style>

<script>
    (function () {
        var banner = document.getElementById('appDownloadBanner');
        if (!banner) return;

        var ua = navigator.userAgent || '';
        var isAndroid = /Android/i.test(ua);
        var isIOS = /iPhone|iPad|iPod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        // Inside an app WebView (our own app, Instagram, etc.): Android adds "; wv)", iOS WebViews lack "Safari"
        var inWebView = (isAndroid && /; wv\)/.test(ua)) || (isIOS && !/Safari/i.test(ua));
        if ((!isAndroid && !isIOS) || inWebView) return;

        var STORAGE_KEY = 'appBannerClosedAt';
        var HIDE_FOR_MS = 7 * 24 * 60 * 60 * 1000;
        try {
            var closedAt = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10);
            if (closedAt && Date.now() - closedAt < HIDE_FOR_MS) return;
        } catch (e) { /* storage blocked: just show the banner */ }

        banner.querySelector('[data-app-dl-link]').href = isAndroid
            ? 'https://play.google.com/store/apps/details?id=com.tradingbelaraby.courses'
            : 'https://apps.apple.com/app/id6761126211';
        banner.hidden = false;

        banner.querySelector('[data-app-dl-close]').addEventListener('click', function () {
            banner.hidden = true;
            try { localStorage.setItem(STORAGE_KEY, String(Date.now())); } catch (e) {}
        });
    })();
</script>
