<?php

use Liberta\Router\Router;
use Liberta\Router\Middleware\MiddlewarePipeline;
use Liberta\Http\Request;
use Liberta\Http\Response;
use Liberta\Sql\DB;
use App\Repositories\UserRepository;
use App\Services\TokenService;
use App\Http\Controllers\AuthController;

$router      = require __DIR__.'/../routes/api.php';
$middlewares = require __DIR__.'/../config/middleware.php';
$config      = require __DIR__.'/../config/services.php';

try {

    $route = $router->dispatch(
        $_SERVER['REQUEST_METHOD'],
        parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    );

    if (!$route) {
        Response::error('Not Found', 404)->send();
        exit;
    }

    $request = (new Request(
        $_SERVER['REQUEST_METHOD'],
        $_SERVER['REQUEST_URI'],
        getallheaders(),
        $_GET,
        $_POST
    ))->withParams($route['params']);

    // --- explicit dependency wiring ---
    $db          = new DB($config['database']);
    $users       = new UserRepository($db);
    $tokenService = new TokenService(
        $users,
        $config['jwt']['secret'],
        $config['jwt']['algorithm'] ?? 'HS256'
    );
    $controller  = new AuthController($tokenService);

    $pipeline = new MiddlewarePipeline(
        $route['middlewares'],
        $middlewares
    );

    $response = $pipeline->handle($request, function ($request) use ($controller, $route) {
        [$class, $method] = $route['handler'];
        // controller already wired, just call method
        return $controller->$method($request);
    });

    if (!$response instanceof Response) {
        throw new RuntimeException('Controller must return Response');
    }

    $response->send();

} catch (Throwable $e) {

    $statusCode = $e->getCode() ?: 500;

    $message = match (true) {
        $e instanceof \Liberta\Exception\ValidationException => $e->getMessage(),
        $e instanceof \Liberta\Exception\NotFoundException => 'Not Found',
        $e instanceof \Liberta\Exception\BadRequestException => 'Bad Request',
        $e instanceof \Liberta\Exception\UnauthorizedException => 'Unauthorized',
        $e instanceof \Liberta\Exception\ForbiddenException => 'Forbidden',
        $e instanceof \Liberta\Exception\TooManyRequestsException => 'Too Many Requests',
        default => 'Internal Server Error',
    };

    error_log(sprintf(
        '[%s] %s in %s:%d',
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));

    Response::error($message, $statusCode)->send();
}
