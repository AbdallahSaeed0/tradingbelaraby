<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pending card payment timeout
    |--------------------------------------------------------------------------
    |
    | A card order (visa / stripe) that is still unpaid after this many minutes
    | is marked "failed" and its pending enrollments are cancelled, so the
    | student can try to buy the course again. Bank transfers are not affected:
    | they stay pending until an admin confirms or rejects them.
    |
    */

    'pending_timeout_minutes' => (int) env('PAYMENT_PENDING_TIMEOUT', 15),

    'expirable_methods' => ['visa', 'stripe'],

];
