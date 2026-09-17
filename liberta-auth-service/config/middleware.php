<?php

use App\Http\Middleware\ServiceAuthMiddleware;

$config = require __DIR__.'/services.php';

return [
    'service_auth' => fn() => new ServiceAuthMiddleware($config['auth']['key']),
];
