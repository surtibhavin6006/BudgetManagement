<?php

namespace App\CQRS\Budget\Commands;

final class CreateBudgetCommand
{
    public function __construct(
        public readonly int    $userId,
        public readonly int    $categoryId,
        public readonly string $month,
        public readonly float  $amountLimit,
    ) {}
}
