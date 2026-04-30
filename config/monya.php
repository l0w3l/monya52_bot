<?php

return [
    'chat_id' => (int) env('MONYA_CHAT_ID'),

    'storages' => [
        'music' => (int) env('MONYA_MUSIC_STORAGE'),
        'memes' => (int) env('MONYA_MEMES_STORAGE'),
        'movies' => (int) env('MONYA_MOVIES_STORAGE'),
    ],

    'inline' => [
        'limit' => (int) env('MONYA_INLINE_LIMIT', 10),
        'ttl' => (int) env('MONYA_INLINE_TTL', default: 300),
        'is_personal' => (bool) env('MONYA_INLINE_IS_PERSONAL', false),
    ],

    'hosted_url' => env('MONYA_HOSTED_URL'),
    'token' => env('MONYA_TOKEN'),
];
