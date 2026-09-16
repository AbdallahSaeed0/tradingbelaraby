<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/auth/google/callback'),
        'android_client_id' => env('GOOGLE_ANDROID_CLIENT_ID'),
        'ios_client_id' => env('GOOGLE_IOS_CLIENT_ID'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URI', env('APP_URL') . '/auth/twitter/callback'),
    ],

    /*
    | Sign in with Apple (native app uses identity token; web uses redirect).
    | Optional: key_id, team_id, private_key for JWT client_secret generation — see socialiteproviders/apple docs.
    */
    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID'),
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect' => env('APPLE_REDIRECT_URI', env('APP_URL') . '/auth/apple/callback'),
        'key_id' => env('APPLE_KEY_ID'),
        'team_id' => env('APPLE_TEAM_ID'),
        'private_key' => env('APPLE_PRIVATE_KEY'),
        'passphrase' => env('APPLE_PASSPHRASE'),
        'iap_shared_secret' => env('APPLE_IAP_SHARED_SECRET'),
        'iap_key_id' => env('APPLE_IAP_KEY_ID'),
        'iap_issuer_id' => env('APPLE_IAP_ISSUER_ID'),
        'iap_private_key_path' => env('APPLE_IAP_PRIVATE_KEY_PATH', storage_path('app/apple/SubscriptionKey_' . env('APPLE_IAP_KEY_ID') . '.p8')),
        'iap_bundle_id' => env('APPLE_IAP_BUNDLE_ID', 'com.education.coursesApp'),
    ],

    /*
    | Google Play Billing: purchase verification (Android app) and automatic
    | in-app product provisioning for paid courses via the Android Publisher API.
    */
    'google_play' => [
        'package_name' => env('GOOGLE_PLAY_PACKAGE_NAME', 'com.education.coursesApp'),
        // Relative env values are anchored to base_path() so this resolves the same
        // whether it's read from `php artisan` (cwd = app root) or a web request
        // (cwd = public/, since that's the actual document root on this server).
        'service_account_path' => ($p = env('GOOGLE_PLAY_SERVICE_ACCOUNT_PATH'))
            ? (str_starts_with($p, '/') || preg_match('#^[A-Za-z]:[\\\\/]#', $p) ? $p : base_path($p))
            : storage_path('app/google/play-service-account.json'),
        'default_currency' => env('GOOGLE_PLAY_DEFAULT_CURRENCY', 'SAR'),
        'product_id_prefix' => env('GOOGLE_PLAY_PRODUCT_ID_PREFIX', 'course_'),
    ],

    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
    ],

    'telegram' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'meta_whatsapp' => [
        'token'           => env('META_WHATSAPP_TOKEN'),
        'phone_number_id' => env('META_WHATSAPP_PHONE_NUMBER_ID'),
        'otp_template'    => env('META_WHATSAPP_OTP_TEMPLATE', 'otp_verification'),
        'api_version'     => env('META_WHATSAPP_API_VERSION', 'v21.0'),
        'verify_token'    => env('META_WHATSAPP_VERIFY_TOKEN'),
    ],

];
