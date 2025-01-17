<?php

return [
    'credit_card' => [
        'api_key' => env('CREDIT_CARD_API_KEY'),
        'secret' => env('CREDIT_CARD_SECRET'),
        'sandbox' => env('CREDIT_CARD_SANDBOX', true),
    ],
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'sandbox' => env('PAYPAL_SANDBOX', true),
    ],
];