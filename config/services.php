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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    /*
    | Avisos de visita a la página de cierre (ntfy / Discord).
    | Usar config() en código — no env() — para que funcione con config:cache.
    */
    'site_visit' => [
        // Fallback: el topic ya está en uso en el celular; evita silencio si falta la env en Render.
        'ntfy_topic' => env('SITE_VISIT_NTFY_TOPIC', 'diario-nahysh-visitas-5660d0'),
        'discord_webhook' => env('SITE_VISIT_DISCORD_WEBHOOK'),
        'throttle_minutes' => (int) env('SITE_VISIT_THROTTLE_MINUTES', 15),
    ],

];
