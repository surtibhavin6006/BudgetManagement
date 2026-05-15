<?php

namespace App\CQRS\Budget\Handlers;

use App\CQRS\Budget\Commands\DeleteBudgetCommand;
use App\Repositories\Budget\BudgetRepositoryInterface;

class DeleteBudgetHandler
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
    ) {}

    public function handle(DeleteBudgetCommand $command): void
    {
        $this->repository->delete($command->budget);
    }
}
