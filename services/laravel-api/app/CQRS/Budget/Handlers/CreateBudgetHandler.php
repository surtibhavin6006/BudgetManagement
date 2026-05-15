<?php

namespace App\CQRS\Budget\Handlers;

use App\CQRS\Budget\Commands\CreateBudgetCommand;
use App\Models\Budget;
use App\Repositories\Budget\BudgetRepositoryInterface;

class CreateBudgetHandler
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
    ) {}

    public function handle(CreateBudgetCommand $command): Budget
    {
        return $this->repository->create(
            $command->userId,
            $command->categoryId,
            $command->month,
            $command->amountLimit,
        );
    }
}
