{{--
    "Get the app" banner pinned to the top of the screen on phone browsers only
    (stays visible while scrolling; the page is padded by its height so nothing hides under it).
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
    .app-dl-banner { display: flex; align-items: center; gap: 10px; padding: 10px 12px; padding-top: max(10px, env(safe-area-inset-top, 0px)); background: #fff; border-bottom: 1px solid rgba(0, 0, 0, .08); box-shadow: 0 2px 10px rgba(0, 0, 0, .12); position: fixed; top: 0; left: 0; right: 0; z-index: 1035; }
    .app-dl-banner[hidden] { display: none; }
    /* 36px tap target (the visible × stays small) */
    .app-dl-banner__close { background: none; border: 0; font-size: 22px; line-height: 1; color: #888; width: 36px; height: 36px; margin: -6px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; }
    /* The mobile menu lives inside the header's layer (z-index 1030), so the banner would cover its top:
       hide the banner while any menu or popup is open */
    .app-dl-banner.is-covered { visibility: hidden; }
    .app-dl-banner__icon { width: 40px; height: 40px; border-radius: 10px; object-fit: contain; flex-shrink: 0; }
    .app-dl-banner__text { display: flex; flex-direction: column; min-width: 0; flex: 1; font-size: 12px; line-height: 1.35; color: #555; }
    .app-dl-banner__text strong { font-size: 14px; color: #1f2937; }
    .app-dl-banner__cta { flex-shrink: 0; background: #e85d26; color: #fff !important; font-weight: 700; font-size: 13px; padding: 7px 14px; border-radius: 999px; text-decoration: none; animation: app-dl-pulse 2.4s ease-in-out infinite; }
    @keyframes app-dl-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(232, 93, 38, .45); } 50% { box-shadow: 0 0 0 7px rgba(232, 93, 38, 0); } }
    @media (prefers-reduced-motion: reduce) { .app-dl-banner__cta { animation: none; } }
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

        // Fixed banner: push the page down by its height (and keep it right on rotate / font load)
        var basePadding = parseFloat(getComputedStyle(document.body).paddingTop) || 0;
        function syncPadding() {
            document.body.style.paddingTop = banner.hidden ? '' : (basePadding + banner.offsetHeight) + 'px';
        }
        syncPadding();
        window.addEventListener('resize', syncPadding);
        window.addEventListener('load', syncPadding);

        // Step aside while the mobile menu (offcanvas) or a popup (modal) is open
        ['show.bs.offcanvas', 'show.bs.modal'].forEach(function (evt) {
            document.addEventListener(evt, function () { banner.classList.add('is-covered'); });
        });
        ['hidden.bs.offcanvas', 'hidden.bs.modal'].forEach(function (evt) {
            document.addEventListener(evt, function () {
                if (!document.querySelector('.offcanvas.show, .modal.show')) banner.classList.remove('is-covered');
            });
        });

        banner.querySelector('[data-app-dl-close]').addEventListener('click', function () {
            banner.hidden = true;
            syncPadding();
            try { localStorage.setItem(STORAGE_KEY, String(Date.now())); } catch (e) {}
        });
    })();
</script>
