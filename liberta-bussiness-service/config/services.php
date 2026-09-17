<?php

use Liberta\Config\Env;

// Load .env file
Env::load(__DIR__ . '/../.env');

return [
    'auth' => [
        'url' => Env::getString('AUTH_URL', 'http://auth-service.internal'),
        'key' => Env::required('AUTH_KEY'),
    ],

    // Multi-database connections
    // Each key is a connection name. 'primary' is the default.
    // For multi-tenant: each tenant gets its own connection name.
    'connections' => [
        'primary' => [
            'driver'   => Env::getString('DB_DRIVER', 'mysql'),
            'host'     => Env::getString('DB_HOST', 'localhost'),
            'port'     => Env::getInt('DB_PORT', 3306),
            'charset'  => Env::getString('DB_CHARSET', 'utf8mb4'),
            'database' => Env::getString('DB_DATABASE', 'business'),
            'username' => Env::getString('DB_USERNAME', 'root'),
            'password' => Env::getString('DB_PASSWORD', ''),
        ],

        // Example: tenant-specific databases
        // 'tenant_a' => [
        //     'driver'   => 'mysql',
        //     'host'     => 'localhost',
        //     'port'     => 3306,
        //     'charset'  => 'utf8mb4',
        //     'database' => 'business_tenant_a',
        //     'username' => 'root',
        //     'password' => '',
        // ],
    ],
];
