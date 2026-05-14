<?php

namespace App\Providers;

use App\Repositories\Auth\AuthRepository;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Budget\BudgetRepository;
use App\Repositories\Budget\BudgetRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Statement\StatementRepository;
use App\Repositories\Statement\StatementRepositoryInterface;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class,        AuthRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class,    CategoryRepository::class);
        $this->app->bind(BudgetRepositoryInterface::class,      BudgetRepository::class);
        $this->app->bind(StatementRepositoryInterface::class,   StatementRepository::class);
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
    }
}
