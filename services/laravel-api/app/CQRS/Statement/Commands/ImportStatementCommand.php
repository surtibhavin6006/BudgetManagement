<?php

namespace App\CQRS\Statement\Commands;

final class ImportStatementCommand
{
    public function __construct(
        public readonly int $userId,
        public readonly int $statementId,
    ) {}
}
