<?php

use App\Http\Controllers\AuthController;

/** @var \Liberta\Router\Router $router */

$router->group(['prefix' => '', 'middleware' => ['service_auth']], function ($router) {
    $router->post('/validate', [AuthController::class, 'validate']);
});
