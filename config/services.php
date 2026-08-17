<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'tap' => [
        'public_key' => env('TAP_PUBLIC_KEY'),
        'secret_key' => env('TAP_SECRET_KEY'),
        'base_url'   => env('TAP_BASE_URL', 'https://api.tap.company'),
        'currency'   => env('TAP_CURRENCY', 'SAR'),
        // Where the client's browser lands after completing the hosted payment page.
        'frontend_redirect_url' => env('TAP_FRONTEND_REDIRECT_URL', env('APP_URL')),
    ],

];
