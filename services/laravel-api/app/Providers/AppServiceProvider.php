<?php

namespace App\Providers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Statement;
use App\Models\User;
use App\Observers\CategoryObserver;
use App\Observers\UserObserver;
use App\Policies\BudgetPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\StatementPolicy;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Budget::class, BudgetPolicy::class);
        Gate::policy(Statement::class, StatementPolicy::class);

        User::observe(UserObserver::class);
        Category::observe(CategoryObserver::class);

        Scramble::afterOpenApiGenerated(function (OpenApi $openApi) {
            $openApi->secure(SecurityScheme::http('bearer'));
        });
    }
}
