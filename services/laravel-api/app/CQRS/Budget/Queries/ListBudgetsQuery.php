<?php

namespace App\CQRS\Budget\Queries;

final class ListBudgetsQuery
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $month,
    ) {}
}
