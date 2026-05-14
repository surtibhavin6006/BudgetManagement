<?php

namespace App\Services\Statement;

use App\Models\Statement;
use Illuminate\Database\Eloquent\Collection;

interface StatementServiceInterface
{
    public function index(int $userId): Collection;

    public function pendingTransactions(int $userId, int $statementId): Collection;

    public function import(int $userId, int $statementId): void;
}
