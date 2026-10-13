<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable NBE as the card gateway
    |--------------------------------------------------------------------------
    |
    | When true, the "visa" checkout option goes through NBE (Mastercard
    | Hosted Checkout) instead of CyberSource Unified Checkout.
    |
    */
    'enabled' => (bool) env('NBE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Gateway host
    |--------------------------------------------------------------------------
    |
    | Test: https://test-nbe.gateway.mastercard.com
    | Live: provided by NBE after test sign-off.
    |
    */
    'base_url' => rtrim(env('NBE_BASE_URL', 'https://test-nbe.gateway.mastercard.com'), '/'),

    'api_version' => env('NBE_API_VERSION', '100'),

    /*
    |--------------------------------------------------------------------------
    | Merchant Credentials
    |--------------------------------------------------------------------------
    |
    | The API username is always "merchant.{MERCHANT_ID}".
    |
    */
    'merchant_id' => env('NBE_MERCHANT_ID'),
    'api_password' => env('NBE_API_PASSWORD'),
    'merchant_name' => env('NBE_MERCHANT_NAME', env('APP_NAME')),

    /*
    |--------------------------------------------------------------------------
    | Charge currency & SAR conversion
    |--------------------------------------------------------------------------
    |
    | Orders are priced/displayed in SAR, but NBE charges in the merchant's
    | currency (the test merchant TESTEGPTEST only accepts EGP).
    | NBE_SAR_RATE is how much 1 SAR is worth in NBE_CURRENCY.
    |
    */
    'currency' => env('NBE_CURRENCY', 'EGP'),

    'sar_rate' => (float) env('NBE_SAR_RATE', 13.0),
];
