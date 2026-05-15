<?php

namespace App\DTOs\Budget;

readonly class StoreBudgetDTO
{
    public function __construct(
        public int    $userId,
        public int    $categoryId,
        public string $month,
        public float  $amountLimit,
    ) {}
}
