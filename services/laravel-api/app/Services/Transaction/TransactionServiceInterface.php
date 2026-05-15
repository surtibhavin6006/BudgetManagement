<?php

namespace App\Services\Transaction;

use Illuminate\Database\Eloquent\Collection;

interface TransactionServiceInterface
{
    public function list(int $userId, ?string $month, ?int $categoryId): Collection;
}
