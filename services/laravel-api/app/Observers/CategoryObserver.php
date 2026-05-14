<?php

namespace App\Observers;

use App\Models\Category;
use App\Repositories\Budget\BudgetRepositoryInterface;

class CategoryObserver
{
    public function __construct(
        private readonly BudgetRepositoryInterface $budgetRepository,
    ) {}

    public function deleting(Category $category): void
    {
        $this->budgetRepository->deleteAllForCategory($category->id);
    }

    public function restoring(Category $category): void
    {
        $this->budgetRepository->restoreAllForCategory($category->id);
    }
}
