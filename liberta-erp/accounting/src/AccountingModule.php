<?php

namespace Modules\Accounting;

use Liberta\Router\Module;
use Liberta\Router\Router;

class AccountingModule implements Module
{
    public function name(): string
    {
        return 'accounting';
    }

    public function routes(Router $router): void
    {
        $router->group(['prefix' => '/accounting'], function (Router $router) {
            $router->get('/chart-of-accounts', [\Modules\Accounting\Http\Controllers\ChartOfAccountsController::class, 'index']);
            $router->get('/chart-of-accounts/{id}', [\Modules\Accounting\Http\Controllers\ChartOfAccountsController::class, 'show']);
            $router->post('/chart-of-accounts', [\Modules\Accounting\Http\Controllers\ChartOfAccountsController::class, 'store']);
            $router->put('/chart-of-accounts/{id}', [\Modules\Accounting\Http\Controllers\ChartOfAccountsController::class, 'update']);
            $router->delete('/chart-of-accounts/{id}', [\Modules\Accounting\Http\Controllers\ChartOfAccountsController::class, 'destroy']);

            $router->get('/journal-entries', [\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'index']);
            $router->get('/journal-entries/{id}', [\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'show']);
            $router->post('/journal-entries', [\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'store']);
            $router->put('/journal-entries/{id}', [\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'update']);
            $router->delete('/journal-entries/{id}', [\Modules\Accounting\Http\Controllers\JournalEntryController::class, 'destroy']);

            $router->get('/ledger', [\Modules\Accounting\Http\Controllers\LedgerController::class, 'index']);
            $router->get('/ledger/{id}', [\Modules\Accounting\Http\Controllers\LedgerController::class, 'show']);

            $router->get('/trial-balance', [\Modules\Accounting\Http\Controllers\TrialBalanceController::class, 'index']);

            $router->get('/income-statement', [\Modules\Accounting\Http\Controllers\IncomeStatementController::class, 'index']);

            $router->get('/balance-sheet', [\Modules\Accounting\Http\Controllers\BalanceSheetController::class, 'index']);
        });
    }

    public function middleware(): array
    {
        return ['auth', 'rbac'];
    }
}
