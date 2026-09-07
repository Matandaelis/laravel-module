<?php

return [
    'default_cycle' => env('ROSCA_DEFAULT_CYCLE', 'monthly'),

    // Selection strategy: round_robin or weighted_random
    'selection_strategy' => env('ROSCA_SELECTION_STRATEGY', 'round_robin'),

    // Notify winners when selected
    'notify_on_win' => env('ROSCA_NOTIFY_ON_WIN', true),

    // Schedule for closing rounds (cron expression) — default daily at midnight
    'close_rounds_schedule' => env('ROSCA_CLOSE_ROUNDS_SCHEDULE', '0 0 * * *'),

    // Payment gateway: 'mpesa' or 'manual'
    'gateway' => env('ROSCA_GATEWAY', 'mpesa'),

    'mpesa' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
        'environment' => env('MPESA_ENV', 'sandbox'), // sandbox|production
        'b2c_shortcode' => env('MPESA_B2C_SHORTCODE'),
        'b2c_initiator_name' => env('MPESA_B2C_INITIATOR'),
        'b2c_security_credential' => env('MPESA_B2C_SECURITY_CREDENTIAL'),
        'callback_url' => env('MPESA_CALLBACK_URL'),
    ],
];
