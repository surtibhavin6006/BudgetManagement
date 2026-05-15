<?php

namespace App\CQRS\Statement\Queries;

final class ListStatementsQuery
{
    public function __construct(
        public readonly int $userId,
    ) {}
}
