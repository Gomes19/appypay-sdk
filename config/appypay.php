<?php

return [
    'base_url' => env('APPYPAY_BASE_URL', 'https://gwy-api.appypay.co.ao/v2.0'),
    'token_url' => env('APPYPAY_TOKEN_URL', 'https://auth.appypay.co.ao/connect/token'),
    'client_id' => env('APPYPAY_CLIENT_ID'),
    'client_secret' => env('APPYPAY_CLIENT_SECRET'),
    'resource' => env('APPYPAY_RESOURCE', 'https://gwy-api.appypay.co.ao'),
    'timeout' => env('APPYPAY_TIMEOUT', 120),
    'connect_timeout' => env('APPYPAY_CONNECT_TIMEOUT', 15),
    'accept_language' => env('APPYPAY_ACCEPT_LANGUAGE', 'pt-AO'),
    'assertion' => env('APPYPAY_ASSERTION'),
    'payment_methods' => [
        'gpo_qr' => env('APPYPAY_PAYMENT_METHOD_GPO_QR'),
        'gpo_express' => env('APPYPAY_PAYMENT_METHOD_GPO_EXPRESS'),
        'ref' => env('APPYPAY_PAYMENT_METHOD_REFERENCE'),
    ],
];
