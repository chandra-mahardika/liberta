<?php

use Liberta\Config\Env;

// Load .env file
Env::load(__DIR__ . '/../.env');

return [
    'auth' => [
        'url' => Env::getString('AUTH_URL', 'http://auth-service.internal'),
        'key' => Env::required('AUTH_KEY'),
    ],

    'database' => [
        'driver'   => Env::getString('DB_DRIVER', 'mysql'),
        'host'     => Env::getString('DB_HOST', 'localhost'),
        'port'     => Env::getInt('DB_PORT', 3306),
        'charset'  => Env::getString('DB_CHARSET', 'utf8mb4'),
        'database' => Env::getString('DB_DATABASE', 'liberta_auth'),
        'username' => Env::getString('DB_USERNAME', 'root'),
        'password' => Env::getString('DB_PASSWORD', ''),
    ],

    'jwt' => [
        'secret'    => Env::required('JWT_SECRET'),
        'algorithm' => Env::getString('JWT_ALGORITHM', 'HS256'),
        'expiry'    => Env::getInt('JWT_EXPIRY', 3600),
    ],
];
