<?php

namespace App\Services\Statement;

use Illuminate\Database\Eloquent\Collection;

interface StatementServiceInterface
{
    public function list(int $userId): Collection;

    public function pendingTransactions(int $userId, int $statementId): Collection;

    public function import(int $userId, int $statementId): void;
}
