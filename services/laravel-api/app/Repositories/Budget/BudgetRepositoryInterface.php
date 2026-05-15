<?php

namespace App\Repositories\Budget;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;

interface BudgetRepositoryInterface
{
    public function allForUserByMonth(int $userId, string $month): Collection;

    public function create(int $userId, int $categoryId, string $month, float $amountLimit): Budget;

    public function update(Budget $budget, float $amountLimit): Budget;

    public function delete(Budget $budget): void;

    public function deleteAllForCategory(int $categoryId): void;

    public function restoreAllForCategory(int $categoryId): void;
}
