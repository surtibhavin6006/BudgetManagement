<?php

namespace App\CQRS\Transaction\Queries;

final class ListTransactionsQuery
{
    public function __construct(
        public readonly int     $userId,
        public readonly ?string $month,
        public readonly ?int    $categoryId,
    ) {}
}
