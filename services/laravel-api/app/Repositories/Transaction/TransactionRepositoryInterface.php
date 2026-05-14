<?php

namespace App\Repositories\Transaction;

use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function allForUser(int $userId, ?string $month, ?int $categoryId): Collection;

    public function bulkCreate(array $rows): void;

    public function deleteAllForUser(int $userId): void;

    public function restoreAllForUser(int $userId): void;
}
