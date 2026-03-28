<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تم الشراء بنجاح</title>
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            margin: 0;
            padding: 24px;
            background: #f5f5f5;
            color: #1a1a1a;
            text-align: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 32px 24px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        h1 {
            font-size: 1.25rem;
            margin: 0 0 16px;
            line-height: 1.5;
            color: #17506B;
        }
        p { margin: 0 0 20px; color: #444; line-height: 1.6; }
        .btn {
            display: inline-block;
            background: #F15A29;
            color: #fff !important;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn:hover { opacity: 0.92; }
        #store-wrap { display: none; margin-top: 24px; padding-top: 20px; border-top: 1px solid #eee; }
        #store-wrap.visible { display: block; }
        .store-link { color: #17506B; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>تم الشراء بنجاح! افتح التطبيق للمتابعة</h1>
        <p>إذا لم يُفتح التطبيق تلقائياً، اضغط الزر أدناه.</p>
        <button type="button" class="btn" id="open-app">فتح التطبيق</button>
        <div id="store-wrap">
            <p>لم يُفتح التطبيق؟ حمّله من App Store.</p>
            <a href="#" class="store-link" id="store-link" target="_blank" rel="noopener">App Store</a>
        </div>
    </div>
    @php
        $universalDeepLink = 'https://tradingbelaraby.com/app/enrollment-success?' . http_build_query([
            'course_id' => (string) $courseId,
            'order_id' => (string) $orderId,
        ]);
    @endphp
    <script>
        (function () {
            var deepLink = @json($universalDeepLink);
            var appStoreId = @json($appStoreAppleId ?? '');
            function goApp() {
                window.location.href = deepLink;
            }
            document.getElementById('open-app').addEventListener('click', goApp);
            setTimeout(goApp, 0);
            setTimeout(function () {
                var wrap = document.getElementById('store-wrap');
                var link = document.getElementById('store-link');
                if (appStoreId && String(appStoreId).trim() !== '') {
                    link.href = 'https://apps.apple.com/app/id' + encodeURIComponent(String(appStoreId).trim());
                    wrap.classList.add('visible');
                } else {
                    link.href = 'https://apps.apple.com/search?term=' + encodeURIComponent('تداول بالعربي');
                    wrap.classList.add('visible');
                }
            }, 2000);
        })();
    </script>
</body>
</html>
