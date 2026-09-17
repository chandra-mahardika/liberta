<?php

namespace Modules\Crm;

use Liberta\Router\Module;
use Liberta\Router\Router;

class CrmModule implements Module
{
    public function name(): string
    {
        return 'crm';
    }

    public function routes(Router $router): void
    {
        $router->group(['prefix' => '/crm'], function (Router $router) {
            $router->get('/contacts', [\Modules\Crm\Http\Controllers\ContactController::class, 'index']);
            $router->get('/contacts/{id}', [\Modules\Crm\Http\Controllers\ContactController::class, 'show']);
            $router->post('/contacts', [\Modules\Crm\Http\Controllers\ContactController::class, 'store']);
            $router->put('/contacts/{id}', [\Modules\Crm\Http\Controllers\ContactController::class, 'update']);
            $router->delete('/contacts/{id}', [\Modules\Crm\Http\Controllers\ContactController::class, 'destroy']);

            $router->get('/leads', [\Modules\Crm\Http\Controllers\LeadController::class, 'index']);
            $router->get('/leads/{id}', [\Modules\Crm\Http\Controllers\LeadController::class, 'show']);
            $router->post('/leads', [\Modules\Crm\Http\Controllers\LeadController::class, 'store']);
            $router->put('/leads/{id}', [\Modules\Crm\Http\Controllers\LeadController::class, 'update']);
            $router->delete('/leads/{id}', [\Modules\Crm\Http\Controllers\LeadController::class, 'destroy']);

            $router->get('/opportunities', [\Modules\Crm\Http\Controllers\OpportunityController::class, 'index']);
            $router->get('/opportunities/{id}', [\Modules\Crm\Http\Controllers\OpportunityController::class, 'show']);
            $router->post('/opportunities', [\Modules\Crm\Http\Controllers\OpportunityController::class, 'store']);
            $router->put('/opportunities/{id}', [\Modules\Crm\Http\Controllers\OpportunityController::class, 'update']);
            $router->delete('/opportunities/{id}', [\Modules\Crm\Http\Controllers\OpportunityController::class, 'destroy']);

            $router->get('/activities', [\Modules\Crm\Http\Controllers\ActivityController::class, 'index']);
            $router->get('/activities/{id}', [\Modules\Crm\Http\Controllers\ActivityController::class, 'show']);
            $router->post('/activities', [\Modules\Crm\Http\Controllers\ActivityController::class, 'store']);
            $router->put('/activities/{id}', [\Modules\Crm\Http\Controllers\ActivityController::class, 'update']);
            $router->delete('/activities/{id}', [\Modules\Crm\Http\Controllers\ActivityController::class, 'destroy']);

            $router->get('/notes', [\Modules\Crm\Http\Controllers\NoteController::class, 'index']);
            $router->get('/notes/{id}', [\Modules\Crm\Http\Controllers\NoteController::class, 'show']);
            $router->post('/notes', [\Modules\Crm\Http\Controllers\NoteController::class, 'store']);
            $router->put('/notes/{id}', [\Modules\Crm\Http\Controllers\NoteController::class, 'update']);
            $router->delete('/notes/{id}', [\Modules\Crm\Http\Controllers\NoteController::class, 'destroy']);
        });
    }

    public function middleware(): array
    {
        return ['auth', 'rbac'];
    }
}
