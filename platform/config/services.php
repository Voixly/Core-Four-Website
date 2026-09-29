<?php

return [
    'postmark' => ['token' => env('POSTMARK_TOKEN')],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'resend' => ['key' => env('RESEND_API_KEY', env('RESEND_KEY'))],
    'jobnimbus' => [
        'key' => env('JOBNIMBUS_API_KEY'),
        'actor' => env('JOBNIMBUS_ACTOR', 'bmedina@corefourroofing.com'),
        'sales_rep' => env('JOBNIMBUS_SALES_REP', 'Bryan Medina'),
        'location' => env('JOBNIMBUS_LOCATION', 'Core Four Roofing'),
        'record_type' => env('JOBNIMBUS_RECORD_TYPE', 'Customer'),
        'status' => env('JOBNIMBUS_STATUS', 'New'),
    ],
];
