<?php

namespace App\CQRS\Budget\Handlers;

use App\CQRS\Budget\Queries\ListBudgetsQuery;
use App\Repositories\Budget\BudgetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListBudgetsHandler
{
    public function __construct(
        private readonly BudgetRepositoryInterface $repository,
    ) {}

    public function handle(ListBudgetsQuery $query): Collection
    {
        return $this->repository->allForUserByMonth($query->userId, $query->month);
    }
}
