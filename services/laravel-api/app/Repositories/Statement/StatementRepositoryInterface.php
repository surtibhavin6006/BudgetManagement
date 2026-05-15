<?php

namespace App\Repositories\Statement;

use App\Models\Statement;
use Illuminate\Database\Eloquent\Collection;

interface StatementRepositoryInterface
{
    public function allForUser(int $userId): Collection;

    public function findForUser(int $userId, int $statementId): Statement;

    public function pendingTransactions(Statement $statement): Collection;

    public function create(array $data): Statement;

    public function markImported(Statement $statement): void;

    public function deleteAllForUser(int $userId): void;

    public function restoreAllForUser(int $userId): void;
}
