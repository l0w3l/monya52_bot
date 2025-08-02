<?php

return [
    'inline' => [
        'limit' => (int) env('MONYA_INLINE_LIMIT', 10),
        'ttl' => (int) env('MONYA_INLINE_TTL', 300),
        'is_personal' => (boolean) env('MONYA_INLINE_IS_PERSONAL', false),
    ],
];
