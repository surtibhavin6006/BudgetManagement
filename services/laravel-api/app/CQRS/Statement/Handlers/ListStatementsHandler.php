<?php

namespace App\CQRS\Statement\Handlers;

use App\CQRS\Statement\Queries\ListStatementsQuery;
use App\Repositories\Statement\StatementRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListStatementsHandler
{
    public function __construct(
        private readonly StatementRepositoryInterface $repository,
    ) {}

    public function handle(ListStatementsQuery $query): Collection
    {
        return $this->repository->allForUser($query->userId);
    }
}
