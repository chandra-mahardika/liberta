<?php

use App\Http\Controllers\HealthController;

/** @var \Liberta\Router\Router $router */

$router->get('/health', [HealthController::class, 'check']);
