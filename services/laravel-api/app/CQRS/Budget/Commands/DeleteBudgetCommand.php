<?php

namespace App\CQRS\Budget\Commands;

use App\Models\Budget;

final class DeleteBudgetCommand
{
    public function __construct(
        public readonly Budget $budget,
    ) {}
}
