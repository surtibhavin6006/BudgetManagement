<?php

namespace App\Services\Transaction;

use App\CQRS\Transaction\Queries\ListTransactionsQuery;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class TransactionService extends BaseService implements TransactionServiceInterface
{
    public function list(int $userId, ?string $month, ?int $categoryId): Collection
    {
        return $this->bus->dispatch(new ListTransactionsQuery($userId, $month, $categoryId));
    }
}
