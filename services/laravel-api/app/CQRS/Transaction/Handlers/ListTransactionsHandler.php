<?php

namespace App\CQRS\Transaction\Handlers;

use App\CQRS\Transaction\Queries\ListTransactionsQuery;
use App\Repositories\Transaction\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListTransactionsHandler
{
    public function __construct(
        private readonly TransactionRepositoryInterface $repository,
    ) {}

    public function handle(ListTransactionsQuery $query): Collection
    {
        return $this->repository->allForUser($query->userId, $query->month, $query->categoryId);
    }
}
