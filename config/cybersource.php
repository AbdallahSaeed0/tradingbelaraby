<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | 'test' uses the sandbox host (apitest.cybersource.com), 'production'
    | uses the live host (api.cybersource.com).
    |
    */
    'environment' => env('CYBERSOURCE_ENVIRONMENT', 'test'),

    'base_url' => env('CYBERSOURCE_ENVIRONMENT', 'test') === 'production'
        ? 'https://api.cybersource.com'
        : 'https://apitest.cybersource.com',

    /*
    |--------------------------------------------------------------------------
    | Merchant Credentials
    |--------------------------------------------------------------------------
    */
    'merchant_id' => env('CYBERSOURCE_MERCHANT_ID'),
    'key' => env('CYBERSOURCE_KEY'),
    'secret' => env('CYBERSOURCE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Unified Checkout
    |--------------------------------------------------------------------------
    */
    'client_version' => env('CYBERSOURCE_CLIENT_VERSION', '1.0.0'),

    // Every origin that will host the Unified Checkout JS SDK.
    'target_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('CYBERSOURCE_TARGET_ORIGINS', env('APP_URL', '')))
    ))),

    'currency' => env('CYBERSOURCE_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | SAR -> USD conversion
    |--------------------------------------------------------------------------
    |
    | Orders are priced/displayed in SAR, but CyberSource charges in USD.
    | This is how many SAR equal 1 USD.
    |
    */
    'sar_to_usd_rate' => (float) env('CYBERSOURCE_SAR_TO_USD_RATE', 3.75),
];
