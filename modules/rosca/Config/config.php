<?php

return [
    'default_cycle' => env('ROSCA_DEFAULT_CYCLE', 'monthly'),

    // Selection strategy: round_robin or weighted_random
    'selection_strategy' => env('ROSCA_SELECTION_STRATEGY', 'round_robin'),

    // Notify winners when selected
    'notify_on_win' => env('ROSCA_NOTIFY_ON_WIN', true),

    // Schedule for closing rounds (cron expression) — default daily at midnight
    'close_rounds_schedule' => env('ROSCA_CLOSE_ROUNDS_SCHEDULE', '0 0 * * *'),
];
