<?php

return [
    'base_url' => env('TABBY_BASE_URL', 'https://api.tabby.ai'),
    'public_key' => env('TABBY_PUBLIC_KEY'), // For frontend if needed
    'secret_key' => env('TABBY_SECRET_KEY'),
    'merchant_code' => env('TABBY_MERCHANT_CODE'),
    'currency' => env('TABBY_CURRENCY', 'SAR'),
    'urls' => [
        'success' => env('TABBY_SUCCESS_URL', env('APP_URL') . '/tabby/success'),
        'failure' => env('TABBY_FAILURE_URL', env('APP_URL') . '/tabby/failure'),
        'cancel' => env('TABBY_CANCEL_URL', env('APP_URL') . '/tabby/cancel'),
    ],
];

