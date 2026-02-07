<?php

return [
    'merchant_id' => env('OPENPAY_MERCHANT_ID'),
    'private_key' => env('OPENPAY_PRIVATE_KEY'),
    'public_key' => env('OPENPAY_PUBLIC_KEY'),
    'sandbox' => env('OPENPAY_SANDBOX', true),
    'country' => env('OPENPAY_COUNTRY', 'mx'),
    'webhook_secret' => env('OPENPAY_WEBHOOK_SECRET'),
    'webhook_user' => env('OPENPAY_WEBHOOK_USER'),
    'webhook_password' => env('OPENPAY_WEBHOOK_PASSWORD'),
];  