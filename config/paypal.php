<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayPal Mode
    |--------------------------------------------------------------------------
    |
    | Mode can be 'sandbox' or 'live'. Use 'sandbox' for testing and
    | 'live' for production transactions.
    |
    */
    'mode' => env('PAYPAL_MODE', 'live'),

    /*
    |--------------------------------------------------------------------------
    | PayPal API Credentials
    |--------------------------------------------------------------------------
    |
    | Your PayPal REST API credentials from the PayPal Developer Dashboard
    |
    */
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'secret' => env('PAYPAL_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | PayPal API URLs
    |--------------------------------------------------------------------------
    |
    | Base URLs for PayPal API endpoints
    |
    */
    'base_url' => env('PAYPAL_MODE', 'live') === 'sandbox'
        ? 'https://api-m.sandbox.paypal.com'
        : 'https://api-m.paypal.com',

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Default currency for PayPal transactions
    |
    */
    'currency' => env('PAYPAL_CURRENCY', 'SAR'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URLs
    |--------------------------------------------------------------------------
    |
    | URLs for handling PayPal payment flow redirects
    |
    */
    'urls' => [
        'success' => env('PAYPAL_SUCCESS_URL', env('APP_URL') . '/paypal/success'),
        'cancel' => env('PAYPAL_CANCEL_URL', env('APP_URL') . '/paypal/cancel'),
        'failure' => env('PAYPAL_FAILURE_URL', env('APP_URL') . '/paypal/failure'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Webhook ID from PayPal Developer Dashboard for signature verification
    |
    */
    'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
];

