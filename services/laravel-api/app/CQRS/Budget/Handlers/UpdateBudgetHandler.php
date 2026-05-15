<?php

namespace App\CQRS\Budget\Handlers;

use App\CQRS\Budget\Commands\UpdateBudgetCommand;
use App\Models\Budget;
use App\Repositories\Budget\BudgetRepositoryInterface;

class UpdateBudgetHandler
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
    ) {}

    public function handle(UpdateBudgetCommand $command): Budget
    {
        return $this->repository->update($command->budget, $command->amountLimit);
    }
}
