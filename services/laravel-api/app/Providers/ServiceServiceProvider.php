<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Budget\BudgetService;
use App\Services\Budget\BudgetServiceInterface;
use App\Services\Category\CategoryService;
use App\Services\Category\CategoryServiceInterface;
use App\Services\Statement\StatementService;
use App\Services\Statement\StatementServiceInterface;
use App\Services\Transaction\TransactionService;
use App\Services\Transaction\TransactionServiceInterface;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class,        AuthService::class);
        $this->app->bind(CategoryServiceInterface::class,    CategoryService::class);
        $this->app->bind(BudgetServiceInterface::class,      BudgetService::class);
        $this->app->bind(StatementServiceInterface::class,   StatementService::class);
        $this->app->bind(TransactionServiceInterface::class, TransactionService::class);
    }
}
