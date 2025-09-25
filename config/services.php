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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'fcm' => [
        'key' => env('FCM_KEY'),
        'project_id' => env('FCM_PROJECT_ID'),
        'credentials_file_path' => env('FCM_CREDENTIALS_FILE_PATH', storage_path('app/firebase/firebase.json')),
    ],
    'finix' => [
        'mode' => env('FINIX_MODE', 'sandbox'),
        'sandbox' => [
            'base_url' => env('FINIX_SANDBOX_BASE_URL', 'https://sandbox.finix.io'),
            'user_name' => env('FINIX_SANDBOX_USER_NAME'),
            'password' => env('FINIX_SANDBOX_PASSWORD'),
            'api_version' => env('FINIX_SANDBOX_API_VERSION', '2022-02-01'),
        ],
        'production' => [
            'base_url' => env('FINIX_PRODUCTION_BASE_URL', 'https://api.finix.io'),
            'user_name' => env('FINIX_PRODUCTION_USER_NAME'),
            'password' => env('FINIX_PRODUCTION_PASSWORD'),
            'api_version' => env('FINIX_PRODUCTION_API_VERSION', '2022-02-01'),
        ],
    ],

];
