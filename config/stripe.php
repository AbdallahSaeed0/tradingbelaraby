<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Stripe as the card gateway
    |--------------------------------------------------------------------------
    |
    | When true, the "visa" checkout option goes through Stripe Checkout
    | (hosted payment page). Takes priority over NBE and CyberSource.
    |
    */
    'enabled' => (bool) env('STRIPE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | API keys
    |--------------------------------------------------------------------------
    |
    | Dashboard → Developers → API keys. Use sk_test_/pk_test_ keys while
    | testing and sk_live_/pk_live_ keys in production.
    |
    */
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Webhook signing secret
    |--------------------------------------------------------------------------
    |
    | Dashboard → Developers → Webhooks → add endpoint {APP_URL}/webhook/stripe
    | listening to checkout.session.completed and
    | checkout.session.async_payment_succeeded, then copy its whsec_ secret.
    |
    */
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Charge currency & SAR conversion
    |--------------------------------------------------------------------------
    |
    | Orders are priced in SAR. Stripe supports SAR directly, so the default
    | rate is 1. To charge in another currency (e.g. USD) set STRIPE_CURRENCY
    | and STRIPE_SAR_RATE to what 1 SAR is worth in it (USD: 0.2667).
    |
    */
    'currency' => strtolower(env('STRIPE_CURRENCY', 'sar')),

    'sar_rate' => (float) env('STRIPE_SAR_RATE', 1.0),
];
