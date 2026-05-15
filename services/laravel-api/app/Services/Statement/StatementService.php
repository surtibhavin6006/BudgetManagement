<?php

namespace App\Services\Statement;

use App\CQRS\Statement\Commands\ImportStatementCommand;
use App\CQRS\Statement\Queries\GetPendingTransactionsQuery;
use App\CQRS\Statement\Queries\ListStatementsQuery;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class StatementService extends BaseService implements StatementServiceInterface
{
    public function list(int $userId): Collection
    {
        return $this->bus->dispatch(new ListStatementsQuery($userId));
    }

    public function pendingTransactions(int $userId, int $statementId): Collection
    {
        return $this->bus->dispatch(new GetPendingTransactionsQuery($userId, $statementId));
    }

    public function import(int $userId, int $statementId): void
    {
        $this->bus->dispatch(new ImportStatementCommand($userId, $statementId));
    }
}
