<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function allForUser(int $userId, ?string $month, ?int $categoryId): Collection
    {
        return Transaction::with('category')
            ->where('user_id', $userId)
            ->where('is_confirmed', true)
            ->when($month, fn ($q) => $q->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$month]))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderByDesc('date')
            ->get();
    }

    public function bulkCreate(array $rows): void
    {
        Transaction::insert($rows);
    }

    public function deleteAllForUser(int $userId): void
    {
        Transaction::where('user_id', $userId)->delete();
    }

    public function restoreAllForUser(int $userId): void
    {
        Transaction::onlyTrashed()->where('user_id', $userId)->restore();
    }
}
