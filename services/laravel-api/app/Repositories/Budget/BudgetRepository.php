<?php

namespace App\Repositories\Budget;

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

    public function create(int $userId, int $categoryId, string $month, float $amountLimit): Budget
    {
        return Budget::create([
            'user_id'      => $userId,
            'category_id'  => $categoryId,
            'month'        => $month,
            'amount_limit' => $amountLimit,
        ]);
    }

    public function update(Budget $budget, float $amountLimit): Budget
    {
        $budget->update(['amount_limit' => $amountLimit]);

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
