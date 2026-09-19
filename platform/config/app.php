<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'Core Four Roofing'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'https://corefourroofing.com'),
    'timezone' => env('APP_TIMEZONE', 'America/Chicago'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'aliases' => Facade::defaultAliases()->merge([])->toArray(),
    'office_phone' => env('OFFICE_PHONE', '(281) 541-0027'),
    'office_phone_tel' => env('OFFICE_PHONE_TEL', '2815410027'),
    'office_address' => env('OFFICE_ADDRESS', '22955 State Highway 249 Suite 26, Tomball, TX 77375'),
];
