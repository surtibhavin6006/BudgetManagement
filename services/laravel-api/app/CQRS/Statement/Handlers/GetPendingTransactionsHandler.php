<?php

namespace App\CQRS\Statement\Handlers;

use App\CQRS\Statement\Queries\GetPendingTransactionsQuery;
use App\Repositories\Statement\StatementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetPendingTransactionsHandler
{
    public function __construct(
        private readonly StatementRepositoryInterface $repository,
    ) {}

    public function handle(GetPendingTransactionsQuery $query): Collection
    {
        $statement = $this->repository->findForUser($query->userId, $query->statementId);

        return $this->repository->pendingTransactions($statement);
    }
}
