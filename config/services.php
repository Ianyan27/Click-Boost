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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'clickup' => [
        'base_url' => env('CLICKUP_BASE_URL'),
        'token' => env('CLICKUP_API_TOKEN'),
        'clickup' => [
            'base_url' => env('CLICKUP_BASE_URL'),
            'token' => env('CLICKUP_TOKEN'),
            'list_id' => env('CLICKUP_LIST_ID'), // Members List ID: 901613065989
            'email_field_id' => env('CLICKUP_EMAIL_FIELD_ID'),
            'role_field_id' => env('CLICKUP_ROLE_FIELD_ID'),
        ],
    ]
];
