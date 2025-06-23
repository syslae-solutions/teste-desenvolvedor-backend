<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for Cross-Origin Resource Sharing
    | or "CORS". This determines which cross-origin requests your application
    | may execute. You may pass in paths or hosts to automatically handle
    | CORS requests. By default, all origins and all HTTP methods are allowed.
    |
    */

    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173', 'http://127.0.0.1:5173'], // <<-- ENDEREÇO DO SEU REACT
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Permite todos os cabeçalhos (incluindo Authorization)
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, // Mantenha 'false' se estiver usando tokens (Sanctum)

];
