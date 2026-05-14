<?php

namespace App\Observers;

use App\Models\User;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Statement\StatementRepositoryInterface;
use App\Repositories\Transaction\TransactionRepositoryInterface;

class UserObserver
{
    public function __construct(
        private readonly CategoryRepositoryInterface    $categoryRepository,
        private readonly StatementRepositoryInterface   $statementRepository,
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function deleting(User $user): void
    {
        // Each category delete triggers CategoryObserver → cascades into budgets
        $this->categoryRepository
            ->allForUser($user->id)
            ->each(fn ($category) => $category->delete());

        $this->statementRepository->deleteAllForUser($user->id);
        $this->transactionRepository->deleteAllForUser($user->id);
    }

    public function restoring(User $user): void
    {
        // Each category restore triggers CategoryObserver → cascades into budgets
        $this->categoryRepository
            ->allTrashedForUser($user->id)
            ->each(fn ($category) => $category->restore());

        $this->statementRepository->restoreAllForUser($user->id);
        $this->transactionRepository->restoreAllForUser($user->id);
    }
}
