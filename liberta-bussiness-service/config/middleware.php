<?php

use App\Http\Middleware\RbacMiddleware;
use Liberta\Rbac\Client\HttpAuthClient;

$config = require __DIR__.'/services.php';

$authClient = new HttpAuthClient(
    $config['auth']['url'],
    $config['auth']['key']
);

return [
    'rbac' => fn() => new RbacMiddleware($authClient),
];
