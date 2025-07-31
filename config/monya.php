<?php

return [
    'inline' => [
        'limit' => env('MONYA_INLINE_LIMIT', 10),
        'ttl' => env('MONYA_INLINE_TTL', 300),
        'is_personal' => env('MONYA_INLINE_IS_PERSONAL', false),
    ],
];
