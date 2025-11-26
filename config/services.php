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

    'uptime-monitor' => [
        'check_interval' => env('UPTIME_MONITOR_CHECK_INTERVAL', 2), // in minutes
        'http_timeout' => env('UPTIME_CHECK_TIMEOUT', 10), // in seconds
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ]

];
