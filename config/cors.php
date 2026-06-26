<?php

return [

    'paths' => [
        'api/*',
        'v1/*',
        'sanctum/csrf-cookie'
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'https://mammary-lagoon-headboard.ngrok-free.dev',
        'https://khareej-qader.vercel.app'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
