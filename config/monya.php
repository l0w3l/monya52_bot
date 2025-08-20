<?php

return [
    'inline' => [
        'limit' => (int) env('MONYA_INLINE_LIMIT', 10),
        'ttl' => (int) env('MONYA_INLINE_TTL', 300),
        'is_personal' => (bool) env('MONYA_INLINE_IS_PERSONAL', false),
    ],

    'hosted_url' => env('MONYA_HOSTED_URL'),
];
