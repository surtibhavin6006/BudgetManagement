<?php

namespace App\Repositories\Budget;

use App\DTOs\Budget\StoreBudgetDTO;
use App\DTOs\Budget\UpdateBudgetDTO;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;

class BudgetRepository implements BudgetRepositoryInterface
{
    public function allForUserByMonth(int $userId, string $month): Collection
    {
        return Budget::with('category')
            ->where('user_id', $userId)
            ->where('month', $month)
            ->orderBy('created_at')
            ->get();
    }

    public function create(StoreBudgetDTO $dto): Budget
    {
        return Budget::create([
            'user_id'      => $dto->userId,
            'category_id'  => $dto->categoryId,
            'month'        => $dto->month,
            'amount_limit' => $dto->amountLimit,
        ]);
    }

    public function update(Budget $budget, UpdateBudgetDTO $dto): Budget
    {
        $budget->update(['amount_limit' => $dto->amountLimit]);

        return $budget->fresh();
    }

    public function delete(Budget $budget): void
    {
        $budget->delete();
    }

    public function deleteAllForCategory(int $categoryId): void
    {
        Budget::where('category_id', $categoryId)->delete();
    }

    public function restoreAllForCategory(int $categoryId): void
    {
        Budget::onlyTrashed()->where('category_id', $categoryId)->restore();
    }
}
