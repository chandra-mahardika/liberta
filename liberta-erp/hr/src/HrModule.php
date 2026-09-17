<?php

namespace Modules\Hr;

use Liberta\Router\Module;
use Liberta\Router\Router;

class HrModule implements Module
{
    public function name(): string
    {
        return 'hr';
    }

    public function routes(Router $router): void
    {
        $router->group(['prefix' => '/hr'], function (Router $router) {
            $router->get('/employees', [\Modules\Hr\Http\Controllers\EmployeeController::class, 'index']);
            $router->get('/employees/{id}', [\Modules\Hr\Http\Controllers\EmployeeController::class, 'show']);
            $router->post('/employees', [\Modules\Hr\Http\Controllers\EmployeeController::class, 'store']);
            $router->put('/employees/{id}', [\Modules\Hr\Http\Controllers\EmployeeController::class, 'update']);
            $router->delete('/employees/{id}', [\Modules\Hr\Http\Controllers\EmployeeController::class, 'destroy']);

            $router->get('/departments', [\Modules\Hr\Http\Controllers\DepartmentController::class, 'index']);
            $router->get('/departments/{id}', [\Modules\Hr\Http\Controllers\DepartmentController::class, 'show']);
            $router->post('/departments', [\Modules\Hr\Http\Controllers\DepartmentController::class, 'store']);
            $router->put('/departments/{id}', [\Modules\Hr\Http\Controllers\DepartmentController::class, 'update']);
            $router->delete('/departments/{id}', [\Modules\Hr\Http\Controllers\DepartmentController::class, 'destroy']);

            $router->get('/positions', [\Modules\Hr\Http\Controllers\PositionController::class, 'index']);
            $router->get('/positions/{id}', [\Modules\Hr\Http\Controllers\PositionController::class, 'show']);
            $router->post('/positions', [\Modules\Hr\Http\Controllers\PositionController::class, 'store']);
            $router->put('/positions/{id}', [\Modules\Hr\Http\Controllers\PositionController::class, 'update']);
            $router->delete('/positions/{id}', [\Modules\Hr\Http\Controllers\PositionController::class, 'destroy']);

            $router->get('/attendance', [\Modules\Hr\Http\Controllers\AttendanceController::class, 'index']);
            $router->get('/attendance/{id}', [\Modules\Hr\Http\Controllers\AttendanceController::class, 'show']);
            $router->post('/attendance', [\Modules\Hr\Http\Controllers\AttendanceController::class, 'store']);
            $router->put('/attendance/{id}', [\Modules\Hr\Http\Controllers\AttendanceController::class, 'update']);

            $router->get('/payroll', [\Modules\Hr\Http\Controllers\PayrollController::class, 'index']);
            $router->get('/payroll/{id}', [\Modules\Hr\Http\Controllers\PayrollController::class, 'show']);
            $router->post('/payroll', [\Modules\Hr\Http\Controllers\PayrollController::class, 'store']);
        });
    }

    public function middleware(): array
    {
        return ['auth', 'rbac'];
    }
}
