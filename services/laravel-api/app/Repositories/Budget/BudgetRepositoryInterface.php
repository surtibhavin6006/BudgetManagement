<?php

namespace App\Repositories\Budget;

use App\DTOs\Budget\StoreBudgetDTO;
use App\DTOs\Budget\UpdateBudgetDTO;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;

interface BudgetRepositoryInterface
{
    public function allForUserByMonth(int $userId, string $month): Collection;

    public function create(StoreBudgetDTO $dto): Budget;

    public function update(Budget $budget, UpdateBudgetDTO $dto): Budget;

    public function delete(Budget $budget): void;

    public function deleteAllForCategory(int $categoryId): void;

    public function restoreAllForCategory(int $categoryId): void;
}
