<?php

namespace App\CQRS\Budget\Commands;

use App\Models\Budget;

final class UpdateBudgetCommand
{
    public function __construct(
        public readonly Budget $budget,
        public readonly float  $amountLimit,
    ) {}
}
