<?php

namespace App\DTOs\Budget;

readonly class UpdateBudgetDTO
{
    public function __construct(
        public float $amountLimit,
    ) {}
}
