<?php

namespace App\Services\Statement;

use App\Models\Statement;
use App\Repositories\Statement\StatementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class StatementService implements StatementServiceInterface
{
    public function __construct(
        private readonly StatementRepositoryInterface $statementRepository,
    ) {}

    public function index(int $userId): Collection
    {
        return $this->statementRepository->allForUser($userId);
    }

    public function pendingTransactions(int $userId, int $statementId): Collection
    {
        $statement = $this->statementRepository->findForUser($userId, $statementId);

        return $this->statementRepository->pendingTransactions($statement);
    }

    public function import(int $userId, int $statementId): void
    {
        $statement = $this->statementRepository->findForUser($userId, $statementId);

        $this->statementRepository->markImported($statement);
    }
}
