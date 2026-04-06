<?php

return [
    'app_name' => env('APP_NAME', 'NexaPHP'),
    'app_url' => env('APP_URL', 'http://localhost:7272'),
    'db' => [
        'driver' => env('DB_CONNECTION', 'sqlite'), 
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', '3306'),
        'user' => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'dbname' => env('DB_NAME', 'nexaphp_db'),
        'db_path' => __DIR__ . '/storage/db/database.sqlite',
    ],
    'mail' => [
        'host' => env('MAIL_HOST', 'localhost'),
        'port' => env('MAIL_PORT', 587),
        'user' => env('MAIL_USERNAME', ''),
        'password' => env('MAIL_PASSWORD', ''),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'from_email' => env('MAIL_FROM_ADDRESS', 'no-reply@nexaphp.com'),
        'from_name' => env('MAIL_FROM_NAME', env('APP_NAME', 'NexaPHP')),
    ],
    'timezone' => 'UTC'
];
