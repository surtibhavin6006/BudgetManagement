<?php

namespace App\CQRS\Statement\Queries;

final class GetPendingTransactionsQuery
{
    public function __construct(
        public readonly int $userId,
        public readonly int $statementId,
    ) {}
}
