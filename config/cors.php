<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // Dominios del FrontEnd separados por coma. Ej: https://mi-app.vercel.app,http://localhost:5173
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:5173')))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['Content-Disposition'],
    'max_age' => 0,
    'supports_credentials' => false,
];
