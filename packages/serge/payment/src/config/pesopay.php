<?php 

return [
    'enable' => env('PAYMENT_PESOPAY_ENABLE'),
    'default' => env('PAYMENT_PESOPAY_SANDBOX'),
    'live' => [
        'merchantID' => env('PAYMENT_PESOPAY_LIVE_MERCHANT_ID'),
        'secureHashSecret' => env('PAYMENT_PESOPAY_LIVE_SECURE_HASH_SECRET'),
        'paymentUrl' => env('PAYMENT_PESOPAY_LIVE_PAYMENT_URL'),
    ],
    'sandbox' => [
        'merchantID' => env('PAYMENT_PESOPAY_SANDBOX_MERCHANT_ID'),
        'secureHashSecret' => env('PAYMENT_PESOPAY_SANDBOX_SECURE_HASH_SECRET'),
        'paymentUrl' => env('PAYMENT_PESOPAY_SANDBOX_PAYMENT_URL'),
    ],
    'method' => env('PAYMENT_PESOPAY_METHOD'),
    'type' => env('PAYMENT_PESOPAY_TYPE'),
    'currencyCode' => env('PAYMENT_PESOPAY_CURRENCY_CODE'),
    'language' => env('PAYMENT_PESOPAY_LANGUAGE'),
    'successUrl' => env('PAYMENT_PESOPAY_SUCCESS_URL'),
    'failUrl' => env('PAYMENT_PESOPAY_FAIL_URL'),
    'cancelUrl' => env('PAYMENT_PESOPAY_CANCELL_URL'),
];